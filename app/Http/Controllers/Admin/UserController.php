<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CONTROLLER: the administrator creates and manages accounts, mainly for
 * inspectors and other administration staff.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->query('role'), fn ($q, $r) => $q->where('role', $r))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            })
            ->orderBy('role')->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'filters' => $request->only(['role', 'q'])]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['role' => 'inspector', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Account created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // Stop the administrator from locking themselves out.
        if ($user->id === $request->user()->id) {
            $data['role'] = $user->role;
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']); // keep the current password
        }
        $data['is_active'] = $user->id === $request->user()->id ? true : $request->boolean('is_active');

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Account updated.');
    }

    /** Switch an account on or off. */
    public function toggle(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->name . ($user->is_active ? ' was re-activated.' : ' was deactivated.'));
    }
}
