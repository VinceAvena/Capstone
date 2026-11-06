<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stall;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CONTROLLER: the administrator approves, edits, and deactivates stalls.
 */
class StallController extends Controller
{
    public function index(Request $request)
    {
        $stalls = Stall::with('vendor')
            ->withCount(['complaints as confirmed_count' => fn ($q) => $q->whereNotNull('confirmed_at')])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('section'), fn ($q, $s) => $q->where('section', $s))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('name', 'like', "%{$term}%")
                        ->orWhere('stall_number', 'like', "%{$term}%")
                        ->orWhereHas('vendor', fn ($v) => $v->where('name', 'like', "%{$term}%"));
                });
            })
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'active' THEN 1 ELSE 2 END")
            ->orderBy('stall_number')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stalls.index', [
            'stalls' => $stalls,
            'sections' => config('tindapresyo.sections'),
            'filters' => $request->only(['status', 'section', 'q']),
        ]);
    }

    public function edit(Stall $stall)
    {
        return view('admin.stalls.edit', [
            'stall' => $stall->load('vendor'),
            'sections' => config('tindapresyo.sections'),
        ]);
    }

    public function update(Request $request, Stall $stall)
    {
        $data = $request->validate([
            'stall_number' => ['required', 'string', 'max:20', Rule::unique('stalls', 'stall_number')->ignore($stall->id)],
            'name' => ['required', 'string', 'max:120'],
            'section' => ['required', Rule::in(config('tindapresyo.sections'))],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $data['stall_number'] = strtoupper($data['stall_number']);
        $stall->update($data);

        return redirect()->route('admin.stalls.index')->with('success', "Stall {$stall->stall_number} was updated.");
    }

    /** Approve, deactivate, or re-activate a stall. */
    public function setStatus(Request $request, Stall $stall)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['active', 'inactive'])]]);

        $stall->update(['status' => $data['status']]);

        $word = $data['status'] === 'active' ? 'approved / activated' : 'deactivated';

        return back()->with('success', "Stall {$stall->stall_number} was {$word}.");
    }
}
