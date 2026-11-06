@extends('layouts.app')
@section('title', 'Review ' . $complaint->reference_no)

@section('content')
@php
    $buttonLabels = [
        'under_review' => 'Start review',
        'confirmed' => 'Confirm overpricing',
        'dismissed' => 'Dismiss complaint',
        'resolved' => 'Mark as resolved',
    ];
@endphp
<div class="page-head">
    <div><h1>{{ $complaint->reference_no }}</h1><p>Filed {{ $complaint->created_at->format('M j, Y g:i A') }} by {{ $complaint->shopper->name }}</p></div>
    <div class="actions"><x-status-badge :status="$complaint->status" /><a class="btn ghost" href="{{ route('admin.complaints.index') }}">← All complaints</a></div>
</div>

<div class="grid split">
    <div>
        {{-- Verification: the posted price (snapshot) against what the shopper reports --}}
        <div class="card mb">
            <h2>Verification: posted price vs. reported price</h2>
            <p>{{ $complaint->commodity->name }} at stall <a href="{{ route('stalls.show', $complaint->stall) }}">{{ $complaint->stall->stall_number }} · {{ $complaint->stall->name }}</a>
               (vendor: {{ $complaint->stall->vendor->name }})</p>
            <div class="compare">
                <div class="box"><div class="muted small">Posted price</div><div class="big">₱{{ number_format($complaint->posted_price, 2) }}</div><div class="small muted">posted {{ \Illuminate\Support\Carbon::parse($complaint->posted_price_date)->format('M j, Y') }}</div></div>
                <div class="box over"><div class="muted small">Reported price</div><div class="big">₱{{ number_format($complaint->reported_price, 2) }}</div></div>
                <div class="box"><div class="muted small">Overcharge</div><div class="big">+₱{{ number_format($complaint->overcharge(), 2) }}</div><div class="small muted">{{ $complaint->overchargePercent() }}% over</div></div>
            </div>
            @if ($currentPrice && (float) $currentPrice->price !== (float) $complaint->posted_price)
                <div class="alert warn small mt">The stall's <strong>current</strong> posted price is now ₱{{ number_format($currentPrice->price, 2) }} (posted {{ \Illuminate\Support\Carbon::parse($currentPrice->price_date)->format('M j') }}). The comparison above uses the price that was posted when the complaint was filed.</div>
            @endif
            @if ($complaint->description)<p class="mt"><strong>Shopper's note:</strong> {{ $complaint->description }}</p>@endif
            <div class="evidence mt">
                <strong>Photo evidence</strong><br>
                <a href="{{ route('complaints.photo', $complaint) }}" target="_blank" rel="noopener">
                    <img src="{{ route('complaints.photo', $complaint) }}" alt="Photo evidence for {{ $complaint->reference_no }}">
                </a>
            </div>
        </div>

        {{-- Resolution tracking: the next allowed status changes --}}
        <div class="card mb">
            <h2>Update status</h2>
            @if ($complaint->allowedTransitions())
                <form method="POST" action="{{ route('admin.complaints.status', $complaint) }}">
                    @csrf
                    <div class="field">
                        <label for="note">Remarks / reason / resolution <span class="muted">(required to dismiss or resolve)</span></label>
                        <textarea id="note" name="note" maxlength="1000" class="@error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                        <div class="hint">The shopper can read this note.</div>
                    </div>
                    <div class="actions">
                        @foreach ($complaint->allowedTransitions() as $next)
                            <button type="submit" name="status" value="{{ $next }}"
                                    class="btn {{ $next === 'dismissed' ? 'ghost' : ($next === 'confirmed' ? 'danger' : 'primary') }}">
                                {{ $buttonLabels[$next] ?? $next }}
                            </button>
                        @endforeach
                    </div>
                </form>
            @else
                <p class="muted">This complaint is <strong>{{ $complaint->statusLabel() }}</strong>, so no further status changes are possible.</p>
            @endif
        </div>

        @if ($complaint->canExchangeMessages())
            <div class="card">
                <h2>Message the vendor</h2>
                <form method="POST" action="{{ route('complaints.messages.store', $complaint) }}">
                    @csrf
                    <div class="field">
                        <textarea name="message" required maxlength="1000" placeholder="Ask the vendor to explain, correct the price, or confirm the action taken." class="@error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                        <div class="hint">Only you and the vendor can see messages. The shopper cannot.</div>
                    </div>
                    <button type="submit" class="btn primary">Send to vendor</button>
                </form>
            </div>
        @endif
    </div>

    <div>
        <div class="card mb">
            <h2>Stall record</h2>
            <dl class="facts">
                <dt>Confirmed complaints</dt><dd>{{ $violationCount }} / {{ config('tindapresyo.violation_threshold') }} to flag</dd>
                <dt>Flag</dt><dd>@if ($complaint->stall->is_flagged)<span class="badge flag">Flagged repeat violator</span>@else Not flagged @endif</dd>
                <dt>Vendor phone</dt><dd>{{ $complaint->stall->vendor->phone ?: '-' }}</dd>
            </dl>
            @if ($stallHistory->isNotEmpty())
                <h3 class="mt">Other complaints at this stall</h3>
                <ul class="small" style="padding-left:18px;margin:0">
                    @foreach ($stallHistory as $h)
                        <li><a href="{{ route('admin.complaints.show', $h) }}">{{ $h->reference_no }}</a> · {{ $h->commodity->name }} · {{ \App\Models\Complaint::STATUSES[$h->status] }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card">
            <h2>History</h2>
            <x-timeline :logs="$logs" :admin="true" />
        </div>
    </div>
</div>
@endsection
