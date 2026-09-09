@extends('layouts.header')

@section('title', 'Plan & Billing | SALTiii')

@section('css')
<style>
    .plan-intro{display:flex;align-items:center;justify-content:space-between;gap:28px;margin-bottom:24px;padding:25px 28px;border:1px solid #cfe2e3;border-radius:14px;background:linear-gradient(120deg,#eaf6f3,#f7fbfa)}
    .plan-intro h2{margin:0 0 7px;color:#0c3442;font-size:23px;font-weight:700}.plan-intro p{margin:0;color:#637a83}.plan-status{display:flex;flex:0 0 auto;align-items:center;gap:9px;padding:9px 13px;border-radius:30px;color:#176c55;background:#dff2eb;font-size:12px;font-weight:700}.plan-status:before{width:8px;height:8px;border-radius:50%;background:#24a27d;content:""}
    .plan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.plan-card{display:flex;min-height:440px;flex-direction:column;padding:27px;border:1px solid #dce7e7;border-radius:14px;background:#fff;box-shadow:0 8px 28px rgba(12,52,66,.055)}.plan-card.current{border:2px solid #008fc7;background:#f0f9fa}.plan-head{display:flex;align-items:center;justify-content:space-between;gap:12px}.plan-name{color:#0c3442;font-size:13px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.current-label{padding:5px 9px;border-radius:20px;color:#fff;background:#008fc7;font-size:9px;font-weight:700;text-transform:uppercase}.plan-price{margin:25px 0 5px;color:#0c3442;font-size:43px;font-weight:700;letter-spacing:-.05em}.plan-price sup{font-size:20px}.plan-price small{color:#637a83;font-size:13px;font-weight:400;letter-spacing:0}.plan-for{min-height:43px;color:#637a83;font-size:13px}.plan-card hr{margin:23px 0;border-color:#dce7e7}.plan-list{padding:0;margin:0 0 25px;list-style:none;color:#385965;font-size:13px}.plan-list li{display:flex;gap:9px;margin-bottom:11px}.plan-list li:before{color:#168c68;content:"✓";font-weight:700}.plan-card .btn{margin-top:auto}.billing-help{display:flex;align-items:center;justify-content:center;gap:8px;margin:25px 0 0;color:#71868d;font-size:12px}.billing-help a{color:#0076a5;font-weight:700}
    @media(max-width:900px){.plan-grid{grid-template-columns:1fr}.plan-card{min-height:auto}.plan-for{min-height:0}.plan-intro{align-items:flex-start;flex-direction:column}}
</style>
@endsection

@section('content')
<section class="plan-intro"><div><h2>Your SALTiii workspace</h2><p>Start simple and move to a larger plan when your team needs more structure and support.</p></div><span class="plan-status">Personal plan active</span></section>

<div class="plan-grid">
    <article class="plan-card current"><div class="plan-head"><span class="plan-name">Personal</span><span class="current-label">Current plan</span></div><div class="plan-price"><sup>$</sup>6.99 <small>/ month</small></div><p class="plan-for">For independent professionals organizing client and project work.</p><hr><ul class="plan-list"><li>Unlimited tasks and projects</li><li>Unlimited activity log</li><li>List and board views</li><li>Advanced search filters</li><li>Files up to 100 MB each</li></ul><button class="btn btn-outline-primary w-100" type="button" disabled>Current plan</button></article>
    <article class="plan-card"><div class="plan-head"><span class="plan-name">Teams</span></div><div class="plan-price"><sup>$</sup>9.99 <small>/ month</small></div><p class="plan-for">For growing teams coordinating shared work, time, and reporting.</p><hr><ul class="plan-list"><li>Everything in Personal</li><li>Cross-project status updates</li><li>Payroll and payment reporting</li><li>Shared goals and visibility</li><li>Workflow automation</li></ul><a class="btn btn-primary w-100" href="mailto:info@saltiii.com?subject=SALTiii%20Teams%20Plan">Ask about Teams</a></article>
    <article class="plan-card"><div class="plan-head"><span class="plan-name">Corporate</span></div><div class="plan-price"><sup>$</sup>29 <small>/ month</small></div><p class="plan-for">For organizations that need more control, onboarding, and support.</p><hr><ul class="plan-list"><li>Everything in Teams</li><li>Advanced permissions and roles</li><li>Premium support and onboarding</li><li>Enterprise integrations</li><li>Custom security options</li></ul><a class="btn btn-outline-primary w-100" href="mailto:info@saltiii.com?subject=SALTiii%20Corporate%20Plan">Talk to sales</a></article>
</div>
<p class="billing-help"><i class="ri-customer-service-2-line" aria-hidden="true"></i>Questions about billing? <a href="mailto:info@saltiii.com">Contact support</a>.</p>
@endsection
