<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stall;

/**
 * CONTROLLER: REPEAT VIOLATION FLAGGING screen. The flag itself is set
 * automatically by Stall::refreshFlag() when a complaint is confirmed;
 * here the administrator reviews flagged stalls and may clear a flag.
 */
class FlaggedStallController extends Controller
{
    public function index()
    {
        $flagged = Stall::with('vendor')->where('is_flagged', true)->orderByDesc('flagged_at')->get();

        // Stalls with at least one confirmed complaint that are not flagged yet.
        $watch = Stall::with('vendor')
            ->where('is_flagged', false)
            ->whereHas('complaints', fn ($q) => $q->whereNotNull('confirmed_at'))
            ->get()
            ->each(fn ($s) => $s->violations = $s->violationCount())
            ->filter(fn ($s) => $s->violations > 0)
            ->sortByDesc('violations')
            ->values();

        return view('admin.flagged.index', [
            'flagged' => $flagged->each(fn ($s) => $s->violations = $s->violationCount()),
            'watch' => $watch,
            'threshold' => config('tindapresyo.violation_threshold'),
        ]);
    }

    public function clear(Stall $stall)
    {
        $stall->clearFlag();

        return back()->with('success', "The flag on stall {$stall->stall_number} was cleared. Counting starts again from now.");
    }
}
