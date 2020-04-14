<?php

namespace App\Service;

use App\Token;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    use TokenTrait;

    public function authorize(string $token, string $email, string $password): bool
    {
        $token = $this->findToken($token);
        if (!$token) {
            return false;
        }

        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return false;
        }

        if (Hash::check($password, $user->password)) {
            $token->user_id = $user->id;
            $token->save();

            DB::table('orders')
                ->where('holder_type', Token::class)
                ->where('holder_id', $token->id)
                ->where('closed', '=', false)
                ->update(['holder_type' => User::class, 'holder_id' => $user->id]);

            return true;
        }

        return false;
    }

    public function logout(string $token)
    {
        $token = $this->findToken($token);
        if (!$token) {
            return false;
        }

        $user = $token->user;
        DB::table('orders')
            ->where('holder_type', User::class)
            ->where('holder_id', $user->id)
            ->where('closed', '=', false)
            ->update(['holder_type' => Token::class, 'holder_id' => $token->id]);

        $token->user_id = null;
        $token->save();

        return true;
    }

    public function userInfo(string $token): array
    {
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        /** @var User $user */
        if ($user = $token->user) {
            return [
                'user' => [
                    'email' => $user->email
                ]
            ];
        }

        return ['user' => null];
    }
}