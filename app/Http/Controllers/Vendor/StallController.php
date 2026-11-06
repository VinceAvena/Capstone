<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Stall;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CONTROLLER: VENDOR STALL REGISTRATION. A vendor can register more than
 * one stall. Each new stall waits for the administrator's approval.
 */
class StallController extends Controller
{
    public function index(Request $request)
    {
        return view('vendor.stalls.index', [
            'stalls' => $request->user()->stalls()->orderBy('stall_number')->get(),
        ]);
    }

    public function create()
    {
        return view('vendor.stalls.create', ['sections' => config('tindapresyo.sections')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stall_number' => ['required', 'string', 'max:20', 'unique:stalls,stall_number'],
            'name' => ['required', 'string', 'max:120'],
            'section' => ['required', Rule::in(config('tindapresyo.sections'))],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Stall::create([
            'user_id' => $request->user()->id,
            'stall_number' => strtoupper($data['stall_number']),
            'name' => $data['name'],
            'section' => $data['section'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('vendor.stalls.index')
            ->with('success', 'Stall registered. It can post prices once the administrator approves it.');
    }
}
