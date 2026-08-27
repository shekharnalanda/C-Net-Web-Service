@php
    $template = in_array($business->template_key ?? 'modern', ['modern','professional','creative','education-pro','business-pro'], true)
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
.nav-links{display:flex;gap:22px;align-items:center}
.nav-links a:hover{text-decoration:underline}
.pro-kicker{display:inline-flex;align-items:center;gap:8px;padding:7px 13px;border-radius:999px;background:#ffffff18;border:1px solid #ffffff45;font-size:13px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;margin-bottom:18px}
.hero-actions{display:flex;justify-content:center;gap:10px;flex-wrap:wrap}
.btn-outline{background:transparent!important;border:2px solid #fff}
.feature-strip{margin-top:-34px;position:relative;z-index:3}
.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;background:#fff;border-radius:18px;box-shadow:0 18px 45px #03111e22;overflow:hidden}
.feature-item{padding:24px;text-align:center;border-right:1px solid #e4edf5}
.feature-item:last-child{border-right:0}
.feature-item strong{display:block;color:var(--dark);font-size:18px}
.feature-item span{font-size:13px;color:#64748b}
.why-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:38px;align-items:center}
.why-panel{background:linear-gradient(145deg,var(--dark),var(--primary));color:#fff;padding:35px;border-radius:22px;box-shadow:0 18px 45px #03111e22}
.why-list{display:grid;gap:13px;margin-top:20px}
.why-list div{display:flex;gap:10px;align-items:flex-start}
.why-list b{color:#ffd166}
.section-lead{max-width:720px;margin:-8px 0 28px;color:#607286;font-size:17px}
.pro-cta{padding:38px 0;background:linear-gradient(120deg,var(--primary),#09a9d1);color:#fff}
.pro-cta .container{display:flex;align-items:center;justify-content:space-between;gap:24px}
.pro-cta h2{color:#fff;margin:0;font-size:30px}
.pro-cta p{margin:5px 0 0}
.pro-cta .btn{background:#fff;color:var(--primary);white-space:nowrap}
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

/* Education & Institute Pro */
body.template-education-pro{font-family:Arial,sans-serif;background:#f8fbff}
body.template-education-pro nav strong{color:#082f49}
body.template-education-pro .hero{background:linear-gradient(118deg,#031d35dd,#0756a3e8),radial-gradient(circle at 80% 20%,#f59e0b,#0756a3 48%,#031d35);text-align:left;padding:112px 20px 125px}
body.template-education-pro .hero .container{max-width:1080px}
body.template-education-pro .hero h1{max-width:850px}
body.template-education-pro .hero p{margin:0 0 25px;max-width:720px}
body.template-education-pro .hero-actions{justify-content:flex-start}
body.template-education-pro .card{border:0;border-radius:16px;border-top:5px solid #f59e0b;box-shadow:0 12px 32px #052f4914}
body.template-education-pro h2{color:#0756a3}
body.template-education-pro .btn{background:#f59e0b;color:#172033}
body.template-education-pro .contact{background:linear-gradient(125deg,#031d35,#0756a3)}

/* Business & Services Pro */
body.template-business-pro{font-family:Arial,sans-serif;background:#fff}
body.template-business-pro nav{padding:20px 0}
body.template-business-pro .hero{background:linear-gradient(115deg,#07111f,#0756a3 62%,#10b981);text-align:left;padding:112px 20px 125px}
body.template-business-pro .hero p{margin:0 0 25px;max-width:720px}
body.template-business-pro .hero-actions{justify-content:flex-start}
body.template-business-pro .feature-grid{border-radius:6px}
body.template-business-pro .card{border:0;border-radius:8px;box-shadow:0 10px 30px #11182718;border-left:4px solid #10b981}
body.template-business-pro .card:before{color:#10b981}
body.template-business-pro h2{color:#0756a3}
body.template-business-pro .why-panel{border-radius:8px;background:linear-gradient(145deg,#111827,#0756a3)}
body.template-business-pro .contact{background:#111827}

@media(max-width:720px){
    .grid,.contact-grid,.feature-grid,.why-grid{grid-template-columns:1fr}
    .feature-item{border-right:0;border-bottom:1px solid #e4edf5}
    .hero,body.template-education-pro .hero,body.template-business-pro .hero{padding:78px 15px 88px}
    nav{flex-direction:column;gap:10px;text-align:center}
    .nav-links{gap:14px;flex-wrap:wrap;justify-content:center}
    .pro-cta .container{display:block;text-align:center}
    .pro-cta .btn{margin-top:18px}
}
</style>
@include('partials.seo')
</head>

<body class="template-{{ $template }}">
<nav class="container">
    <strong>{{ $business->website_name ?: $business->business_name }}</strong>
    <div class="nav-links">
        <a href="#about">About</a>
        <a href="#services">{{ $template === 'education-pro' ? 'Programs' : 'Services' }}</a>
        <a href="#contact">Contact</a>
    </div>
</nav>

<section class="hero">
    <div class="container">
        @if(in_array($template, ['education-pro','business-pro'], true))
            <span class="pro-kicker">
                {{ $template === 'education-pro' ? 'Admissions & Learning Excellence' : 'Trusted Professional Solutions' }}
            </span>
        @else
            <span class="category">{{ $business->category }}</span>
        @endif
        <h1>{{ $business->website_name ?: $business->business_name }}</h1>
        <p>{{ $business->tagline ?: $business->business_name }}</p>
        <div class="hero-actions">
            @if($business->whatsapp)
                <a class="btn"
                   href="https://wa.me/{{ preg_replace('/\D/','',$business->whatsapp) }}"
                   target="_blank" rel="noopener">WhatsApp Us</a>
            @endif
            @if(in_array($template, ['education-pro','business-pro'], true))
                <a class="btn btn-outline" href="#services">
                    {{ $template === 'education-pro' ? 'Explore Programs' : 'Explore Services' }}
                </a>
            @endif
        </div>
    </div>
</section>

@if(in_array($template, ['education-pro','business-pro'], true))
<section class="feature-strip">
    <div class="container feature-grid">
        <div class="feature-item">
            <strong>{{ $template === 'education-pro' ? 'Quality Education' : 'Professional Quality' }}</strong>
            <span>Focused on dependable results</span>
        </div>
        <div class="feature-item">
            <strong>{{ $template === 'education-pro' ? 'Student Support' : 'Customer Support' }}</strong>
            <span>Clear guidance and assistance</span>
        </div>
        <div class="feature-item">
            <strong>Easy Connectivity</strong>
            <span>Call, email or WhatsApp</span>
        </div>
    </div>
</section>
@endif

<section class="about" id="about">
    <div class="container">
        <h2>About Us</h2>
        <p>{{ $business->about_business }}</p>
        <p><strong>Managed by:</strong> {{ $business->owner_name }}</p>
    </div>
</section>

@if(count($services))
<section class="services" id="services">
    <div class="container">
        <h2>{{ $template === 'education-pro' ? 'Courses & Programs' : 'Our Services' }}</h2>
        @if(in_array($template, ['education-pro','business-pro'], true))
            <p class="section-lead">
                {{ $template === 'education-pro'
                    ? 'Explore learning opportunities designed to build knowledge, confidence and future-ready skills.'
                    : 'Practical services delivered with quality, transparency and attention to your requirements.' }}
            </p>
        @endif
        <div class="grid">
            @foreach($services as $service)
                <div class="card">{{ $service }}</div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(in_array($template, ['education-pro','business-pro'], true))
<section>
    <div class="container why-grid">
        <div>
            <h2>Why Choose {{ $business->website_name ?: $business->business_name }}?</h2>
            <p class="section-lead">
                A trusted local presence, clear communication and a commitment to serving every
                {{ $template === 'education-pro' ? 'student and family' : 'customer and client' }} professionally.
            </p>
        </div>
        <div class="why-panel">
            <strong style="font-size:24px">Our Commitment</strong>
            <div class="why-list">
                <div><b>✓</b><span>Transparent information and responsive support</span></div>
                <div><b>✓</b><span>Professional service with personal attention</span></div>
                <div><b>✓</b><span>Convenient contact through phone, email and WhatsApp</span></div>
            </div>
        </div>
    </div>
</section>

<section class="pro-cta">
    <div class="container">
        <div>
            <h2>{{ $template === 'education-pro' ? 'Ready to Begin Your Learning Journey?' : 'Ready to Work With Us?' }}</h2>
            <p>Contact us today for complete information and personal assistance.</p>
        </div>
        <a class="btn" href="#contact">Contact Now</a>
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
