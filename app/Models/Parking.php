<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    protected $fillable = [
        'id',
        'car_id',
        'paid',
        'comment',
    ];

    public function car()
    {
        return $this->belongsTo('App\Car');
    }
}
