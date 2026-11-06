@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="page-head"><div><h1>My profile</h1><p>{{ $user->roleLabel() }} · {{ $user->email }}</p></div></div>

<div class="card form-card">
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PUT')
        <div class="field">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="@error('name') is-invalid @enderror">
        </div>
        <div class="field">
            <label for="phone">Mobile number</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>

        <h3 class="mt2">Change password <span class="muted small">(leave blank to keep the current one)</span></h3>
        <div class="field">
            <label for="current_password">Current password</label>
            <input type="password" id="current_password" name="current_password" autocomplete="current-password" class="@error('current_password') is-invalid @enderror">
        </div>
        <div class="row">
            <div class="field">
                <label for="password">New password</label>
                <input type="password" id="password" name="password" autocomplete="new-password" class="@error('password') is-invalid @enderror">
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>
        </div>
        <button type="submit" class="btn primary">Save changes</button>
    </form>
</div>
@endsection
