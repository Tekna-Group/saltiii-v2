@extends('layouts.app')
@section('css')
<style>
  .form-group {
    position: relative;
    margin-bottom: 1.5rem;
  }

  .form-group label {
    position: absolute;
    top: -8px;
    left: 12px;
    background-color: #fff;
    font-size: 12px;
    color: #6c757d;
    padding: 0 4px;
  }

  .form-group .form-control {
    padding: 10px;
    font-size: 14px;
  }
</style>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4 card-bg-fill">
            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Create your Saltiii account</h5>
                    {{-- <p class="text-muted">Sign up to continue to {{ config('app.name', 'Laravel') }}</p> --}}
                </div>

                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('register') }}" onsubmit='show();'>
                        @csrf
                         @if($errors->any())
                            <div class="mt-3 form-group alert alert-danger alert-dismissable">
                                <strong>{{$errors->first()}}</strong>
                            </div>
                        @endif
                        <div class="mb-3 form-group">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" 
                                   name="name" value="{{ old('name') }}" placeholder="" required autofocus>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="mb-3 form-group">
                            <label for="email" class="form-label">Email Address<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" placeholder="" 
                                   value="{{ old('email') }}" name="email" required>
                        </div>

                        <div class="mb-3 form-group">
                            <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                            <div class=" auth-pass-inputgroup">
                                <input type="password" name="password" class="form-control  password-input{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                       placeholder="" id="password" required>
                                <button class="btn btn-link position-absolute end-0 top-0 text-muted" type="button" id="password-addon">
                                    <i class="ri-eye-fill align-middle"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-group">
                            <label class="form-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <div class="auth-pass-inputgroup    ">
                                <input type="password" name="password_confirmation" class="form-control  " 
                                       placeholder="" id="password-confirm" required>
                                <button class="btn btn-link position-absolute end-0 top-0 text-muted" type="button" id="password-addon">
                                    <i class="ri-eye-fill align-middle"></i>
                                </button>
                            </div>
                        </div>

                     

                       

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">Sign Up</button>
                        </div>

                        <div class="mt-2 text-center">
                            
                            <div>
                                <a href="{{ url('auth/google') }}" 
                                   class="btn btn-light border d-flex align-items-center justify-content-center px-4 py-2" 
                                   style="gap: 8px; font-weight: 500; border-radius: 8px;">
                                    <img src="https://developers.google.com/identity/images/g-logo.png" 
                                         alt="Google Logo" style="width:20px; height:20px;">
                                    Continue with Google
                                </a>
                            </div>
                        </div>
                           <div class="mb-4">
                            <p class="mb-0 fs-12 text-muted fst-italic">
                                By signing up, you accept our
                                <a href="#" onclick="openTermsModal()" class="text-primary text-decoration-underline fw-medium">
                                    Terms 
                                </a> and <a href="#" onclick="openPrivacyModal()" class="text-primary text-decoration-underline fw-medium">
                                    Privacy Policy 
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="mb-0">Already have an account? 
                <a href="{{url('/')}}" onclick='show()' class="fw-semibold text-primary text-decoration-underline"> Sign in </a>
            </p>
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
</script>
</script>

@endsection
