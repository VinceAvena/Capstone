@extends('layouts.app')
@section('title', 'Commodities')

@section('content')
<div class="page-head">
    <div><h1>Commodities</h1><p>The basic goods whose prices are posted and monitored.</p></div>
    <a class="btn primary" href="{{ route('admin.commodities.create') }}">Add commodity</a>
</div>

<form method="GET" action="{{ route('admin.commodities.index') }}" class="filters">
    <div class="field"><label for="q">Search</label><input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}"></div>
    <div class="field">
        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">All</option>
            @foreach ($categories as $c)<option value="{{ $c }}" @selected(($filters['category'] ?? '') === $c)>{{ $c }}</option>@endforeach
        </select>
    </div>
    <div class="actions"><button class="btn primary" type="submit">Filter</button><a class="btn ghost" href="{{ route('admin.commodities.index') }}">Reset</a></div>
</form>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Category</th><th>Unit</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($commodities as $c)
                <tr>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td>{{ $c->category }}</td>
                    <td>{{ $c->unit }}</td>
                    <td>@if ($c->is_active)<span class="badge ok">In use</span>@else<span class="badge s-inactive">Switched off</span>@endif</td>
                    <td class="right"><a class="btn small ghost" href="{{ route('admin.commodities.edit', $c) }}">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No commodities found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
