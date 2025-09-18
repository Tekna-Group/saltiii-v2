@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4 card-bg-fill">

            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Create New Account</h5>
                    <p class="text-muted">Sign up to continue to {{ config('app.name', 'Laravel') }}</p>
                </div>
                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('register') }}" onsubmit='show();'>
                        @csrf
                         <div class="mb-3">
                            <label for="email" class="form-label">Name<span class="text-danger">*</span></label>
                            <input  class="form-control" id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" placeholder="Name" required autofocus>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email address" value="{{ old('email') }}" name="email" required>
                            <div class="invalid-feedback">
                                Please enter email
                            </div>
                        </div>
                       

                        <div class="mb-3">
                            <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                            <div class="position-relative auth-pass-inputgroup">
                                <input type="password" name="password" class="form-control pe-5 password-input form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" onpaste="return false" placeholder="Enter password" id="password" aria-describedby="passwordInput"  required>
                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                               
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <div class="position-relative auth-pass-inputgroup">
                                <input type="password" name="password_confirmation" class="form-control pe-5 password-input form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" onpaste="return false" placeholder="Enter password" id="password-confirm" aria-describedby="passwordInput"  required>
                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                               
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="mb-0 fs-12 text-muted fst-italic">By registering you agree to the Saltiii <a href="#" class="text-primary text-decoration-underline fst-normal fw-medium">Terms of Use</a></p>
                        </div>
                        
                       @if($errors->any())
                            <div class="mt-3 form-group alert alert-danger alert-dismissable">
                                {{-- <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> --}}
                                <strong>{{$errors->first()}}</strong>
                            </div>
                        @endif

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">Sign Up</button>
                        </div>

                        <div class="mt-4 text-center">
                            <div class="signin-other-title">
                                <h5 class="fs-13 mb-4 title text-muted">Create account with</h5>
                            </div>

                            <div>
                                <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                                <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                                <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                                <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

        <div class="mt-4 text-center">
            <p class="mb-0">Already have an account ? <a href="{{url('/')}}" onclick='show()' class="fw-semibold text-primary text-decoration-underline"> Signin </a> </p>
        </div>

    </div>
</div>

@endsection
