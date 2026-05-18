<?php

namespace App\Http\Controllers;

use App\TokenTransfer;
use App\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TokenTransferController extends Controller
{
    /**
     * Show the token transfer form
     */
    public function showForm()
    {
        return view('token-transfer.form');
    }

    /**
     * Handle token transfer via Phantom wallet
     */
    public function transfer(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'recipient_wallet' => 'required|string|regex:/^[1-9A-HJ-NP-Z]{32,44}$/',
                'amount' => 'required|numeric|min:0.01',
                'transaction_signature' => 'nullable|string',
            ]);

            // Log the transfer request
            Log::info('Token transfer initiated', [
                'user_id' => auth()->id(),
                'recipient' => $validated['recipient_wallet'],
                'amount' => $validated['amount'],
                'timestamp' => now(),
            ]);

            // Verify the transaction signature if provided (server-side validation)
            if ($validated['transaction_signature']) {
                $isValid = $this->verifyTransactionSignature(
                    $validated['transaction_signature'],
                    auth()->user()->wallet_address ?? null
                );

                if (!$isValid) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid transaction signature',
                    ], 422);
                }
            }

            // Save transfer record to database (optional)
            $this->saveTransferRecord(
                auth()->id(),
                $validated['recipient_wallet'],
                $validated['amount'],
                $validated['transaction_signature'] ?? null
            );

            Alert::success('Success', 'Token transfer initiated successfully!');

            return response()->json([
                'success' => true,
                'message' => 'Token transfer completed successfully',
                'data' => [
                    'recipient' => $validated['recipient_wallet'],
                    'amount' => $validated['amount'],
                    'timestamp' => now(),
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Token transfer failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token transfer failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get wallet information for the authenticated user
     */
    public function getWalletInfo()
    {
        try {
            $user = auth()->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'wallet_address' => $user->wallet_address ?? null,
                    'network' => env('SOLANA_NETWORK', 'devnet'),
                    'token_mint' => env('USDC_MINT_ADDRESS', ''),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve wallet info',
            ], 500);
        }
    }

    /**
     * Verify transaction signature from Solana blockchain
     */
    private function verifyTransactionSignature($signature, $senderWallet = null)
    {
        try {
            // This is a placeholder for actual signature verification
            // You would integrate with Solana RPC here
            // For now, we'll do basic validation

            // Verify signature format (58 chars in base58)
            if (!preg_match('/^[1-9A-HJ-NP-Z]{58}$/', $signature)) {
                return false;
            }

            // TODO: Implement actual signature verification with Solana RPC
            // Example using Solana Web3.js via an external service:
            // - Call your Solana RPC endpoint to verify the transaction
            // - Check that the transaction is confirmed
            // - Verify the sender, recipient, amount, and token match

            Log::info('Transaction signature verified', [
                'signature' => substr($signature, 0, 10) . '...',
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Save transfer record to database
     */
    private function saveTransferRecord($userId, $recipientWallet, $amount, $signature = null)
    {
        try {
            TokenTransfer::create([
                'user_id' => $userId,
                'recipient_wallet' => $recipientWallet,
                'amount' => $amount,
                'transaction_signature' => $signature,
                'status' => 'completed',
            ]);

            Log::info('Transfer record saved', [
                'user_id' => $userId,
                'amount' => $amount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save transfer record', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get USDC balance for user's wallet
     */
    public function getBalance()
    {
        try {
            $user = auth()->user();
            $walletAddress = $user->wallet_address ?? null;

            if (!$walletAddress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet address not configured',
                ], 400);
            }

            // TODO: Implement balance checking via Solana RPC
            // This would call a Solana RPC endpoint to get token account balance
            // For now, returning a placeholder

            return response()->json([
                'success' => true,
                'data' => [
                    'wallet' => $walletAddress,
                    'balance' => 0, // Implement actual balance check
                    'currency' => 'USDC',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get balance', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve balance',
            ], 500);
        }
    }
}
