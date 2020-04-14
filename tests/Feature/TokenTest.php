<?php

namespace Tests\Feature;

use App\Token;
use Tests\TestCase;

class TokenTest extends TestCase
{
    public function testTokenCreatedInDatabase()
    {
        $response = $this->get('/api/token');
        $response->assertStatus(200);
        
        $token = $response->json()['token'];

        $this->assertTrue(Token::where('token', '=', $token)->exists());
    }
}
