@extends('layouts.app')
@section('title', 'Stalls')

@section('content')
<div class="page-head"><div><h1>Stalls</h1><p>Approve newly registered stalls and manage existing ones.</p></div></div>

<form method="GET" action="{{ route('admin.stalls.index') }}" class="filters">
    <div class="field"><label for="q">Search</label><input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Stall, name or vendor"></div>
    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">All</option>
            @foreach (\App\Models\Stall::STATUSES as $k => $l)<option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $l }}</option>@endforeach
        </select>
    </div>
    <div class="field">
        <label for="section">Section</label>
        <select id="section" name="section">
            <option value="">All</option>
            @foreach ($sections as $s)<option value="{{ $s }}" @selected(($filters['section'] ?? '') === $s)>{{ $s }}</option>@endforeach
        </select>
    </div>
    <div class="actions"><button class="btn primary" type="submit">Filter</button><a class="btn ghost" href="{{ route('admin.stalls.index') }}">Reset</a></div>
</form>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Name</th><th>Vendor</th><th>Section</th><th class="num">Confirmed</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($stalls as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong></td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->vendor->name }}<div class="muted small">{{ $s->vendor->email }}</div></td>
                    <td>{{ $s->section }}</td>
                    <td class="num">{{ $s->confirmed_count }}</td>
                    <td><x-status-badge :status="$s->status" /> @if ($s->is_flagged)<span class="badge flag">Flagged</span>@endif</td>
                    <td class="right nowrap">
                        <a class="btn small ghost" href="{{ route('admin.stalls.edit', $s) }}">Edit</a>
                        @if ($s->status !== 'active')
                            <form method="POST" action="{{ route('admin.stalls.status', $s) }}" class="inline">@csrf @method('PATCH')
                                <input type="hidden" name="status" value="active">
                                <button class="btn small primary" type="submit">{{ $s->status === 'pending' ? 'Approve' : 'Activate' }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.stalls.status', $s) }}" class="inline" data-confirm="Deactivate stall {{ $s->stall_number }}? It will disappear from the price board.">@csrf @method('PATCH')
                                <input type="hidden" name="status" value="inactive">
                                <button class="btn small ghost" type="submit">Deactivate</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No stalls found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $stalls->links() }}
</div>
@endsection
