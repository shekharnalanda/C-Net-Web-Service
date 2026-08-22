<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $service ? 'Edit' : 'Add' }} Service | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
header{background:#07223d;color:#fff;padding:18px}.container{width:min(750px,92%);margin:auto}
header .container{display:flex;justify-content:space-between;align-items:center}header a{color:#fff;text-decoration:none}
.box{background:#fff;margin-top:32px;padding:34px;border-radius:16px;box-shadow:0 8px 30px #00000010}
h1{color:#07223d;margin-top:0}label{display:block;font-weight:700;margin:16px 0 7px}
input,textarea{width:100%;padding:12px;border:1px solid #c6d3e0;border-radius:8px;font:inherit}
textarea{min-height:130px;resize:vertical}.row{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.check{display:flex;align-items:center;gap:9px}.check input{width:auto}.error{background:#feecef;color:#a31327;padding:13px;border-radius:8px}
button{background:linear-gradient(135deg,#0756a3,#09a9d1);color:#fff;border:0;padding:13px 22px;border-radius:8px;font-weight:800;margin-top:21px;cursor:pointer}
@media(max-width:600px){.row{grid-template-columns:1fr}}
</style>
</head>
<body>
<header>
<div class="container">
    <strong>C-Net Web Services</strong>
    <a href="{{ route('admin.services.index') }}">← Services</a>
</div>
</header>

<div class="container">
<div class="box">
    <h1>{{ $service ? 'Edit Service' : 'Add New Service' }}</h1>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ $service ? route('admin.services.update', $service->id) : route('admin.services.store') }}">
        @csrf
        @if($service) @method('PUT') @endif

        <label>Service Title</label>
        <input name="title" required value="{{ old('title', $service->title ?? '') }}">

        <div class="row">
            <div>
                <label>URL Slug</label>
                <input name="slug" value="{{ old('slug', $service->slug ?? '') }}" placeholder="Auto-generated if blank">
            </div>
            <div>
                <label>Icon/Emoji</label>
                <input name="icon" value="{{ old('icon', $service->icon ?? '🌐') }}">
            </div>
        </div>

        <label>Short Description</label>
        <textarea name="short_description" required>{{ old('short_description', $service->short_description ?? '') }}</textarea>

        <label>Complete Service Details</label>
        <textarea name="description" required>{{ old('description', $service->description ?? '') }}</textarea>

        <label>Display Order</label>
        <input type="number" min="0" max="999" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}">

        <label class="check">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active ?? true))>
            Publish this service
        </label>

        <button type="submit">{{ $service ? 'Update Service' : 'Create Service' }}</button>
    </form>
</div>
</div>
</body>
</html>
