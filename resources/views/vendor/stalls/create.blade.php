@extends('layouts.app')
@section('title', 'Register a Stall')

@section('content')
<div class="page-head"><div><h1>Register a stall</h1><p>The market administration office will review and approve it.</p></div></div>

<div class="card form-card">
    <form method="POST" action="{{ route('vendor.stalls.store') }}">
        @csrf
        <div class="row">
            <div class="field">
                <label for="stall_number">Stall number</label>
                <input type="text" id="stall_number" name="stall_number" value="{{ old('stall_number') }}" required placeholder="e.g. V-12" class="@error('stall_number') is-invalid @enderror">
            </div>
            <div class="field">
                <label for="section">Market section</label>
                <select id="section" name="section" required class="@error('section') is-invalid @enderror">
                    <option value="">Choose a section</option>
                    @foreach ($sections as $s)<option value="{{ $s }}" @selected(old('section') === $s)>{{ $s }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="field">
            <label for="name">Stall name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="@error('name') is-invalid @enderror">
        </div>
        <div class="field">
            <label for="description">What do you sell? <span class="muted">(optional)</span></label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        <div class="actions">
            <button type="submit" class="btn primary">Submit for approval</button>
            <a class="btn ghost" href="{{ route('vendor.stalls.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
