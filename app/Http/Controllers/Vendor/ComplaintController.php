<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

/**
 * CONTROLLER: the vendor's view of complaints against their stalls.
 * A vendor only sees a complaint AFTER the administrator has verified it
 * (status confirmed or resolved), which protects honest vendors from false
 * accusations. The shopper's identity is never shown to the vendor.
 */
class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::with(['stall', 'commodity'])
            ->whereHas('stall', fn ($q) => $q->where('user_id', $request->user()->id))
            ->whereIn('status', ['confirmed', 'resolved'])
            ->latest()
            ->paginate(15);

        return view('vendor.complaints.index', ['complaints' => $complaints]);
    }

    public function show(Request $request, Complaint $complaint)
    {
        $complaint->load(['stall', 'commodity']);

        abort_unless($complaint->stall->user_id === $request->user()->id, 403);
        abort_unless($complaint->isVisibleToVendor(), 404);

        return view('vendor.complaints.show', [
            'complaint' => $complaint,
            'logs' => $complaint->logs()->with('user')->whereIn('audience', ['all', 'vendor'])->get(),
        ]);
    }
}
