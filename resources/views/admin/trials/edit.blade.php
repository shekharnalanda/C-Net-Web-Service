<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Trial Website | C-Net Web Services</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
.container{width:min(1000px,94%);margin:auto}
header{background:#07223d;color:#fff;padding:16px 0}
.top{display:flex;justify-content:space-between;align-items:center;gap:15px}
.brand{display:flex;align-items:center;gap:11px}
.brand img{width:48px;height:48px;object-fit:contain;border-radius:12px}
header h1{margin:0;font-size:22px}
header a{color:#fff;text-decoration:none;border:1px solid #ffffff55;padding:9px 13px;border-radius:7px}
main{padding:28px 0}
.box{background:#fff;padding:24px;border-radius:15px;box-shadow:0 7px 25px #0001}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}
.full{grid-column:1/-1}
label{display:block;font-weight:700;margin-bottom:7px}
input,select,textarea{width:100%;padding:12px;border:1px solid #bdcad8;border-radius:8px;font:inherit}
textarea{min-height:105px;resize:vertical}
.notice{background:#e4f7eb;color:#176b38;padding:13px;border-radius:8px;margin-bottom:18px}
.errors{background:#feecef;color:#a31327;padding:13px;border-radius:8px;margin-bottom:18px}
.url{background:#edf6ff;padding:13px;border-radius:8px;margin-bottom:20px;overflow-wrap:anywhere}
.actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:22px}
button,.btn{border:0;padding:12px 18px;border-radius:8px;color:#fff;text-decoration:none;font-weight:800;cursor:pointer}
.save{background:#0756a3}.open{background:#078447}
.warning{background:#fff4d8;padding:13px;border-radius:8px;margin:18px 0}
@media(max-width:650px){.grid{grid-template-columns:1fr}.full{grid-column:auto}.top{align-items:flex-start;flex-direction:column}}
</style>
@include('partials.seo')
</head>
<body>
<header>
<div class="container top">
    <div class="brand">
        <img src="/images/cnet-favicon.png" alt="C-Net Logo">
        <h1>Edit Trial Website</h1>
    </div>
    <a href="{{ route('admin.trials.index') }}">← Trial Applications</a>
</div>
</header>

<main class="container">
@if(session('success'))
<div class="notice">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="errors">
@foreach($errors->all() as $error)
<div>{{ $error }}</div>
@endforeach
</div>
@endif

<div class="box">
<div class="url">
<strong>Live URL:</strong>
<a href="{{ $trial->trial_url }}" target="_blank">{{ $trial->trial_url }}</a>
</div>

<form method="POST" action="{{ route('admin.trial-websites.update',$trial->id) }}">
@csrf
@method('PATCH')

<div class="grid">
<div>
<label>Website Name *</label>
<input name="website_name" required value="{{ old('website_name',$trial->website_name) }}">
</div>

<div>
<label>Business Name *</label>
<input name="business_name" required value="{{ old('business_name',$trial->business_name) }}">
</div>

<div>
<label>Authority Name *</label>
<input name="owner_name" required value="{{ old('owner_name',$trial->owner_name) }}">
</div>

<div>
<label>Phone *</label>
<input name="phone" required value="{{ old('phone',$trial->phone) }}">
</div>

<div>
<label>Email</label>
<input type="email" name="email" value="{{ old('email',$trial->email) }}">
</div>

<div>
<label>WhatsApp</label>
<input name="whatsapp" value="{{ old('whatsapp',$trial->whatsapp) }}">
</div>

<div>
<label>Subdomain *</label>
<input name="desired_slug" required value="{{ old('desired_slug',$trial->desired_slug) }}">
<small>.mciedu.com</small>
</div>

<div>
<label>Category *</label>
<input name="category" required value="{{ old('category',$trial->category) }}">
</div>

<div>
<label>Template *</label>
<select name="template_key" required>
@foreach(['modern','professional','creative'] as $template)
<option value="{{ $template }}" @selected(old('template_key',$trial->template_key)===$template)>
{{ ucfirst($template) }}
</option>
@endforeach
</select>
</div>

<div>
<label>Theme Color *</label>
<input type="color" name="theme_color" required value="{{ old('theme_color',$trial->theme_color ?: '#0756a3') }}">
</div>

<div>
<label>Status *</label>
<select name="status" required>
@foreach(['pending','approved','suspended','expired','rejected','upgraded'] as $status)
<option value="{{ $status }}" @selected(old('status',$trial->status)===$status)>
{{ ucfirst($status) }}
</option>
@endforeach
</select>
</div>

<div>
<label>Expiry Date and Time</label>
<input
    type="datetime-local"
    name="expires_at"
    value="{{ old('expires_at',$trial->expires_at ? date('Y-m-d\TH:i',strtotime($trial->expires_at)) : '') }}"
>
</div>

<div class="full">
<label>Tagline</label>
<input name="tagline" value="{{ old('tagline',$trial->tagline) }}">
</div>

<div class="full">
<label>About Business *</label>
<textarea name="about_business" required>{{ old('about_business',$trial->about_business) }}</textarea>
</div>

<div class="full">
<label>Services Offered</label>
<textarea name="services_offered">{{ old('services_offered',$trial->services_offered) }}</textarea>
</div>

<div class="full">
<label>Address</label>
<textarea name="address">{{ old('address',$trial->address) }}</textarea>
</div>

<div class="full">
<label>Admin Notes</label>
<textarea name="admin_notes">{{ old('admin_notes',$trial->admin_notes) }}</textarea>
</div>
</div>

<div class="warning">
Subdomain बदलने पर पुराना URL बंद हो जाएगा। Delete करने के लिए Trial Applications page पर जाएँ।
</div>

<div class="actions">
<button type="submit" class="save">Save Website Changes</button>
<a class="btn open" href="{{ $trial->trial_url }}" target="_blank">Open Live Website</a>
</div>
</form>
</div>
</main>
</body>
</html>
