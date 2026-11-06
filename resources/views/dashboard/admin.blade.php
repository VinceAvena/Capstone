@extends('layouts.app')
@section('title', 'Administrator Dashboard')

@section('content')
<div class="page-head">
    <div><h1>Market Administrator Dashboard</h1><p>Welcome back, {{ auth()->user()->name }}. Here is what needs attention today.</p></div>
    <a class="btn primary" href="{{ route('admin.reports.index') }}">Generate reports</a>
</div>

<h2>Complaints</h2>
<div class="grid cols-4 mb">
    <a class="card stat {{ $counts['pending'] ? 'warn' : '' }}" href="{{ route('admin.complaints.index', ['status' => 'pending']) }}"><div class="num">{{ $counts['pending'] }}</div><div class="lbl">Pending: waiting for review</div></a>
    <a class="card stat" href="{{ route('admin.complaints.index', ['status' => 'under_review']) }}"><div class="num">{{ $counts['under_review'] }}</div><div class="lbl">Under review</div></a>
    <a class="card stat {{ $counts['confirmed'] ? 'bad' : '' }}" href="{{ route('admin.complaints.index', ['status' => 'confirmed']) }}"><div class="num">{{ $counts['confirmed'] }}</div><div class="lbl">Confirmed: not yet resolved</div></a>
    <a class="card stat" href="{{ route('admin.complaints.index', ['status' => 'resolved']) }}"><div class="num">{{ $counts['resolved_month'] }}</div><div class="lbl">Resolved this month</div></a>
</div>

<h2>Stalls and prices</h2>
<div class="grid cols-4 mb">
    <a class="card stat {{ $counts['flagged'] ? 'bad' : '' }}" href="{{ route('admin.flagged.index') }}"><div class="num">{{ $counts['flagged'] }}</div><div class="lbl">Flagged repeat violators</div></a>
    <a class="card stat {{ $counts['stalls_pending'] ? 'warn' : '' }}" href="{{ route('admin.stalls.index', ['status' => 'pending']) }}"><div class="num">{{ $counts['stalls_pending'] }}</div><div class="lbl">Stalls waiting for approval</div></a>
    <a class="card stat {{ $counts['not_posted'] ? 'warn' : '' }}" href="{{ route('verification.index') }}"><div class="num">{{ $counts['not_posted'] }}</div><div class="lbl">Stalls with no price today</div></a>
    <a class="card stat" href="{{ route('verification.index') }}"><div class="num">{{ $counts['unverified'] }}</div><div class="lbl">Today's prices not yet verified</div></a>
</div>

<div class="card flush">
    <div style="padding:16px 18px 0"><h2>Latest complaints</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Stall</th><th>Commodity</th><th class="num">Posted → Reported</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($recent as $c)
                <tr>
                    <td class="nowrap">{{ $c->reference_no }}</td>
                    <td>{{ $c->stall->stall_number }} · {{ $c->stall->name }}</td>
                    <td>{{ $c->commodity->name }}</td>
                    <td class="num">₱{{ number_format($c->posted_price, 2) }} → <span class="up">₱{{ number_format($c->reported_price, 2) }}</span></td>
                    <td><x-status-badge :status="$c->status" /></td>
                    <td class="right"><a class="btn small ghost" href="{{ route('admin.complaints.show', $c) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No complaints yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
