@extends('layouts.app')
@section('title', 'Vendor Dashboard')

@section('content')
<div class="page-head">
    <div><h1>Vendor Dashboard</h1><p>Welcome, {{ auth()->user()->name }}. Keep your prices posted every day.</p></div>
    <a class="btn primary" href="{{ route('posting.index') }}">Post today's prices</a>
</div>

@if ($open->isNotEmpty())
    <div class="alert warn">
        <strong>{{ $open->count() }} verified complaint(s)</strong> need your attention. Please review them and reply to the market administrator.
        <a href="{{ route('vendor.complaints.index') }}">View complaints →</a>
    </div>
@endif

<h2>My stalls</h2>
<div class="grid cols-2 mb">
    @forelse ($stalls as $s)
        <div class="card">
            <div class="actions" style="justify-content:space-between">
                <h3 style="margin:0">{{ $s->stall_number }} · {{ $s->name }}</h3>
                <x-status-badge :status="$s->status" />
            </div>
            <p class="muted">{{ $s->section }}</p>

            @if ($s->status === 'active')
                <p class="small">
                    @if ($s->isTrusted()) <span class="badge ok">✔ Trusted stall today</span> @endif
                    <span class="badge s-under_review">Posted {{ $s->consistency(7) }} of the last 7 days</span>
                </p>
            @endif

            @if ($s->is_flagged)
                <div class="alert error small"><strong>Your stall is flagged</strong> because of repeated confirmed overpricing complaints. Please make sure your posted prices match what you charge.</div>
            @endif

            @if ($s->status === 'pending')
                <p class="small">Waiting for approval by the market administration office. You can post prices once it is approved.</p>
            @elseif ($s->status === 'active')
                <p class="small">
                    @if ($s->hasPostedToday())
                        <span class="badge ok">Posted today</span>
                    @else
                        <span class="badge stale">No price posted today</span>
                    @endif
                </p>
                <a class="btn small primary" href="{{ route('posting.edit', $s) }}">Post prices</a>
                <a class="btn small ghost" href="{{ route('stalls.show', $s) }}">View public page</a>
            @else
                <p class="small muted">This stall is inactive.</p>
            @endif
        </div>
    @empty
        <div class="card"><p>You have no stalls yet.</p><a class="btn primary" href="{{ route('vendor.stalls.create') }}">Register a stall</a></div>
    @endforelse
</div>

@if (collect($insight)->flatten(1)->isNotEmpty())
    <div class="card flush mb">
        <div style="padding:16px 18px 0">
            <h2>How your prices compare with the market</h2>
            <p class="muted small">Based on today's posted prices of all approved stalls. Pricing close to the market average keeps shoppers coming and avoids disputes.</p>
        </div>
        @foreach ($insight as $stallId => $rows)
            @if ($rows)
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>{{ $stalls->firstWhere('id', $stallId)->stall_number }} · Commodity</th><th class="num">Your price</th><th class="num">Market average</th><th class="num">Lowest in market</th><th>Position</th></tr></thead>
                        <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ $r['name'] }} <span class="muted small">/ {{ $r['unit'] }}</span></td>
                                <td class="num money">₱{{ number_format($r['mine'], 2) }}</td>
                                <td class="num">₱{{ number_format($r['avg'], 2) }}</td>
                                <td class="num">₱{{ number_format($r['min'], 2) }}</td>
                                <td>
                                    @if ($r['percent'] > 5) <span class="badge stale">{{ $r['percent'] }}% above average</span>
                                    @elseif ($r['percent'] < -5) <span class="badge ok">{{ abs($r['percent']) }}% below average</span>
                                    @else <span class="badge s-under_review">Near average</span> @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    </div>
@endif

<div class="grid cols-2">
    <a class="card stat {{ $open->count() ? 'bad' : '' }}" href="{{ route('vendor.complaints.index') }}"><div class="num">{{ $open->count() }}</div><div class="lbl">Confirmed complaints to answer</div></a>
    <a class="card stat" href="{{ route('vendor.complaints.index') }}"><div class="num">{{ $resolvedCount }}</div><div class="lbl">Resolved complaints</div></a>
</div>
@endsection
