@extends('layouts.app')

@section('title', 'Create your SALTiii account')

@section('css')
<style>
    :root{--auth-ink:#0c3442;--auth-blue:#008fc7;--auth-blue-dark:#0076a5;--auth-orange:#ff7a3d;--auth-cream:#fbfaf5;--auth-mint:#e9f5f1;--auth-muted:#607781;--auth-line:#d9e6e6;--auth-white:#fff}
    body{color:var(--auth-ink);background:var(--auth-cream);font-family:hkgrotesk,"Segoe UI",sans-serif}
    .auth-page-wrapper{min-height:100vh;padding:0!important;background:var(--auth-cream)}
    .auth-one-bg-position,.auth-page-content>.container>.row:first-child{display:none!important}
    .auth-page-content,.auth-page-content>.container{min-height:100vh;padding:0!important;max-width:none!important}
    .auth-shell{display:grid;min-height:100vh;grid-template-columns:minmax(360px,.86fr) minmax(560px,1.14fr)}
    .auth-story{position:relative;display:flex;min-height:100vh;justify-content:space-between;flex-direction:column;overflow:hidden;padding:48px clamp(40px,5vw,76px);color:#fff;background:var(--auth-ink)}
    .auth-story:before,.auth-story:after{position:absolute;border:1px solid rgba(255,255,255,.09);border-radius:50%;content:"";pointer-events:none}
    .auth-story:before{top:-150px;right:-160px;width:440px;height:440px}.auth-story:after{right:-65px;bottom:-205px;width:520px;height:520px}
    .story-grid{position:absolute;right:12%;bottom:17%;width:120px;height:90px;opacity:.2;background-image:radial-gradient(#5ec7e7 1.4px,transparent 1.4px);background-size:13px 13px}
    .auth-brand{position:relative;z-index:1;display:inline-block;width:142px}.auth-brand img{width:100%;height:auto}
    .story-content{position:relative;z-index:1;max-width:560px;margin:75px 0}
    .story-kicker{display:flex;align-items:center;gap:10px;margin-bottom:22px;color:#8dd9e9;font-size:12px;font-weight:700;letter-spacing:.15em;text-transform:uppercase}.story-kicker:before{width:24px;height:2px;background:currentColor;content:""}
    .story-content h1{max-width:550px;margin:0 0 24px;color:#fff;font-size:clamp(45px,5.2vw,72px);font-weight:700;line-height:.98;letter-spacing:-.055em}.story-content h1 em{color:#62cbea;font-style:normal}
    .story-copy{max-width:500px;margin-bottom:36px;color:#bfd3d8;font-size:18px;line-height:1.65}
    .benefit-list{display:grid;gap:15px;margin:0;padding:0;list-style:none}.benefit-list li{display:flex;align-items:center;gap:12px;color:#eef7f8;font-size:15px}.benefit-list span{display:grid;width:27px;height:27px;flex:0 0 auto;place-items:center;border-radius:50%;color:#8ce2c8;background:rgba(72,193,157,.13);font-size:13px;font-weight:700}
    .story-proof{position:relative;z-index:1;display:flex;align-items:center;gap:14px;padding-top:26px;border-top:1px solid rgba(255,255,255,.12);color:#a9c1c7;font-size:13px}.proof-avatars{display:flex}.proof-avatar{display:grid;width:31px;height:31px;place-items:center;margin-left:-7px;border:2px solid var(--auth-ink);border-radius:50%;color:var(--auth-ink);background:#b8e7df;font-size:9px;font-weight:700}.proof-avatar:first-child{margin-left:0}.proof-avatar:nth-child(2){background:#ffd0b8}.proof-avatar:nth-child(3){background:#addcf0}
    .auth-form-side{display:flex;min-height:100vh;align-items:center;justify-content:center;padding:42px clamp(24px,6vw,94px);background:radial-gradient(circle at 100% 0,rgba(255,122,61,.1),transparent 27%),var(--auth-cream)}
    .form-wrap{width:min(100%,590px)}
    .form-top{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:34px}.back-link{display:inline-flex;align-items:center;gap:8px;color:var(--auth-muted);font-size:14px;font-weight:600}.back-link:hover{color:var(--auth-blue)}.signin-prompt{margin:0;color:var(--auth-muted);font-size:14px}.signin-prompt a{color:var(--auth-blue-dark);font-weight:700}
    .invite-banner{display:flex;align-items:flex-start;gap:12px;margin-bottom:22px;padding:15px 17px;border:1px solid #b7dfe9;border-radius:12px;color:#245a68;background:#eaf7fa;font-size:14px;line-height:1.5}.invite-icon{display:grid;width:30px;height:30px;flex:0 0 auto;place-items:center;border-radius:8px;color:#fff;background:var(--auth-blue);font-weight:700}
    .form-heading{margin-bottom:27px}.form-heading h2{margin:0 0 10px;color:var(--auth-ink);font-size:clamp(32px,4vw,45px);font-weight:700;letter-spacing:-.045em}.form-heading p{margin:0;color:var(--auth-muted);font-size:16px;line-height:1.55}.trial-note{display:inline-flex;align-items:center;gap:7px;margin-top:13px;padding:7px 10px;border-radius:30px;color:#176c55;background:#def2eb;font-size:12px;font-weight:700}.trial-note:before{content:"✓"}
    .google-button{display:flex;width:100%;min-height:52px;align-items:center;justify-content:center;gap:11px;border:1px solid var(--auth-line);border-radius:9px;color:var(--auth-ink);background:#fff;font-size:15px;font-weight:700;transition:border-color .2s,box-shadow .2s,transform .2s}.google-button:hover{border-color:#aac5ca;color:var(--auth-ink);box-shadow:0 7px 20px rgba(12,52,66,.08);transform:translateY(-1px)}.google-button img{width:19px;height:19px}
    .form-divider{display:flex;align-items:center;gap:14px;margin:22px 0;color:#8a9da4;font-size:12px;text-align:center}.form-divider:before,.form-divider:after{flex:1;height:1px;background:var(--auth-line);content:""}
    .error-summary{display:flex;align-items:flex-start;gap:10px;margin-bottom:20px;padding:13px 15px;border:1px solid #f0c1ba;border-radius:9px;color:#8d3228;background:#fff0ed;font-size:14px}.error-summary strong{display:block;margin-bottom:2px}
    .field-grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}.field.full{grid-column:1/-1}.field label{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:7px;color:var(--auth-ink);font-size:13px;font-weight:700}.field label small{color:#8a9da4;font-size:11px;font-weight:500}.input-wrap{position:relative}.input-wrap .field-icon{position:absolute;top:50%;left:14px;color:#7f969e;font-size:17px;pointer-events:none;transform:translateY(-50%)}
    .field-input{display:block;width:100%;height:50px;padding:11px 43px 11px 42px;border:1px solid #cfdfe1;border-radius:9px;outline:0;color:var(--auth-ink);background:#fff;font-size:14px;transition:border-color .2s,box-shadow .2s}.field-input::placeholder{color:#9aabb1}.field-input:hover{border-color:#acc7cb}.field-input:focus{border-color:var(--auth-blue);box-shadow:0 0 0 4px rgba(0,143,199,.1)}.field-input.is-invalid{border-color:#d95445}.field-input[readonly]{color:#577078;background:#f2f7f6;cursor:not-allowed}
    .toggle-password{position:absolute;top:50%;right:6px;display:grid;width:38px;height:38px;place-items:center;border:0;border-radius:7px;color:#657d85;background:transparent;cursor:pointer;transform:translateY(-50%)}.toggle-password:hover{color:var(--auth-blue-dark);background:#edf6f7}.toggle-password:focus-visible,.google-button:focus-visible,.submit-button:focus-visible,.legal-link:focus-visible,.modal-close:focus-visible{outline:3px solid rgba(0,143,199,.25);outline-offset:2px}
    .field-error{display:block;margin-top:6px;color:#b33a2f;font-size:12px;font-weight:600}.field-help{display:block;margin-top:6px;color:#7b9097;font-size:11px}
    .strength{margin-top:9px}.strength-bars{display:grid;grid-template-columns:repeat(4,1fr);gap:5px}.strength-bars i{height:3px;border-radius:4px;background:#dfe9e9;transition:background .2s}.strength-text{display:flex;justify-content:space-between;margin-top:5px;color:#80949b;font-size:10px}.strength[data-score="1"] i:nth-child(1){background:#dc5f50}.strength[data-score="2"] i:nth-child(-n+2){background:#ee9c3f}.strength[data-score="3"] i:nth-child(-n+3){background:#45a989}.strength[data-score="4"] i{background:#168c68}
    .form-footer{margin-top:22px}.submit-button{display:flex;width:100%;min-height:54px;align-items:center;justify-content:center;gap:10px;border:1px solid var(--auth-blue);border-radius:9px;color:#fff;background:var(--auth-blue);font-size:15px;font-weight:700;transition:background .2s,transform .2s,box-shadow .2s}.submit-button:hover{border-color:var(--auth-blue-dark);background:var(--auth-blue-dark);box-shadow:0 9px 22px rgba(0,143,199,.2);transform:translateY(-1px)}.submit-button[disabled]{cursor:wait;opacity:.75;transform:none}.submit-spinner{display:none;width:16px;height:16px;border:2px solid rgba(255,255,255,.45);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite}.submit-button.loading .submit-spinner{display:block}.submit-button.loading .submit-arrow{display:none}@keyframes spin{to{transform:rotate(360deg)}}
    .terms-copy{margin:15px auto 0;max-width:510px;color:#778d94;font-size:11px;line-height:1.55;text-align:center}.legal-link{padding:0;border:0;color:var(--auth-blue-dark);background:none;font-size:inherit;font-weight:700;text-decoration:underline;cursor:pointer}.security-line{display:flex;align-items:center;justify-content:center;gap:7px;margin-top:12px;color:#617b83;font-size:11px}.security-line span{color:#168c68}
    .support-line{margin:26px 0 0;color:#8a9da4;font-size:12px;text-align:center}.support-line a{color:var(--auth-ink);font-weight:700}
    .legal-modal[hidden]{display:none}.legal-modal{position:fixed;inset:0;z-index:1080;display:grid;padding:20px;place-items:center;background:rgba(4,29,38,.72);backdrop-filter:blur(5px)}.legal-dialog{position:relative;width:min(100%,760px);max-height:min(84vh,760px);overflow:auto;padding:34px;border-radius:16px;background:#fff;box-shadow:0 30px 90px rgba(4,29,38,.28)}.legal-dialog h2{margin:0 45px 8px 0;color:var(--auth-ink);font-size:28px;font-weight:700}.legal-updated{color:#83969d;font-size:12px}.legal-body{margin-top:24px;color:#4f6871;font-size:14px;line-height:1.65}.legal-body h3{margin:22px 0 8px;color:var(--auth-ink);font-size:16px}.legal-body ul{padding-left:20px}.legal-body a{color:var(--auth-blue-dark);text-decoration:underline}.modal-close{position:absolute;top:18px;right:18px;display:grid;width:38px;height:38px;place-items:center;border:1px solid var(--auth-line);border-radius:50%;color:var(--auth-ink);background:#fff;font-size:21px;cursor:pointer}
    @media(max-width:1050px){.auth-shell{grid-template-columns:.75fr 1.25fr}.auth-story{padding:40px}.story-content h1{font-size:52px}.story-copy{font-size:16px}}
    @media(max-width:820px){.auth-shell{display:block}.auth-story{min-height:auto;padding:28px 24px 34px}.story-content{margin:42px 0 18px}.story-content h1{max-width:620px;font-size:clamp(38px,10vw,54px)}.story-copy{margin-bottom:24px}.benefit-list{grid-template-columns:1fr 1fr;gap:10px}.benefit-list li:last-child{grid-column:1/-1}.story-proof{display:none}.auth-form-side{min-height:auto;padding:40px 24px 60px}.form-wrap{width:min(100%,640px)}}
    @media(max-width:560px){.auth-story{padding:24px 18px 30px}.auth-brand{width:122px}.story-content{margin-top:34px}.story-content h1{font-size:40px}.story-copy{font-size:15px}.benefit-list{display:none}.auth-form-side{padding:29px 17px 45px}.form-top{margin-bottom:28px}.signin-prompt{font-size:12px}.field-grid{grid-template-columns:1fr;gap:15px}.field.full{grid-column:auto}.form-heading h2{font-size:34px}.legal-dialog{padding:27px 22px}}
    @media(prefers-reduced-motion:reduce){*,*:before,*:after{scroll-behavior:auto!important;animation:none!important;transition:none!important}}
</style>
@endsection

@section('content')
<div class="auth-shell">
    <aside class="auth-story" aria-label="Why teams choose SALTiii">
        <a class="auth-brand" href="{{ url('/') }}" aria-label="SALTiii homepage"><img src="{{ asset('images/Saltiii-Logo-White.svg') }}" alt="SALTiii"></a>
        <div class="story-content">
            <span class="story-kicker">Your work, finally connected</span>
            <h1>Turn busy work into <em>forward motion.</em></h1>
            <p class="story-copy">Plan projects, track every hour, and keep payments moving—all in one clear workspace built for growing teams.</p>
            <ul class="benefit-list"><li><span>✓</span>Unlimited tasks and projects</li><li><span>✓</span>Clear board and list views</li><li><span>✓</span>Time and payroll reporting</li></ul>
        </div>
        <div class="story-proof"><div class="proof-avatars" aria-hidden="true"><span class="proof-avatar">AV</span><span class="proof-avatar">TG</span><span class="proof-avatar">SY</span></div><span>Trusted by modern teams building what's next.</span></div>
        <div class="story-grid" aria-hidden="true"></div>
    </aside>

    <section class="auth-form-side">
        <div class="form-wrap">
            <div class="form-top"><a class="back-link" href="{{ url('/') }}"><span aria-hidden="true">←</span> Back to website</a><p class="signin-prompt">Already a member? <a href="{{ route('login') }}">Log in</a></p></div>

            @if(isset($invitation) && $invitation)
                <div class="invite-banner"><span class="invite-icon" aria-hidden="true">i</span><div><strong>You're invited to {{ $invitation->group->name }}</strong><br>Set up your account with {{ $invitation->email }} to join the team.</div></div>
            @endif

            <header class="form-heading">
                <h2>{{ isset($invitation) && $invitation ? 'Join your team' : 'Create your account' }}</h2>
                <p>{{ isset($invitation) && $invitation ? 'You are one quick step away from your shared workspace.' : 'Get your workspace running in about a minute.' }}</p>
                @unless(isset($invitation) && $invitation)<span class="trial-note">30-day trial · No credit card required</span>@endunless
            </header>

            <a href="{{ url('auth/google') }}" class="google-button"><img src="https://developers.google.com/identity/images/g-logo.png" alt="" aria-hidden="true">Continue with Google</a>
            <div class="form-divider">or continue with email</div>

            @if($errors->any())
                <div class="error-summary" role="alert"><span aria-hidden="true">!</span><div><strong>We couldn't create your account yet.</strong>Please check the highlighted information below.</div></div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registration-form" novalidate>
                @csrf
                @if(isset($invitation) && $invitation)<input type="hidden" name="invitation_token" value="{{ $invitation->token }}">@endif

                <div class="field-grid">
                    <div class="field full">
                        <label for="name">Full name <small>Required</small></label>
                        <div class="input-wrap"><i class="ri-user-line field-icon" aria-hidden="true"></i><input id="name" type="text" class="field-input{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" placeholder="e.g. Alex Morgan" autocomplete="name" maxlength="255" required autofocus></div>
                        @if($errors->has('name'))<span class="field-error" role="alert">{{ $errors->first('name') }}</span>@endif
                    </div>

                    <div class="field full">
                        <label for="email">Work email <small>Required</small></label>
                        <div class="input-wrap"><i class="ri-mail-line field-icon" aria-hidden="true"></i><input id="email" type="email" class="field-input{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email', isset($invitation) && $invitation ? $invitation->email : '') }}" placeholder="you@company.com" autocomplete="email" maxlength="255" required {{ isset($invitation) && $invitation ? 'readonly' : '' }}></div>
                        @if($errors->has('email'))<span class="field-error" role="alert">{{ $errors->first('email') }}</span>@endif
                    </div>

                    <div class="field">
                        <label for="password">Password <small>6+ characters</small></label>
                        <div class="input-wrap"><i class="ri-lock-2-line field-icon" aria-hidden="true"></i><input id="password" type="password" class="field-input{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Create a password" autocomplete="new-password" minlength="6" required><button class="toggle-password" type="button" data-target="password" aria-label="Show password" aria-pressed="false"><i class="ri-eye-line" aria-hidden="true"></i></button></div>
                        <div class="strength" id="password-strength" data-score="0" aria-live="polite"><div class="strength-bars" aria-hidden="true"><i></i><i></i><i></i><i></i></div><div class="strength-text"><span id="strength-label">Use 6+ characters</span><span>Stronger is safer</span></div></div>
                        @if($errors->has('password'))<span class="field-error" role="alert">{{ $errors->first('password') }}</span>@endif
                    </div>

                    <div class="field">
                        <label for="password-confirm">Confirm password <small>Required</small></label>
                        <div class="input-wrap"><i class="ri-shield-check-line field-icon" aria-hidden="true"></i><input id="password-confirm" type="password" class="field-input" name="password_confirmation" placeholder="Repeat password" autocomplete="new-password" minlength="6" required><button class="toggle-password" type="button" data-target="password-confirm" aria-label="Show password confirmation" aria-pressed="false"><i class="ri-eye-line" aria-hidden="true"></i></button></div>
                        <span class="field-help" id="password-match" aria-live="polite">Enter the same password again.</span>
                    </div>
                </div>

                <div class="form-footer">
                    <button class="submit-button" type="submit" id="submit-button"><span class="submit-spinner" aria-hidden="true"></span><span class="submit-label">{{ isset($invitation) && $invitation ? 'Create account & join team' : 'Start my free trial' }}</span><span class="submit-arrow" aria-hidden="true">→</span></button>
                    <p class="terms-copy">By creating an account, you agree to SALTiii's <button class="legal-link" type="button" data-modal="terms-modal">Terms of Use</button> and acknowledge the <button class="legal-link" type="button" data-modal="privacy-modal">Privacy Policy</button>.</p>
                    <p class="security-line"><span aria-hidden="true">●</span>Your account details are encrypted in transit.</p>
                </div>
            </form>
            <p class="support-line">Need help? <a href="mailto:info@saltiii.com">Contact SALTiii support</a></p>
        </div>
    </section>
</div>

<div class="legal-modal" id="privacy-modal" role="dialog" aria-modal="true" aria-labelledby="privacy-title" hidden>
    <article class="legal-dialog"><button class="modal-close" type="button" aria-label="Close privacy policy">×</button><h2 id="privacy-title">Privacy Policy</h2><span class="legal-updated">Last updated October 8, 2025</span><div class="legal-body"><p>SALTiii values your trust and is committed to protecting your personal information. This policy explains how information is collected, used, disclosed, and safeguarded when you use our workflow management, timekeeping, and payroll services.</p><h3>Information we collect</h3><p>We collect information needed to create and manage your account, including your name, email address, and information you provide while using SALTiii. Payments are processed securely by Stripe; SALTiii does not store complete payment card details.</p><h3>How information is used</h3><ul><li>Create and manage user accounts</li><li>Provide project, task, timekeeping, and payroll features</li><li>Send service updates and account notifications</li><li>Improve the product and meet legal obligations</li></ul><h3>Sharing and retention</h3><p>We do not sell, rent, or trade personal data. Information is shared only with service providers where necessary, for legal requirements, or for legitimate business operations. Data is retained only as long as needed for service and legal obligations.</p><h3>Your choices</h3><p>You may request access, correction, or deletion of your personal data, subject to legal limitations. SALTiii is not directed to people under 18.</p><h3>Contact</h3><p>Questions about privacy can be sent to <a href="mailto:info@saltiii.com">info@saltiii.com</a> or +1 (864) 772-3521.</p></div></article>
</div>

<div class="legal-modal" id="terms-modal" role="dialog" aria-modal="true" aria-labelledby="terms-title" hidden>
    <article class="legal-dialog"><button class="modal-close" type="button" aria-label="Close terms of use">×</button><h2 id="terms-title">Terms of Use</h2><span class="legal-updated">SALTiii account terms</span><div class="legal-body"><p>By creating an account, you agree to use SALTiii lawfully and provide accurate account information. You are responsible for maintaining the confidentiality of your sign-in credentials and for activity performed through your account.</p><h3>Using the service</h3><p>You may not misuse the service, attempt unauthorized access, interfere with other users, or use SALTiii to violate applicable law or another person's rights.</p><h3>Subscriptions</h3><p>Plan features and pricing are shown before purchase. Subscriptions may be canceled through account management. Promotional trial and refund terms are subject to the offer presented when you register.</p><h3>Availability</h3><p>SALTiii may update features to improve security, performance, and usability. Contact us before relying on the service for a requirement that needs a specific contractual commitment.</p><h3>Contact</h3><p>For the current complete terms or questions about your account, contact <a href="mailto:info@saltiii.com">info@saltiii.com</a>.</p></div></article>
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
    var form=document.getElementById('registration-form');
    var password=document.getElementById('password');
    var confirmation=document.getElementById('password-confirm');
    var match=document.getElementById('password-match');
    var strength=document.getElementById('password-strength');
    var strengthLabel=document.getElementById('strength-label');
    var submit=document.getElementById('submit-button');
    var lastFocus=null;

    document.querySelectorAll('.toggle-password').forEach(function(button){button.addEventListener('click',function(){var input=document.getElementById(button.getAttribute('data-target'));var reveal=input.type==='password';input.type=reveal?'text':'password';button.setAttribute('aria-pressed',String(reveal));button.setAttribute('aria-label',reveal?'Hide password':'Show password');button.querySelector('i').className=reveal?'ri-eye-off-line':'ri-eye-line';input.focus()})});

    function updateStrength(){var value=password.value;var score=0;if(value.length>=6)score++;if(value.length>=10)score++;if(/[A-Z]/.test(value)&&/[a-z]/.test(value))score++;if(/\d/.test(value)&&/[^A-Za-z0-9]/.test(value))score++;strength.setAttribute('data-score',score);var labels=['Use 6+ characters','Needs a little more','Fair password','Good password','Strong password'];strengthLabel.textContent=labels[score]}
    function updateMatch(){if(!confirmation.value){match.textContent='Enter the same password again.';match.style.color='';return}var same=password.value===confirmation.value;match.textContent=same?'Passwords match.':'Passwords do not match yet.';match.style.color=same?'#168c68':'#b33a2f'}
    password.addEventListener('input',function(){updateStrength();updateMatch()});confirmation.addEventListener('input',updateMatch);

    form.addEventListener('submit',function(event){if(!form.checkValidity()||password.value!==confirmation.value){event.preventDefault();form.reportValidity();if(password.value!==confirmation.value){confirmation.setCustomValidity('Passwords must match.');confirmation.reportValidity()}return}confirmation.setCustomValidity('');submit.classList.add('loading');submit.disabled=true;submit.querySelector('.submit-label').textContent='Creating your workspace…'});
    confirmation.addEventListener('input',function(){confirmation.setCustomValidity('')});

    function closeModal(modal){modal.hidden=true;document.body.style.overflow='';if(lastFocus)lastFocus.focus()}
    document.querySelectorAll('[data-modal]').forEach(function(button){button.addEventListener('click',function(){lastFocus=button;var modal=document.getElementById(button.getAttribute('data-modal'));modal.hidden=false;document.body.style.overflow='hidden';modal.querySelector('.modal-close').focus()})});
    document.querySelectorAll('.legal-modal').forEach(function(modal){modal.querySelector('.modal-close').addEventListener('click',function(){closeModal(modal)});modal.addEventListener('click',function(event){if(event.target===modal)closeModal(modal)})});
    document.addEventListener('keydown',function(event){if(event.key==='Escape'){var open=document.querySelector('.legal-modal:not([hidden])');if(open)closeModal(open)}});
});
</script>
@endsection
