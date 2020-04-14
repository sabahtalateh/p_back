<?php

namespace Tests\Feature;

use App\Dish;
use Tests\TestCase;

class AuthTest extends TestCase
{
    private $token = null;

    public function testHistorySavedForLoggedInUser()
    {
        // Login as user1
        $token = $this->getToken();
        $this->post('/api/login', [
            'email' => 'user3@mail.com',
            'password' => 'password'
        ], ['X-TOKEN' => $token]);

        // Check history is empty
        $history = $this->get('/api/history', ['X-TOKEN' => $token])->json();
        $historyCount = count($history);

        // Add a dish to cart
        $this->post('/api/to-cart', ['dishId' => Dish::all()[0]->id], ['X-TOKEN' => $token])->json();

        // Confirm order
        $this->post('/api/order', ['phone' => '123', 'address' => '123'], ['X-TOKEN' => $token])->json();

        // Check history
        $history = $this->get('/api/history', ['X-TOKEN' => $token])->json();
        $newHistoryCount = count($history);
        $this->assertEquals($newHistoryCount, $historyCount + 1);

        // Logout
        $this->post('/api/logout', [], ['X-TOKEN' => $token]);

        // Again add a dish to cart
        $this->post('/api/to-cart', ['dishId' => Dish::all()[0]->id], ['X-TOKEN' => $token])->json();

        // Again confirm order
        $this->post('/api/order', ['phone' => '123', 'address' => '123'], ['X-TOKEN' => $token])->json();

        // Login and check that the history orders count are the same
        $this->post('/api/login', [
            'email' => 'user3@mail.com',
            'password' => 'password'
        ], ['X-TOKEN' => $token]);
        $history = $this->get('/api/history', ['X-TOKEN' => $token])->json();
        $this->assertEquals(count($history), $newHistoryCount);
    }

    private function getToken()
    {
        if (null === $this->token) {
            $this->token = $this->get('/api/token')->json()['token'];
        }
        return $this->token;
    }
}
