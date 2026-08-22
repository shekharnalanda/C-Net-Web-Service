<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $plan ? 'Edit' : 'Add' }} Plan | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}.container{width:min(750px,92%);margin:auto}
header{background:#07223d;color:#fff;padding:18px}header .container{display:flex;justify-content:space-between}header a{color:#fff;text-decoration:none}
.box{background:#fff;margin-top:32px;padding:34px;border-radius:16px;box-shadow:0 8px 30px #0001}h1{color:#07223d}
label{display:block;font-weight:700;margin:15px 0 7px}input,textarea{width:100%;padding:12px;border:1px solid #c6d3e0;border-radius:8px;font:inherit}textarea{min-height:130px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:18px}.check{display:flex;align-items:center;gap:8px}.check input{width:auto}
button{background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;border:0;padding:13px 22px;border-radius:8px;font-weight:800;margin-top:20px}
.error{background:#feecef;color:#a31327;padding:12px;border-radius:8px}@media(max-width:600px){.row{grid-template-columns:1fr}}
</style>
    @include('partials.seo')
</head>
<body>
<header><div class="container"><strong>C-Net Web Services</strong><a href="{{ route('admin.plans.index') }}">← Plans</a></div></header>
<div class="container"><div class="box">
<h1>{{ $plan ? 'Edit Plan' : 'Add New Plan' }}</h1>
@if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ $plan ? route('admin.plans.update',$plan->id) : route('admin.plans.store') }}">
@csrf @if($plan) @method('PUT') @endif
<label>Plan Title</label><input name="title" required value="{{ old('title',$plan->title ?? '') }}">
<div class="row"><div><label>URL Slug</label><input name="slug" value="{{ old('slug',$plan->slug ?? '') }}"></div>
<div><label>Service Type</label><input name="service_type" required value="{{ old('service_type',$plan->service_type ?? '') }}"></div></div>
<div class="row"><div><label>Price Label</label><input name="price_label" required value="{{ old('price_label',$plan->price_label ?? 'Contact for Price') }}"></div>
<div><label>Duration</label><input name="duration" value="{{ old('duration',$plan->duration ?? '') }}"></div></div>
<label>Features — one feature per line</label><textarea name="features" required>{{ old('features',$plan->features ?? '') }}</textarea>
<label>Display Order</label><input type="number" name="sort_order" min="0" max="999" value="{{ old('sort_order',$plan->sort_order ?? 0) }}">
<label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$plan->is_featured ?? false))> Recommended plan</label>
<label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$plan->is_active ?? true))> Publish plan</label>
<button>Save Plan</button>
</form>
</div></div>
</body>
</html>
