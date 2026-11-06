@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="page-head">
    <div><h1>Hello, {{ auth()->user()->name }}</h1><p>Check prices before you buy, and report it if you were charged more than the posted price.</p></div>
    <a class="btn accent" href="{{ route('shopper.complaints.create') }}">Report overpricing</a>
</div>

<div class="grid cols-4 mb">
    <div class="card stat"><div class="num">{{ $counts['total'] }}</div><div class="lbl">Complaints filed</div></div>
    <div class="card stat {{ $counts['open'] ? 'warn' : '' }}"><div class="num">{{ $counts['open'] }}</div><div class="lbl">Still open</div></div>
    <div class="card stat"><div class="num">{{ $counts['resolved'] }}</div><div class="lbl">Resolved</div></div>
    <div class="card stat"><div class="num">{{ $counts['dismissed'] }}</div><div class="lbl">Dismissed</div></div>
</div>

@if ($deals->isNotEmpty())
    <div class="card flush mb">
        <div style="padding:16px 18px 0"><h2>Lowest posted prices today</h2><p class="muted small">The cheapest approved stall for each commodity sold by more than one stall.</p></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Commodity</th><th>Cheapest stall</th><th class="num">Price</th><th></th></tr></thead>
                <tbody>
                @foreach ($deals as $d)
                    <tr>
                        <td><strong>{{ $d->commodity->name }}</strong></td>
                        <td>{{ $d->stall->stall_number }} · {{ $d->stall->name }}</td>
                        <td class="num money">₱{{ number_format($d->price, 2) }} <span class="muted small">/ {{ $d->commodity->unit }}</span></td>
                        <td class="right"><a class="btn small ghost" href="{{ route('stalls.show', $d->stall) }}">View stall</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="card flush">
    <div style="padding:16px 18px 0"><h2>My recent complaints</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Stall</th><th>Commodity</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($recent as $c)
                <tr>
                    <td class="nowrap">{{ $c->reference_no }}</td>
                    <td>{{ $c->stall->stall_number }} · {{ $c->stall->name }}</td>
                    <td>{{ $c->commodity->name }}</td>
                    <td><x-status-badge :status="$c->status" /></td>
                    <td class="right"><a class="btn small ghost" href="{{ route('shopper.complaints.show', $c) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">You have not filed any complaints.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
