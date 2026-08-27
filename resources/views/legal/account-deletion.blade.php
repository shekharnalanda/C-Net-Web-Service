<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Account & Data Deletion | C-Net Web Services</title>
    <meta name="description" content="Request deletion of a C-Net Web Services mobile app client account and associated data.">
    <style>
        :root{--navy:#061d36;--blue:#0756a3;--cyan:#09a9d1;--orange:#ff7a1a;--paper:#f4f8fc;--ink:#172033}
        *{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font:16px/1.6 Arial,sans-serif}
        header{background:linear-gradient(135deg,var(--navy),var(--blue),var(--cyan));color:#fff;padding:40px 20px}header div,main{width:min(760px,92%);margin:auto}
        h1{margin:0;font-size:clamp(28px,5vw,44px)}header p{margin:6px 0 0;color:#dff5ff}main{background:#fff;margin-top:26px;margin-bottom:36px;padding:clamp(22px,5vw,44px);border-radius:18px;box-shadow:0 10px 35px #061d3615}
        label{display:block;font-weight:700;margin:15px 0 6px}input,textarea{width:100%;padding:13px;border:1px solid #bfd0df;border-radius:9px;font:inherit}textarea{min-height:110px;resize:vertical}
        button{width:100%;margin-top:20px;padding:14px;border:0;border-radius:9px;background:var(--blue);color:#fff;font-size:17px;font-weight:800;cursor:pointer}.check{display:flex;gap:10px;align-items:flex-start}.check input{width:auto;margin-top:6px}.error{color:#b42318;font-size:14px}.success{background:#e7f8ed;color:#146c36;padding:15px;border-radius:9px}.info{background:#eef6ff;padding:15px;border-radius:9px;border-left:5px solid var(--cyan)}a{color:var(--blue)}
    </style>
</head>
<body>
<header><div><h1>Account & Data Deletion</h1><p>C-Net Web Services mobile app and client portal</p></div></header>
<main>
    @if(session('success'))<p class="success">{{ session('success') }}</p>@endif
    <div class="info"><strong>What happens:</strong> We verify ownership through the registered email, revoke mobile access tokens and delete or anonymise associated personal data, trial records and project information that we are not legally required to retain. Processing normally takes up to 30 days.</div>

    <form method="POST" action="{{ route('account-deletion.request') }}">
        @csrf
        <label for="name">Full name</label>
        <input id="name" name="name" value="{{ old('name') }}" required maxlength="100">
        @error('name')<div class="error">{{ $message }}</div>@enderror

        <label for="email">Registered email address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="150">
        @error('email')<div class="error">{{ $message }}</div>@enderror

        <label for="phone">Mobile number (optional)</label>
        <input id="phone" name="phone" value="{{ old('phone') }}" maxlength="20">

        <label for="reason">Additional information (optional)</label>
        <textarea id="reason" name="reason" maxlength="1000">{{ old('reason') }}</textarea>

        <label class="check"><input type="checkbox" name="confirmation" value="1" required><span>I request deletion of my C-Net Web Services client account and associated data and understand that deleted trial/project access may not be recoverable.</span></label>
        @error('confirmation')<div class="error">Please confirm the deletion request.</div>@enderror

        <button type="submit">Submit Deletion Request</button>
    </form>
    <p style="margin-top:22px">For privacy details, read our <a href="{{ route('privacy-policy') }}">Privacy Policy</a>. For assistance, email <a href="mailto:cnetbiharsharif@gmail.com">cnetbiharsharif@gmail.com</a>.</p>
</main>
</body>
</html>
