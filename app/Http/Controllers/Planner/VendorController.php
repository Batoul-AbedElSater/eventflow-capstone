<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Event $event)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        $vendors = Vendor::all();

        return view('planner.events.vendor.vendor', compact('event', 'vendors'));
    }
}
