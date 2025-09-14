<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentEvent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = true;

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}

