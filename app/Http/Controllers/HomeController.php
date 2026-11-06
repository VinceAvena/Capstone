<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Complaint;
use App\Models\Price;
use App\Models\Stall;

/**
 * CONTROLLER: the public landing page with a few live figures.
 */
class HomeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        return view('home', [
            'stats' => [
                'stalls' => Stall::active()->count(),
                'commodities' => Commodity::active()->count(),
                'postedToday' => Price::where('price_date', $today)->count(),
                'resolved' => Complaint::where('status', 'resolved')->count(),
            ],
            'latest' => Price::current()
                ->with(['stall', 'commodity'])
                ->join('stalls', 'stalls.id', '=', 'prices.stall_id')
                ->where('stalls.status', 'active')
                ->orderByDesc('prices.updated_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
