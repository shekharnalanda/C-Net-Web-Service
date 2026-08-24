<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Enquiry Dashboard | C-Net Web Services</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#26384a;font-family:Arial,sans-serif}
header{background:#07223d;color:#fff;padding:16px 0}.container{width:min(1200px,94%);margin:auto}
.top{display:flex;justify-content:space-between;align-items:center;gap:20px}.admin-brand{display:flex;align-items:center;gap:11px}.admin-brand img{width:48px;height:48px;object-fit:contain;border-radius:12px;flex:0 0 48px}.top h1{font-size:22px}
.actions{display:flex;gap:12px;align-items:center}.actions a,.actions button{color:#fff;background:#ffffff18;border:1px solid #ffffff45;padding:9px 13px;border-radius:7px;text-decoration:none;cursor:pointer}
main{padding:30px 0}.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:17px;margin-bottom:25px}
.card{background:#fff;border-radius:14px;padding:20px;box-shadow:0 5px 20px #0000000b}.card strong{display:block;font-size:30px;color:#0756a3}.card span{color:#64748b}
.notice{background:#e7f7ed;color:#176b38;padding:13px;border-radius:8px;margin-bottom:18px}
.table-box{background:#fff;border-radius:14px;overflow:auto;box-shadow:0 5px 20px #0000000b}
table{width:100%;border-collapse:collapse;min-width:1050px}th{background:#eaf2fa;color:#07223d;text-align:left}th,td{padding:13px;border-bottom:1px solid #e7edf4;vertical-align:top}
.small{font-size:13px;color:#64748b}.message{max-width:280px;white-space:pre-wrap}
select{padding:8px;border:1px solid #bac8d8;border-radius:6px}.save{background:#0756a3;color:#fff;border:0;padding:8px 11px;border-radius:6px;cursor:pointer}
.delete{background:#c92b3d;color:#fff;border:0;padding:8px 11px;border-radius:6px;cursor:pointer}
.empty{text-align:center;padding:35px;color:#64748b}
@media(max-width:750px){.cards{grid-template-columns:1fr 1fr}.top{align-items:flex-start;flex-direction:column}}
</style>
    @include('partials.seo')
</head>
<body>
<header>
<div class="container top">
    <div class="admin-brand"><img src="/images/cnet-favicon.png" width="48" height="48" alt="C-Net Web Services Logo"><h1>C-Net Web Services — Admin Dashboard</h1></div>
    <div class="actions">
        <a href="{{ route('admin.projects.index') }}">Live Website Projects</a><a href="{{ route('admin.services.index') }}">Manage Services</a><a href="{{ route('admin.plans.index') }}">Manage Plans</a><a href="{{ route('admin.trials.index') }}">Trial Applications</a><a href="{{ route('admin.password.edit') }}">Change Password</a><a href="/" target="_blank">View Website</a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</div>
</header>

<main class="container">
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <div class="cards">
        <div class="card"><strong>{{ $counts['total'] }}</strong><span>Total Enquiries</span></div>
        <div class="card"><strong>{{ $counts['new'] }}</strong><span>New</span></div>
        <div class="card"><strong>{{ $counts['contacted'] }}</strong><span>Contacted</span></div>
        <div class="card"><strong>{{ $counts['completed'] }}</strong><span>Completed</span></div>
    </div>

    <div class="table-box">
        <table>
            <thead>
            <tr>
                <th>Date</th><th>Customer</th><th>Contact</th><th>Service</th>
                <th>Requirement</th><th>Status</th><th>Delete</th>
            </tr>
            </thead>
            <tbody>
            @forelse($enquiries as $enquiry)
                <tr>
                    <td class="small">{{ \Carbon\Carbon::parse($enquiry->created_at)->format('d M Y, h:i A') }}</td>
                    <td><strong>{{ $enquiry->name }}</strong></td>
                    <td>{{ $enquiry->phone }}<br><span class="small">{{ $enquiry->email }}</span></td>
                    <td>{{ $enquiry->service }}</td>
                    <td class="message">{{ $enquiry->message ?: '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.enquiries.status', $enquiry->id) }}">
                            @csrf @method('PATCH')
                            <select name="status">
                                @foreach(['new','contacted','in_progress','completed','closed'] as $status)
                                    <option value="{{ $status }}" @selected($enquiry->status === $status)>
                                        {{ ucwords(str_replace('_',' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="save">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.enquiries.delete', $enquiry->id) }}" onsubmit="return confirm('Delete this enquiry permanently?')">
                            @csrf @method('DELETE')
                            <button class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No enquiries received yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
