@extends('layouts.app')
@section('title', 'Stall ' . $stall->stall_number)

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $stall->name }}</h1>
        <p>Stall {{ $stall->stall_number }} · {{ $stall->section }} · Vendor: {{ $stall->vendor->name }}</p>
    </div>
    @auth
        @if (auth()->user()->isShopper())
            <a class="btn accent" href="{{ route('shopper.complaints.create', ['stall' => $stall->id]) }}">Report overpricing at this stall</a>
        @endif
    @endauth
</div>

<div class="actions mb">
    @if ($trusted)
        <span class="badge ok" title="Approved, not flagged, posted today, and every price verified by an inspector">✔ Trusted stall today</span>
    @endif
    <span class="badge s-under_review" title="Days with posted prices in the last 7 days">Posted prices {{ $consistency }} of the last 7 days</span>
</div>

@if ($stall->description)
    <p class="muted">{{ $stall->description }}</p>
@endif

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Commodity</th><th class="num">Posted price</th><th>Change</th><th>Last posted</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($prices as $p)
                <tr>
                    <td><strong>{{ $p->commodity->name }}</strong></td>
                    <td class="num money">₱{{ number_format($p->price, 2) }} <span class="muted small">/ {{ $p->commodity->unit }}</span></td>
                    <td class="nowrap">
                        @php $prev = $previous[$p->commodity_id] ?? null; $diff = $prev ? (float) $p->price - (float) $prev->price : null; @endphp
                        @if ($diff === null)
                            <span class="muted small">First price</span>
                        @elseif ($diff > 0.004)
                            <span class="up">▲ ₱{{ number_format($diff, 2) }}</span>
                        @elseif ($diff < -0.004)
                            <span style="color:#14603a;font-weight:600">▼ ₱{{ number_format(abs($diff), 2) }}</span>
                        @else
                            <span class="muted small">No change</span>
                        @endif
                    </td>
                    <td class="nowrap">{{ \Illuminate\Support\Carbon::parse($p->price_date)->format('M j, Y') }}</td>
                    <td>
                        @if ($p->isStale()) <span class="badge stale">Not updated today</span> @endif
                        @if ($p->isVerified()) <span class="badge ok">Verified</span> @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">This stall has not posted any prices yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<p class="mt"><a href="{{ route('prices.index') }}">← Back to the price board</a></p>
@endsection
