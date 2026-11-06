<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Stall;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * CONTROLLER: login, registration, and logout.
 * Shoppers and vendors may register themselves. Administrator and inspector
 * accounts are created by the administrator (Admin\UserController).
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // is_active => true means deactivated accounts cannot log in.
        $attempt = Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'));

        if (! $attempt) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'The email or password is wrong, or the account is deactivated.']);
        }

        $request->session()->regenerate(); // protects against session fixation

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register', ['sections' => config('tindapresyo.sections')]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'account_type' => ['required', Rule::in(['shopper', 'vendor'])],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // Stall details are only required when registering as a vendor.
            'stall_number' => ['required_if:account_type,vendor', 'nullable', 'string', 'max:20', 'unique:stalls,stall_number'],
            'stall_name' => ['required_if:account_type,vendor', 'nullable', 'string', 'max:120'],
            'section' => ['required_if:account_type,vendor', 'nullable', Rule::in(config('tindapresyo.sections'))],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'role' => $data['account_type'],
            ]);

            if ($data['account_type'] === 'vendor') {
                // New stalls wait for the administrator's approval.
                Stall::create([
                    'user_id' => $user->id,
                    'stall_number' => strtoupper($data['stall_number']),
                    'name' => $data['stall_name'],
                    'section' => $data['section'],
                    'status' => 'pending',
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        $message = $user->isVendor()
            ? 'Welcome! Your stall was registered and is waiting for approval by the market administration office.'
            : 'Welcome to TindaPresyo!';

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}
