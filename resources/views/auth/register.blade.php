@extends('layouts.app')
@section('css')
<style>
  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
  }

  .form-group .form-control {
    height: 46px;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
  }

  .promo-bar {
    position: relative;
    isolation: isolate;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    row-gap: 16px;
    column-gap: 24px;
    overflow: hidden;
    margin-top: 1.5rem;
    padding: 18px 26px;
    border: 1px solid rgba(255, 255, 255, .16);
    border-radius: 14px;
    color: #fff;
    background:
      radial-gradient(circle at 6% 22%, rgba(255, 255, 255, .16) 0 2px, transparent 3px),
      radial-gradient(circle at 20% 78%, rgba(255, 255, 255, .13) 0 2px, transparent 3px),
      radial-gradient(circle at 88% 18%, rgba(255, 255, 255, .16) 0 2px, transparent 3px),
      radial-gradient(circle at 96% 68%, rgba(255, 255, 255, .13) 0 2px, transparent 3px),
      radial-gradient(circle at 55% 85%, rgba(255, 255, 255, .11) 0 2px, transparent 3px),
      linear-gradient(115deg, #5c0a15 0%, #a01326 40%, #e11d34 72%, #ff4d54 100%);
    box-shadow: 0 18px 36px -14px rgba(120, 10, 25, .5);
  }

  .promo-bar::before,
  .promo-bar::after {
    position: absolute;
    z-index: -1;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .07);
    content: "";
  }

  .promo-bar::before {
    top: -95px;
    left: 20%;
  }

  .promo-bar::after {
    right: -50px;
    bottom: -95px;
  }

  .promo-bar__title {
    display: flex;
    flex: 1 1 260px;
    align-items: center;
    gap: 8px;
    margin: 0;
    color: #fff;
    font-size: 15.5px;
    font-weight: 700;
    line-height: 1.35;
  }

  .promo-bar__title > span:first-child {
    font-size: 20px;
    line-height: 1;
  }

  .promo-bar__subtitle {
    display: block;
    margin-top: 3px;
    color: rgba(255, 255, 255, .8);
    font-size: 12.5px;
    font-weight: 500;
  }

  @media (max-width: 575.98px) {
    .promo-bar {
      flex-direction: column;
      align-items: stretch;
      padding: 20px;
    }

    .promo-bar__title {
      justify-content: center;
    }
  }

  .register-title {
    margin-bottom: 8px;
    color: #172554;
    font-size: 26px;
    font-weight: 800;
  }

  .register-subtitle {
    margin-bottom: 4px;
    color: #475569;
    font-size: 15px;
  }

  .register-subtitle-muted {
    max-width: 400px;
    margin: 0 auto;
    color: #94a3b8;
    font-size: 13.5px;
    line-height: 1.5;
  }

  .trust-badges {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 8px 14px;
    margin: 20px 0 24px;
    color: #1e3a8a;
    font-size: 13px;
    font-weight: 600;
  }

  .trust-badges .dot {
    color: #cbd5e1;
  }

  .btn-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    color: #1e293b;
    font-size: 15px;
    font-weight: 600;
    transition: background-color .15s ease;
  }

  .btn-google:hover {
    background: #f8fafc;
    color: #1e293b;
  }

  .divider-text {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 22px 0;
    color: #94a3b8;
    font-size: 13px;
  }

  .divider-text::before,
  .divider-text::after {
    flex: 1;
    height: 1px;
    background: #e2e8f0;
    content: "";
  }

  .input-icon-wrap {
    position: relative;
  }

  .input-icon-wrap .form-icon {
    position: absolute;
    top: 50%;
    left: 14px;
    color: #94a3b8;
    font-size: 15px;
    pointer-events: none;
    transform: translateY(-50%);
  }

  .form-group .form-control.ps-icon {
    padding-left: 40px;
  }
