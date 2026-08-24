<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Trial Websites | C-Net Web Services</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
header{background:#07223d;color:#fff;padding:18px}.container{width:min(1280px,96%);margin:auto}
.top{display:flex;justify-content:space-between;align-items:center;gap:15px}.top a{color:#fff;text-decoration:none;border:1px solid #ffffff55;padding:8px 12px;border-radius:7px}
main{padding:28px 0}.notice{background:#e5f8ed;color:#176b38;padding:13px;border-radius:8px;margin-bottom:16px}
.application{background:#fff;border-radius:14px;padding:22px;margin-bottom:20px;box-shadow:0 5px 20px #0000000c}
.heading{display:flex;justify-content:space-between;gap:20px;border-bottom:1px solid #e4ebf2;padding-bottom:13px;margin-bottom:15px}
.heading h2{margin:0;color:#07223d}.badge{padding:6px 11px;border-radius:20px;background:#eaf2fa;font-weight:700;height:max-content}
.details{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}.details strong{display:block;color:#07223d}
.actions{display:grid;grid-template-columns:repeat(4,1fr);gap:13px;margin-top:20px;padding-top:17px;border-top:1px solid #e4ebf2}
form{background:#f7f9fc;padding:13px;border-radius:9px}select,textarea,button{width:100%;padding:9px;border:1px solid #bac8d8;border-radius:6px;margin-top:7px}
textarea{min-height:62px}.save{background:#0756a3;color:#fff;border:0;font-weight:700}.extend{background:#087d50;color:#fff;border:0;font-weight:700}
.upgrade{background:#8a4b08;color:#fff;border:0;font-weight:700}.suspend{background:#6b4f00;color:#fff;border:0;font-weight:700}.restore{background:#087d50;color:#fff;border:0;font-weight:700}
.delete{background:#bd2638;color:#fff;border:0;font-weight:700}.trial-link{display:inline-block;color:#0756a3;font-weight:700;margin-top:8px}
.mini-actions{display:grid;gap:8px}
@media(max-width:1050px){.actions{grid-template-columns:1fr 1fr}}
@media(max-width:750px){.details,.actions{grid-template-columns:1fr}.heading,.top{flex-direction:column;align-items:flex-start}}
</style>
@include('partials.seo')
</head>
<body>
<header>
<div class="container top">
    <strong>Trial Website Management</strong>
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
</div>
</header>

<main class="container">
@if(session('success'))<div class="notice">{{ session('success') }}</div>@endif

@forelse($applications as $application)
<section class="application">
    <div class="heading">
        <div>
            <h2>{{ $application->website_name ?: $application->business_name }}</h2>
            <span>{{ $application->category }} • {{ $application->owner_name }}</span>
        </div>
        <span class="badge">{{ strtoupper($application->status) }}</span>
    </div>

    <div class="details">
        <div><strong>Authority Contact</strong>{{ $application->phone }}<br>{{ $application->email }}</div>
        <div><strong>Subdomain</strong>{{ $application->desired_slug }}.mciedu.com</div>
        <div><strong>Template</strong>{{ ucfirst($application->template_key ?: 'modern') }}</div>
        <div><strong>Valid Until</strong>{{ $application->expires_at ?: 'Not started' }}</div>
    </div>

    <p>{{ $application->about_business }}</p>

    @if($application->trial_url)
        <a class="trial-link" target="_blank" href="{{ $application->trial_url }}">Open Client Website ↗</a>
    @endif

    <div class="actions">
        <form method="POST" action="{{ route('admin.trials.status',$application->id) }}">
            @csrf @method('PATCH')
            <strong>Update Status</strong>
            <select name="status">
                @foreach(['pending','approved','suspended','rejected','expired','upgraded'] as $status)
                    <option value="{{ $status }}" @selected($application->status===$status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <textarea name="admin_notes" placeholder="Admin notes">{{ $application->admin_notes }}</textarea>
            <button class="save">Save Status</button>
        </form>

        <form method="POST" action="{{ route('admin.trials.extend',$application->id) }}">
            @csrf
            <strong>Extend Trial</strong>
            <p>Add seven more active days.</p>
            <button class="extend">Extend by 7 Days</button>
        </form>

        <form method="POST" action="{{ route('admin.trials.upgrade',$application->id) }}">
            @csrf
            <strong>Upgrade to Paid Plan</strong>
            <select name="selected_plan_id" required>
                <option value="">Select Plan</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->title }}</option>
                @endforeach
            </select>
            <textarea name="admin_notes" placeholder="Payment or upgrade notes"></textarea>
            <button class="upgrade">Mark as Upgraded</button>
        </form>

        <div class="mini-actions">
            @if($application->status === 'suspended')
                <form method="POST" action="{{ route('admin.trials.restore',$application->id) }}">
                    @csrf
                    <strong>Restore Website</strong>
                    <button class="restore">Restore for 7 Days</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.trials.suspend',$application->id) }}">
                    @csrf
                    <strong>Suspend Website</strong>
                    <button class="suspend">Suspend Now</button>
                </form>
            @endif

            @if($application->status !== 'upgraded')
                <form method="POST"
                      action="{{ route('admin.trials.destroy',$application->id) }}"
                      onsubmit="return confirm('Permanently delete this trial website and client trial record?')">
                    @csrf @method('DELETE')
                    <strong>Permanent Delete</strong>
                    <button class="delete">Delete Trial</button>
                </form>
            @endif
        </div>
    </div>
</section>
@empty
<div class="application">No trial websites have been created yet.</div>
@endforelse
</main>
    @include('admin.trials.quick-management')
</body>
</html>
