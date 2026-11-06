<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Price;
use App\Models\Stall;

/**
 * CONTROLLER: the first page after login. Each role gets its own summary.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        // Active stalls that have not posted any price today.
        $notPosted = fn () => Stall::active()
            ->whereDoesntHave('prices', fn ($q) => $q->where('price_date', $today));

        if ($user->isAdmin()) {
            return view('dashboard.admin', [
                'counts' => [
                    'pending' => Complaint::where('status', 'pending')->count(),
                    'under_review' => Complaint::where('status', 'under_review')->count(),
                    'confirmed' => Complaint::where('status', 'confirmed')->count(),
                    'resolved_month' => Complaint::where('status', 'resolved')
                        ->where('resolved_at', '>=', now()->startOfMonth())->count(),
                    'flagged' => Stall::where('is_flagged', true)->count(),
                    'stalls_pending' => Stall::where('status', 'pending')->count(),
                    'not_posted' => $notPosted()->count(),
                    'unverified' => Price::where('price_date', $today)->whereNull('verified_at')->count(),
                ],
                'recent' => Complaint::with(['stall', 'commodity'])->latest()->limit(6)->get(),
            ]);
        }

        if ($user->isInspector()) {
            return view('dashboard.inspector', [
                'notPosted' => $notPosted()->orderBy('stall_number')->limit(12)->get(),
                'notPostedCount' => $notPosted()->count(),
                'unverified' => Price::where('price_date', $today)->whereNull('verified_at')->count(),
                'postedToday' => Price::where('price_date', $today)->count(),
            ]);
        }

        if ($user->isVendor()) {
            $stalls = $user->stalls()->withCount('prices')->orderBy('stall_number')->get();
            $complaints = Complaint::with(['stall', 'commodity'])
                ->whereIn('stall_id', $stalls->pluck('id'))
                ->whereIn('status', ['confirmed', 'resolved'])
                ->latest()->get();

            // MARKET INSIGHT for vendors: how each of my prices compares with the market.
            $market = Price::current()
                ->join('stalls', 'stalls.id', '=', 'prices.stall_id')
                ->where('stalls.status', 'active')
                ->get()
                ->groupBy('commodity_id');

            $insight = [];
            foreach ($stalls->where('status', 'active') as $stall) {
                $rows = [];
                foreach ($stall->prices()->current()->with('commodity')->get() as $mine) {
                    $group = $market->get($mine->commodity_id);
                    if (! $group || $group->count() < 2) {
                        continue; // nothing to compare with
                    }
                    $avg = (float) $group->avg('price');
                    $rows[] = [
                        'name' => $mine->commodity->name,
                        'unit' => $mine->commodity->unit,
                        'mine' => (float) $mine->price,
                        'avg' => $avg,
                        'min' => (float) $group->min('price'),
                        'percent' => $avg > 0 ? round(((float) $mine->price - $avg) / $avg * 100, 1) : 0,
                    ];
                }
                $insight[$stall->id] = $rows;
            }

            return view('dashboard.vendor', [
                'insight' => $insight,
                'stalls' => $stalls,
                'open' => $complaints->where('status', 'confirmed'),
                'resolvedCount' => $complaints->where('status', 'resolved')->count(),
            ]);
        }

        $mine = $user->complaints();

        // BEST DEALS for shoppers: the cheapest approved stall for each commodity sold by 2+ stalls.
        $deals = Price::current()
            ->with(['stall', 'commodity'])
            ->join('stalls', 'stalls.id', '=', 'prices.stall_id')
            ->where('stalls.status', 'active')
            ->get()
            ->groupBy('commodity_id')
            ->filter(fn ($group) => $group->count() >= 2)
            ->map(fn ($group) => $group->sortBy('price')->first())
            ->take(8);

        return view('dashboard.shopper', [
            'deals' => $deals,
            'counts' => [
                'total' => (clone $mine)->count(),
                'open' => (clone $mine)->whereIn('status', ['pending', 'under_review', 'confirmed'])->count(),
                'resolved' => (clone $mine)->where('status', 'resolved')->count(),
                'dismissed' => (clone $mine)->where('status', 'dismissed')->count(),
            ],
            'recent' => $user->complaints()->with(['stall', 'commodity'])->latest()->limit(5)->get(),
        ]);
    }
}
