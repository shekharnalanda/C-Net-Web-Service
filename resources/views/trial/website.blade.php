@php
    $template = in_array($business->template_key ?? 'modern', ['modern','professional','creative'], true)
        ? $business->template_key
        : 'modern';

    $services = array_values(array_filter(array_map(
        'trim',
        preg_split('/\r\n|\r|\n/', $business->services_offered ?? '')
    )));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $business->website_name ?: $business->business_name }}</title>
<meta name="description" content="{{ $business->tagline ?: $business->about_business }}">
<style>
*{box-sizing:border-box}
:root{--primary:{{ $business->theme_color }};--dark:#071c32;--light:#f4f8fc;--accent:#09a9d1}
body{margin:0;font-family:Arial,sans-serif;color:#26384a;line-height:1.7;background:#fff}
.container{width:min(1080px,92%);margin:auto}
nav{display:flex;align-items:center;justify-content:space-between;padding:17px 0}
nav strong{font-size:21px;color:var(--dark)}
nav a{color:var(--primary);text-decoration:none;font-weight:700}
.hero{color:#fff;padding:100px 20px;text-align:center}
.hero h1{font-size:clamp(40px,7vw,72px);line-height:1.05;margin:0 0 14px}
.hero p{font-size:21px;max-width:750px;margin:0 auto 25px}
.category{display:inline-block;padding:6px 14px;border:1px solid #ffffff66;border-radius:30px;margin-bottom:18px}
section{padding:68px 0}
h2{color:var(--primary);font-size:clamp(30px,5vw,40px);margin:0 0 18px}
.about p{font-size:18px;white-space:pre-line}
.services{background:var(--light)}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.card{background:#fff;border:1px solid #dce6ef;padding:24px;box-shadow:0 8px 25px #0000000b}
.card:before{content:"✓";display:block;color:var(--primary);font-size:25px;font-weight:900}
.contact{background:var(--dark);color:#fff}.contact h2{color:#fff}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:30px}
.btn{display:inline-block;background:var(--primary);color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:800;margin:7px 8px 0 0}
footer{text-align:center;padding:22px;background:#03111e;color:#aebdca;font-size:13px}

/* Modern */
body.template-modern .hero{background:linear-gradient(125deg,#071c32,var(--primary),#09a9d1)}
body.template-modern .card{border-radius:16px}
body.template-modern .hero .btn{background:#fff;color:var(--primary)}

/* Professional */
body.template-professional{font-family:Georgia,"Times New Roman",serif}
body.template-professional .hero{background:linear-gradient(120deg,#101c33,#243b60)}
body.template-professional nav{border-bottom:3px solid #c99700}
body.template-professional .card{border-radius:2px;border-top:4px solid #c99700}
body.template-professional h2{color:#9a7300}
body.template-professional .btn{border-radius:2px;background:#c99700}

/* Creative */
body.template-creative{background:#fffaff}
body.template-creative .hero{background:linear-gradient(135deg,#6a11cb,var(--primary),#ff4e88)}
body.template-creative .card{border-radius:28px;transform:rotate(-1deg)}
body.template-creative .card:nth-child(even){transform:rotate(1deg)}
body.template-creative .btn{border-radius:999px}
body.template-creative h2{color:#7b22b8}

@media(max-width:720px){
    .grid,.contact-grid{grid-template-columns:1fr}
    .hero{padding:75px 15px}
    nav{flex-direction:column;gap:8px;text-align:center}
}
</style>
@include('partials.seo')
</head>

<body class="template-{{ $template }}">
<nav class="container">
    <strong>{{ $business->website_name ?: $business->business_name }}</strong>
    <a href="#contact">Contact Us</a>
</nav>

<section class="hero">
    <div class="container">
        <span class="category">{{ $business->category }}</span>
        <h1>{{ $business->website_name ?: $business->business_name }}</h1>
        <p>{{ $business->tagline ?: $business->business_name }}</p>
        @if($business->whatsapp)
            <a class="btn"
               href="https://wa.me/{{ preg_replace('/\D/','',$business->whatsapp) }}"
               target="_blank" rel="noopener">WhatsApp Us</a>
        @endif
    </div>
</section>

<section class="about">
    <div class="container">
        <h2>About Us</h2>
        <p>{{ $business->about_business }}</p>
        <p><strong>Managed by:</strong> {{ $business->owner_name }}</p>
    </div>
</section>

@if(count($services))
<section class="services">
    <div class="container">
        <h2>Our Services</h2>
        <div class="grid">
            @foreach($services as $service)
                <div class="card">{{ $service }}</div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="contact" id="contact">
    <div class="container contact-grid">
        <div>
            <h2>Contact Us</h2>
            <p>{{ $business->address }}</p>
        </div>
        <div>
            <p><strong>Authority:</strong> {{ $business->owner_name }}</p>
            <p><strong>Phone:</strong> <a style="color:#fff" href="tel:{{ $business->phone }}">{{ $business->phone }}</a></p>
            <p><strong>Email:</strong> <a style="color:#fff" href="mailto:{{ $business->email }}">{{ $business->email }}</a></p>

            @if($business->whatsapp)
                <a class="btn"
                   href="https://wa.me/{{ preg_replace('/\D/','',$business->whatsapp) }}"
                   target="_blank" rel="noopener">Chat on WhatsApp</a>
            @endif
        </div>
    </div>
</section>

<footer>
    Trial website powered by C-Net Web Services •
    Valid until {{ \Carbon\Carbon::parse($business->expires_at)->format('d M Y') }}
</footer>
</body>
</html>
