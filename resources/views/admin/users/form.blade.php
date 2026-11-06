@extends('layouts.app')
@section('title', $user->exists ? 'Edit Account' : 'Add Account')

@section('content')
<div class="page-head"><div><h1>{{ $user->exists ? 'Edit account' : 'Add account' }}</h1></div></div>

<div class="card form-card">
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif
        <div class="field">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="@error('name') is-invalid @enderror">
        </div>
        <div class="row">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="@error('email') is-invalid @enderror">
            </div>
            <div class="field">
                <label for="phone">Mobile number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
            </div>
        </div>
        <div class="row">
            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role" @disabled($user->exists && $user->id === auth()->id())>
                    @foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}" @selected(old('role', $user->role) === $k)>{{ $l }}</option>@endforeach
                </select>
                @if ($user->exists && $user->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <div class="hint">You cannot change your own role.</div>
                @endif
            </div>
            <div class="field">
                <label for="password">Password {{ $user->exists ? '(leave blank to keep)' : '' }}</label>
                <input type="password" id="password" name="password" autocomplete="new-password" {{ $user->exists ? '' : 'required' }} class="@error('password') is-invalid @enderror">
                <div class="hint">At least 8 characters.</div>
            </div>
        </div>
        @unless ($user->exists && $user->id === auth()->id())
            <div class="field">
                <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))> Account is active</label>
            </div>
        @endunless
        <div class="actions">
            <button type="submit" class="btn primary">Save</button>
            <a class="btn ghost" href="{{ route('admin.users.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
