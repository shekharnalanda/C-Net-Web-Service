<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Website Plans | C-Net Web Services</title>
<meta name="description" content="Website design, hosting and digital service plans from C-Net Web Services.">
<style>
*{box-sizing:border-box}body{margin:0;background:#f4f8fc;color:#293c4f;font-family:Arial,sans-serif}
header{background:#fff;padding:17px 0;box-shadow:0 2px 14px #00000010}.container{width:min(1120px,92%);margin:auto}
nav{display:flex;justify-content:space-between;align-items:center}nav strong{color:#07223d;font-size:22px}nav a{color:#0756a3;text-decoration:none;font-weight:700}
.hero{text-align:center;background:linear-gradient(125deg,#061d36,#0756a3,#09a9d1);color:#fff;padding:65px 20px}
.hero h1{font-size:clamp(38px,6vw,58px);margin:0 0 12px}.hero p{font-size:18px;color:#dcefff}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:23px;padding:45px 0 70px}
.card{background:#fff;border:1px solid #dce7f1;border-radius:18px;padding:29px;position:relative;box-shadow:0 10px 30px #00000009}
.card.featured{border:2px solid #08a5cf;transform:translateY(-8px)}
.tag{position:absolute;right:18px;top:-13px;background:#08a5cf;color:#fff;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:800}
.card h2{color:#07223d;margin:3px 0}.type{color:#0756a3;font-weight:700}.price{font-size:26px;font-weight:900;color:#07223d;margin:18px 0 4px}.duration{color:#64748b}
ul{padding:0;margin:23px 0;list-style:none}li{padding:8px 0;border-bottom:1px solid #edf2f7}li:before{content:"✓";color:#07934a;font-weight:900;margin-right:9px}
.btn{display:block;text-align:center;background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;text-decoration:none;padding:12px;border-radius:8px;font-weight:800}
.empty{text-align:center;padding:60px}
@media(max-width:850px){.grid{grid-template-columns:1fr 1fr}.card.featured{transform:none}}
@media(max-width:560px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<header><nav class="container"><strong>C-Net Web Services</strong><a href="/">← Home</a></nav></header>
<section class="hero"><h1>Website Plans & Packages</h1><p>Choose a solution and customize it according to your requirement.</p></section>

<div class="container grid">
@forelse($plans as $plan)
    <article class="card {{ $plan->is_featured ? 'featured' : '' }}">
        @if($plan->is_featured)<span class="tag">RECOMMENDED</span>@endif
        <div class="type">{{ $plan->service_type }}</div>
        <h2>{{ $plan->title }}</h2>
        <div class="price">{{ $plan->price_label }}</div>
        @if($plan->duration)<div class="duration">{{ $plan->duration }}</div>@endif
        <ul>
            @foreach(preg_split('/\r\n|\r|\n/', $plan->features) as $feature)
                @if(trim($feature) !== '')<li>{{ trim($feature) }}</li>@endif
            @endforeach
        </ul>
        <a class="btn" href="{{ route('enquiry.create') }}">Choose This Plan</a>
    </article>
@empty
    <div class="empty">Plans will be available soon.</div>
@endforelse
</div>
</body>
</html>
