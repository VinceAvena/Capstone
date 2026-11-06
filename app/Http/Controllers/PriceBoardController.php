<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Price;
use App\Models\Stall;
use Illuminate\Http\Request;

/**
 * CONTROLLER: the public price board. Anyone can look up current posted
 * prices, filter them, and compare stalls for one commodity.
 */
class PriceBoardController extends Controller
{
    public function index(Request $request)
    {
        $query = Price::current()
            ->with(['stall', 'commodity'])
            ->join('stalls', 'stalls.id', '=', 'prices.stall_id')
            ->join('commodities', 'commodities.id', '=', 'prices.commodity_id')
            ->where('stalls.status', 'active')
            ->where('commodities.is_active', true);

        if ($section = $request->query('section')) {
            $query->where('stalls.section', $section);
        }
        if ($commodityId = $request->query('commodity')) {
            $query->where('prices.commodity_id', (int) $commodityId);
        }
        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('stalls.name', 'like', "%{$q}%")
                    ->orWhere('stalls.stall_number', 'like', "%{$q}%")
                    ->orWhere('commodities.name', 'like', "%{$q}%");
            });
        }

        match ($request->query('sort')) {
            'price_asc' => $query->orderBy('prices.price'),
            'price_desc' => $query->orderByDesc('prices.price'),
            'recent' => $query->orderByDesc('prices.price_date'),
            default => $query->orderBy('commodities.name')->orderBy('prices.price'),
        };

        // When one commodity is chosen, show the cheapest / dearest / average.
        $summary = null;
        if ($request->query('commodity')) {
            $all = (clone $query)->get();
            if ($all->isNotEmpty()) {
                $summary = [
                    'min' => $all->min('price'),
                    'max' => $all->max('price'),
                    'avg' => $all->avg('price'),
                    'count' => $all->count(),
                ];
            }
        }

        return view('prices.index', [
            'prices' => $query->paginate(20)->withQueryString(),
            'sections' => config('tindapresyo.sections'),
            'commodities' => Commodity::active()->orderBy('name')->get(),
            'summary' => $summary,
            'filters' => $request->only(['section', 'commodity', 'q', 'sort']),
        ]);
    }

    /** Public page of one stall with all its current prices. */
    public function stall(Stall $stall)
    {
        abort_unless($stall->status === 'active', 404);

        $prices = $stall->prices()->current()->with('commodity')->get()
            ->sortBy(fn ($p) => $p->commodity->name)->values();

        // The price before the current one, so shoppers see whether a price went up or down.
        $previous = [];
        foreach ($prices as $p) {
            $previous[$p->commodity_id] = $stall->prices()
                ->where('commodity_id', $p->commodity_id)
                ->where('price_date', '<', $p->price_date)
                ->orderByDesc('price_date')
                ->first();
        }

        return view('stalls.show', [
            'stall' => $stall->load('vendor'),
            'prices' => $prices,
            'previous' => $previous,
            'trusted' => $stall->isTrusted(),
            'consistency' => $stall->consistency(7),
        ]);
    }
}
