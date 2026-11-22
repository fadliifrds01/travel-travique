<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'destination_id'.
        'price',
        'duration_days',
        'quota',
    ];
}
