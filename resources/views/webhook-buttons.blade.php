@extends('layouts.app')

@section('css')
<style>
    .webhook-shell {
        max-width: 1040px;
        margin: 0 auto 48px;
    }

    .webhook-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .webhook-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 12px;
    }

    .webhook-btn {
        width: 100%;
        min-height: 48px;
        text-align: left;
    }

    .webhook-url {
        font-size: 12px;
        word-break: break-all;
        color: #64748b;
    }
</style>
@endsection

@section('content')
<div class="webhook-shell">
    <div class="webhook-card p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="mb-1">GHL Webhook Test Buttons</h4>
                <p class="text-muted mb-0">Click a button to send a sample test payload through Saltiii.</p>
            </div>
            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm">Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        <div class="webhook-grid">
            @foreach($events as $eventName => $label)
                <form method="POST" action="{{ route('webhook.buttons.trigger') }}" class="border rounded p-3">
                    @csrf
                    <input type="hidden" name="event_name" value="{{ $eventName }}">
                    <button type="submit" class="btn btn-primary webhook-btn">
                        {{ $label }}
                    </button>
                    <div class="mt-2">
                        <code>{{ $eventName }}</code>
                    </div>
                    <div class="webhook-url mt-2">
                        {{ $webhooks[$eventName] ?? 'No webhook URL configured' }}
                    </div>
                </form>
            @endforeach
        </div>
    </div>

    <div class="webhook-card p-4 mt-4">
        <h5 class="mb-3">Latest Webhook Test Logs</h5>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>HTTP</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td><code>{{ $log->event_name }}</code></td>
                            <td>{{ ucfirst($log->status) }}</td>
                            <td>{{ $log->response_status ?: '-' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($log->error_message, 80) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted">No webhook logs yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
