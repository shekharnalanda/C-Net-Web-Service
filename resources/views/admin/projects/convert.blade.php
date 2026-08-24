<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Convert Trial to Live Website</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
.container{width:min(900px,94%);margin:auto}header{background:#07223d;color:#fff;padding:18px 0}
header a{color:#fff}.box{background:#fff;padding:25px;border-radius:15px;margin:28px 0;box-shadow:0 8px 25px #0001}
.summary{background:#edf6ff;padding:16px;border-radius:10px;margin-bottom:20px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}label{display:block;font-weight:700;margin-bottom:7px}
input,select,textarea{width:100%;padding:12px;border:1px solid #bdcad8;border-radius:8px;font:inherit}
textarea{min-height:115px}.full{grid-column:1/-1}.error{color:#a31327;margin-top:5px}
button{background:#0756a3;color:#fff;border:0;padding:13px 22px;border-radius:8px;font-weight:800;cursor:pointer}
.warning{background:#fff4d8;padding:13px;border-radius:8px;margin:18px 0}
@media(max-width:650px){.grid{grid-template-columns:1fr}.full{grid-column:auto}}
</style>
@include('partials.seo')
</head>
<body>
<header><div class="container"><h1>Convert to Live Website</h1><a href="{{ route('admin.projects.index') }}">← Live Projects</a></div></header>
<main class="container">
<div class="box">
<div class="summary">
<strong>{{ $trial->website_name ?? $trial->business_name }}</strong><br>
Business: {{ $trial->business_name }}<br>
Authority: {{ $trial->owner_name }}<br>
Contact: {{ $trial->phone }} | {{ $trial->email ?: 'Email not provided' }}<br>
Trial URL: {{ $trial->trial_url ?: 'Not available' }}
</div>

@if($errors->any())
<div class="warning">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
@endif

<form method="POST" action="{{ route('admin.projects.store',$trial->id) }}">
@csrf
<div class="grid">
<div>
<label>Project Name *</label>
<input name="project_name" required value="{{ old('project_name',$trial->website_name ?? $trial->business_name) }}">
</div>
<div>
<label>Website Type *</label>
<select name="project_type" required>
<option value="full_website">Full Website</option>
<option value="dynamic_website">Dynamic Website with Admin Panel</option>
<option value="online_store">Online Shop / Booking / Payment Website</option>
</select>
</div>
<div>
<label>Selected Plan</label>
<select name="selected_plan_id">
<option value="">Custom Plan</option>
@foreach($plans as $plan)
<option value="{{ $plan->id }}">{{ $plan->title }} — {{ $plan->price_label }}</option>
@endforeach
</select>
</div>
<div>
<label>Custom Domain *</label>
<input name="custom_domain" required placeholder="clientdomain.com" value="{{ old('custom_domain') }}">
</div>
<div>
<label>Quoted Amount</label>
<input type="number" min="0" step="0.01" name="quoted_amount" value="{{ old('quoted_amount') }}">
</div>
<div>
<label>Paid Amount</label>
<input type="number" min="0" step="0.01" name="paid_amount" value="{{ old('paid_amount',0) }}">
</div>
<div>
<label>Payment Status *</label>
<select name="payment_status" required>
<option value="pending">Pending</option>
<option value="partial">Partially Paid</option>
<option value="paid">Fully Paid</option>
</select>
</div>
<div class="full">
<label>Project Requirements</label>
<textarea name="requirements">{{ old('requirements',$trial->services_offered) }}</textarea>
</div>
<div class="full">
<label>Admin Notes</label>
<textarea name="admin_notes">{{ old('admin_notes',$trial->admin_notes) }}</textarea>
</div>
</div>
<div class="warning">
Conversion के बाद trial expiry हट जाएगी और client की पुरानी details इसी paid project से जुड़ी रहेंगी। Domain खरीद या DNS change स्वतः नहीं होगा।
</div>
<button type="submit">Convert and Create Project</button>
</form>
</div>
</main>
</body>
</html>
