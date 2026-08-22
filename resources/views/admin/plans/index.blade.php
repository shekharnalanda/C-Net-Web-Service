<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Plans Management | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
header{background:#07223d;color:#fff;padding:17px 0}.container{width:min(1100px,94%);margin:auto}
.top{display:flex;justify-content:space-between;align-items:center}.btn{display:inline-block;padding:9px 14px;border:0;border-radius:7px;text-decoration:none;font-weight:700;cursor:pointer}
.back{color:#fff;border:1px solid #ffffff55}.add{background:#08a2cc;color:#fff}.edit{background:#0756a3;color:#fff}.delete{background:#c52a3c;color:#fff}
main{padding:30px 0}.notice{background:#e6f8ed;color:#176b38;padding:13px;border-radius:8px;margin-bottom:16px}
table{width:100%;border-collapse:collapse;min-width:800px;background:#fff}th,td{padding:13px;border-bottom:1px solid #e5edf4;text-align:left}th{background:#eaf2fa}
.table{overflow:auto;border-radius:13px;box-shadow:0 5px 20px #0000000b}.badge{padding:5px 9px;border-radius:20px;font-size:12px}.yes{background:#e2f7e9;color:#16713a}.no{background:#fce7ea;color:#a91c31}
</style>
</head>
<body>
<header><div class="container top"><strong>Plans Management</strong><div><a class="btn back" href="{{ route('admin.dashboard') }}">Dashboard</a> <a class="btn add" href="{{ route('admin.plans.create') }}">+ Add Plan</a></div></div></header>
<main class="container">
@if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
<div class="table"><table>
<thead><tr><th>Order</th><th>Plan</th><th>Type</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@foreach($plans as $plan)
<tr>
<td>{{ $plan->sort_order }}</td><td><strong>{{ $plan->title }}</strong></td><td>{{ $plan->service_type }}</td><td>{{ $plan->price_label }}</td>
<td><span class="badge {{ $plan->is_active ? 'yes' : 'no' }}">{{ $plan->is_active ? 'Published' : 'Hidden' }}</span></td>
<td><a class="btn edit" href="{{ route('admin.plans.edit',$plan->id) }}">Edit</a>
<form style="display:inline" method="POST" action="{{ route('admin.plans.destroy',$plan->id) }}" onsubmit="return confirm('Delete this plan?')">@csrf @method('DELETE')<button class="btn delete">Delete</button></form></td>
</tr>
@endforeach
</tbody>
</table></div>
</main>
</body>
</html>
