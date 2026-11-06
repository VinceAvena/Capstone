@extends('layouts.app')
@section('title', 'Edit Stall ' . $stall->stall_number)

@section('content')
<div class="page-head"><div><h1>Edit stall {{ $stall->stall_number }}</h1><p>Vendor: {{ $stall->vendor->name }}</p></div></div>

<div class="card form-card">
    <form method="POST" action="{{ route('admin.stalls.update', $stall) }}">
        @csrf @method('PUT')
        <div class="row">
            <div class="field">
                <label for="stall_number">Stall number</label>
                <input type="text" id="stall_number" name="stall_number" value="{{ old('stall_number', $stall->stall_number) }}" required class="@error('stall_number') is-invalid @enderror">
            </div>
            <div class="field">
                <label for="section">Market section</label>
                <select id="section" name="section" required>
                    @foreach ($sections as $s)<option value="{{ $s }}" @selected(old('section', $stall->section) === $s)>{{ $s }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="field">
            <label for="name">Stall name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $stall->name) }}" required class="@error('name') is-invalid @enderror">
        </div>
        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $stall->description) }}</textarea>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary">Save changes</button>
            <a class="btn ghost" href="{{ route('admin.stalls.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
