<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\User;

class Vendor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'category',
        'email',
        'phoneNumber',
        'website',
        'rating',
        'imageIcon',
        'description',
        'locations',
        'instagram',
    ];
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_vendors');
    }

    public function favoritedBy(){
        return $this->belongsToMany(User::class,'user_vendor_favorites');
    }

}
