<?php

namespace App\Service;

use App\Token;
use Webpatser\Uuid\Uuid;

class TokenService
{
    public function create(): Token
    {
        $token = new Token();
        $token->token = Uuid::generate(4)->string;
        $token->save();

        return $token;
    }
}
