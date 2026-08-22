<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Enquiry | C-Net Web Services</title>
    <style>
        *{box-sizing:border-box} body{margin:0;font-family:Arial,sans-serif;background:#f2f7fc;color:#26384a}
        .header{background:linear-gradient(120deg,#061d36,#0756a3,#09a9d1);color:#fff;padding:42px 20px;text-align:center}
        .header h1{margin:0 0 8px;font-size:38px}.header p{margin:0;color:#dbeeff}
        .wrap{width:min(720px,92%);margin:35px auto}.box{background:#fff;border-radius:18px;padding:34px;box-shadow:0 12px 40px #07223d17}
        label{display:block;font-weight:700;margin:17px 0 7px;color:#07223d}
        input,select,textarea{width:100%;padding:13px;border:1px solid #cbd8e6;border-radius:8px;font:inherit}
        input:focus,select:focus,textarea:focus{outline:2px solid #75ccea;border-color:#0756a3}
        textarea{min-height:120px;resize:vertical}
        button,.back{display:inline-block;border:0;border-radius:9px;padding:13px 22px;font-weight:700;text-decoration:none;cursor:pointer}
        button{background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;margin-top:20px}
        .back{color:#0756a3;margin-left:8px}.success{background:#e8f8ee;color:#176b38;padding:14px;border-radius:8px;margin-bottom:18px}
        .error{color:#bb1e2d;font-size:14px;margin-top:5px}
    </style>
    @include('partials.seo')
</head>
<body>
<div class="header">
    <h1>Request a Website</h1>
    <p>Tell us what you need and our team will contact you.</p>
</div>

<div class="wrap">
    <div class="box">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('enquiry.store') }}">
            @csrf

            <label for="name">Your Name *</label>
            <input id="name" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <label for="phone">Mobile Number *</label>
            <input id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone') <div class="error">{{ $message }}</div> @enderror

            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}">
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label for="service">Required Service *</label>
            <select id="service" name="service" required>
                <option value="">Select Service</option>
                <option>Domain Registration</option>
                <option>Web Hosting</option>
                <option>Static Website</option>
                <option>Dynamic Website</option>
                <option>SEO Services</option>
                <option>Digital Promotion</option>
                <option>7–10 Day Trial Website</option>
                <option>Other Requirement</option>
            </select>

            <label for="message">Requirement Details</label>
            <textarea id="message" name="message">{{ old('message') }}</textarea>

            <button type="submit">Submit Enquiry</button>
            <a href="/" class="back">← Back to Home</a>
        </form>
    </div>
</div>
</body>
</html>
