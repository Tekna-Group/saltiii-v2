@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card mt-4">
            <div class="card-body p-4 text-center">
                <div class="avatar-lg mx-auto mb-3">
                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                        <i class="ri-team-line"></i>
                    </div>
                </div>
                <h4 class="mb-2">Join {{ $invitation->group->name }}</h4>
                <p class="text-muted mb-4">
                    {{ $invitation->group->owner->name }} invited {{ $invitation->email }} to join this group for team access and billing.
                </p>

                @if(!$invitation->isPending())
                    <div class="alert alert-warning mb-0">
                        This invitation is no longer available.
                    </div>
                @elseif(auth()->check())
                    <form method="POST" action="{{ route('team-groups.invite.join', $invitation->token) }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">
                            <i class="ri-login-circle-line align-bottom me-1"></i> Join Group
                        </button>
                    </form>
                @else
                    <a href="{{ route('register', ['invite' => $invitation->token]) }}" class="btn btn-primary">
                        <i class="ri-user-add-line align-bottom me-1"></i> Sign Up and Join
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-light ms-2">Log In</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
