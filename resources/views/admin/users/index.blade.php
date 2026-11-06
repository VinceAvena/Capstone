@extends('layouts.app')
@section('title', 'Users')

@section('content')
<div class="page-head">
    <div><h1>User accounts</h1><p>Create inspector and administrator accounts, and switch accounts on or off.</p></div>
    <a class="btn primary" href="{{ route('admin.users.create') }}">Add account</a>
</div>

<form method="GET" action="{{ route('admin.users.index') }}" class="filters">
    <div class="field"><label for="q">Search</label><input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name or email"></div>
    <div class="field">
        <label for="role">Role</label>
        <select id="role" name="role">
            <option value="">All roles</option>
            @foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}" @selected(($filters['role'] ?? '') === $k)>{{ $l }}</option>@endforeach
        </select>
    </div>
    <div class="actions"><button class="btn primary" type="submit">Filter</button><a class="btn ghost" href="{{ route('admin.users.index') }}">Reset</a></div>
</form>

<div class="card flush">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roleLabel() }}</td>
                    <td>@if ($user->is_active)<span class="badge ok">Active</span>@else<span class="badge s-inactive">Deactivated</span>@endif</td>
                    <td class="right nowrap">
                        <a class="btn small ghost" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                        @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline" data-confirm="{{ $user->is_active ? 'Deactivate' : 'Re-activate' }} {{ $user->name }}?">
                                @csrf @method('PATCH')
                                <button class="btn small ghost" type="submit">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
