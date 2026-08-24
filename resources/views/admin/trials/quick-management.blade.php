<section class="trial-quick-management" style="width:min(1200px,94%);margin:30px auto">
<div style="background:#fff;padding:22px;border-radius:14px;box-shadow:0 5px 20px #00000012;overflow:auto">
<h2 style="color:#07223d">Quick Trial Website Management</h2>

<p>
किसी trial website को खोलें, उसकी जानकारी edit करें,
तुरंत suspend करें अथवा permanently delete करें।
</p>

<table style="width:100%;border-collapse:collapse;min-width:900px">
<thead>
<tr style="background:#eaf2fa">
<th style="padding:12px;text-align:left">Website</th>
<th style="padding:12px;text-align:left">Client</th>
<th style="padding:12px;text-align:left">Status</th>
<th style="padding:12px;text-align:left">Expiry</th>
<th style="padding:12px;text-align:left">Actions</th>
</tr>
</thead>

<tbody>
@forelse($applications as $application)
<tr style="border-bottom:1px solid #e5ebf1">
<td style="padding:12px">
<strong>
{{ $application->website_name ?? $application->business_name }}
</strong>
<br>
<a href="{{ $application->trial_url }}" target="_blank">
{{ $application->trial_url }}
</a>
</td>

<td style="padding:12px">
{{ $application->owner_name }}
<br>
<small>{{ $application->phone }}</small>
</td>

<td style="padding:12px">
<strong>{{ ucfirst($application->status) }}</strong>
</td>

<td style="padding:12px">
{{ $application->expires_at ?: 'No expiry' }}
</td>

<td style="padding:12px">
<div style="display:flex;gap:7px;flex-wrap:wrap">

<a
    href="{{ $application->trial_url }}"
    target="_blank"
    style="background:#078447;color:#fff;padding:8px 11px;border-radius:6px;text-decoration:none"
>
Open
</a>

<a
    href="{{ route('admin.trial-websites.edit',$application->id) }}"
    style="background:#0756a3;color:#fff;padding:8px 11px;border-radius:6px;text-decoration:none"
>
Edit
</a>

@if($application->status === 'suspended')
<form
    method="POST"
    action="{{ route('admin.trial-websites.restore',$application->id) }}"
>
@csrf
@method('PATCH')
<button
    type="submit"
    style="background:#925f05;color:#fff;border:0;padding:8px 11px;border-radius:6px;cursor:pointer"
>
Restore
</button>
</form>
@else
<form
    method="POST"
    action="{{ route('admin.trial-websites.suspend',$application->id) }}"
>
@csrf
@method('PATCH')
<button
    type="submit"
    onclick="return confirm('इस trial website को तुरंत suspend करना है?')"
    style="background:#925f05;color:#fff;border:0;padding:8px 11px;border-radius:6px;cursor:pointer"
>
Suspend
</button>
</form>
@endif

<form
    method="POST"
    action="{{ route('admin.trial-websites.destroy',$application->id) }}"
    onsubmit="return confirm('चेतावनी: यह trial website और client record permanently delete हो जाएगा। इसे वापस नहीं लाया जा सकेगा। क्या आप निश्चित हैं?')"
>
@csrf
@method('DELETE')
<button
    type="submit"
    style="background:#c3263d;color:#fff;border:0;padding:8px 11px;border-radius:6px;cursor:pointer"
>
Delete
</button>
</form>

</div>
</td>
</tr>
@empty
<tr>
<td colspan="5" style="padding:25px;text-align:center;color:#64748b">
कोई trial website उपलब्ध नहीं है।
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</section>
