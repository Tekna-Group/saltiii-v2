@extends('layouts.header')

@section('content')
<div class="container-fluid">

    <div class="row justify-content-center mt-5">
        <div class="col-lg-5">
            <div class="text-center mb-4 pb-2">
                <h4 class="fw-semibold fs-22">Choose Your Plan Now</h4>
                <p class="text-muted mb-4 fs-15">Start simple. Upgrade anytime as your business grows.</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="row">

                <!-- BASIC PLAN -->
                <div class="col-lg-4">
                    <div class="card pricing-box border border-danger shadow border-danger">
                        <div class="card-body p-4 m-2">

                            <div class="text-center">
                                <h5 class="fw-semibold">BASIC</h5>
                                <p class="text-muted">For individuals & small teams</p>
                            </div>

                            <div class="text-center pt-3">
                                <h2 class="text-success">$6.99 <span class="fs-13 text-muted">/ Per Month</span></h2>
                            </div>

                            <hr class="my-4 text-muted">

                            <ul class="list-unstyled vstack gap-3 text-muted">
                                <li>✔ Supercharge your workflow</li>
                                <li>✔ Unlimited tasks, projects & messages</li>
                                <li>✔ Unlimited activity log</li>
                                <li>✔ Unlimited file storage (100 MB/file)</li>
                                <li>✔ Control assignees & due-dates</li>
                                <li>✔ List & Board view</li>
                                <li>✔ Advanced search filters</li>
                            </ul>

                            <div class="mt-4 text-center">
                                <a href="javascript:void(0);" class="btn btn-success w-100 disabled" tabindex="-1" aria-disabled="true">Current Plan</a>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- PRO PLAN (BLURRED) -->
                <div class="col-lg-4">
                    <div class="card pricing-box position-relative" style="filter: blur(4px); pointer-events: none;">
                        <div class="card-body p-4 m-2 text-center">
                            <h5 class="fw-semibold">PRO PLAN</h5>
                            <p class="text-muted">Coming Soon</p>
                            <h2>$12.99</h2>
                            <p>More advanced features...</p>
                        </div>
                    </div>

                    <div class="text-center mt-2">
                        <span class="badge bg-warning">Locked</span>
                    </div>
                </div>

                <!-- ENTERPRISE PLAN (BLURRED) -->
                <div class="col-lg-4">
                    <div class="card pricing-box position-relative" style="filter: blur(4px); pointer-events: none;">
                        <div class="card-body p-4 m-2 text-center">
                            <h5 class="fw-semibold">ENTERPRISE</h5>
                            <p class="text-muted">Coming Soon</p>
                            <h2>$29.99</h2>
                            <p>Full system access...</p>
                        </div>
                    </div>

                    <div class="text-center mt-2">
                        <span class="badge bg-danger">Locked</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="{{asset('inside_css/assets/js/pages/pricing.init.js')}}"></script>
@endsection