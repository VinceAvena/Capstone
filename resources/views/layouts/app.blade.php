<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TindaPresyo') · {{ config('tindapresyo.market_name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>

{{-- ============ Top bar and menu (the menu depends on the user's role) ============ --}}
@php $u = auth()->user(); @endphp
<header class="topbar">
    <div class="container">
        <a class="brand" href="{{ route('home') }}" aria-label="TindaPresyo home">
            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <path d="M4 16.5V5a1 1 0 0 1 1-1h11.5a1 1 0 0 1 .7.3l10.5 10.5a1 1 0 0 1 0 1.4l-10.9 10.9a1 1 0 0 1-1.4 0L4.3 17.2a1 1 0 0 1-.3-.7Z" fill="#e9a21b"/>
                <circle cx="10" cy="10" r="2.2" fill="#114a30"/>
                <path d="M14 17.5h6M14 20.5h6M16 14.5h2.6a1.6 1.6 0 0 1 0 3.2H15.6" stroke="#114a30" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <span>TindaPresyo<small>{{ config('tindapresyo.market_name') }}</small></span>
        </a>

        <button class="nav-toggle" id="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="main-nav">☰ Menu</button>

        <nav class="nav" id="main-nav" aria-label="Main menu">
            @guest
                <a href="{{ route('prices.index') }}" class="{{ request()->routeIs('prices.*') ? 'active' : '' }}">Price Board</a>
                <a href="{{ route('track') }}" class="{{ request()->routeIs('track') ? 'active' : '' }}">Track a Complaint</a>
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Log in</a>
                <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Register</a>
            @else
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

                @if ($u->isAdmin())
                    <a href="{{ route('admin.complaints.index') }}" class="{{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">Complaints</a>
                    <a href="{{ route('admin.stalls.index') }}" class="{{ request()->routeIs('admin.stalls.*') ? 'active' : '' }}">Stalls</a>
                    <a href="{{ route('admin.flagged.index') }}" class="{{ request()->routeIs('admin.flagged.*') ? 'active' : '' }}">Flagged</a>
                    <a href="{{ route('admin.commodities.index') }}" class="{{ request()->routeIs('admin.commodities.*') ? 'active' : '' }}">Commodities</a>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Reports</a>
                    <a href="{{ route('admin.evaluation.index') }}" class="{{ request()->routeIs('admin.evaluation.*') ? 'active' : '' }}">Evaluation</a>
                @endif

                @if ($u->isVendor() || $u->isStaff())
                    <a href="{{ route('posting.index') }}" class="{{ request()->routeIs('posting.*') ? 'active' : '' }}">Post Prices</a>
                @endif
                @if ($u->isStaff())
                    <a href="{{ route('verification.index') }}" class="{{ request()->routeIs('verification.*') ? 'active' : '' }}">Verify Prices</a>
                @endif
                @if ($u->isVendor())
                    <a href="{{ route('vendor.stalls.index') }}" class="{{ request()->routeIs('vendor.stalls.*') ? 'active' : '' }}">My Stalls</a>
                    <a href="{{ route('vendor.complaints.index') }}" class="{{ request()->routeIs('vendor.complaints.*') ? 'active' : '' }}">Complaints</a>
                @endif
                @if ($u->isShopper())
                    <a href="{{ route('shopper.complaints.create') }}" class="{{ request()->routeIs('shopper.complaints.create') ? 'active' : '' }}">Report Overpricing</a>
                    <a href="{{ route('shopper.complaints.index') }}" class="{{ request()->routeIs('shopper.complaints.index', 'shopper.complaints.show') ? 'active' : '' }}">My Complaints</a>
                @endif

                <a href="{{ route('prices.index') }}" class="{{ request()->routeIs('prices.*') ? 'active' : '' }}">Price Board</a>
                @unless ($u->isAdmin())
                    <a href="{{ route('evaluation.form') }}" class="{{ request()->routeIs('evaluation.form') ? 'active' : '' }}">Evaluate</a>
                @endunless

                <span class="who">{{ $u->name }}</span>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="linklike">Log out</button>
                </form>
            @endguest
        </nav>
    </div>
</header>

{{-- ============ Page content ============ --}}
<main class="container">
    @if (session('success'))
        <div class="alert success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert error" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert error" role="alert">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="footer">
    <div class="container">
        TindaPresyo: Web-Based Price Monitoring and Overpricing Complaint System ·
        St. Paul University Philippines · Capstone Project
    </div>
</footer>

</body>
</html>
