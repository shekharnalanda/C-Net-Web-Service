<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'C-Net Web Services | वेबसाइट, CRM, ERP और Digital Support')</title>
    <meta name="description" content="@yield('description', 'C-Net Web Services: Website Development, Digital Marketing, CRM, School ERP, Custom Software और संस्थान को डिजिटल बनाने की सेवाएँ।')">
    @php
        $publicPageTitle = trim($__env->yieldContent('title', 'C-Net Web Services | Complete Digital Solutions'));
        $publicPageDescription = trim($__env->yieldContent('description', 'वेबसाइट, Digital Marketing, CRM, School ERP, Software और तकनीकी Support की सेवाएँ।'));
    @endphp
    @include('partials.seo')
    @include('partials.public-style')
</head>
<body>
<a class="skip-link" href="#main-content">मुख्य जानकारी पर जाएँ</a>
<header class="site-header"><nav class="container" aria-label="मुख्य नेविगेशन">
    <a href="{{ route('home') }}" class="brand"><img src="/images/cnet-favicon.png" width="46" height="46" alt="C-Net Web Services Logo"><span><strong>C-Net Web Services</strong><small>MCI Educational Group</small></span></a>
    <div class="nav-links"><a href="{{ route('home') }}#services">हमारी सेवाएँ</a><a href="{{ route('home') }}#process">काम की प्रक्रिया</a><a href="{{ route('plans') }}">Plans</a><a href="{{ route('trial.apply') }}">Free Trial</a><a class="btn btn-primary" href="{{ route('enquiry.create') }}">अपनी जरूरत बताएँ</a></div>
    <details class="mobile-menu"><summary>मेन्यू</summary><div><a href="{{ route('home') }}">होम पेज</a><a href="{{ route('home') }}#services">सभी सेवाएँ</a><a href="{{ route('home') }}#process">काम की प्रक्रिया</a><a href="{{ route('plans') }}">Plans</a><a href="{{ route('trial.apply') }}">Free Trial</a><a href="{{ route('enquiry.create') }}">Enquiry भेजें</a></div></details>
</nav></header>
<main id="main-content">@yield('content')</main>
<footer><div class="container"><div class="footer-top"><div><strong>C-Net Web Services</strong><p>MCI Educational Group<br>Bihar Sharif, Nalanda, Bihar</p></div><div><strong>आगे का कदम</strong><p><a href="{{ route('enquiry.create') }}">अपनी जरूरत भेजें</a><br><a href="{{ route('plans') }}">Website Plans देखें</a><br><a href="{{ route('home') }}">होम पेज पर लौटें</a></p></div><div><strong>आपकी सहायता के लिए</strong><p><a href="tel:+917782801846">+91 77828 01846</a><br><a href="{{ route('admin.login') }}">Admin Login</a><br><a href="{{ route('privacy-policy') }}">Privacy Policy</a></p></div></div><div class="footer-app"><span>Android मोबाइल से भी सेवाओं तक पहुँचें।</span><a class="app-download" href="https://github.com/shekharnalanda/C-Net-Web-Service/releases/download/android-latest/C-Net-Web-Services.apk">Install Mobile App</a></div><small>© {{ date('Y') }} C-Net Web Services. All Rights Reserved.</small></div></footer>
@include('partials.contact-buttons')
</body></html>
