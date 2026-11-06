@extends('layouts.app')
@section('title', 'Log in')

@section('content')
<div class="card form-card" style="margin:0 auto">
    <h1>Log in</h1>
    <p class="muted">Use the account you registered with the market administration office.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="@error('email') is-invalid @enderror">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <div class="field">
            <label class="check"><input type="checkbox" name="remember" value="1"> Keep me logged in</label>
        </div>
        <button type="submit" class="btn primary block">Log in</button>
    </form>

    <p class="mt muted small">No account yet? <a href="{{ route('register') }}">Register as a shopper or vendor</a>.</p>

    @if (app()->environment('local'))
        <div class="alert info small mt">
            <strong>Demo accounts</strong> (password: <code>password</code>)<br>
            admin@tindapresyo.test · inspector@tindapresyo.test<br>
            vendor1@tindapresyo.test · shopper1@tindapresyo.test
        </div>
    @endif
</div>
@endsection
