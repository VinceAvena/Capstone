<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

/**
 * CONTROLLER: lets anyone follow a complaint with its reference number.
 * Only the status history is shown. No names, photos, or messages.
 */
class ComplaintTrackController extends Controller
{
    public function index(Request $request)
    {
        $ref = strtoupper(trim((string) $request->query('ref')));
        $complaint = null;

        if ($ref !== '') {
            $complaint = Complaint::with(['stall', 'commodity'])->where('reference_no', $ref)->first();
        }

        return view('track.index', [
            'ref' => $ref,
            'complaint' => $complaint,
            'logs' => $complaint
                ? $complaint->logs()->where('audience', 'all')->where('type', 'status')->get()
                : collect(),
        ]);
    }
}
