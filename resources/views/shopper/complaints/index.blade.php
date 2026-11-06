@extends('layouts.app')
@section('title', 'My Complaints')

@section('content')
<div class="page-head">
    <div><h1>My complaints</h1><p>Follow each report from submission to resolution.</p></div>
    <a class="btn accent" href="{{ route('shopper.complaints.create') }}">Report overpricing</a>
</div>

<div class="tabs">
    <a href="{{ route('shopper.complaints.index') }}" class="{{ ! $status ? 'active' : '' }}">All</a>
    @foreach (\App\Models\Complaint::STATUSES as $key => $label)
        <a href="{{ route('shopper.complaints.index', ['status' => $key]) }}" class="{{ $status === $key ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Filed</th><th>Stall</th><th>Commodity</th><th class="num">Posted → Paid</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($complaints as $c)
                <tr>
                    <td class="nowrap">{{ $c->reference_no }}</td>
                    <td class="nowrap">{{ $c->created_at->format('M j, Y') }}</td>
                    <td>{{ $c->stall->stall_number }} · {{ $c->stall->name }}</td>
                    <td>{{ $c->commodity->name }}</td>
                    <td class="num">₱{{ number_format($c->posted_price, 2) }} → <span class="up">₱{{ number_format($c->reported_price, 2) }}</span></td>
                    <td><x-status-badge :status="$c->status" /></td>
                    <td class="right"><a class="btn small ghost" href="{{ route('shopper.complaints.show', $c) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No complaints here.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $complaints->links() }}
</div>
@endsection
