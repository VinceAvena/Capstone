@extends('layouts.app')
@section('title', 'Complaints Against My Stalls')

@section('content')
<div class="page-head">
    <div><h1>Verified complaints</h1><p>You only see a complaint after the market administrator has verified it. A shopper's identity is never shown.</p></div>
</div>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Stall</th><th>Commodity</th><th class="num">Posted → Reported</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($complaints as $c)
                <tr>
                    <td class="nowrap">{{ $c->reference_no }}</td>
                    <td>{{ $c->stall->stall_number }}</td>
                    <td>{{ $c->commodity->name }}</td>
                    <td class="num">₱{{ number_format($c->posted_price, 2) }} → <span class="up">₱{{ number_format($c->reported_price, 2) }}</span></td>
                    <td><x-status-badge :status="$c->status" /></td>
                    <td class="right"><a class="btn small ghost" href="{{ route('vendor.complaints.show', $c) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No verified complaints against your stalls. Keep it up!</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $complaints->links() }}
</div>
@endsection
