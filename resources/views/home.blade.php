@extends('layouts.app')
@section('title', 'Home')

@section('content')
<section class="hero">
    <h1>Know the posted price. Report overpricing. Keep the market fair.</h1>
    <p>TindaPresyo lets vendors post daily prices, shoppers report when they are charged more than the posted price, and the market administration office verify and follow up every complaint.</p>
    <div class="actions">
        <a class="btn accent" href="{{ route('prices.index') }}">View today's prices</a>
        @guest
            <a class="btn ghost" href="{{ route('register') }}">Create an account</a>
        @else
            <a class="btn ghost" href="{{ route('dashboard') }}">Go to my dashboard</a>
        @endguest
    </div>
</section>

<div class="grid cols-4 mb">
    <div class="card stat"><div class="num">{{ $stats['stalls'] }}</div><div class="lbl">Registered stalls</div></div>
    <div class="card stat"><div class="num">{{ $stats['commodities'] }}</div><div class="lbl">Monitored commodities</div></div>
    <div class="card stat"><div class="num">{{ $stats['postedToday'] }}</div><div class="lbl">Prices posted today</div></div>
    <div class="card stat"><div class="num">{{ $stats['resolved'] }}</div><div class="lbl">Complaints resolved</div></div>
</div>

<div class="grid split">
    <div class="card flush">
        <div style="padding:16px 18px 0"><h2>Latest posted prices</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Commodity</th><th>Stall</th><th class="num">Price</th><th>Posted</th></tr></thead>
                <tbody>
                @forelse ($latest as $p)
                    <tr>
                        <td>{{ $p->commodity->name }}</td>
                        <td><a href="{{ route('stalls.show', $p->stall) }}">{{ $p->stall->stall_number }}</a> · {{ $p->stall->name }}</td>
                        <td class="num money">₱{{ number_format($p->price, 2) }} <span class="muted small">/ {{ $p->commodity->unit }}</span></td>
                        <td class="nowrap">{{ \Illuminate\Support\Carbon::parse($p->price_date)->format('M j') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No prices have been posted yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px 18px"><a href="{{ route('prices.index') }}">See the full price board →</a></div>
    </div>

    <div class="card">
        <h2>How it works</h2>
        <ol class="steps">
            <li><strong>Vendors post prices.</strong> Each stall updates its prices every day. Inspectors verify them during rounds.</li>
            <li><strong>Shoppers compare.</strong> Anyone can check the price board before buying.</li>
            <li><strong>Overcharged? Report it.</strong> Attach a photo of the receipt or price tag.</li>
            <li><strong>The office verifies.</strong> The administrator compares your report with the posted price and follows it until it is resolved.</li>
        </ol>
        <a class="btn ghost block" href="{{ route('track') }}">Track a complaint</a>
    </div>
</div>
@endsection
