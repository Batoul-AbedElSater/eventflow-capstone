<?php

namespace App\Http\Controllers\Api\Planner;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
public function index(Event $event): JsonResponse{
    if($event->planner_id!== Auth::id()){
        return response()->json(['message'=>'Forbidden.'],403);
    }
    $vendors =Vendor::all();
    return response()->json([
        'event'=>$event,
        'vendors'=>$vendors,
    ]);
}

public function show(Event $event,Vendor $vendor): JsonResponse{
    if($event->planner_id!== Auth::id()){
        return response()->json(['message'=>'Forbidden.'],403);
    }
    return response()->json([
        'event'=>$event,
        'vendor'=>$vendor,
    ]);
}

public function favorites(int $eventId): JsonResponse {
$event =Event::findOrFail($eventId);
$vendors=$event->vendors()->wherePivot('is_favorite',true)->get();
 return response()->json([
    'event'=>$event,
    'vendors'=>$vendors,
 ]);
}

public function toggleFavorite(Event $event,Vendor $vendor): JsonResponse{
    if($event->planner_id!== Auth::id()){
return response()->json(['message'=>'Forbidden.'],403);
    }
    $existing=$event->vendors()->where('vendor_id',$vendor->id)->first();
    if($existing){
        $currentStatus=$existing->pivot->is_favorite;
        $event->vendors()->updateExistingPivot($vendor->id,
        ['is_favorite'=> !$currentStatus,
        ]);
        $isfavorite=!$currentStatus;

    }
    else{
        $event->vendors()->attach($vendor->id,['is_favorite'=>true]);
        $isfavorite=true;
    }
    return response()->json([
        'message'=>'Favorite status updates',
        'is_favorite'=>$isfavorite
    ]);
}

public function removeFavorite(Event $event,Vendor $vendor): JsonResponse{
    if($event->planner_id!==Auth::id()){
        return response()->json(['message'=>'Forbidden.'],403);
    }
    $event->vendors()->detach($vendor->id);
    return response()->json([
        'message'=>'vendor removed from favorites',
    ]);
}
}
