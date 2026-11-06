@extends('layouts.app')
@section('title', 'Post prices · ' . $stall->stall_number)

@section('content')
<div class="page-head">
    <div>
        <h1>Post prices: {{ $stall->stall_number }} · {{ $stall->name }}</h1>
        <p>Prices for {{ \Illuminate\Support\Carbon::parse($today)->format('l, F j, Y') }}. Leave a row empty if this stall does not sell that item.</p>
    </div>
    <a class="btn ghost" href="{{ route('posting.index') }}">← Back</a>
</div>

@if (auth()->user()->isStaff())
    <div class="alert info small">Prices you post as market staff are marked <strong>verified</strong> automatically.</div>
@else
    <div class="alert info small">The boxes start with your most recent price. Change what is different, then press <strong>Save today's prices</strong>. An inspector will verify them during rounds.</div>
@endif

<form method="POST" action="{{ route('posting.update', $stall) }}">
    @csrf @method('PUT')
    <div class="card flush">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Commodity</th><th>Unit</th><th>Last posted</th><th class="num">Price today (₱)</th></tr></thead>
                <tbody>
                @foreach ($groups as $category => $items)
                    <tr class="group-row"><td colspan="4">{{ $category }}</td></tr>
                    @foreach ($items as $c)
                        @php
                            $last = $current->get($c->id);
                            $start = old("prices.{$c->id}", $last?->price);
                        @endphp
                        <tr class="price-row">
                            <td><label for="p{{ $c->id }}" style="font-weight:600;margin:0">{{ $c->name }}</label></td>
                            <td>{{ $c->unit }}</td>
                            <td class="muted small">
                                @if ($last)
                                    ₱{{ number_format($last->price, 2) }} · {{ \Illuminate\Support\Carbon::parse($last->price_date)->format('M j') }}
                                    @if ($last->isVerified()) <span class="badge ok">Verified</span> @endif
                                @else
                                    None yet
                                @endif
                            </td>
                            <td class="num">
                                <input type="number" step="0.01" min="0.01" max="100000" inputmode="decimal"
                                       id="p{{ $c->id }}" name="prices[{{ $c->id }}]"
                                       value="{{ $start !== null ? number_format((float) $start, 2, '.', '') : '' }}"
                                       data-original="{{ $last?->price }}"
                                       class="price-input @error('prices.'.$c->id) is-invalid @enderror">
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="actions mt">
        <button type="submit" class="btn primary">Save today's prices</button>
        <a class="btn ghost" href="{{ route('posting.index') }}">Cancel</a>
    </div>
</form>
@endsection
