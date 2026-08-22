<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:linear-gradient(135deg,#061d36,#0756a3,#09a9d1);min-height:100vh;display:grid;place-items:center;padding:20px}
.box{background:#fff;width:min(430px,100%);padding:38px;border-radius:20px;box-shadow:0 20px 60px #0005}
.logo{width:58px;height:58px;background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;border-radius:15px;display:grid;place-items:center;font-size:32px;font-weight:900;margin:auto}
h1{text-align:center;color:#07223d;margin:14px 0 5px}.sub{text-align:center;color:#64748b;margin-bottom:25px}
label{display:block;font-weight:700;margin:15px 0 7px;color:#07223d}
input{width:100%;padding:13px;border:1px solid #cbd5e1;border-radius:8px;font:inherit}
.remember{display:flex;align-items:center;gap:8px;margin-top:14px}.remember input{width:auto}
button{width:100%;padding:13px;margin-top:20px;border:0;border-radius:9px;background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;font-weight:800;font-size:16px;cursor:pointer}
.error{background:#feecef;color:#a31327;padding:12px;border-radius:8px;margin-bottom:15px}
.back{display:block;text-align:center;margin-top:18px;color:#0756a3;text-decoration:none}
</style>
    @include('partials.seo')
</head>
<body>
<div class="box">
    <div class="logo">C</div>
    <h1>Admin Login</h1>
    <p class="sub">C-Net Web Services</p>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.authenticate') }}">
        @csrf
        <label>Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label>Password</label>
        <input type="password" name="password" required>

        <label class="remember">
            <input type="checkbox" name="remember" value="1"> Keep me logged in
        </label>

        <button type="submit">Login to Dashboard</button>
    </form>

    <a class="back" href="/">← Back to Website</a>
</div>
</body>
</html>
