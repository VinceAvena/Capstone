<?php

namespace App\Http\Controllers\Shopper;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\Complaint;
use App\Models\Stall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CONTROLLER: COMPLAINT SUBMISSION WITH PHOTO EVIDENCE (shoppers).
 *
 * The system only accepts a complaint when:
 *   1. the stall is a registered, approved stall,
 *   2. that stall has a posted price for the commodity,
 *   3. the reported price is HIGHER than the posted price, and
 *   4. a photo (receipt or price tag) is attached.
 */
class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = $request->user()->complaints()
            ->with(['stall', 'commodity'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('shopper.complaints.index', [
            'complaints' => $complaints,
            'status' => $request->query('status'),
        ]);
    }

    public function create(Request $request)
    {
        return view('shopper.complaints.create', [
            'stalls' => Stall::active()->orderBy('stall_number')->get(),
            'selectedStall' => $request->query('stall'),
            'maxKb' => config('tindapresyo.photo_max_kb'),
        ]);
    }

    /** JSON list of the current posted prices of one stall (used by the form's JavaScript). */
    public function stallPrices(Stall $stall)
    {
        abort_unless($stall->status === 'active', 404);

        $rows = $stall->prices()->current()->with('commodity')->get()
            ->filter(fn ($p) => $p->commodity && $p->commodity->is_active)
            ->sortBy(fn ($p) => $p->commodity->name)
            ->map(fn ($p) => [
                'commodity_id' => $p->commodity_id,
                'name' => $p->commodity->name,
                'unit' => $p->commodity->unit,
                'price' => (float) $p->price,
                'date' => $p->price_date,
            ])->values();

        return response()->json($rows);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stall_id' => ['required', 'integer', 'exists:stalls,id'],
            'commodity_id' => ['required', 'integer', 'exists:commodities,id'],
            'reported_price' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:' . config('tindapresyo.photo_max_kb')],
        ], [
            'photo.required' => 'Please attach a photo of the receipt or price tag as evidence.',
            'photo.image' => 'The evidence must be an image file.',
            'photo.max' => 'The photo is too large. Please upload one under ' . (config('tindapresyo.photo_max_kb') / 1024) . ' MB.',
        ]);

        $stall = Stall::active()->find($data['stall_id']);
        if (! $stall) {
            return back()->withInput()->withErrors(['stall_id' => 'That stall is not registered or not approved.']);
        }

        $posted = $stall->latestPriceFor((int) $data['commodity_id']);
        if (! $posted) {
            return back()->withInput()->withErrors(['commodity_id' => 'This stall has no posted price for that commodity, so it cannot be verified.']);
        }

        $reported = round((float) $data['reported_price'], 2);
        if ($reported <= (float) $posted->price) {
            return back()->withInput()->withErrors([
                'reported_price' => 'The price you paid (₱' . number_format($reported, 2) . ') is not higher than the posted price (₱'
                    . number_format((float) $posted->price, 2) . '). This is not overpricing.',
            ]);
        }

        // One complaint per shopper, stall, and commodity per day (stops accidental duplicates).
        $duplicate = $request->user()->complaints()
            ->where('stall_id', $stall->id)
            ->where('commodity_id', $data['commodity_id'])
            ->whereDate('created_at', now()->toDateString())
            ->exists();
        if ($duplicate) {
            return back()->withInput()->withErrors(['commodity_id' => 'You already reported this commodity at this stall today.']);
        }

        // Photos go to PRIVATE storage; they are only served through ComplaintPhotoController.
        $path = Storage::disk('local')->putFile('complaint-photos', $request->file('photo'));

        $complaint = Complaint::create([
            'shopper_id' => $request->user()->id,
            'stall_id' => $stall->id,
            'commodity_id' => $data['commodity_id'],
            'price_id' => $posted->id,
            'posted_price' => $posted->price,      // snapshot, so later price edits cannot change it
            'posted_price_date' => $posted->price_date,
            'reported_price' => $reported,
            'description' => $data['description'] ?? null,
            'photo_path' => $path,
            'status' => 'pending',
        ]);

        $complaint->logs()->create([
            'user_id' => $request->user()->id,
            'type' => 'status',
            'audience' => 'all',
            'to_status' => 'pending',
            'message' => 'Complaint submitted.',
        ]);

        return redirect()->route('shopper.complaints.show', $complaint)
            ->with('success', "Complaint submitted. Your reference number is {$complaint->reference_no}.");
    }

    public function show(Request $request, Complaint $complaint)
    {
        abort_unless($complaint->shopper_id === $request->user()->id, 403);

        return view('shopper.complaints.show', [
            'complaint' => $complaint->load(['stall', 'commodity']),
            'logs' => $complaint->logs()->with('user')->where('audience', 'all')->get(),
        ]);
    }
}