</style>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7 col-xl-6">
        <section class="promo-bar" aria-labelledby="promo-bar-title">
            @if(isset($invitation) && $invitation)
                <h2 class="promo-bar__title" id="promo-bar-title">
                    <span aria-hidden="true">📩</span>
                    You're invited to {{ $invitation->group->name }}
                </h2>
            @else
                <h2 class="promo-bar__title" id="promo-bar-title">
                    <span aria-hidden="true">🎁</span>
                    <span>
                        <strong class="d-block">Your free 30-day trial is ready</strong>
                        <span class="promo-bar__subtitle">Get started in less than 60 seconds.</span>
                    </span>
                </h2>
            @endif
        </section>

        <div class="card mt-4 card-bg-fill">
            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h1 class="register-title">Create Your Free Saltiii Account</h1>
                    <p class="register-subtitle">Start exploring Saltiii today with a full 30-day free trial.</p>
                    <p class="register-subtitle-muted">Stay organized, meet deadlines, and keep every project moving without juggling multiple apps.</p>
                </div>

                <div class="trust-badges">
                    <span><i class="ri-shield-check-line align-middle" aria-hidden="true"></i> No credit card required</span>
                    <span class="dot" aria-hidden="true">&bull;</span>
                    <span>Cancel anytime</span>
                    <span class="dot" aria-hidden="true">&bull;</span>
                    <span>Setup takes less than 60 seconds</span>
                </div>

                <a href="{{ url('auth/google') }}" class="btn-google">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo" style="width:20px; height:20px;">
                    Continue with Google
                </a>

                <div class="divider-text">or create an account with email</div>

                <div class="p-2">
                    <form method="POST" action="{{ route('register') }}" onsubmit='show();' id="registration-form">
                        @csrf
                        @if(isset($invitation) && $invitation)
                            <input type="hidden" name="invitation_token" value="{{ $invitation->token }}">
                            <div class="alert alert-info">
                                You are joining <strong>{{ $invitation->group->name }}</strong>. Use <strong>{{ $invitation->email }}</strong> to accept this invite.
                            </div>
                        @endif
                         @if($errors->any())
                            <div class="mt-3 form-group alert alert-danger alert-dismissable">
                                <strong>{{$errors->first()}}</strong>
                            </div>
                        @endif
                        <div class="mb-3 form-group">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="ri-user-line form-icon" aria-hidden="true"></i>
                                <input id="name" type="text" class="form-control ps-icon{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                       name="name" value="{{ old('name') }}" placeholder="Enter your name" required autofocus>
                            </div>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3 form-group">
                            <label for="email" class="form-label">Email Address<span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="ri-mail-line form-icon" aria-hidden="true"></i>
                                <input type="email" class="form-control ps-icon" id="email" placeholder="Enter your email address"
                                       value="{{ old('email', isset($invitation) && $invitation ? $invitation->email : '') }}" name="email" required>
                            </div>
                        </div>

                        <div class="mb-3 form-group">
                            <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap auth-pass-inputgroup">
                                <i class="ri-lock-2-line form-icon" aria-hidden="true"></i>
                                <input type="password" name="password" class="form-control ps-icon password-input{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       placeholder="Create a strong password" id="password" required>
                                <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon">
                                    <i class="ri-eye-fill align-middle"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-group">
                            <label class="form-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap auth-pass-inputgroup">
                                <i class="ri-shield-check-line form-icon" aria-hidden="true"></i>
                                <input type="password" name="password_confirmation" class="form-control ps-icon password-input"
                                       placeholder="Re-enter your password" id="password-confirm" required>
                                <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-confirm-addon">
                                    <i class="ri-eye-fill align-middle"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">Start My Free 30-Day Trial</button>
                        </div>

                        <div class="mt-3 text-center">
                            <p class="mb-0 fs-12 text-muted">
                                <i class="ri-lock-2-fill" aria-hidden="true"></i>
                                By continuing, you agree to Saltiii's
                                <a href="#" onclick="openTermsModal()" class="text-primary text-decoration-underline fw-medium">Terms</a>
                                and
                                <a href="#" onclick="openPrivacyModal()" class="text-primary text-decoration-underline fw-medium">Privacy Policy</a>.
                                <br>Your information is protected and will never be sold.
                            </p>
                        </div>

                        <div class="mt-3 text-center">
                            <p class="mb-0">Already have an account?
                                <a href="{{url('/')}}" onclick='show()' class="fw-semibold text-primary text-decoration-underline"> Log in </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Privacy Policy Modal --}}
