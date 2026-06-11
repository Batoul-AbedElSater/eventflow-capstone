<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorOrder extends Model
{
    protected $fillable = [
        'task_id',
        'vendor_id',
        'assistant_id',
        'price',
        'notes',
        
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function assistant()
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }
}