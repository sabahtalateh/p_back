<?php

namespace App\Service;

use App\Token;

trait TokenTrait
{
    protected function findToken(string $token)
    {
        return Token::where('token', '=', $token)->latest('id')->first();
    }
}
