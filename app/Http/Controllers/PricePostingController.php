<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Price;
use App\Models\Stall;
use Illuminate\Http\Request;

/**
 * CONTROLLER: DAILY PRICE POSTING.
 * Vendors post prices for their own approved stalls. Market inspectors and
 * the administrator can post for any approved stall. Prices posted by staff
 * count as verified right away; vendor prices wait for an inspector.
 */
class PricePostingController extends Controller
{
    /** Step 1: choose which stall to post for. */
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $stalls = Stall::query()
            ->when($user->isVendor(), fn ($q) => $q->where('user_id', $user->id))
            ->when(! $user->isVendor(), fn ($q) => $q->where('status', 'active'))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('stall_number', 'like', "%{$term}%"));
            })
            ->when($request->query('section'), fn ($q, $s) => $q->where('section', $s))
            ->withCount(['prices as posted_today' => fn ($q) => $q->where('price_date', $today)])
            ->with('vendor')
            ->orderBy('stall_number')
            ->get();

        return view('posting.index', [
            'stalls' => $stalls,
            'sections' => config('tindapresyo.sections'),
            'filters' => $request->only(['q', 'section']),
        ]);
    }

    /** Step 2: the price form for one stall. */
    public function edit(Request $request, Stall $stall)
    {
        $this->authorizePosting($request, $stall);

        $commodities = Commodity::active()->orderBy('category')->orderBy('name')->get();

        // The newest posted price per commodity, used as the starting value.
        $current = $stall->prices()->current()->get()->keyBy('commodity_id');

        return view('posting.edit', [
            'stall' => $stall,
            'groups' => $commodities->groupBy('category'),
            'current' => $current,
            'today' => now()->toDateString(),
        ]);
    }

    /** Step 3: save the prices as today's posted prices. */
    public function update(Request $request, Stall $stall)
    {
        $this->authorizePosting($request, $stall);

        $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0.01', 'max:100000'],
        ], [
            'prices.*.numeric' => 'Prices must be numbers, for example 185.50.',
            'prices.*.min' => 'A price must be more than zero.',
            'prices.*.max' => 'That price is too large.',
        ]);

        $user = $request->user();
        $today = now()->toDateString();
        $validIds = Commodity::active()->pluck('id')->all();
        $saved = 0;

        foreach ($request->input('prices', []) as $commodityId => $value) {
            if ($value === null || $value === '' || ! in_array((int) $commodityId, $validIds, true)) {
                continue;
            }

            $value = round((float) $value, 2);
            $price = Price::firstOrNew([
                'stall_id' => $stall->id,
                'commodity_id' => (int) $commodityId,
                'price_date' => $today,
            ]);

            $changed = ! $price->exists || (float) $price->price !== $value;

            $price->price = $value;
            $price->posted_by = $user->id;

            if ($user->isStaff()) {
                // Staff entries are verified immediately.
                $price->verified_by = $user->id;
                $price->verified_at = now();
            } elseif ($changed) {
                // A vendor changed the number, so any earlier verification no longer applies.
                $price->verified_by = null;
                $price->verified_at = null;
            }

            $price->save();
            $saved++;
        }

        if ($saved === 0) {
            return back()->withErrors(['prices' => 'Enter at least one price before saving.']);
        }

        return redirect()->route('posting.index')
            ->with('success', "{$saved} price(s) posted for stall {$stall->stall_number} on " . now()->format('M j, Y') . '.');
    }

    /** Vendors may only post for their own approved stalls; staff for any approved stall. */
    private function authorizePosting(Request $request, Stall $stall): void
    {
        abort_unless($stall->canBePostedBy($request->user()), 403, 'You cannot post prices for this stall (it may not be approved yet).');
    }
}
