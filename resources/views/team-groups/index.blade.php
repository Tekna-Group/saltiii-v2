@extends('layouts.header')

@section('css')
<style>
    .team-shell {
        display: grid;
        grid-template-columns: minmax(240px, 320px) 1fr;
        gap: 1rem;
    }
    .team-list-item {
        border: 1px solid #eef0f2;
        border-radius: 8px;
        padding: 12px;
        background: #fff;
    }
    .team-list-item.active {
        border-color: #3577f1;
        background: #f4f8ff;
    }
    .invite-link-box {
        border: 1px dashed #b6c2cf;
        border-radius: 8px;
        background: #f8f9fa;
        padding: 12px;
        word-break: break-all;
    }
    .invite-url {
        max-width: 320px;
        font-size: 12px;
        color: #495057;
        background: #f8f9fa;
        border: 1px solid #e9ebec;
        border-radius: 8px;
        padding: 8px 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .invite-copy-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 991px) {
        .team-shell {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
@include('error')

@if(session('invite_link'))
    <div class="alert {{ session('invite_mail_sent', true) ? 'alert-success' : 'alert-warning' }} d-flex align-items-start justify-content-between gap-3">
        <div>
            <strong>{{ session('invite_mail_sent', true) ? 'Invite email sent:' : 'Invite link ready:' }}</strong>
            @if(!session('invite_mail_sent', true))
                <div class="small mt-1">Email could not be sent. You can still copy and share this link manually.</div>
            @endif
            <div class="invite-link-box mt-2">{{ session('invite_link') }}</div>
        </div>
        <button class="btn btn-sm btn-soft-primary invite-copy-btn" type="button" data-invite-link="{{ session('invite_link') }}">
            <i class="ri-file-copy-line align-bottom me-1"></i> Copy
        </button>
    </div>
@endif

<div class="team-shell">
    <div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Groups</h5>
            </div>
            <div class="card-body">
                @forelse($groups as $group)
                    <a href="{{ route('team-groups.index', ['group' => $group->id]) }}" class="team-list-item {{ $activeGroup && $activeGroup->id === $group->id ? 'active' : '' }} mb-2 d-block text-decoration-none">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-xs">
                                <div class="avatar-title bg-primary-subtle text-primary rounded">
                                    <i class="ri-team-line"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-body">{{ $group->name }}</div>
                                <div class="fs-12 text-muted">{{ $group->members->count() }} member{{ $group->members->count() === 1 ? '' : 's' }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-muted mb-0">Create your first group for team billing and invites.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Create Group</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('team-groups.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex. Acme Ops Team" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="ri-add-line align-bottom me-1"></i> Create Group
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div>
        @if($activeGroup)
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1">{{ $activeGroup->name }}</h5>
                        <p class="text-muted mb-0">
                            Owner: {{ $activeGroup->owner->name }}
                            <span class="mx-2">|</span>
                            Billing payer: {{ optional($activeGroup->billingUser)->name ?? $activeGroup->owner->name }}
                        </p>
                    </div>
                    @if($activeGroup->owner_id === auth()->id())
                        <span class="badge bg-success-subtle text-success">Owner</span>
                    @else
                        <span class="badge bg-info-subtle text-info">Member</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($activeGroup->owner_id === auth()->id())
                        <form method="POST" action="{{ route('team-groups.invite', $activeGroup->id) }}" class="row g-2 align-items-end mb-4">
                            @csrf
                            <div class="col-md">
                                <label class="form-label">Invite Email</label>
                                <input type="email" name="email" class="form-control" placeholder="teammate@example.com" required>
                            </div>
                            <div class="col-md-auto">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ri-mail-send-line align-bottom me-1"></i> Create Invite
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="row g-3">
                        <div class="col-xl-5">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3">Members</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeGroup->members as $member)
                                                <tr>
                                                    <td>{{ $member->user->name }}</td>
                                                    <td>{{ $member->user->email }}</td>
                                                    <td><span class="badge bg-light text-body">{{ ucfirst($member->role) }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3">Invitations</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Email</th>
                                                <th>Invite Link</th>
                                                <th>Status</th>
                                                <th>Expires</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeGroup->invitations->sortByDesc('created_at') as $invite)
                                                @php
                                                    $inviteLink = route('team-groups.invite.show', $invite->token);
                                                @endphp
                                                <tr>
                                                    <td>{{ $invite->email }}</td>
                                                    <td>
                                                        <div class="invite-url" title="{{ $inviteLink }}">{{ $inviteLink }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $invite->status === 'accepted' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                                            {{ ucfirst($invite->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $invite->expires_at ? $invite->expires_at->format('M d, Y') : 'No expiry' }}</td>
                                                    <td class="text-end">
                                                        <div class="d-inline-flex gap-1">
                                                            <button type="button" class="btn btn-sm btn-soft-primary invite-copy-btn" data-invite-link="{{ $inviteLink }}" title="Copy invite link">
                                                                <i class="ri-file-copy-line"></i>
                                                            </button>
                                                            @if($activeGroup->owner_id === auth()->id() && $invite->status === 'pending')
                                                                <form method="POST" action="{{ route('team-groups.invite.resend', [$activeGroup->id, $invite->id]) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-soft-secondary" title="Resend invite email">
                                                                        <i class="ri-mail-send-line"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-muted text-center">No invites yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                            <i class="ri-team-line"></i>
                        </div>
                    </div>
                    <h5>No group yet</h5>
                    <p class="text-muted mb-0">Create a group to invite teammates and manage team payment under one owner account.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
    document.querySelectorAll('.invite-copy-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const inviteLink = this.getAttribute('data-invite-link');
            const icon = this.querySelector('i');
            const originalClass = icon.className;

            copyInviteLink(inviteLink).then(() => {
                icon.className = 'ri-check-line';
                this.classList.remove('btn-soft-primary');
                this.classList.add('btn-soft-success');

                setTimeout(() => {
                    icon.className = originalClass;
                    this.classList.remove('btn-soft-success');
                    this.classList.add('btn-soft-primary');
                }, 1400);
            });
        });
    });

    function copyInviteLink(inviteLink) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(inviteLink);
        }

        const textarea = document.createElement('textarea');
        textarea.value = inviteLink;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        return Promise.resolve();
    }
</script>
@endsection
