<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Website Created Successfully | C-Net Web Services</title>
<style>
*{box-sizing:border-box}
:root{--blue:#0756a3;--dark:#061d36;--cyan:#09a9d1;--orange:#f58220;--green:#078447}
body{margin:0;min-height:100vh;font-family:Arial,sans-serif;background:linear-gradient(135deg,#061d36,#0756a3 58%,#09a9d1);color:#23374a;padding:28px 16px}
.wrap{width:min(760px,100%);margin:auto}
.card{background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 24px 70px #00172e99}
.top{text-align:center;padding:38px 28px 28px;background:linear-gradient(180deg,#effff5,#fff)}
.check{width:82px;height:82px;border-radius:50%;display:grid;place-items:center;margin:0 auto 20px;background:var(--green);color:#fff;font-size:48px;font-weight:900;box-shadow:0 10px 30px #0784474d}
h1{color:#063b71;font-size:clamp(30px,6vw,46px);line-height:1.12;margin:0 0 12px}
.welcome{font-size:19px;color:#526679;margin:0}
.details{padding:30px}
.site-box{background:#f2f8ff;border:2px solid #cfe4f7;border-radius:16px;padding:22px;text-align:center}
.site-label{font-size:13px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.08em}
.site-name{font-size:27px;font-weight:900;color:#062d53;margin:7px 0}
.site-url{display:block;color:#0756a3;font-size:17px;word-break:break-all;margin:8px 0 15px}
.open{display:inline-block;background:linear-gradient(135deg,var(--orange),#ff9a3d);color:#fff;text-decoration:none;padding:15px 28px;border-radius:10px;font-size:18px;font-weight:900;box-shadow:0 8px 22px #f5822044}
.copy{display:inline-block;background:#eaf2fa;color:#0756a3;border:0;padding:13px 18px;border-radius:10px;font-weight:800;cursor:pointer;margin:8px}
.info{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:20px}
.info div{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:15px}
.info strong{display:block;color:#063b71;margin-bottom:4px}
.message{margin-top:22px;padding:18px;background:#fff8ec;border-left:5px solid var(--orange);border-radius:9px;line-height:1.65}
.actions{text-align:center;margin-top:24px}
.actions a{color:#0756a3;font-weight:800;text-decoration:none;margin:7px 12px;display:inline-block}
.brand{text-align:center;color:#d9efff;padding:22px;font-size:14px}
@media(max-width:600px){.info{grid-template-columns:1fr}.details{padding:22px}.top{padding:30px 20px 24px}.open{width:100%}}
</style>
</head>
<body>
<div class="wrap">
<main class="card">
<section class="top">
<div class="check">✓</div>
<h1>Congratulations!</h1>
<p class="welcome">
Welcome to C-Net Web Services.<br>
Your Trial Website has been created successfully.
</p>
</section>

<section class="details">
<div class="site-box">
<div class="site-label">Your New Website</div>
<div class="site-name">{{ $website['website_name'] }}</div>
<a class="site-url" href="{{ $website['trial_url'] }}" target="_blank" rel="noopener">
{{ $website['trial_url'] }}
</a>
<a class="open" href="{{ $website['trial_url'] }}" target="_blank" rel="noopener">
Open My Website ↗
</a>
<button class="copy" type="button" onclick="copyWebsiteLink()">Copy Link</button>
</div>

<div class="info">
<div>
<strong>Subdomain</strong>
{{ $website['desired_slug'] }}.mciedu.com
</div>
<div>
<strong>Free Trial Valid Until</strong>
{{ $website['expires_at'] }}
</div>
</div>

<div class="message">
<strong>Next Step:</strong>
Please open your website and review its design and information.
If you like this template, C-Net Web Services can expand the same design
into your complete professional website with additional pages, admin panel
and your own domain.
</div>

<div class="actions">
<a href="{{ $website['trial_url'] }}" target="_blank" rel="noopener">View Website</a>
<a href="{{ route('trial.apply') }}">Create Another Trial</a>
<a href="/">C-Net Web Services Home</a>
</div>
</section>
</main>

<div class="brand">
Powered by <strong>C-Net Web Services</strong> • web.mciedu.com
</div>
</div>

<script>
function copyWebsiteLink() {
    const url = @json($website['trial_url']);

    navigator.clipboard.writeText(url).then(function () {
        const button = document.querySelector('.copy');
        button.textContent = 'Link Copied ✓';
        setTimeout(function () {
            button.textContent = 'Copy Link';
        }, 2000);
    });
}
</script>
</body>
</html>
