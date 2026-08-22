@php
    $host = request()->getHost();
    $path = request()->path();

    $seoDescription = match (true) {
        request()->is('plans') =>
            'Affordable website design, domain, hosting, SEO and digital promotion plans from C-Net Web Services.',
        request()->is('enquiry') =>
            'Contact C-Net Web Services for website design, domain, hosting, SEO and digital promotion services.',
        request()->is('trial/apply') =>
            'Apply for a temporary website trial and preview your business website with C-Net Web Services.',
        request()->is('services/*') =>
            'Professional domain, hosting, website design, development, SEO and promotion services.',
        default =>
            'C-Net Web Services provides domain, hosting, professional website design, SEO and digital promotion solutions.',
    };

    $canonicalUrl = $host === 'web.mciedu.com'
        ? 'https://web.mciedu.com'.($path === '/' ? '' : '/'.ltrim($path, '/'))
        : 'https://'.$host.request()->getPathInfo();

    $privatePage = request()->is('admin*')
        || ($host !== 'web.mciedu.com')
        || (request()->is('trial/*') && !request()->is('trial/apply'));

    $businessSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => 'C-Net Web Services',
        'url' => 'https://web.mciedu.com',
        'description' => 'Domain, hosting, website design, SEO and digital promotion services.',
        'areaServed' => 'India',
        'serviceType' => [
            'Domain Registration',
            'Web Hosting',
            'Website Design',
            'Website Development',
            'Search Engine Optimization',
            'Digital Promotion',
        ],
    ];
@endphp

<meta name="google-site-verification" content="bD6w1iioM8iZct5WyRBey9B2QW-UIonDWArr6JLVhsQ">
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-0BXSP581FD"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("js", new Date());
gtag("config", "G-0BXSP581FD", { anonymize_ip: true });
</script>
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta name="robots" content="{{ $privatePage ? 'noindex, nofollow' : 'index, follow, max-image-preview:large' }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="C-Net Web Services">
<meta property="og:title" content="@yield('title', 'C-Net Web Services | Complete Website Solutions')">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="en_IN">
<meta property="og:image" content="https://web.mciedu.com/favicon.ico">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="@yield('title', 'C-Net Web Services | Complete Website Solutions')">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="https://web.mciedu.com/favicon.ico">

@if(!$privatePage && $host === 'web.mciedu.com')
<script type="application/ld+json">{!! json_encode($businessSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endif
