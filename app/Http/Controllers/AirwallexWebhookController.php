<?php

namespace App\Http\Controllers;

use App\PaymentPosting;
use App\Services\AirwallexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AirwallexWebhookController extends Controller
{
    public function handle(Request $request, AirwallexService $airwallex)
    {
        $rawBody = $request->getContent();
        $timestamp = $request->header('x-timestamp');
        $signature = $request->header('x-signature');

        if (!$airwallex->verifyWebhookSignature($timestamp, $signature, $rawBody)) {
            return response()->json(['message' => 'Invalid webhook signature.'], 401);
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            return response()->json(['message' => 'Invalid webhook payload.'], 400);
        }

        $eventName = $payload['name'] ?? $payload['event_type'] ?? '';
        $resource = $payload['data']['object'] ?? $payload['data'] ?? $payload['object'] ?? [];
        if (!is_array($resource)) {
            $resource = [];
        }
        $transferId = $resource['id'] ?? $resource['transfer_id'] ?? null;

        if (!$transferId) {
            return response()->json(['received' => true]);
        }

        $posting = PaymentPosting::with(['activities.user.salary'])
            ->where('airwallex_transfer_id', $transferId)
            ->first();

        if (!$posting) {
            Log::warning('Airwallex webhook transfer was not found', [
                'transfer_id' => $transferId,
                'event' => $eventName,
            ]);

            return response()->json(['received' => true]);
        }

        $status = strtoupper($resource['status'] ?? $this->statusFromEvent($eventName));

        // Airwallex does not guarantee webhook delivery order. Never regress a
        // completed payroll posting when an older event arrives later.
        if ($posting->status === 'Completed' && $status !== 'COMPLETED') {
            return response()->json(['received' => true]);
        }

        $updates = ['airwallex_status' => $status];

        if ($status === 'COMPLETED') {
            $updates['status'] = 'Completed';
        } elseif (in_array($status, ['FAILED', 'CANCELLED'], true)) {
            $updates['status'] = 'Payment Failed';
        } else {
            $updates['status'] = 'Processing';
        }

        $posting->update($updates);

        if ($status === 'COMPLETED') {
            foreach ($posting->activities as $activity) {
                $hourlyRate = optional($activity->user->salary)->salary ?? 0;
                $activity->payment_method = 'Airwallex';
                $activity->payment_reference = $posting->reference_number;
                $activity->payment_amount = $activity->hours * $hourlyRate;
                $activity->paid_at = now();
                $activity->payment_approved_by = $posting->payment_approved_by;
                $activity->save();
            }
        }

        Log::info('Airwallex payroll status updated', [
            'payment_posting_id' => $posting->id,
            'transfer_id' => $transferId,
            'status' => $status,
            'event' => $eventName,
        ]);

        return response()->json(['received' => true]);
    }

    private function statusFromEvent($eventName)
    {
        $eventName = strtolower((string) $eventName);

        foreach (['completed', 'failed', 'cancelled', 'scheduled', 'processing', 'in_approval'] as $status) {
            if (substr($eventName, -strlen($status)) === $status) {
                return $status;
            }
        }

        return 'processing';
    }
}
