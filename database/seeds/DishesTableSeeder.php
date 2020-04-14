<?php

use Carbon\Carbon;
use FakerRestaurant\Provider\de_AT\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DishesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new Restaurant($faker));

        DB::table('order_dishes')->delete();
        DB::table('orders')->delete();
        DB::table('dishes')->delete();

        $pizzas = [
            [
                'name' => 'Peperoni Pizza',
                'image' => 'https://i.ytimg.com/vi/_sc-27CBnHM/maxresdefault.jpg'
            ],
            [
                'name' => 'Chicken Pizza',
                'image' => 'https://www.bakingmad.com/BakingMad/media/content/Recipes/Bread-Dough/Chicken-and-pepper-pizza/1-Turkey-and-pepper-pizza.jpg'
            ],
            [
                'name' => 'American Pizza',
                'image' => 'https://assets.change.org/photos/1/vz/pv/tiVzpVJcSCabmMU-800x450-noPad.jpg?1524762406'
            ],
            [
                'name' => 'Ungerground Pizza',
                'image' => 'https://cdn-www.konbini.com/en/files/2019/02/tortuesninja-feat.jpg'
            ],
            [
                'name' => 'Royal Pizza',
                'image' => 'https://cdn.trendhunterstatic.com/thumbs/pizza-meme.jpeg'
            ],
            [
                'name' => 'Batman Pizza',
                'image' => 'https://2.bp.blogspot.com/-Qz9cFVd5-FE/U53sRQoHFUI/AAAAAAAAfmM/uczPN-4m3o4/s1600/batman_pizza_1.jpg'
            ],
            [
                'name' => 'Square Pizza',
                'image' => 'https://i.redd.it/a4fd927p8wu21.jpg'
            ],
            [
                'name' => 'Anime Pizza',
                'image' => 'https://pm1.narvii.com/6545/02818429c48ebd6fbcd033e9b98fec4153ab5d38_hq.jpg'
            ],
            [
                'name' => 'Antibacterial Pizza',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91%2BmVBFDcOL._SL1500_.jpg'
            ],
            [
                'name' => 'Tomato Pizza',
                'image' => 'https://s23991.pcdn.co/wp-content/uploads/2014/06/pizza-marinara.jpg'
            ],
            [
                'name' => 'Four Cheese Pizza',
                'image' => 'https://files2.hungryforever.com/wp-content/uploads/2017/03/25125257/Cheese-PIzza1.jpg'
            ],
            [
                'name' => 'Five Cheese Pizza',
                'image' => 'https://files2.hungryforever.com/wp-content/uploads/2017/03/25125257/Cheese-PIzza1.jpg'
            ],
        ];

        foreach ($pizzas as $p) {
            DB::table('dishes')->insert([
                'name' => $p['name'],
                'description' => $faker->sentence,
                'image' => $p['image'],
                'cost' => rand(2, 10) * 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
