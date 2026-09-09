@extends('layouts.app')

@section('title', 'Forgot password | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-auth-recovery.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="recovery-shell">
    <aside class="recovery-story" aria-label="Password recovery steps">
        <a class="recovery-brand" href="{{ url('/') }}" aria-label="SALTiii homepage"><img src="{{ asset('images/Saltiii-Logo-White.svg') }}" alt="SALTiii"></a>
        <div class="recovery-copy"><span class="recovery-kicker">Account recovery</span><h1>Let’s get you <em>back to work.</em></h1><p>Resetting your password is quick and secure. We’ll send a private recovery link to the email connected to your account.</p><div class="recovery-steps" aria-label="Recovery process"><div class="recovery-step active"><span>1</span>Enter your account email</div><div class="recovery-step"><span>2</span>Open the secure link we send</div><div class="recovery-step"><span>3</span>Choose a new password</div></div></div>
        <p class="recovery-support">Still having trouble? <a href="mailto:info@saltiii.com">Contact SALTiii support</a></p>
    </aside>

    <main class="recovery-form-side"><div class="recovery-wrap">
        <div class="recovery-top"><a class="back-link" href="{{ route('login') }}"><span aria-hidden="true">←</span>Back to login</a><p class="top-help">Need help? <a href="mailto:info@saltiii.com">Contact us</a></p></div>
        <div class="recovery-icon" aria-hidden="true"><i class="ri-mail-send-line"></i></div>
        <header class="recovery-heading"><h2>Forgot your password?</h2><p>Enter the email address you use for SALTiii. If an account matches, we’ll send instructions for creating a new password.</p></header>

        @if(session('status'))<div class="status-box" role="status"><i class="ri-checkbox-circle-line" aria-hidden="true"></i><div><strong>Check your inbox.</strong><br>{{ session('status') }}</div></div>@endif
        @if($errors->has('email'))<div class="error-box" role="alert"><i class="ri-error-warning-line" aria-hidden="true"></i><div><strong>We couldn’t send the reset link.</strong><br>{{ $errors->first('email') }}</div></div>@endif

        <form method="POST" action="{{ route('password.email') }}" id="recovery-form">@csrf
            <div class="field"><label for="email">Email address <small>Required</small></label><div class="input-wrap"><i class="ri-mail-line field-icon" aria-hidden="true"></i><input id="email" type="email" class="field-input{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="you@company.com" autocomplete="email" maxlength="255" required autofocus></div>@if($errors->has('email'))<span class="field-error">{{ $errors->first('email') }}</span>@endif</div>
            <button class="submit-button" type="submit" id="recovery-submit"><span class="submit-spinner" aria-hidden="true"></span><span class="submit-label">Send reset link</span><span class="submit-arrow" aria-hidden="true">→</span></button>
        </form>
        <p class="security-note"><i class="ri-shield-check-line" aria-hidden="true"></i>For your security, reset links expire automatically.</p><p class="recovery-foot">Remembered your password? <a href="{{ route('login') }}">Log in instead</a></p>
    </div></main>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){var form=document.getElementById('recovery-form');var button=document.getElementById('recovery-submit');form.addEventListener('submit',function(){if(form.checkValidity()){button.disabled=true;button.classList.add('loading');button.querySelector('.submit-label').textContent='Sending secure link…';show()}})});</script>
@endsection
