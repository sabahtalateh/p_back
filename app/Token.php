<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    public function orders()
    {
        return $this->morphMany(Order::class, 'holder');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
