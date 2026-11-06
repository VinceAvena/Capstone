@extends('layouts.app')
@section('title', 'My Stalls')

@section('content')
<div class="page-head">
    <div><h1>My stalls</h1><p>Every stall you registered and its approval status.</p></div>
    <a class="btn primary" href="{{ route('vendor.stalls.create') }}">Register a stall</a>
</div>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Name</th><th>Section</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($stalls as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong></td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->section }}</td>
                    <td>
                        <x-status-badge :status="$s->status" />
                        @if ($s->is_flagged) <span class="badge flag">Flagged</span> @endif
                    </td>
                    <td class="right">
                        @if ($s->status === 'active')
                            <a class="btn small primary" href="{{ route('posting.edit', $s) }}">Post prices</a>
                            <a class="btn small ghost" href="{{ route('stalls.show', $s) }}">Public page</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">You have not registered a stall yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
