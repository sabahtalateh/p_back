<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function orders()
    {
        return $this->morphMany(Order::class, 'holder');
    }

    public function token()
    {
        return $this->hasOne(Token::class);
    }
}
