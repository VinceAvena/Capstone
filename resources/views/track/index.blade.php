@extends('layouts.app')
@section('title', 'Track a Complaint')

@section('content')
<div class="page-head">
    <div><h1>Track a complaint</h1><p>Enter the reference number you received when you filed your complaint.</p></div>
</div>

<form method="GET" action="{{ route('track') }}" class="filters">
    <div class="field" style="max-width:340px">
        <label for="ref">Reference number</label>
        <input type="text" id="ref" name="ref" value="{{ $ref }}" placeholder="TP-20261102-ABCDE" required>
    </div>
    <button type="submit" class="btn primary">Track</button>
</form>

@if ($ref !== '' && ! $complaint)
    <div class="alert error">No complaint was found with the reference number <strong>{{ $ref }}</strong>. Please check it and try again.</div>
@endif

@if ($complaint)
    <div class="grid split">
        <div class="card">
            <h2>Status history</h2>
            <x-timeline :logs="$logs" />
        </div>
        <div class="card">
            <h3>{{ $complaint->reference_no }}</h3>
            <p><x-status-badge :status="$complaint->status" /></p>
            <dl class="facts">
                <dt>Stall</dt><dd>{{ $complaint->stall->stall_number }} · {{ $complaint->stall->name }}</dd>
                <dt>Commodity</dt><dd>{{ $complaint->commodity->name }}</dd>
                <dt>Filed</dt><dd>{{ $complaint->created_at->format('M j, Y') }}</dd>
            </dl>
        </div>
    </div>
@endif
@endsection
