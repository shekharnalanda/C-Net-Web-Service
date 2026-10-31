@extends('layouts.public')
@section('title', 'अपनी डिजिटल जरूरत बताएँ | C-Net Web Services')
@section('description', 'Website, Digital Marketing, CRM, School ERP, Software और Business Digitization के लिए C-Net Web Services को अपनी जरूरत भेजें।')
@section('content')
<section class="enquiry-intro soft"><div class="container enquiry-layout"><div><span class="section-label">शुरुआत एक बातचीत से</span><h1>अपनी जरूरत बताएँ</h1><p>कौन-सा काम व्यवस्थित करना है, अभी क्या व्यवस्था है और आपको क्या चाहिए—Form में लिखें। हमारी टीम संपर्क करके जरूरी जानकारी और अगला कदम स्पष्ट करेगी।</p><ul><li>नई वेबसाइट या पुरानी साइट में बदलाव</li><li>Digital Marketing और ग्राहक Enquiries</li><li>CRM, School ERP और Software</li><li>पूरी संस्था का Digitization या Support</li></ul><p class="scope-note">प्रस्ताव में काम का दायरा, समय और लागत तय होंगे।</p><a class="text-link" href="{{ route('home') }}">← होम पेज पर लौटें</a></div><div class="enquiry-form">
@if(session('success'))<div class="form-success" role="status">आपकी Enquiry जमा हो गई है। धन्यवाद! हमारी टीम आपसे संपर्क करेगी।</div>@endif
@if($errors->any())<div class="form-error" role="alert">कृपया नीचे दिए गए विवरण जाँचकर फिर भेजें।</div>@endif
<form method="POST" action="{{ route('enquiry.store') }}">@csrf
<label for="name">आपका नाम <span>(जरूरी)</span></label><input id="name" name="name" maxlength="100" autocomplete="name" value="{{ old('name') }}" required>@error('name')<div class="field-error">{{ $message }}</div>@enderror
<label for="phone">मोबाइल नंबर <span>(जरूरी)</span></label><input id="phone" type="tel" name="phone" maxlength="20" autocomplete="tel" value="{{ old('phone') }}" required>@error('phone')<div class="field-error">{{ $message }}</div>@enderror
<label for="email">Email Address <span>(OTP सत्यापन जरूरी)</span></label><input id="email" type="email" name="email" maxlength="150" autocomplete="email" value="{{ old('email') }}" required>

<div class="email-otp-step">
<button type="submit" formmethod="POST" formaction="{{ route('enquiry.email-otp.send') }}" formnovalidate>Send email OTP</button>
<label for="enquiry-otp">Email OTP</label>
<input id="enquiry-otp" type="text" name="otp" inputmode="numeric" autocomplete="one-time-code" maxlength="6" value="{{ old('otp') }}">
<button type="submit" formmethod="POST" formaction="{{ route('enquiry.email-otp.verify') }}" formnovalidate>Verify OTP</button>
@if(session('otp_sent'))<p role="status">{{ session('otp_sent') }}</p>@endif
@if(session('otp_verified'))<p role="status">{{ session('otp_verified') }}</p>@endif
</div>@error('email')<div class="field-error">{{ $message }}</div>@enderror
<label for="service">कौन-सी सेवा चाहिए? <span>(जरूरी)</span></label><select id="service" name="service" required><option value="">सेवा चुनें</option>@foreach($services as $service)<option value="{{ $service->title }}" @selected(old('service', $selectedService?->title) === $service->title)>{{ config('service-guide.services.'.$service->slug.'.label', $service->title) }}</option>@endforeach<option value="Trial Website" @selected(old('service') === 'Trial Website')>One-page Trial Website</option><option value="Other Requirement" @selected(old('service') === 'Other Requirement')>अन्य जरूरत / पहले सलाह चाहिए</option></select>@error('service')<div class="field-error">{{ $message }}</div>@enderror
<label for="message">अपनी जरूरत का विवरण <span>(वैकल्पिक)</span></label><textarea id="message" name="message" maxlength="2000" rows="6" placeholder="संस्था का नाम, काम, जरूरी सुविधाएँ, Users/Branches और उपलब्ध समय या Budget बताएँ।">{{ old('message') }}</textarea>@error('message')<div class="field-error">{{ $message }}</div>@enderror
<button class="btn btn-primary" type="submit">Enquiry भेजें</button><p class="form-note">आपके दिए संपर्क पर इसी जरूरत के बारे में बात की जाएगी।</p>
</form></div></div></section>
@endsection
