<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deliveries;

class DeliveriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 2500; $i++) {
            Deliveries::create([
                'order_id' => $faker->numberBetween(0),
                'customer_id' => $faker->numberBetween(1000, 1200),
                'city' => $faker->city(),
                'state' => $faker->state(),
                'country' => $faker->country(),
                'cost' => $faker->randomFloat(2, 1, 300),
                'status' => $faker->randomElement([
                    'Delivered',
                    'Pending',
                    'Cancelled',
                    'In transit'
                ]),
                'dispatch_date' => $faker->dateTimeBetween('-5 months', 'now'),
                'estimated_delivery_date' => $faker->dateTimeBetween('now', '+2 months'),
                'notes' => $faker->text(1000)
            ]);
        }
    }
}
