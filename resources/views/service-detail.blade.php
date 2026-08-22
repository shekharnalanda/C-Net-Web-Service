<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $service->title }} | C-Net Web Services</title>
<meta name="description" content="{{ $service->short_description }}">
<style>
*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;color:#2b3e50;background:#f5f8fc}
header{background:#fff;padding:16px 0;box-shadow:0 2px 12px #00000012}.container{width:min(950px,92%);margin:auto}
nav{display:flex;justify-content:space-between;align-items:center}nav strong{color:#07223d;font-size:21px}nav a{text-decoration:none;color:#0756a3;font-weight:700}
.hero{background:linear-gradient(125deg,#061d36,#0756a3,#09a9d1);color:#fff;padding:70px 0}
.icon{font-size:52px}.hero h1{font-size:clamp(38px,6vw,58px);margin:10px 0}.hero p{font-size:19px;color:#e0f0ff}
.content{background:#fff;margin:35px auto;padding:38px;border-radius:18px;box-shadow:0 10px 35px #0000000c}
.content h2{color:#07223d}.details{white-space:pre-line;font-size:17px;line-height:1.8}
.btn{display:inline-block;margin-top:26px;padding:13px 22px;background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;text-decoration:none;border-radius:8px;font-weight:800}
</style>
    @include('partials.seo')
</head>
<body>
<header><nav class="container"><strong>C-Net Web Services</strong><a href="/">← Home</a></nav></header>
<section class="hero">
<div class="container">
    <div class="icon">{{ $service->icon }}</div>
    <h1>{{ $service->title }}</h1>
    <p>{{ $service->short_description }}</p>
</div>
</section>
<div class="container content">
    <h2>Service Details</h2>
    <div class="details">{{ $service->description }}</div>
    <a class="btn" href="{{ route('enquiry.create') }}">Request This Service</a>
</div>
    @include('partials.contact-buttons')
</body>
</html>
