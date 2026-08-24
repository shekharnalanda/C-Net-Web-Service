<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $project->project_name }} | Live Project</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
.container{width:min(1050px,94%);margin:auto}header{background:#07223d;color:#fff;padding:18px 0}header a{color:#fff}
.box{background:#fff;padding:23px;border-radius:14px;margin:23px 0;box-shadow:0 6px 22px #0001}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}label{display:block;font-weight:700;margin-bottom:7px}
input,select,textarea{width:100%;padding:11px;border:1px solid #bdcad8;border-radius:8px;font:inherit}
textarea{min-height:110px}.full{grid-column:1/-1}.check{display:flex;gap:10px;padding:10px;border-bottom:1px solid #edf1f5}
.check input{width:auto}.notice{background:#e4f7eb;color:#176b38;padding:13px;border-radius:8px}
button{background:#0756a3;color:#fff;border:0;padding:13px 22px;border-radius:8px;font-weight:800}
.client{line-height:1.8}@media(max-width:650px){.grid{grid-template-columns:1fr}.full{grid-column:auto}}
</style>
@include('partials.seo')
</head>
<body>
<header><div class="container"><h1>{{ $project->project_name }}</h1><a href="{{ route('admin.projects.index') }}">← All Live Projects</a></div></header>
<main class="container">
@if(session('success'))<div class="notice">{{ session('success') }}</div>@endif

<div class="box client">
<h2>Client and Project Details</h2>
<strong>Business:</strong> {{ $project->business_name }}<br>
<strong>Authority:</strong> {{ $project->owner_name }}<br>
<strong>Phone:</strong> {{ $project->phone }}<br>
<strong>Email:</strong> {{ $project->email ?: 'Not provided' }}<br>
<strong>Trial:</strong> @if($project->trial_url)<a href="{{ $project->trial_url }}" target="_blank">{{ $project->trial_url }}</a>@endif<br>
<strong>Custom Domain:</strong> https://{{ $project->custom_domain }}<br>
<strong>Type:</strong> {{ ucwords(str_replace('_',' ',$project->project_type)) }}<br>
<strong>Plan:</strong> {{ $project->plan_title ?: 'Custom Plan' }}
</div>

<form method="POST" action="{{ route('admin.projects.update',$project->id) }}">
@csrf
@method('PATCH')

<div class="box">
<h2>Project Management</h2>
<div class="grid">
<div>
<label>Project Status</label>
<select name="project_status">
@foreach(['planning','domain_setup','development','testing','ready_to_launch','launched','on_hold'] as $status)
<option value="{{ $status }}" @selected($project->project_status===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>
@endforeach
</select>
</div>
<div>
<label>Payment Status</label>
<select name="payment_status">
@foreach(['pending','partial','paid'] as $status)
<option value="{{ $status }}" @selected($project->payment_status===$status)>{{ ucfirst($status) }}</option>
@endforeach
</select>
</div>
<div><label>Quoted Amount</label><input type="number" step="0.01" min="0" name="quoted_amount" value="{{ $project->quoted_amount }}"></div>
<div><label>Paid Amount</label><input type="number" step="0.01" min="0" name="paid_amount" value="{{ $project->paid_amount }}"></div>
<div class="full"><label>Requirements</label><textarea name="requirements">{{ $project->requirements }}</textarea></div>
<div class="full"><label>Admin Notes</label><textarea name="admin_notes">{{ $project->admin_notes }}</textarea></div>
</div>
</div>

<div class="box">
<h2>Deployment Checklist</h2>
@foreach($checklist as $item)
<label class="check">
<input type="checkbox" name="completed_items[]" value="{{ $item['key'] }}" @checked($item['done'])>
<span>{{ $item['label'] }}</span>
</label>
@endforeach
</div>

<button type="submit">Save Project Progress</button>
</form>
</main>
</body>
</html>
