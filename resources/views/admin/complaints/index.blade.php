@extends('layouts.app')
@section('title', 'Complaints')

@section('content')
<div class="page-head"><div><h1>Overpricing complaints</h1><p>Verify each complaint against the posted price and follow it until it is resolved.</p></div></div>

<div class="tabs">
    <a href="{{ route('admin.complaints.index') }}" class="{{ empty($filters['status']) ? 'active' : '' }}">All ({{ $counts->sum() }})</a>
    @foreach (\App\Models\Complaint::STATUSES as $key => $label)
        <a href="{{ route('admin.complaints.index', ['status' => $key]) }}" class="{{ ($filters['status'] ?? '') === $key ? 'active' : '' }}">{{ $label }} ({{ $counts[$key] ?? 0 }})</a>
    @endforeach
</div>

<form method="GET" action="{{ route('admin.complaints.index') }}" class="filters">
    @if (! empty($filters['status'])) <input type="hidden" name="status" value="{{ $filters['status'] }}"> @endif
    <div class="field"><label for="q">Search</label><input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Reference, stall or shopper"></div>
    <div class="field"><label for="from">From</label><input type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}"></div>
    <div class="field"><label for="to">To</label><input type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}"></div>
    <div class="actions"><button class="btn primary" type="submit">Filter</button><a class="btn ghost" href="{{ route('admin.complaints.index') }}">Reset</a></div>
</form>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Filed</th><th>Stall</th><th>Commodity</th><th class="num">Posted → Reported</th><th>Shopper</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($complaints as $c)
                <tr>
                    <td class="nowrap">{{ $c->reference_no }}</td>
                    <td class="nowrap">{{ $c->created_at->format('M j, Y') }}</td>
                    <td>{{ $c->stall->stall_number }} · {{ $c->stall->name }} @if ($c->stall->is_flagged)<span class="badge flag">Flagged</span>@endif</td>
                    <td>{{ $c->commodity->name }}</td>
                    <td class="num">₱{{ number_format($c->posted_price, 2) }} → <span class="up">₱{{ number_format($c->reported_price, 2) }}</span></td>
                    <td>{{ $c->shopper->name }}</td>
                    <td><x-status-badge :status="$c->status" /></td>
                    <td class="right"><a class="btn small primary" href="{{ route('admin.complaints.show', $c) }}">Review</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">No complaints match your filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $complaints->links() }}
</div>
@endsection
