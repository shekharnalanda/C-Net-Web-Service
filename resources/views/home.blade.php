<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="C-Net Web Services - Domain, Hosting, Website Design, SEO and Digital Promotion services.">
    <title>C-Net Web Services | Complete Website Solutions</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--blue:#0756a3;--dark:#07223d;--cyan:#09a9d1;--light:#f4f8fc;--white:#fff;--text:#334155}
        body{font-family:Arial,sans-serif;color:var(--text);line-height:1.6;background:#fff}
        html{scroll-behavior:smooth}
        .container{width:min(1140px,92%);margin:auto}
        header{background:#fff;box-shadow:0 2px 14px #00000014;position:sticky;top:0;z-index:50}
        nav{height:76px;display:flex;align-items:center;justify-content:space-between}
        .brand{display:flex;align-items:center;gap:11px;text-decoration:none;color:var(--dark);font-size:21px;font-weight:800}
        .brand-mark{width:46px;height:46px;border-radius:13px;background:linear-gradient(135deg,var(--blue),var(--cyan));display:grid;place-items:center;color:#fff;font-size:27px;font-weight:900}
        .nav-links{display:flex;align-items:center;gap:25px}
        .nav-links a{text-decoration:none;color:var(--dark);font-weight:600}
        .nav-links a:hover{color:var(--blue)}
        .btn{display:inline-block;background:linear-gradient(135deg,var(--blue),var(--cyan));color:#fff!important;text-decoration:none;padding:12px 22px;border-radius:9px;font-weight:700;border:0}
        .menu{display:none;background:none;border:0;font-size:27px}
        .hero{background:linear-gradient(120deg,#061d36,#0756a3 68%,#09a9d1);color:#fff;padding:94px 0;overflow:hidden}
        .hero-grid{display:grid;grid-template-columns:1.2fr .8fr;gap:50px;align-items:center}
        .tag{display:inline-block;background:#ffffff20;border:1px solid #ffffff48;padding:6px 14px;border-radius:30px;margin-bottom:18px}
        h1{font-size:clamp(39px,6vw,66px);line-height:1.1;margin-bottom:20px}
        .hero p{font-size:19px;color:#e2efff;max-width:650px;margin-bottom:29px}
        .hero-actions{display:flex;gap:14px;flex-wrap:wrap}
        .btn-white{background:#fff;color:var(--blue)!important}
        .btn-outline{background:transparent;border:1px solid #ffffffaa}
        .hero-card{background:#ffffff12;border:1px solid #ffffff3d;border-radius:24px;padding:30px;backdrop-filter:blur(8px)}
        .hero-card h3{font-size:25px;margin-bottom:15px}
        .hero-card li{list-style:none;padding:10px 0;border-bottom:1px solid #ffffff20}
        section{padding:78px 0}
        .section-head{text-align:center;max-width:720px;margin:0 auto 42px}
        .section-head span{color:var(--blue);font-weight:800;text-transform:uppercase;letter-spacing:1px}
        .section-head h2{font-size:clamp(30px,4vw,43px);color:var(--dark);margin:8px 0}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
        .card{background:#fff;border:1px solid #e3ebf3;border-radius:17px;padding:27px;transition:.25s}
        .card:hover{transform:translateY(-6px);box-shadow:0 16px 35px #0756a31b;border-color:#89c6e5}
        .icon{width:52px;height:52px;border-radius:13px;background:#eaf6ff;display:grid;place-items:center;font-size:25px;margin-bottom:18px}
        .card h3{color:var(--dark);font-size:21px;margin-bottom:8px}
        .card h3 a{color:var(--dark);text-decoration:none}.card h3 a:hover{color:var(--blue)}
        .services{background:var(--light)}
        .trial{background:var(--dark);color:#fff}
        .trial-wrap{display:grid;grid-template-columns:1fr auto;gap:40px;align-items:center}
        .trial h2{font-size:clamp(30px,4vw,45px);margin-bottom:12px}
        .trial p{color:#c9d9e8;max-width:750px;font-size:18px}
        .steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
        .step{text-align:center}
        .num{width:50px;height:50px;margin:0 auto 15px;border-radius:50%;background:var(--blue);color:#fff;display:grid;place-items:center;font-weight:900;font-size:20px}
        .contact{background:var(--light)}
        .contact-box{background:#fff;border-radius:22px;padding:42px;box-shadow:0 14px 40px #0000000d;display:grid;grid-template-columns:1fr 1fr;gap:45px}
        .contact h2{font-size:36px;color:var(--dark);margin-bottom:14px}
        .contact-list div{padding:10px 0}
        footer{background:#04182b;color:#b9cada;padding:28px 0;text-align:center}
        footer strong{color:#fff}
        @media(max-width:850px){
            .hero-grid,.contact-box,.trial-wrap{grid-template-columns:1fr}
            .grid{grid-template-columns:1fr 1fr}
            .steps{grid-template-columns:1fr 1fr}
            .menu{display:block}
            .nav-links{display:none;position:absolute;top:76px;left:0;right:0;background:#fff;padding:20px;flex-direction:column;box-shadow:0 10px 20px #0002}
            .nav-links.open{display:flex}
        }
        @media(max-width:560px){
            .grid,.steps{grid-template-columns:1fr}
            .hero{padding:65px 0}
            .contact-box{padding:26px}
        }
    </style>
</head>
<body>
<header>
    <nav class="container">
        <a href="/" class="brand">
            <span class="brand-mark">C</span>
            <span>C-Net Web Services</span>
        </a>
        <button class="menu" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#services">Services</a>
            <a href="#trial">Free Trial</a><a href="/plans">Plans</a>
            <a href="#process">Process</a>
            <a href="#contact" class="btn">Contact Us</a>
        </div>
    </nav>
</header>

<main>
<section class="hero" id="home">
    <div class="container hero-grid">
        <div>
            <span class="tag">Your Complete Digital Partner</span>
            <h1>We Build Websites That Grow Your Business</h1>
            <p>Domain, hosting, professional website design, SEO and digital promotion—everything your business needs at one trusted place.</p>
            <div class="hero-actions">
                <a href="#contact" class="btn btn-white">Start Your Website</a>
                <a href="#trial" class="btn btn-outline">Try One-Page Website</a>
            </div>
        </div>
        <div class="hero-card">
            <h3>Complete Website Solutions</h3>
            <ul>
                <li>✓ Domain Registration & Management</li>
                <li>✓ Fast and Secure Web Hosting</li>
                <li>✓ Static & Dynamic Websites</li>
                <li>✓ SEO and Digital Promotion</li>
                <li>✓ Website Maintenance & Support</li>
            </ul>
        </div>
    </div>
</section>

<section class="services" id="services">
    <div class="container">
        <div class="section-head">
            <span>Our Services</span>
            <h2>Everything You Need to Succeed Online</h2>
            <p>Customized and affordable digital solutions for businesses, institutions and professionals.</p>
        </div>
        <div class="grid">
            <div class="card"><div class="icon">🌐</div><h3><a href="/services/domain-services">Domain Services</a></h3><p>Domain search, registration, renewal and complete domain management assistance.</p></div>
            <div class="card"><div class="icon">☁️</div><h3><a href="/services/web-hosting">Web Hosting</a></h3><p>Reliable, secure and high-performance hosting plans with technical support.</p></div>
            <div class="card"><div class="icon">🎨</div><h3><a href="/services/website-designing">Website Designing</a></h3><p>Modern, responsive and user-friendly websites customized for your brand.</p></div>
            <div class="card"><div class="icon">📄</div><h3><a href="/services/static-websites">Static Websites</a></h3><p>Fast and affordable websites for small businesses, portfolios and information pages.</p></div>
            <div class="card"><div class="icon">⚙️</div><h3><a href="/services/dynamic-websites">Dynamic Websites</a></h3><p>Database-driven websites with admin panel, forms and advanced business features.</p></div>
            <div class="card"><div class="icon">📈</div><h3>SEO & Promotion</h3><p>Search optimization and digital promotion to improve visibility and customer reach.</p></div>
        </div>
    </div>
</section>

<section class="trial" id="trial">
    <div class="container trial-wrap">
        <div>
            <h2>Launch Your Trial Website</h2>
            <p>Create a professional one-page website on our subdomain and use it free for 7–10 days. Upgrade to a complete plan when you are satisfied.</p>
        </div>
        <a href="/trial/apply" class="btn btn-white">Request Free Trial</a>
    </div>
</section>

<section id="process">
    <div class="container">
        <div class="section-head">
            <span>How It Works</span>
            <h2>From Idea to Live Website</h2>
        </div>
        <div class="steps">
            <div class="step"><div class="num">1</div><h3>Tell Us Your Need</h3><p>Share your business and website requirements.</p></div>
            <div class="step"><div class="num">2</div><h3>Select a Plan</h3><p>Choose the right solution for your budget.</p></div>
            <div class="step"><div class="num">3</div><h3>Design & Development</h3><p>We create and customize your website.</p></div>
            <div class="step"><div class="num">4</div><h3>Go Live</h3><p>Your website is tested and published.</p></div>
        </div>
    </div>
</section>

<section class="contact" id="contact">
    <div class="container contact-box">
        <div>
            <span style="color:#0756a3;font-weight:800">LET'S WORK TOGETHER</span>
            <h2>Ready to Build Your Website?</h2>
            <p>Contact C-Net Web Services for a customized website solution designed around your goals and budget.</p>
        </div>
        <div class="contact-list">
            <div><strong>Business:</strong> C-Net Web Services</div>
            <div><strong>Organization:</strong> MCI Educational Group</div>
            <div><strong>Location:</strong> Bihar Sharif, Nalanda, Bihar</div>
            <div><strong>Website:</strong> web.mciedu.com</div>
            <a href="/enquiry" class="btn" style="margin-top:15px">Send Enquiry</a>
        </div>
    </div>
</section>
</main>

<footer>
    <div class="container">
        © {{ date('Y') }} <strong>C-Net Web Services</strong>. All Rights Reserved.
    </div>
</footer>
</body>
</html>
