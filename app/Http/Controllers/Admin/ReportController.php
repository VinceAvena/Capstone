<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Price;
use App\Models\Stall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * CONTROLLER: GENERATION OF PRICE MONITORING AND COMPLIANCE REPORTS.
 *
 * One date range (and an optional market section) feeds four reports:
 *   1. Price monitoring   - lowest / highest / average posted price per commodity
 *   2. Posting compliance - how many days each stall posted prices
 *   3. Complaints         - complaints by status and confirmed violations per stall
 *   4. Flagged stalls     - repeat violators
 * The page can be printed, and every report can be downloaded as CSV (Excel).
 */
class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to, $section] = $this->filters($request);

        $complaints = $this->complaints($from, $to, $section);

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'section' => $section,
            'sections' => config('tindapresyo.sections'),
            'priceSummary' => $this->priceSummary($from, $to, $section),
            'compliance' => $this->postingCompliance($from, $to, $section),
            'complaintsByStatus' => $complaints->groupBy('status')->map->count(),
            'complaintTotal' => $complaints->count(),
            'violations' => $this->violationsPerStall($complaints),
            'avgOvercharge' => $this->averageOverchargePercent($complaints),
            'flagged' => $this->flagged($section),
            'marketName' => config('tindapresyo.market_name'),
        ]);
    }

    public function export(Request $request, string $type)
    {
        abort_unless(in_array($type, ['prices', 'compliance', 'complaints', 'flagged'], true), 404);

        [$from, $to, $section] = $this->filters($request);

        [$header, $rows] = match ($type) {
            'prices' => [
                ['Commodity', 'Category', 'Unit', 'Stalls posting', 'Entries', 'Lowest', 'Highest', 'Average'],
                $this->priceSummary($from, $to, $section)->map(fn ($r) => [
                    $r->name, $r->category, $r->unit, $r->stalls, $r->entries,
                    number_format($r->min_price, 2, '.', ''), number_format($r->max_price, 2, '.', ''), number_format($r->avg_price, 2, '.', ''),
                ])->all(),
            ],
            'compliance' => [
                ['Stall', 'Name', 'Vendor', 'Section', 'Days posted', 'Days in period', 'Compliance %', 'Last posted'],
                $this->postingCompliance($from, $to, $section)->map(fn ($s) => [
                    $s->stall_number, $s->name, $s->vendor?->name, $s->section,
                    $s->days_posted, $s->days_total, $s->rate, $s->last_posted,
                ])->all(),
            ],
            'complaints' => [
                ['Reference', 'Filed', 'Stall', 'Commodity', 'Posted price', 'Reported price', 'Overcharge', 'Overcharge %', 'Status'],
                $this->complaints($from, $to, $section)->map(fn ($c) => [
                    $c->reference_no, $c->created_at->format('Y-m-d'), $c->stall->stall_number, $c->commodity->name,
                    $c->posted_price, $c->reported_price, number_format($c->overcharge(), 2, '.', ''), $c->overchargePercent(), $c->statusLabel(),
                ])->all(),
            ],
            'flagged' => [
                ['Stall', 'Name', 'Vendor', 'Section', 'Flagged on', 'Confirmed complaints (since last clear)'],
                $this->flagged($section)->map(fn ($s) => [
                    $s->stall_number, $s->name, $s->vendor?->name, $s->section,
                    $s->flagged_at?->format('Y-m-d'), $s->violationCount(),
                ])->all(),
            ],
        };

        $filename = "tindapresyo-{$type}-{$from}-to-{$to}.csv";

        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // byte-order mark so Excel reads the peso text correctly
            fputcsv($out, $header, ',', '"', '\\');
            foreach ($rows as $row) {
                fputcsv($out, $row, ',', '"', '\\');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ---- Report data ---------------------------------------------------

    /** Read and sanitize the date range and section from the address. */
    private function filters(Request $request): array
    {
        $valid = fn ($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);

        $from = $valid($request->query('from')) ? $request->query('from') : now()->startOfMonth()->toDateString();
        $to = $valid($request->query('to')) ? $request->query('to') : now()->toDateString();
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $section = in_array($request->query('section'), config('tindapresyo.sections'), true) ? $request->query('section') : null;

        return [$from, $to, $section];
    }

    private function priceSummary(string $from, string $to, ?string $section): Collection
    {
        return Price::query()
            ->join('stalls', 'stalls.id', '=', 'prices.stall_id')
            ->join('commodities', 'commodities.id', '=', 'prices.commodity_id')
            ->whereBetween('prices.price_date', [$from, $to])
            ->when($section, fn ($q) => $q->where('stalls.section', $section))
            ->groupBy('commodities.id', 'commodities.name', 'commodities.category', 'commodities.unit')
            ->selectRaw('commodities.name as name, commodities.category as category, commodities.unit as unit,
                COUNT(DISTINCT prices.stall_id) as stalls, COUNT(*) as entries,
                MIN(prices.price) as min_price, MAX(prices.price) as max_price, AVG(prices.price) as avg_price')
            ->orderBy('commodities.category')->orderBy('commodities.name')
            ->toBase()
            ->get();
    }

    private function postingCompliance(string $from, string $to, ?string $section): Collection
    {
        // Days that count: from the start of the range up to today at most.
        $last = min($to, now()->toDateString());
        $daysTotal = max(1, (int) Carbon::parse($from)->diffInDays(Carbon::parse($last), true) + 1);
        if ($from > $last) {
            $daysTotal = 1;
        }

        return Stall::active()
            ->when($section, fn ($q) => $q->where('section', $section))
            ->with('vendor')
            ->select('stalls.*')
            ->selectSub(
                Price::query()->selectRaw('COUNT(DISTINCT price_date)')
                    ->whereColumn('prices.stall_id', 'stalls.id')
                    ->whereBetween('price_date', [$from, $to]),
                'days_posted'
            )
            ->selectSub(
                Price::query()->selectRaw('MAX(price_date)')->whereColumn('prices.stall_id', 'stalls.id'),
                'last_posted'
            )
            ->orderBy('stall_number')
            ->get()
            ->each(function ($s) use ($daysTotal) {
                $s->days_total = $daysTotal;
                $s->rate = round(min(100, ((int) $s->days_posted / $daysTotal) * 100), 1);
            });
    }

    private function complaints(string $from, string $to, ?string $section): Collection
    {
        return Complaint::with(['stall.vendor', 'commodity'])
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->when($section, fn ($q) => $q->whereHas('stall', fn ($s) => $s->where('section', $section)))
            ->latest()
            ->get();
    }

    /** Confirmed complaints grouped per stall, worst first. */
    private function violationsPerStall(Collection $complaints): Collection
    {
        return $complaints->whereNotNull('confirmed_at')
            ->groupBy('stall_id')
            ->map(fn ($group) => (object) [
                'stall' => $group->first()->stall,
                'count' => $group->count(),
                'avg_percent' => round($group->avg(fn ($c) => $c->overchargePercent()), 1),
            ])
            ->sortByDesc('count')
            ->values();
    }

    private function averageOverchargePercent(Collection $complaints): ?float
    {
        $confirmed = $complaints->whereNotNull('confirmed_at');

        return $confirmed->isEmpty() ? null : round($confirmed->avg(fn ($c) => $c->overchargePercent()), 1);
    }

    private function flagged(?string $section): Collection
    {
        return Stall::with('vendor')->where('is_flagged', true)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->orderByDesc('flagged_at')->get();
    }
}
