<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Live Website Projects | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
header{background:#07223d;color:#fff;padding:15px 0}.container{width:min(1200px,94%);margin:auto}
.top{display:flex;align-items:center;justify-content:space-between;gap:15px}.brand{display:flex;align-items:center;gap:11px}
.brand img{width:48px;height:48px;object-fit:contain;border-radius:12px}.top h1{font-size:22px;margin:0}
.top a{color:#fff;text-decoration:none;border:1px solid #ffffff55;padding:9px 13px;border-radius:7px}
main{padding:28px 0}h2{color:#07223d}.notice{background:#e4f7eb;color:#176b38;padding:13px;border-radius:8px}
.box{background:#fff;border-radius:14px;padding:20px;margin:20px 0;box-shadow:0 5px 20px #0000000b;overflow:auto}
table{width:100%;border-collapse:collapse;min-width:850px}th,td{padding:12px;border-bottom:1px solid #e4ebf2;text-align:left}
th{background:#eaf2fa;color:#07223d}.btn{display:inline-block;background:#0756a3;color:#fff;padding:8px 12px;border-radius:7px;text-decoration:none;font-weight:700}
.status{font-weight:700;color:#0756a3}.empty{padding:25px;text-align:center;color:#64748b}
</style>
@include('partials.seo')
</head>
<body>
<header>
<div class="container top">
    <div class="brand">
        <img src="/images/cnet-favicon.png" alt="C-Net Logo">
        <h1>Live Website Projects</h1>
    </div>
    <a href="{{ route('admin.dashboard') }}">← Dashboard</a>
</div>
</header>

<main class="container">
@if(session('success'))<div class="notice">{{ session('success') }}</div>@endif

<h2>Current Paid Projects</h2>
<div class="box">
@if($projects->isEmpty())
    <div class="empty">अभी कोई live website project नहीं है।</div>
@else
<table>
<thead><tr><th>Project</th><th>Client</th><th>Domain</th><th>Type</th><th>Payment</th><th>Status</th><th>Action</th></tr></thead>
<tbody>
@foreach($projects as $project)
<tr>
<td><strong>{{ $project->project_name }}</strong><br><small>{{ $project->plan_title ?: 'Custom Plan' }}</small></td>
<td>{{ $project->business_name }}<br><small>{{ $project->phone }}</small></td>
<td>{{ $project->custom_domain }}</td>
<td>{{ ucwords(str_replace('_',' ',$project->project_type)) }}</td>
<td>{{ ucfirst($project->payment_status) }}</td>
<td class="status">{{ ucwords(str_replace('_',' ',$project->project_status)) }}</td>
<td><a class="btn" href="{{ route('admin.projects.show',$project->id) }}">Manage</a></td>
</tr>
@endforeach
</tbody>
</table>
@endif
</div>

<h2>Trials Available for Conversion</h2>
<div class="box">
@if($convertibleTrials->isEmpty())
    <div class="empty">Conversion के लिए कोई pending trial नहीं है।</div>
@else
<table>
<thead><tr><th>Website</th><th>Business</th><th>Authority</th><th>Contact</th><th>Trial Status</th><th>Action</th></tr></thead>
<tbody>
@foreach($convertibleTrials as $trial)
<tr>
<td>{{ $trial->website_name ?? $trial->business_name }}</td>
<td>{{ $trial->business_name }}</td>
<td>{{ $trial->owner_name }}</td>
<td>{{ $trial->phone }}<br><small>{{ $trial->email }}</small></td>
<td>{{ ucfirst($trial->status) }}</td>
<td><a class="btn" href="{{ route('admin.projects.convert',$trial->id) }}">Convert to Live</a></td>
</tr>
@endforeach
</tbody>
</table>
@endif
</div>
</main>
</body>
</html>
