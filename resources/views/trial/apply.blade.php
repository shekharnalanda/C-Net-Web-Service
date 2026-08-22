<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Apply for Free Trial Website | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f2f7fc;color:#293c4f;font-family:Arial,sans-serif}
header{background:linear-gradient(125deg,#061d36,#0756a3,#09a9d1);color:#fff;text-align:center;padding:50px 20px}
header h1{font-size:clamp(34px,5vw,50px);margin:0 0 10px}.container{width:min(820px,92%);margin:auto}
.box{background:#fff;margin:35px auto;padding:35px;border-radius:18px;box-shadow:0 10px 35px #0001}
label{display:block;font-weight:700;margin:16px 0 7px;color:#07223d}input,textarea,select{width:100%;padding:12px;border:1px solid #c6d4e1;border-radius:8px;font:inherit}
textarea{min-height:110px}.row{display:grid;grid-template-columns:1fr 1fr;gap:18px}
button{background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;border:0;padding:14px 23px;border-radius:8px;font-weight:800;margin-top:22px}
.success{background:#e5f8ed;color:#176b38;padding:14px;border-radius:8px}.error{background:#feecef;color:#a31327;padding:13px;border-radius:8px}
.back{display:inline-block;margin:20px 0;color:#0756a3;text-decoration:none;font-weight:700}
@media(max-width:650px){.row{grid-template-columns:1fr}}
</style>
    @include('partials.seo')
</head>
<body>
<header><h1>Get Your Free Trial Website</h1><p>Submit your business details for a 7–10 day one-page website trial.</p></header>
<div class="container">
<div class="box">
@if(session('success'))<div class="success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif

<form method="POST" action="{{ route('trial.store') }}">
@csrf
<div class="row">
<div><label>Business Name *</label><input name="business_name" required value="{{ old('business_name') }}"></div>
<div><label>Owner Name *</label><input name="owner_name" required value="{{ old('owner_name') }}"></div>
</div>

<div class="row">
<div><label>Mobile Number *</label><input name="phone" required value="{{ old('phone') }}"></div>
<div><label>Email</label><input type="email" name="email" value="{{ old('email') }}"></div>
</div>

<div class="row">
<div><label>Desired Website Name *</label><input name="desired_slug" required placeholder="example: my-business" value="{{ old('desired_slug') }}"></div>
<div><label>Business Category *</label><input name="category" required placeholder="Education, Shop, Consultant..." value="{{ old('category') }}"></div>
</div>

<label>Business Tagline</label><input name="tagline" value="{{ old('tagline') }}">
<label>About Your Business *</label><textarea name="about_business" required>{{ old('about_business') }}</textarea>
<label>Services or Products — one per line</label><textarea name="services_offered">{{ old('services_offered') }}</textarea>
<label>Business Address</label><textarea name="address">{{ old('address') }}</textarea>

<div class="row">
<div><label>WhatsApp Number</label><input name="whatsapp" value="{{ old('whatsapp') }}"></div>
<div><label>Website Colour</label><input type="color" name="theme_color" value="{{ old('theme_color','#0756a3') }}"></div>
</div>

<button type="submit">Submit Trial Application</button>
</form>
<a class="back" href="/">← Back to Home</a>
</div>
</div>
    @include('partials.contact-buttons')
</body>
</html>
