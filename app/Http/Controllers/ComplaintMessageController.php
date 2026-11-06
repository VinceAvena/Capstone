<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

/**
 * CONTROLLER: COMMUNICATION between the market administrator and a vendor
 * about a verified complaint. Messages are stored in the complaint's history
 * and are never shown to the shopper.
 */
class ComplaintMessageController extends Controller
{
    public function store(Request $request, Complaint $complaint)
    {
        $user = $request->user();
        $complaint->load('stall');

        $allowed = $user->isAdmin()
            || ($user->isVendor() && $complaint->stall->user_id === $user->id);
        abort_unless($allowed, 403);
        abort_unless($complaint->canExchangeMessages(), 403, 'Messages are only allowed on verified complaints.');

        $data = $request->validate(['message' => ['required', 'string', 'max:1000']]);

        $complaint->logs()->create([
            'user_id' => $user->id,
            'type' => 'message',
            'audience' => 'vendor',
            'message' => $data['message'],
        ]);

        return back()->with('success', 'Message sent.');
    }
}
