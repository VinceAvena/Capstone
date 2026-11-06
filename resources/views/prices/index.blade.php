@extends('layouts.app')
@section('title', 'Price Board')

@section('content')
<div class="page-head">
    <div>
        <h1>Price Board</h1>
        <p>Current posted prices of every approved stall. Compare before you buy.</p>
    </div>
    @auth
        @if (auth()->user()->isShopper())
            <a class="btn accent" href="{{ route('shopper.complaints.create') }}">Report overpricing</a>
        @endif
    @endauth
</div>

<form method="GET" action="{{ route('prices.index') }}" class="filters">
    <div class="field">
        <label for="q">Search</label>
        <input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Commodity, stall name or number">
    </div>
    <div class="field">
        <label for="commodity">Commodity</label>
        <select id="commodity" name="commodity">
            <option value="">All commodities</option>
            @foreach ($commodities as $c)
                <option value="{{ $c->id }}" @selected(($filters['commodity'] ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="section">Section</label>
        <select id="section" name="section">
            <option value="">All sections</option>
            @foreach ($sections as $s)
                <option value="{{ $s }}" @selected(($filters['section'] ?? '') === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="sort">Sort by</label>
        <select id="sort" name="sort">
            <option value="" @selected(empty($filters['sort']))>Commodity, cheapest first</option>
            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Lowest price</option>
            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Highest price</option>
            <option value="recent" @selected(($filters['sort'] ?? '') === 'recent')>Recently updated</option>
        </select>
    </div>
    <div class="actions">
        <button type="submit" class="btn primary">Apply</button>
        <a class="btn ghost" href="{{ route('prices.index') }}">Reset</a>
    </div>
</form>

@if ($summary)
    <div class="grid cols-4 mb">
        <div class="card stat"><div class="num">₱{{ number_format($summary['min'], 2) }}</div><div class="lbl">Lowest</div></div>
        <div class="card stat"><div class="num">₱{{ number_format($summary['avg'], 2) }}</div><div class="lbl">Average</div></div>
        <div class="card stat"><div class="num">₱{{ number_format($summary['max'], 2) }}</div><div class="lbl">Highest</div></div>
        <div class="card stat"><div class="num">{{ $summary['count'] }}</div><div class="lbl">Stalls selling it</div></div>
    </div>
@endif

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Commodity</th><th>Stall</th><th>Section</th><th class="num">Posted price</th><th>Last posted</th><th>Status</th></tr>
            </thead>
            <tbody>
            @forelse ($prices as $p)
                <tr>
                    <td><strong>{{ $p->commodity->name }}</strong></td>
                    <td><a href="{{ route('stalls.show', $p->stall) }}">{{ $p->stall->stall_number }}</a> · {{ $p->stall->name }}</td>
                    <td>{{ $p->stall->section }}</td>
                    <td class="num money">₱{{ number_format($p->price, 2) }} <span class="muted small">/ {{ $p->commodity->unit }}</span></td>
                    <td class="nowrap">{{ \Illuminate\Support\Carbon::parse($p->price_date)->format('M j, Y') }}</td>
                    <td>
                        @if ($summary && (float) $p->price === (float) $summary['min']) <span class="badge s-under_review">Lowest price</span> @endif
                        @if ($p->isStale()) <span class="badge stale" title="Not updated today">Not updated today</span> @endif
                        @if ($p->isVerified()) <span class="badge ok" title="Checked by a market inspector">Verified</span> @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No prices match your search.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $prices->links() }}
</div>
@endsection
