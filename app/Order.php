<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function holder()
    {
        return $this->morphTo();
    }

    public function dishes()
    {
        return $this->hasMany(OrderDish::class);
    }

    public function scopeActive($query)
    {
        return $query->where('closed', '=', false);
    }

    public function scopeClosed($query)
    {
        return $query->where('closed', '=', true);
    }
}
