<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    public function orders()
    {
        return $this->hasMany(OrderDish::class);
    }
}
