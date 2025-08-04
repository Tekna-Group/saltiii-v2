@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4 card-bg-fill">

            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">{{ __('Reset Password') }}</h5>
                    <p class="text-muted">{{ __('Reset Password') }} with {{ config('app.name', 'Laravel') }}</p>

                    {{-- <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop" colors="primary:#0ab39c" class="avatar-xl"></lord-icon> --}}

                </div>

                <div class="alert border-0 alert-warning text-center mb-2 mx-2" role="alert">
                    Enter your email and new password!
                </div>
                <div class="p-2">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input id="email" type="email" class="form-control form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input id="password" type="password" class="form-control form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="password_confirmation"  required>
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-success w-100" type="submit">Reset Password</button>
                        </div>
                        
                        @if ($errors->any())
                        <div class="mt-3 alert alert-danger alert-dismissable">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    </form><!-- end form -->
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

        <div class="mt-4 text-center">
            <p class="mb-0">Wait, I remember my password... <a href="{{url('/')}}" class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
        </div>

    </div>
</div>
@endsection
