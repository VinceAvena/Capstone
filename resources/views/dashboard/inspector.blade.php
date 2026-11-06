@extends('layouts.app')
@section('title', 'Inspector Dashboard')

@section('content')
<div class="page-head">
    <div><h1>Market Inspector Dashboard</h1><p>Today's rounds: post prices on behalf of vendors and verify what they posted.</p></div>
    <div class="actions">
        <a class="btn primary" href="{{ route('verification.index') }}">Verify prices</a>
        <a class="btn ghost" href="{{ route('posting.index') }}">Post prices</a>
    </div>
</div>

<div class="grid cols-3 mb">
    <div class="card stat"><div class="num">{{ $postedToday }}</div><div class="lbl">Prices posted today</div></div>
    <a class="card stat {{ $unverified ? 'warn' : '' }}" href="{{ route('verification.index') }}"><div class="num">{{ $unverified }}</div><div class="lbl">Waiting for verification</div></a>
    <a class="card stat {{ $notPostedCount ? 'warn' : '' }}" href="{{ route('verification.index') }}"><div class="num">{{ $notPostedCount }}</div><div class="lbl">Stalls with no price today</div></a>
</div>

<div class="card flush">
    <div style="padding:16px 18px 0"><h2>Stalls that have not posted today</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Name</th><th>Section</th><th></th></tr></thead>
            <tbody>
            @forelse ($notPosted as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong></td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->section }}</td>
                    <td class="right"><a class="btn small primary" href="{{ route('posting.edit', $s) }}">Post prices</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Every approved stall has posted prices today. 🎉</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
