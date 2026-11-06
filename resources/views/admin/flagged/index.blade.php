@extends('layouts.app')
@section('title', 'Flagged Stalls')

@section('content')
<div class="page-head">
    <div><h1>Repeat violation flagging</h1><p>A stall is flagged automatically when it reaches <strong>{{ $threshold }}</strong> confirmed overpricing complaints.</p></div>
</div>

<div class="card flush mb">
    <div style="padding:16px 18px 0"><h2>Flagged stalls</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Vendor</th><th>Section</th><th>Flagged on</th><th class="num">Confirmed</th><th></th></tr></thead>
            <tbody>
            @forelse ($flagged as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong> · {{ $s->name }} <span class="badge flag">Flagged</span></td>
                    <td>{{ $s->vendor->name }}<div class="muted small">{{ $s->vendor->phone }}</div></td>
                    <td>{{ $s->section }}</td>
                    <td class="nowrap">{{ $s->flagged_at?->format('M j, Y') }}</td>
                    <td class="num">{{ $s->violations }}</td>
                    <td class="right nowrap">
                        <a class="btn small ghost" href="{{ route('admin.complaints.index', ['q' => $s->stall_number]) }}">Complaints</a>
                        <form method="POST" action="{{ route('admin.flagged.clear', $s) }}" class="inline" data-confirm="Clear the flag on stall {{ $s->stall_number }}? Counting starts again from zero.">
                            @csrf <button class="btn small primary" type="submit">Clear flag</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No stalls are flagged right now.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card flush">
    <div style="padding:16px 18px 0"><h2>Watch list</h2><p class="muted small">Stalls with confirmed complaints that have not reached the limit yet.</p></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Vendor</th><th class="num">Confirmed</th><th class="num">Until flagged</th></tr></thead>
            <tbody>
            @forelse ($watch as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong> · {{ $s->name }}</td>
                    <td>{{ $s->vendor->name }}</td>
                    <td class="num">{{ $s->violations }}</td>
                    <td class="num">{{ max(0, $threshold - $s->violations) }} more</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No stalls on the watch list.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
