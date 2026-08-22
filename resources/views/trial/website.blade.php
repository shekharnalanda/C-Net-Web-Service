<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $business->business_name }}</title>
<meta name="description" content="{{ $business->tagline ?: $business->about_business }}">
<style>
*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;color:#26384a;line-height:1.7}
.container{width:min(1000px,92%);margin:auto}.hero{background:linear-gradient(125deg,#071c32,{{ $business->theme_color }});color:#fff;padding:90px 20px;text-align:center}
.hero h1{font-size:clamp(40px,7vw,68px);margin:0 0 10px}.hero p{font-size:21px;color:#eef7ff}
section{padding:60px 0}h2{color:{{ $business->theme_color }};font-size:34px}.services{background:#f3f7fb}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.card{background:#fff;border:1px solid #dce6ef;border-radius:13px;padding:22px}
.contact{background:#071c32;color:#fff}.contact h2{color:#fff}.btn{display:inline-block;background:{{ $business->theme_color }};color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:800}
footer{text-align:center;padding:20px;background:#03111e;color:#aebdca;font-size:13px}
@media(max-width:650px){.grid{grid-template-columns:1fr}}
</style>
    @include('partials.seo')
</head>
<body>
<section class="hero"><div class="container"><h1>{{ $business->business_name }}</h1><p>{{ $business->tagline ?: $business->category }}</p></div></section>
<section><div class="container"><h2>About Us</h2><p>{{ $business->about_business }}</p></div></section>

@if($business->services_offered)
<section class="services"><div class="container"><h2>Our Services</h2><div class="grid">
@foreach(preg_split('/\r\n|\r|\n/', $business->services_offered) as $service)
@if(trim($service) !== '')<div class="card">{{ trim($service) }}</div>@endif
@endforeach
</div></div></section>
@endif

<section class="contact"><div class="container"><h2>Contact Us</h2>
<p>{{ $business->address }}</p><p>Phone: {{ $business->phone }}</p>
@if($business->email)<p>Email: {{ $business->email }}</p>@endif
@if($business->whatsapp)<a class="btn" href="https://wa.me/{{ preg_replace('/\D/','',$business->whatsapp) }}">WhatsApp Us</a>@endif
</div></section>

<footer>Trial website powered by C-Net Web Services • Valid until {{ \Carbon\Carbon::parse($business->expires_at)->format('d M Y') }}</footer>
</body>
</html>
