@extends('layouts.app')
@section('title', 'Post Prices')

@section('content')
<div class="page-head">
    <div><h1>Post daily prices</h1><p>Choose a stall, then enter or confirm today's price for each commodity.</p></div>
    @if (auth()->user()->isVendor())
        <a class="btn ghost" href="{{ route('vendor.stalls.create') }}">Register another stall</a>
    @endif
</div>

@unless (auth()->user()->isVendor())
    <form method="GET" action="{{ route('posting.index') }}" class="filters">
        <div class="field"><label for="q">Search</label><input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Stall name or number"></div>
        <div class="field">
            <label for="section">Section</label>
            <select id="section" name="section">
                <option value="">All sections</option>
                @foreach ($sections as $s)<option value="{{ $s }}" @selected(($filters['section'] ?? '') === $s)>{{ $s }}</option>@endforeach
            </select>
        </div>
        <div class="actions"><button class="btn primary" type="submit">Search</button><a class="btn ghost" href="{{ route('posting.index') }}">Reset</a></div>
    </form>
@endunless

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Name</th><th>Section</th><th>Today</th><th></th></tr></thead>
            <tbody>
            @forelse ($stalls as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong></td>
                    <td>{{ $s->name }} @if(! auth()->user()->isVendor())<div class="muted small">{{ $s->vendor->name }}</div>@endif</td>
                    <td>{{ $s->section }}</td>
                    <td>
                        @if ($s->status !== 'active')
                            <x-status-badge :status="$s->status" />
                        @elseif ($s->posted_today)
                            <span class="badge ok">{{ $s->posted_today }} posted</span>
                        @else
                            <span class="badge stale">Nothing posted</span>
                        @endif
                    </td>
                    <td class="right">
                        @if ($s->status === 'active')
                            <a class="btn small primary" href="{{ route('posting.edit', $s) }}">Post prices</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No stalls found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
