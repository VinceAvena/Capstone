@extends('layouts.app')
@section('title', 'Complaint ' . $complaint->reference_no)

@section('content')
<div class="page-head">
    <div><h1>{{ $complaint->reference_no }}</h1><p>Filed {{ $complaint->created_at->format('M j, Y g:i A') }}</p></div>
    <div class="actions"><x-status-badge :status="$complaint->status" /><a class="btn ghost" href="{{ route('shopper.complaints.index') }}">← My complaints</a></div>
</div>

<div class="grid split">
    <div class="card">
        <h2>What you reported</h2>
        <p>{{ $complaint->commodity->name }} at <a href="{{ route('stalls.show', $complaint->stall) }}">{{ $complaint->stall->stall_number }} · {{ $complaint->stall->name }}</a></p>
        <div class="compare">
            <div class="box"><div class="muted small">Posted price</div><div class="big">₱{{ number_format($complaint->posted_price, 2) }}</div><div class="small muted">{{ \Illuminate\Support\Carbon::parse($complaint->posted_price_date)->format('M j, Y') }}</div></div>
            <div class="box over"><div class="muted small">You paid</div><div class="big">₱{{ number_format($complaint->reported_price, 2) }}</div></div>
            <div class="box"><div class="muted small">Difference</div><div class="big">+₱{{ number_format($complaint->overcharge(), 2) }}</div><div class="small muted">{{ $complaint->overchargePercent() }}% over</div></div>
        </div>
        @if ($complaint->description)<p class="mt"><strong>Your note:</strong> {{ $complaint->description }}</p>@endif

        @if ($complaint->admin_remarks)
            <div class="alert info mt"><strong>Note from the market administration:</strong> {{ $complaint->admin_remarks }}</div>
        @endif
        @if ($complaint->resolution_notes)
            <div class="alert success"><strong>How it was resolved:</strong> {{ $complaint->resolution_notes }}</div>
        @endif

        <div class="evidence mt">
            <strong>Your photo evidence</strong><br>
            <img src="{{ route('complaints.photo', $complaint) }}" alt="Your photo evidence">
        </div>
    </div>

    <div class="card">
        <h2>Progress</h2>
        <x-timeline :logs="$logs" />
        <p class="small muted">Keep your reference number <strong>{{ $complaint->reference_no }}</strong>. You can also track it without logging in on the <a href="{{ route('track', ['ref' => $complaint->reference_no]) }}">tracking page</a>.</p>
    </div>
</div>
@endsection
