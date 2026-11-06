<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

/**
 * CONTROLLER: COMPLAINT VERIFICATION AND RESOLUTION TRACKING (administrator).
 * The administrator compares the reported price and photo against the posted
 * price, then moves the complaint through its statuses.
 */
class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['stall.vendor', 'commodity', 'shopper'])->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('reference_no', 'like', "%{$q}%")
                    ->orWhereHas('stall', fn ($s) => $s->where('stall_number', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"))
                    ->orWhereHas('shopper', fn ($s) => $s->where('name', 'like', "%{$q}%"));
            });
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Counts for the status tabs.
        $counts = Complaint::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.complaints.index', [
            'complaints' => $query->paginate(15)->withQueryString(),
            'counts' => $counts,
            'filters' => $request->only(['status', 'q', 'from', 'to']),
        ]);
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['stall.vendor', 'commodity', 'shopper', 'handler']);

        return view('admin.complaints.show', [
            'complaint' => $complaint,
            'currentPrice' => $complaint->stall->latestPriceFor($complaint->commodity_id),
            'stallHistory' => Complaint::with('commodity')
                ->where('stall_id', $complaint->stall_id)
                ->where('id', '!=', $complaint->id)
                ->latest()->limit(5)->get(),
            'violationCount' => $complaint->stall->violationCount(),
            'logs' => $complaint->logs()->with('user')->get(),
        ]);
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Complaint::STATUSES))],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        // A dismissal needs a reason and a resolution needs a short description of the action taken.
        if (in_array($data['status'], ['dismissed', 'resolved'], true) && blank($data['note'] ?? null)) {
            return back()->withErrors([
                'note' => $data['status'] === 'dismissed'
                    ? 'Please write the reason for dismissing this complaint.'
                    : 'Please describe how the complaint was resolved.',
            ]);
        }

        if (! $complaint->transitionTo($data['status'], $request->user(), $data['note'] ?? null)) {
            return back()->withErrors(['status' => 'That status change is not allowed from the current status.']);
        }

        return back()->with('success', 'Complaint updated to "' . $complaint->fresh()->statusLabel() . '".');
    }
}
