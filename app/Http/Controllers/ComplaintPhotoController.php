<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CONTROLLER: serves the private photo evidence to the people allowed to see it:
 *   - the administrator
 *   - the shopper who filed the complaint
 *   - the vendor of the stall, once the complaint has been verified
 */
class ComplaintPhotoController extends Controller
{
    public function show(Request $request, Complaint $complaint)
    {
        $user = $request->user();

        $allowed = $user->isAdmin()
            || ($user->isShopper() && $complaint->shopper_id === $user->id)
            || ($user->isVendor() && $complaint->stall->user_id === $user->id && $complaint->isVisibleToVendor());

        abort_unless($allowed, 403);
        abort_unless(Storage::disk('local')->exists($complaint->photo_path), 404);

        return Storage::disk('local')->response($complaint->photo_path);
    }
}
