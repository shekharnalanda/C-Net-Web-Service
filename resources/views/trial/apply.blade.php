<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create Free Trial Website | C-Net Web Services</title>
<style>
*{box-sizing:border-box}
:root{--blue:#0756a3;--dark:#061d36;--cyan:#09a9d1;--light:#f2f7fc;--text:#293c4f}
body{margin:0;background:var(--light);color:var(--text);font-family:Arial,sans-serif;line-height:1.6}
header{background:linear-gradient(125deg,var(--dark),var(--blue),var(--cyan));color:#fff;text-align:center;padding:64px 20px}
header h1{font-size:clamp(34px,6vw,56px);line-height:1.1;margin:0 0 13px}
header p{font-size:18px;max-width:760px;margin:auto;color:#e6f5ff}
.container{width:min(980px,94%);margin:auto}
.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:13px;margin:-25px auto 30px;position:relative}
.step{background:#fff;border-radius:13px;padding:17px;text-align:center;box-shadow:0 8px 25px #0002;font-weight:700}
.step span{display:block;color:var(--blue);font-size:21px}
.box{background:#fff;margin:30px auto;padding:clamp(22px,5vw,42px);border-radius:20px;box-shadow:0 10px 35px #0001}
.section-title{border-bottom:1px solid #dde7f0;padding-bottom:9px;margin:30px 0 18px;color:var(--dark)}
label{display:block;font-weight:700;margin:15px 0 7px;color:var(--dark)}
.required{color:#c62828}
input,textarea,select{width:100%;padding:13px;border:1px solid #c6d4e1;border-radius:9px;font:inherit;background:#fff}
input:focus,textarea:focus,select:focus{outline:3px solid #09a9d122;border-color:var(--cyan)}
textarea{min-height:115px;resize:vertical}
.row{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.slug-wrap{display:flex;align-items:stretch}
.slug-wrap input{border-radius:9px 0 0 9px}
.suffix{display:flex;align-items:center;background:#eaf2fa;border:1px solid #c6d4e1;border-left:0;padding:0 11px;border-radius:0 9px 9px 0;font-weight:700;font-size:13px;white-space:nowrap}
.help{display:block;color:#64748b;font-size:13px;margin-top:5px}
.templates{display:grid;grid-template-columns:repeat(3,1fr);gap:15px}
.template{position:relative;border:2px solid #d9e4ee;border-radius:15px;overflow:hidden;cursor:pointer;background:#fff}
.template input{position:absolute;opacity:0}
.preview{height:115px;padding:18px;color:#fff}
.modern{background:linear-gradient(135deg,#061d36,#09a9d1)}
.professional{background:linear-gradient(135deg,#14213d,#c99700)}
.creative{background:linear-gradient(135deg,#6a11cb,#ff4e88)}
.preview strong{display:block;font-size:20px}.preview small{opacity:.9}
.template-name{display:block;padding:13px;text-align:center;font-weight:800}
.template:has(input:checked){border-color:var(--blue);box-shadow:0 0 0 4px #0756a315}
.consent{display:flex;gap:10px;align-items:flex-start;background:#f5f8fb;padding:15px;border-radius:10px;margin-top:22px}
.consent input{width:auto;margin-top:5px}
button{width:100%;background:linear-gradient(135deg,var(--blue),var(--cyan));color:#fff;border:0;padding:15px 23px;border-radius:9px;font-weight:800;font-size:17px;margin-top:22px;cursor:pointer}
.success{background:#e5f8ed;color:#176b38;padding:18px;border-radius:10px;margin-bottom:20px}
.success a{display:inline-block;background:#087d50;color:#fff;padding:11px 17px;border-radius:8px;text-decoration:none;font-weight:800;margin-top:10px}
.error{background:#feecef;color:#a31327;padding:14px;border-radius:8px;margin-bottom:18px}
.back{display:inline-block;margin:20px 0;color:var(--blue);text-decoration:none;font-weight:700}
@media(max-width:720px){.row,.templates,.steps{grid-template-columns:1fr}.suffix{font-size:11px}.steps{margin-top:20px}}
</style>
@include('partials.seo')
</head>
<body>

<header>
    <h1>Create Your Free Website</h1>
    <p>Enter your business details, choose a design and get your own website instantly on a C-Net subdomain—free for 7 days.</p>
</header>

<div class="container">
    <div class="steps">
        <div class="step"><span>1</span>Enter Details</div>
        <div class="step"><span>2</span>Choose Template</div>
        <div class="step"><span>3</span>Website Goes Live</div>
    </div>

    <div class="box">
        @if(session('success'))
            <div class="success">
                <strong>{{ session('success') }}</strong>

                @if(session('trial_url'))
                    <div>Your website URL:</div>
                    <a href="{{ session('trial_url') }}" target="_blank" rel="noopener">
                        Open {{ session('trial_url') }} ↗
                    </a>
                    <div style="margin-top:10px">
                        Valid until: {{ session('trial_expires_at') }}
                    </div>
                @endif
            </div>
        @endif

        @if($errors->any())
            <div class="error">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('trial.store') }}">
            @csrf

            <h2 class="section-title">Website Information</h2>

            <div class="row">
                <div>
                    <label>Website Name <span class="required">*</span></label>
                    <input name="website_name" required maxlength="150"
                           value="{{ old('website_name') }}"
                           placeholder="Example: Nalanda Coaching">
                </div>

                <div>
                    <label>Website Category <span class="required">*</span></label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        @foreach([
                            'Education & Coaching',
                            'School & Institute',
                            'Shop & Retail',
                            'Professional Services',
                            'Healthcare',
                            'Restaurant & Food',
                            'Hotel & Travel',
                            'Real Estate',
                            'NGO & Social Organization',
                            'Personal Portfolio',
                            'Other Business'
                        ] as $category)
                            <option value="{{ $category }}" @selected(old('category')===$category)>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label>Choose Website Address <span class="required">*</span></label>
            <div class="slug-wrap">
                <input id="desired_slug" name="desired_slug" required
                       minlength="3" maxlength="63"
                       pattern="[a-z0-9][a-z0-9-]{2,62}"
                       value="{{ old('desired_slug') }}"
                       placeholder="nalanda-coaching">
                <span class="suffix">.mciedu.com</span>
            </div>
            <small class="help">Only lowercase letters, numbers and hyphens. Minimum 3 characters.</small>

            <h2 class="section-title">Business and Authority Details</h2>

            <div class="row">
                <div>
                    <label>Business Name <span class="required">*</span></label>
                    <input name="business_name" required maxlength="150"
                           value="{{ old('business_name') }}">
                </div>
                <div>
                    <label>Authority/Owner Name <span class="required">*</span></label>
                    <input name="owner_name" required maxlength="120"
                           value="{{ old('owner_name') }}">
                </div>
            </div>

            <div class="row">
                <div>
                    <label>Authority Contact Number <span class="required">*</span></label>
                    <input type="tel" name="phone" required maxlength="20"
                           value="{{ old('phone') }}">
                </div>
                <div>
                    <label>Authority Email <span class="required">*</span></label>
                    <input type="email" name="email" required maxlength="150"
                           value="{{ old('email') }}">
                </div>
            </div>

            <div class="row">
                <div>
                    <label>WhatsApp Number <span class="required">*</span></label>
                    <input type="tel" name="whatsapp" required maxlength="20"
                           value="{{ old('whatsapp') }}">
                </div>
                <div>
                    <label>Website Colour</label>
                    <input type="color" name="theme_color"
                           value="{{ old('theme_color','#0756a3') }}">
                </div>
            </div>

            <label>Business Tagline</label>
            <input name="tagline" maxlength="250"
                   value="{{ old('tagline') }}"
                   placeholder="A short line describing your business">

            <label>About Business <span class="required">*</span></label>
            <textarea name="about_business" required maxlength="3000"
                      placeholder="Describe your business, experience and specialties">{{ old('about_business') }}</textarea>

            <label>Services or Products <span class="required">*</span></label>
            <textarea name="services_offered" required maxlength="3000"
                      placeholder="Enter one service or product per line">{{ old('services_offered') }}</textarea>

            <label>Complete Business Address <span class="required">*</span></label>
            <textarea name="address" required maxlength="1000">{{ old('address') }}</textarea>

            <h2 class="section-title">Choose Free Template</h2>

            <div class="templates">
                <label class="template">
                    <input type="radio" name="template_key" value="modern"
                           @checked(old('template_key','modern')==='modern') required>
                    <span class="preview modern">
                        <strong>Modern</strong>
                        <small>Bold and attractive</small>
                    </span>
                    <span class="template-name">Modern Business</span>
                </label>

                <label class="template">
                    <input type="radio" name="template_key" value="professional"
                           @checked(old('template_key')==='professional')>
                    <span class="preview professional">
                        <strong>Professional</strong>
                        <small>Formal and trusted</small>
                    </span>
                    <span class="template-name">Professional</span>
                </label>

                <label class="template">
                    <input type="radio" name="template_key" value="creative"
                           @checked(old('template_key')==='creative')>
                    <span class="preview creative">
                        <strong>Creative</strong>
                        <small>Colourful and friendly</small>
                    </span>
                    <span class="template-name">Creative</span>
                </label>
            </div>

            <label class="consent">
                <input type="checkbox" name="terms_accepted" value="1" required
                       @checked(old('terms_accepted'))>
                <span>
                    I confirm that the information is correct and I have authority to publish it.
                    I understand that the free website expires after 7 days unless upgraded.
                </span>
            </label>

            <button type="submit">Create My Free Website Now</button>
        </form>

        <a class="back" href="/">← Back to Home</a>
    </div>
</div>

<script>
const slugInput = document.getElementById('desired_slug');
const websiteName = document.querySelector('[name="website_name"]');

function makeSlug(value) {
    return value.toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 63);
}

let slugEdited = slugInput.value.length > 0;

slugInput.addEventListener('input', function () {
    slugEdited = this.value.length > 0;
    this.value = makeSlug(this.value);
});

websiteName.addEventListener('input', function () {
    if (!slugEdited) {
        slugInput.value = makeSlug(this.value);
    }
});
</script>

@include('partials.contact-buttons')
</body>
</html>
