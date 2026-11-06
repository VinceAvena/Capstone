@extends('layouts.app')
@section('title', 'Complaint ' . $complaint->reference_no)

@section('content')
<div class="page-head">
    <div><h1>{{ $complaint->reference_no }}</h1><p>Stall {{ $complaint->stall->stall_number }} · {{ $complaint->commodity->name }}</p></div>
    <div class="actions"><x-status-badge :status="$complaint->status" /><a class="btn ghost" href="{{ route('vendor.complaints.index') }}">← Back</a></div>
</div>

@if ($complaint->stall->is_flagged)
    <div class="alert error"><strong>This stall is flagged as a repeat violator.</strong> Please keep your posted prices accurate.</div>
@endif

<div class="grid split">
    <div>
        <div class="card mb">
            <h2>Price comparison</h2>
            <div class="compare">
                <div class="box"><div class="muted small">Your posted price</div><div class="big">₱{{ number_format($complaint->posted_price, 2) }}</div><div class="small muted">{{ \Illuminate\Support\Carbon::parse($complaint->posted_price_date)->format('M j, Y') }}</div></div>
                <div class="box over"><div class="muted small">Price reported</div><div class="big">₱{{ number_format($complaint->reported_price, 2) }}</div></div>
                <div class="box"><div class="muted small">Difference</div><div class="big">+₱{{ number_format($complaint->overcharge(), 2) }}</div><div class="small muted">{{ $complaint->overchargePercent() }}% over</div></div>
            </div>
            @if ($complaint->description)
                <p class="mt"><strong>Shopper's note:</strong> {{ $complaint->description }}</p>
            @endif
            <div class="evidence mt">
                <strong>Photo evidence</strong><br>
                <img src="{{ route('complaints.photo', $complaint) }}" alt="Photo evidence for complaint {{ $complaint->reference_no }}">
            </div>
        </div>

        @if ($complaint->canExchangeMessages())
            <div class="card">
                <h2>Reply to the market administration</h2>
                <form method="POST" action="{{ route('complaints.messages.store', $complaint) }}">
                    @csrf
                    <div class="field">
                        <label for="message" class="small muted">Explain the price difference or tell the office what you corrected.</label>
                        <textarea id="message" name="message" required maxlength="1000" class="@error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn primary">Send message</button>
                </form>
            </div>
        @endif
    </div>

    <div class="card">
        <h2>History</h2>
        <x-timeline :logs="$logs" />
    </div>
</div>
@endsection
