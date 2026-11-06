@extends('layouts.app')
@section('title', $commodity->exists ? 'Edit Commodity' : 'Add Commodity')

@section('content')
<div class="page-head"><div><h1>{{ $commodity->exists ? 'Edit commodity' : 'Add commodity' }}</h1></div></div>

<div class="card form-card">
    <form method="POST" action="{{ $commodity->exists ? route('admin.commodities.update', $commodity) : route('admin.commodities.store') }}">
        @csrf
        @if ($commodity->exists) @method('PUT') @endif
        <div class="field">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $commodity->name) }}" required placeholder="e.g. Pork liempo" class="@error('name') is-invalid @enderror">
        </div>
        <div class="row">
            <div class="field">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    @foreach ($categories as $c)<option value="{{ $c }}" @selected(old('category', $commodity->category) === $c)>{{ $c }}</option>@endforeach
                </select>
            </div>
            <div class="field">
                <label for="unit">Unit</label>
                <select id="unit" name="unit" required>
                    @foreach ($units as $u)<option value="{{ $u }}" @selected(old('unit', $commodity->unit) === $u)>{{ $u }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="field">
            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $commodity->is_active ?? true))> In use (vendors can post prices for it)</label>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary">Save</button>
            <a class="btn ghost" href="{{ route('admin.commodities.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
