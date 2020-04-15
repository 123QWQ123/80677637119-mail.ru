<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'id',
        'number',
        'model',
        'brand',
        'color',
    ];

    public function parking()
    {
        return $this->hasMany('App\Parking');
    }
}
