<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * CONTROLLER: a user edits their own name, phone number, and password.
 */
class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->name = $data['name'];
        $user->phone = $data['phone'] ?? null;

        if (! empty($data['password'])) {
            $user->password = $data['password']; // hashed automatically by the model
        }

        $user->save();

        return back()->with('success', 'Your profile was updated.');
    }
}
