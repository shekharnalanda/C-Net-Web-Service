<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Trial Website Unavailable | C-Net Web Services</title>
<style>
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;place-items:center;padding:20px;background:linear-gradient(135deg,#061d36,#0756a3,#09a9d1);font-family:Arial,sans-serif;color:#26384a}
.box{background:#fff;width:min(650px,100%);padding:45px;border-radius:22px;text-align:center;box-shadow:0 20px 70px #0005}
.icon{font-size:55px}.box h1{color:#07223d;font-size:35px;margin:10px 0}.box p{font-size:17px}
.actions{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-top:25px}
.btn{display:inline-block;padding:12px 20px;border-radius:9px;text-decoration:none;font-weight:800;background:#0756a3;color:#fff}
.btn.alt{background:#087d50}
</style>
@include('partials.seo')
</head>
<body>
<div class="box">
    <div class="icon">⏳</div>

    @if($business->status === 'suspended')
        <h1>Website Temporarily Suspended</h1>
        <p>This trial website has been temporarily suspended. Please contact C-Net Web Services for assistance.</p>
    @else
        <h1>Free Trial Has Expired</h1>
        <p>The 7-day free trial for <strong>{{ $business->website_name ?: $business->business_name }}</strong> has ended.</p>
        <p>Upgrade the website to restore it and continue using professional web services.</p>
    @endif

    <div class="actions">
        <a class="btn" href="https://web.mciedu.com/plans">View Plans</a>
        <a class="btn alt" href="https://web.mciedu.com/enquiry">Contact for Upgrade</a>
    </div>
</div>
</body>
</html>
