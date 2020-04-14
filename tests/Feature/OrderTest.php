<?php

namespace Tests\Feature;

use App\Dish;
use Tests\TestCase;

class OrderTest extends TestCase
{
    private $token = null;

    public function testDishAdded()
    {
        $token = $this->getToken();

        $dishes = Dish::all();
        $dish = $dishes[0];

        // Add dish to cart
        $response = $this->post('/api/to-cart', ['dishId' => $dish->id], ['X-TOKEN' => $token]);
        $response->assertStatus(200);
        $json = $response->json();

        // Check dishes count
        $this->assertTrue($json['dishesInCart'] == 1);
    }

    public function testDishesCountIncreases()
    {
        $token = $this->getToken();

        $dishes = Dish::all();
        $dish = $dishes[0];

        // Add 2 same dishes to cart
        $response = $this->post('/api/to-cart', ['dishId' => $dish->id], ['X-TOKEN' => $token]);
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertTrue($json['dishesInCart'] == 1);

        $response = $this->post('/api/to-cart', ['dishId' => $dish->id], ['X-TOKEN' => $token]);
        $json = $response->json();

        // Check dishes count
        $this->assertTrue($json['dishesInCart'] == 2);
    }

    public function testDishRemovedFromCart()
    {
        $token = $this->getToken();

        $dishes = Dish::all();
        $dish = $dishes[0];

        // Add dish to cart
        $this->post('/api/to-cart', ['dishId' => $dish->id], ['X-TOKEN' => $token]);

        // Get cart record id
        $response = $this->get('/api/cart', ['X-TOKEN' => $token])->json();
        $this->assertEquals(count($response['records']), 1);
        $recordId = $response['records'][0]['id'];

        // Remove cart record
        $this->post('/api/cart/remove', ['recordId' => $recordId], ['X-TOKEN' => $token]);
        $response = $this->get('/api/cart', ['X-TOKEN' => $token])->json();

        // Check it removed
        $this->assertEquals(count($response['records']), 0);
    }

    private function getToken()
    {
        if (null === $this->token) {
            $this->token = $this->get('/api/token')->json()['token'];
        }
        return $this->token;
    }
}
