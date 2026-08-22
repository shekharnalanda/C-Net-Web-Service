<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Services Management | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
header{background:#07223d;color:#fff;padding:17px 0}.container{width:min(1150px,94%);margin:auto}
.top{display:flex;justify-content:space-between;align-items:center;gap:15px}
.actions{display:flex;gap:10px}.btn{display:inline-block;padding:10px 15px;border-radius:7px;text-decoration:none;font-weight:700;border:0;cursor:pointer}
.primary{background:#08a0ca;color:#fff}.secondary{border:1px solid #ffffff66;color:#fff}
main{padding:30px 0}.notice{background:#e7f8ed;color:#176b38;padding:13px;border-radius:8px;margin-bottom:18px}
.table-box{background:#fff;border-radius:14px;overflow:auto;box-shadow:0 5px 20px #0000000b}
table{width:100%;border-collapse:collapse;min-width:850px}th{background:#eaf2fa;text-align:left;color:#07223d}
th,td{padding:14px;border-bottom:1px solid #e6edf4}.icon{font-size:25px}
.badge{padding:5px 9px;border-radius:20px;font-size:12px;font-weight:700}.active{background:#e2f7e9;color:#16713a}.hidden{background:#fce7ea;color:#a91c31}
.edit{background:#0756a3;color:#fff}.delete{background:#c52a3c;color:#fff}
.inline{display:inline}.empty{text-align:center;padding:35px;color:#64748b}
</style>
</head>
<body>
<header>
<div class="container top">
    <strong>Services Management</strong>
    <div class="actions">
        <a class="btn secondary" href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a class="btn primary" href="{{ route('admin.services.create') }}">+ Add Service</a>
    </div>
</div>
</header>

<main class="container">
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <div class="table-box">
        <table>
            <thead>
            <tr>
                <th>Order</th><th>Icon</th><th>Service</th><th>URL Slug</th>
                <th>Status</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($services as $service)
                <tr>
                    <td>{{ $service->sort_order }}</td>
                    <td class="icon">{{ $service->icon }}</td>
                    <td>
                        <strong>{{ $service->title }}</strong><br>
                        <small>{{ $service->short_description }}</small>
                    </td>
                    <td>/services/{{ $service->slug }}</td>
                    <td>
                        <span class="badge {{ $service->is_active ? 'active' : 'hidden' }}">
                            {{ $service->is_active ? 'Published' : 'Hidden' }}
                        </span>
                    </td>
                    <td>
                        <a class="btn edit" href="{{ route('admin.services.edit', $service->id) }}">Edit</a>
                        <form class="inline" method="POST" action="{{ route('admin.services.destroy', $service->id) }}" onsubmit="return confirm('Delete this service?')">
                            @csrf @method('DELETE')
                            <button class="btn delete">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No services available.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