<div class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background-color:rgba(0,0,0,0.5); z-index:1050; justify-content:center; align-items:center;">
    <div class="modal-content bg-white rounded shadow-lg p-4" style="max-width:800px; max-height:85vh; overflow-y:auto; position:relative;">
        <button class="btn-close position-absolute top-0 end-0 m-3" onclick="closePrivacyModal()" aria-label="Close"></button>
        <h4 class="mb-3 text-center text-primary">PRIVACY POLICY</h4>
        <div style="position:absolute; top:15px; right:20px;">
            <img src='{{ asset("images/SaltiiiBlack.svg") }}' alt='Saltii Logo' style='height:40px; width:auto;'>
          </div>
        <p><strong>Last Updated:</strong> October 8, 2025</p>

        <p>Welcome to Saltiii (“we,” “our,” or “us”). We value your trust and are committed to protecting your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our workflow management, timekeeping, and payroll system (“Services”).</p>

        <p>By using Saltiii, you agree to the terms of this Privacy Policy.</p>

        <h5>Information We Collect</h5>
        <p>We collect only the information necessary to provide and improve our Services. This includes:</p>

        <ul>
            <li><strong>Personal Information:</strong> Full Name, Phone Number, Email Address, Job Title or Position</li>
            <li><strong>Payment Information:</strong> Saltiii uses Stripe for secure payment processing. Stripe may collect your Name, Card Number, CVV, and Expiration Date. We do not store your payment details. Please refer to Stripe’s <a href="https://stripe.com/privacy" target="_blank">Privacy Policy</a> for more information.</li>
        </ul>

        <h5>How We Use Your Information</h5>
        <ul>
            <li>Create and manage user accounts</li>
            <li>Provide access to project, task, and payroll features</li>
            <li>Communicate updates or notifications</li>
            <li>Improve and personalize user experience</li>
            <li>Process payments and manage billing</li>
            <li>Ensure compliance with legal obligations</li>
        </ul>

        <h5>Data Sharing and Disclosure</h5>
        <p>We do not sell, rent, or trade your personal data. Your information is shared only when necessary:</p>
        <ul>
            <li>With Service Providers (e.g., Stripe)</li>
            <li>For Legal Reasons</li>
            <li>For Business Operations (e.g., mergers or acquisitions)</li>
        </ul>

        <h5>Data Retention</h5>
        <p>We retain personal data only as long as necessary for service or legal obligations, after which it’s securely deleted or anonymized.</p>

        <h5>Data Security</h5>
        <p>We implement administrative, technical, and physical safeguards to protect your information, though no system is completely secure.</p>

        <h5>Your Privacy Rights</h5>
        <ul>
            <li>Access and receive a copy of your data</li>
            <li>Request correction of inaccurate data</li>
            <li>Request deletion of your data (subject to legal limitations)</li>
        </ul>

        <h5>Children’s Privacy</h5>
        <p>Our Services are not directed toward individuals under 18. We do not knowingly collect data from minors.</p>

        <h5>Third-Party Links</h5>
        <p>Our Services may contain links to third-party sites. We are not responsible for their content or privacy practices.</p>

        <h5>Changes to This Privacy Policy</h5>
        <p>We may update this Policy periodically. Check this page for updates with the “Last Updated” date.</p>

        <h5>Contact Us</h5>
        <p>
            Saltiii<br>
            South Carolina, USA<br>
            <a href="mailto:info@saltiii.com">info@saltiii.com</a><br>
            +1-864-772-3521
        </p>

        <div class="text-center mt-4">
            <button class="btn btn-primary px-4" onclick="closePrivacyModal()">Close</button>
        </div>
    </div>
