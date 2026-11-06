@extends('layouts.app')
@section('title', 'Register')

@section('content')
<div class="card form-card" style="margin:0 auto">
    <h1>Create an account</h1>
    <p class="muted">Shoppers can report overpricing. Vendors can register a stall and post daily prices.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="field">
            <label for="account_type">I am a</label>
            <select id="account_type" name="account_type" class="@error('account_type') is-invalid @enderror">
                <option value="shopper" @selected(old('account_type', 'shopper') === 'shopper')>Shopper (I buy at the market)</option>
                <option value="vendor" @selected(old('account_type') === 'vendor')>Vendor (I rent a stall)</option>
            </select>
        </div>

        <div class="row">
            <div class="field">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="@error('name') is-invalid @enderror">
            </div>
            <div class="field">
                <label for="phone">Mobile number <span class="muted">(optional)</span></label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="@error('phone') is-invalid @enderror">
            </div>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="@error('email') is-invalid @enderror">
        </div>
        <div class="row">
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password" class="@error('password') is-invalid @enderror">
                <div class="hint">At least 8 characters.</div>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>
        </div>

        {{-- Shown only when "Vendor" is chosen (see public/js/app.js). --}}
        <div id="stall-fields" style="display:none">
            <div class="alert info small">Your stall will be reviewed and approved by the market administration office before you can post prices.</div>
            <div class="row">
                <div class="field">
                    <label for="stall_number">Stall number</label>
                    <input type="text" id="stall_number" name="stall_number" value="{{ old('stall_number') }}" placeholder="e.g. V-12" class="@error('stall_number') is-invalid @enderror">
                </div>
                <div class="field">
                    <label for="section">Market section</label>
                    <select id="section" name="section" class="@error('section') is-invalid @enderror">
                        <option value="">Choose a section</option>
                        @foreach ($sections as $s)
                            <option value="{{ $s }}" @selected(old('section') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="stall_name">Stall name</label>
                <input type="text" id="stall_name" name="stall_name" value="{{ old('stall_name') }}" class="@error('stall_name') is-invalid @enderror">
            </div>
        </div>

        <button type="submit" class="btn primary block">Register</button>
    </form>
    <p class="mt muted small">Already registered? <a href="{{ route('login') }}">Log in</a>.</p>
</div>
@endsection
