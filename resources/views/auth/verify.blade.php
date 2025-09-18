@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4 card-bg-fill">

            <div class="card-body p-4">
              
                <div class="mb-4">
                    <div class="avatar-lg mx-auto">
                        <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                            <i class="ri-mail-line"></i>
                        </div>
                    </div>
                </div>
          
                  
               
                <div class="p-2 mt-4">
                    <div class="text-muted text-center mb-4 mx-lg-3">
                        <h4>Verify Your Email Address</h4>
                        <p>Before proceeding, please check your email for a verification link.</p>
                    </div>
                </div>
                @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                @endif
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

        <div class="mt-4 text-center">
            <p class="mb-0">Didn't receive a code ? <a href="{{ route('verification.resend') }}" onclick='show();' class="fw-semibold text-primary text-decoration-underline">Resend</a> </p>
        </div>

    </div>
</div>
@endsection
