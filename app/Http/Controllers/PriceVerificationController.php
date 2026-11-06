<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Stall;
use Illuminate\Http\Request;

/**
 * CONTROLLER: PRICE VERIFICATION by market inspectors (and the administrator).
 * During market rounds the inspector checks the stall's price tags and marks
 * the vendor's posted prices as verified.
 */
class PriceVerificationController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            $date = now()->toDateString();
        }

        $unverified = Price::with(['stall', 'commodity', 'poster'])
            ->where('price_date', $date)
            ->whereNull('verified_at')
            ->whereHas('stall', fn ($q) => $q->where('status', 'active'))
            ->get()
            ->sortBy(fn ($p) => $p->stall->stall_number . $p->commodity->name)
            ->groupBy('stall_id');

        $notPosted = Stall::active()
            ->whereDoesntHave('prices', fn ($q) => $q->where('price_date', $date))
            ->with('vendor')
            ->orderBy('stall_number')
            ->get();

        return view('staff.verification', [
            'date' => $date,
            'unverified' => $unverified,
            'notPosted' => $notPosted,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'price_ids' => ['required', 'array', 'min:1'],
            'price_ids.*' => ['integer', 'exists:prices,id'],
        ], ['price_ids.required' => 'Tick at least one price to verify.']);

        $count = Price::whereIn('id', $data['price_ids'])
            ->whereNull('verified_at')
            ->update(['verified_by' => $request->user()->id, 'verified_at' => now()]);

        return back()->with('success', "{$count} price(s) marked as verified.");
    }
}
