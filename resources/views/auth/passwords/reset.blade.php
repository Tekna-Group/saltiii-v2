@extends('layouts.app')

@section('title', 'Choose a new password | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-auth-recovery.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="recovery-shell">
    <aside class="recovery-story" aria-label="Password recovery steps">
        <a class="recovery-brand" href="{{ url('/') }}" aria-label="SALTiii homepage"><img src="{{ asset('images/Saltiii-Logo-White.svg') }}" alt="SALTiii"></a>
        <div class="recovery-copy"><span class="recovery-kicker">Almost there</span><h1>Create a password that <em>keeps work secure.</em></h1><p>Choose a unique password you don’t use elsewhere. A longer passphrase is easier to remember and harder to guess.</p><div class="recovery-steps" aria-label="Recovery process"><div class="recovery-step"><span>✓</span>Recovery link opened</div><div class="recovery-step active"><span>2</span>Choose your new password</div><div class="recovery-step"><span>3</span>Return to your workspace</div></div></div>
        <p class="recovery-support">Didn’t request this change? <a href="mailto:info@saltiii.com">Contact SALTiii support</a></p>
    </aside>

    <main class="recovery-form-side"><div class="recovery-wrap">
        <div class="recovery-top"><a class="back-link" href="{{ route('login') }}"><span aria-hidden="true">←</span>Back to login</a><p class="top-help">Need a new link? <a href="{{ route('password.request') }}">Start again</a></p></div>
        <div class="recovery-icon" aria-hidden="true"><i class="ri-lock-password-line"></i></div>
        <header class="recovery-heading"><h2>Choose a new password</h2><p>Confirm your account email, then create a password with at least six characters.</p></header>

        @if($errors->any())<div class="error-box" role="alert"><i class="ri-error-warning-line" aria-hidden="true"></i><div><strong>We couldn’t update your password yet.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif

        <form method="POST" action="{{ route('password.update') }}" id="reset-form" novalidate>@csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field"><label for="email">Account email <small>Required</small></label><div class="input-wrap"><i class="ri-mail-line field-icon" aria-hidden="true"></i><input id="email" type="email" class="field-input{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email', isset($email) ? $email : '') }}" placeholder="you@company.com" autocomplete="email" maxlength="255" required autofocus></div>@if($errors->has('email'))<span class="field-error">{{ $errors->first('email') }}</span>@endif</div>
            <div class="field"><label for="password">New password <small>6+ characters</small></label><div class="input-wrap"><i class="ri-lock-2-line field-icon" aria-hidden="true"></i><input id="password" type="password" class="field-input{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Create a new password" autocomplete="new-password" minlength="6" required><button class="toggle-password" type="button" data-target="password" aria-label="Show new password" aria-pressed="false"><i class="ri-eye-line" aria-hidden="true"></i></button></div><div class="strength" id="password-strength" data-score="0" aria-live="polite"><div class="strength-bars" aria-hidden="true"><i></i><i></i><i></i><i></i></div><div class="strength-caption"><span id="strength-label">Use 6+ characters</span><span>Stronger is safer</span></div></div>@if($errors->has('password'))<span class="field-error">{{ $errors->first('password') }}</span>@endif</div>
            <div class="field"><label for="password-confirm">Confirm new password <small>Required</small></label><div class="input-wrap"><i class="ri-shield-check-line field-icon" aria-hidden="true"></i><input id="password-confirm" type="password" class="field-input" name="password_confirmation" placeholder="Repeat your new password" autocomplete="new-password" minlength="6" required><button class="toggle-password" type="button" data-target="password-confirm" aria-label="Show password confirmation" aria-pressed="false"><i class="ri-eye-line" aria-hidden="true"></i></button></div><span class="field-help" id="password-match" aria-live="polite">Enter the same password again.</span></div>
            <button class="submit-button" type="submit" id="reset-submit"><span class="submit-spinner" aria-hidden="true"></span><span class="submit-label">Update password</span><span class="submit-arrow" aria-hidden="true">→</span></button>
        </form>
        <p class="security-note"><i class="ri-shield-check-line" aria-hidden="true"></i>Your new password is encrypted before it is stored.</p><p class="recovery-foot">Remembered your password? <a href="{{ route('login') }}">Log in instead</a></p>
    </div></main>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
    var form=document.getElementById('reset-form'),password=document.getElementById('password'),confirmation=document.getElementById('password-confirm'),match=document.getElementById('password-match'),strength=document.getElementById('password-strength'),label=document.getElementById('strength-label'),submit=document.getElementById('reset-submit');
    document.querySelectorAll('.toggle-password').forEach(function(button){button.addEventListener('click',function(){var input=document.getElementById(button.getAttribute('data-target')),reveal=input.type==='password';input.type=reveal?'text':'password';button.setAttribute('aria-pressed',String(reveal));button.setAttribute('aria-label',reveal?'Hide password':'Show password');button.querySelector('i').className=reveal?'ri-eye-off-line':'ri-eye-line';input.focus()})});
    function updateStrength(){var value=password.value,score=0;if(value.length>=6)score++;if(value.length>=10)score++;if(/[A-Z]/.test(value)&&/[a-z]/.test(value))score++;if(/\d/.test(value)&&/[^A-Za-z0-9]/.test(value))score++;strength.setAttribute('data-score',score);label.textContent=['Use 6+ characters','Needs a little more','Fair password','Good password','Strong password'][score]}
    function updateMatch(){if(!confirmation.value){match.textContent='Enter the same password again.';match.style.color='';return}var same=password.value===confirmation.value;match.textContent=same?'Passwords match.':'Passwords do not match yet.';match.style.color=same?'#168c68':'#b74035'}
    password.addEventListener('input',function(){updateStrength();updateMatch()});confirmation.addEventListener('input',function(){confirmation.setCustomValidity('');updateMatch()});
    form.addEventListener('submit',function(event){if(password.value!==confirmation.value)confirmation.setCustomValidity('Passwords must match.');if(!form.checkValidity()){event.preventDefault();form.reportValidity();return}submit.disabled=true;submit.classList.add('loading');submit.querySelector('.submit-label').textContent='Updating password…';show()});
});
</script>
@endsection
