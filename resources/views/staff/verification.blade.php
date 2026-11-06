@extends('layouts.app')
@section('title', 'Verify Prices')

@section('content')
<div class="page-head">
    <div><h1>Verify posted prices</h1><p>Check the stall's price tags during rounds, then tick and verify the prices that match.</p></div>
</div>

<form method="GET" action="{{ route('verification.index') }}" class="filters">
    <div class="field" style="max-width:220px"><label for="date">Date</label><input type="date" id="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"></div>
    <button type="submit" class="btn primary">Show</button>
</form>

<form method="POST" action="{{ route('verification.store') }}">
    @csrf
    <div class="card flush mb">
        <div class="actions" style="padding:14px 18px;justify-content:space-between">
            <h2 style="margin:0">Prices waiting for verification</h2>
            <label class="check" style="margin:0"><input type="checkbox" data-select-all="verify"> Select all</label>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th></th><th>Stall</th><th>Commodity</th><th class="num">Posted price</th><th>Posted by</th></tr></thead>
                <tbody>
                @forelse ($unverified as $stallId => $rows)
                    @foreach ($rows as $p)
                        <tr>
                            <td><input type="checkbox" name="price_ids[]" value="{{ $p->id }}" data-group="verify" aria-label="Verify {{ $p->commodity->name }} at stall {{ $p->stall->stall_number }}"></td>
                            <td>{{ $p->stall->stall_number }} · {{ $p->stall->name }}</td>
                            <td>{{ $p->commodity->name }}</td>
                            <td class="num money">₱{{ number_format($p->price, 2) }} <span class="muted small">/ {{ $p->commodity->unit }}</span></td>
                            <td>{{ $p->poster?->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="5" class="empty">Nothing to verify for this date.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($unverified->isNotEmpty())
        <button type="submit" class="btn primary mb">Verify selected prices</button>
    @endif
</form>

<div class="card flush">
    <div style="padding:16px 18px 0"><h2>Stalls with no price posted on {{ \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Name</th><th>Vendor</th><th></th></tr></thead>
            <tbody>
            @forelse ($notPosted as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong></td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->vendor->name }}</td>
                    <td class="right">
                        @if ($date === now()->toDateString())
                            <a class="btn small primary" href="{{ route('posting.edit', $s) }}">Post prices</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">All approved stalls posted prices on this date.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
