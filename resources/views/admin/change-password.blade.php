<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Change Password | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:#f2f7fc;color:#26384a}
header{background:#07223d;color:#fff;padding:19px}.container{width:min(650px,92%);margin:auto}
header .container{display:flex;justify-content:space-between;align-items:center}
header a{color:#fff;text-decoration:none;border:1px solid #ffffff55;padding:8px 12px;border-radius:7px}
.box{background:#fff;margin-top:38px;padding:35px;border-radius:18px;box-shadow:0 12px 35px #00000010}
h1{color:#07223d;margin-top:0}p{color:#64748b}
label{display:block;font-weight:700;margin:17px 0 7px}
input{width:100%;padding:13px;border:1px solid #cbd5e1;border-radius:8px;font:inherit}
button{margin-top:22px;border:0;border-radius:8px;padding:13px 22px;background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;font-weight:800;cursor:pointer}
.success{background:#e7f8ed;color:#176b38;padding:13px;border-radius:8px;margin-bottom:16px}
.error{background:#feecef;color:#a31327;padding:13px;border-radius:8px;margin-bottom:16px}
.help{font-size:13px;color:#64748b;margin-top:7px}
</style>
</head>
<body>
<header>
    <div class="container">
        <strong>C-Net Web Services</strong>
        <a href="{{ route('admin.dashboard') }}">← Dashboard</a>
    </div>
</header>

<div class="container">
    <div class="box">
        <h1>Change Admin Password</h1>
        <p>Replace the temporary password with your own secure password.</p>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            @method('PUT')

            <label>Current Password</label>
            <input type="password" name="current_password" required autocomplete="current-password">

            <label>New Password</label>
            <input type="password" name="password" required autocomplete="new-password">
            <div class="help">Minimum 8 characters with uppercase, lowercase and number.</div>

            <label>Confirm New Password</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit">Change Password</button>
        </form>
    </div>
</div>
</body>
</html>
