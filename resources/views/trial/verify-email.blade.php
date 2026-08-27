<!doctype html>
<html lang="hi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Email Verification OTP | C-Net Web Services</title>
<style>
*{box-sizing:border-box}
body{
    margin:0;
    min-height:100vh;
    display:grid;
    place-items:center;
    padding:24px;
    font-family:Arial,sans-serif;
    background:linear-gradient(135deg,#052f59,#0874c9);
    color:#172033
}
.card{
    width:min(500px,100%);
    background:#fff;
    border-radius:18px;
    padding:30px;
    box-shadow:0 20px 50px #001a33aa
}
.brand{text-align:center;color:#063b71;margin:0 0 8px;font-size:27px}
.subtitle{text-align:center;color:#64748b;margin:0 0 22px}
.notice{padding:12px;border-radius:8px;margin-bottom:15px}
.success{background:#e9f9ef;color:#126b38}
.error{background:#fff0f0;color:#a32020}
label{font-weight:700;display:block;margin-bottom:8px}
input{
    width:100%;
    padding:15px;
    border:2px solid #cbd5e1;
    border-radius:9px;
    font-size:25px;
    text-align:center;
    letter-spacing:8px
}
button{
    width:100%;
    border:0;
    border-radius:9px;
    padding:14px;
    margin-top:15px;
    font-size:16px;
    font-weight:700;
    cursor:pointer
}
.verify{background:#f58220;color:#fff}
.resend{background:#eaf2fa;color:#0756a3}
.back{display:block;text-align:center;margin-top:18px;color:#0756a3}
.help{text-align:center;color:#64748b;font-size:14px;line-height:1.6}
</style>
</head>
<body>
<main class="card">
<h1 class="brand">C-Net Web Services</h1>
<p class="subtitle">Mandatory Email Verification</p>

@if(session('success'))
<div class="notice success">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="notice error">
@foreach($errors->all() as $error)
<div>{{ $error }}</div>
@endforeach
</div>
@endif

<p class="help">
6 अंकों का OTP <strong>{{ $email }}</strong> पर भेजा गया है।<br>
OTP 5 मिनट तक valid है।
</p>

<form method="POST" action="{{ route('trial.email.verify') }}">
@csrf
<label for="otp">Email OTP</label>
<input
    id="otp"
    name="otp"
    inputmode="numeric"
    autocomplete="one-time-code"
    maxlength="6"
    pattern="[0-9]{6}"
    required
    autofocus
>
<button type="submit" class="verify">
Verify Email &amp; Create Trial Website
</button>
</form>

<form method="POST" action="{{ route('trial.email.resend') }}">
@csrf
<button type="submit" class="resend">
Resend OTP
@if($resendAfter > 0)
({{ $resendAfter }} सेकंड बाद)
@endif
</button>
</form>

<a class="back" href="{{ route('trial.apply') }}">
← Trial form पर वापस जाएँ
</a>
</main>
</body>
</html>