</div>
<div class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background-color:rgba(0,0,0,0.5); z-index:1050; justify-content:center; align-items:center;" id='terms'>
    <div class="modal-content bg-white rounded shadow-lg p-4" style="max-width:800px; max-height:85vh; overflow-y:auto; position:relative;">
        <button class="btn-close position-absolute top-0 end-0 m-3" onclick="closePrivacyModal()" aria-label="Close"></button>
        <h4 class="mb-3 text-center text-primary">PRIVACY POLICY</h4>
        <div style="position:absolute; top:15px; right:20px;">
            <img src='{{ asset("images/SaltiiiBlack.svg") }}' alt='Saltiii Logo' style='height:40px; width:auto;'>
        </div>

        <p><strong>Last Updated:</strong> October 8, 2025</p>

        <p>Welcome to Saltiii (“we,” “our,” or “us”). We value your trust and are committed to protecting your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our workflow management, timekeeping, and payroll system (“Services”).</p>

        <p>By using Saltiii, you agree to the terms of this Privacy Policy.</p>

        <h5>Information We Collect</h5>
        <p>We collect only the information necessary to provide and improve our Services. This includes:</p>

        <p><strong>Personal Information:</strong></p>
        <ul>
            <li>Full Name</li>
            <li>Phone Number</li>
            <li>Email Address</li>
            <li>Job Title or Position</li>
        </ul>

        <p><strong>Payment Information:</strong></p>
        <p>Saltiii uses a third-party payment processor, Stripe, to handle all payment transactions securely. When you make a payment, Stripe may collect and store your:</p>
        <ul>
            <li>Name</li>
            <li>Card Number</li>
            <li>CVV</li>
            <li>Expiration Date</li>
        </ul>
        <p>We do not store or have direct access to your complete payment details. Please refer to Stripe’s <a href="https://stripe.com/privacy" target="_blank">Privacy Policy</a> for more information on how they handle your data.</p>

        <h5>How We Use Your Information</h5>
        <p>We use the information we collect to:</p>
        <ul>
            <li>Create and manage user accounts</li>
            <li>Provide access to project, task, and payroll features</li>
            <li>Communicate important updates or notifications</li>
            <li>Improve and personalize user experience</li>
            <li>Process payments and manage billing</li>
            <li>Ensure compliance with legal obligations</li>
        </ul>

        <h5>Data Sharing and Disclosure</h5>
        <p>We do not sell, rent, or trade your personal data. Your information is shared only when necessary:</p>
        <ul>
            <li><strong>With Service Providers:</strong> such as Stripe for secure payments.</li>
            <li><strong>For Legal Reasons:</strong> when required by law, regulation, or legal process.</li>
            <li><strong>For Business Operations:</strong> if we undergo a merger, acquisition, or asset sale, your data may be transferred as part of that transaction.</li>
        </ul>

        <h5>Data Retention</h5>
        <p>We retain your personal data only for as long as necessary to fulfill the purposes outlined in this Policy, unless a longer retention period is required by law. Once data is no longer needed, it will be securely deleted or anonymized.</p>

        <h5>Data Security</h5>
        <p>We implement administrative, technical, and physical safeguards to protect your information from unauthorized access, loss, misuse, or alteration. However, please note that no online transmission or storage system can be completely secure.</p>

        <h5>Your Privacy Rights</h5>
        <p>Depending on your location, you may have rights to:</p>
        <ul>
            <li>Access and receive a copy of your data</li>
            <li>Request correction of inaccurate data</li>
            <li>Request deletion of your data (subject to legal or contractual limitations)</li>
        </ul>
        <p>To exercise these rights, contact us using the details below.</p>

        <h5>Children’s Privacy</h5>
        <p>Our Services are not directed toward individuals under 18 years old. We do not knowingly collect personal information from minors.</p>

        <h5>Third-Party Links</h5>
        <p>Our Services may contain links to third-party websites. We are not responsible for the privacy practices or content of those external sites.</p>

        <h5>Changes to This Privacy Policy</h5>
        <p>We may update this Privacy Policy from time to time. Any changes will be reflected with a new “Last Updated” date. We encourage you to review this page periodically to stay informed.</p>

        <h5>Contact Us</h5>
        <p>If you have any questions or concerns about this Privacy Policy or your data, please contact us at:</p>

        <p>
            Saltiii<br>
            South Carolina, USA<br>
            <a href="mailto:info@saltiii.com">info@saltiii.com</a><br>
            +1-864-772-3521
        </p>

        <div class="text-center mt-4">
            <button class="btn btn-primary px-4" onclick="closeTermsModal()">Close</button>
        </div>
    </div>
</div>

<script>
function openPrivacyModal() {
    document.querySelector('.modal-overlay').style.display = 'flex';
}
function closePrivacyModal() {
    document.querySelector('.modal-overlay').style.display = 'none';
}
function openTermsModal() {
    document.querySelector('#terms').style.display = 'flex';
}
function closeTermsModal() {
    document.querySelector('#terms').style.display = 'none';
}

document.querySelectorAll('#registration-form .password-addon').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var icon = btn.querySelector('i');
        var isHidden = icon.classList.contains('ri-eye-fill');
        icon.classList.toggle('ri-eye-fill', !isHidden);
        icon.classList.toggle('ri-eye-off-fill', isHidden);
    });
});

</script>

@endsection
