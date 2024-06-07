<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Deliveries;
use Carbon\Carbon;


class DeliveriesControllerTest extends TestCase
{
    use RefreshDatabase;

    private static function _seed(int $quantity = 1, array $data = []) : array
    {
        if(!$data){
            $faker = \Faker\Factory::create();
            $data = [
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
            ];
        }
        for ($i = 0; $i < $quantity; $i++){
            Deliveries::create($data);
        }
        return $data;
    }

    public function testGetAllDeliveriesPaginated()
    {
        $this::_seed(5);
        $response = $this->get('/api/v1/deliveries');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json('data'));
    }

    public function testGetAllDeliveriesIntegrityData()
    {
        $faker = \Faker\Factory::create();
        $data = [
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
            'dispatch_date' => '2024-07-07 12:00:00',
            'estimated_delivery_date' => '2024-07-07 12:00:00',
            'notes' => $faker->text(1000)
        ];
        $this::_seed(1, $data);
        $response = $this->get('/api/v1/deliveries');
        
        $response->assertStatus(200);
        $dataResponse = $response->json('data')[0];

        $data['id'] = $dataResponse['id'];
        $data['created_at'] = $dataResponse['created_at'];
        $data['updated_at'] = $dataResponse['updated_at'];
        $this->assertEquals($data, $response->json('data')[0]);
    }

    public function testGetAllDeliveriesDeliveryDateFilter()
    {
        $faker = \Faker\Factory::create();
        $data = [
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
            'dispatch_date' => '2024-07-07 12:00:00',
            'estimated_delivery_date' => '2024-08-07 12:00:00',
            'notes' => $faker->text(1000)
        ];
        $this::_seed(1, $data);
        $response = $this->get('/api/v1/deliveries?from_delivery_date=2024-08-08');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?from_delivery_date=2024-08-06');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?to_delivery_date=2024-08-06');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?to_delivery_date=2024-08-08');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function testGetAllDeliveriesDispathDateFilter()
    {
        $faker = \Faker\Factory::create();
        $data = [
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
            'dispatch_date' => '2024-07-07 12:00:00',
            'estimated_delivery_date' => '2024-08-07 12:00:00',
            'notes' => $faker->text(1000)
        ];
        $this::_seed(1, $data);
        $response = $this->get('/api/v1/deliveries?from_dispatch_date=2024-07-08');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?from_dispatch_date=2024-07-06');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?to_dispatch_date=2024-07-06');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?to_dispatch_date=2024-07-08');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function testGetAllDeliveriesOrderIdFilter()
    {

        $data = $this::_seed(1);

        $response = $this->get('/api/v1/deliveries?order_id='.$data['order_id']);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?order_id=broken-value');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

    }

    public function testGetAllDeliveriesCustomerIdFilter()
    {

        $data = $this::_seed(1);

        $response = $this->get('/api/v1/deliveries?customer_id='.$data['customer_id']);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?customer_id=broken-value');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

    }

    public function testGetAllDeliveriesCityStateCountryFilter()
    {

        $data = $this::_seed(1);

        $response = $this->get('/api/v1/deliveries?city_state_country='.$data['city']);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?city_state_country='.$data['state']);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?city_state_country='.$data['country']);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?city_state_country=broken-value');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

    }

    public function testGetAllDeliveriesCustomerIdFilters()
    {

        $data = $this::_seed(1);

        $response = $this->get('/api/v1/deliveries?status='.$data['status']);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?status=broken-value');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

    }

    public function testGetAllDeliveriesCostFilters()
    {
        $faker = \Faker\Factory::create();
        $data = [
            'order_id' => $faker->numberBetween(0),
            'customer_id' => $faker->numberBetween(1000, 1200),
            'city' => $faker->city(),
            'state' => $faker->state(),
            'country' => $faker->country(),
            'cost' => 150,
            'status' => $faker->randomElement([
                'Delivered',
                'Pending',
                'Cancelled',
                'In transit'
            ]),
            'dispatch_date' => '2024-07-07 12:00:00',
            'estimated_delivery_date' => '2024-08-07 12:00:00',
            'notes' => $faker->text(1000)
        ];
        $this::_seed(1, $data);

        $response = $this->get('/api/v1/deliveries?min_cost=140');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?min_cost=160');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?max_cost=160');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?max_cost=140');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    public function testGetAllDeliveriesMultipleFilters()
    {
        $data = [
            'order_id' => 1234,
            'customer_id' => 4321,
            'city' => 'Curitiba',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 300,
            'status' => 'In transit',
            'dispatch_date' => '2024-07-07 12:00:00',
            'estimated_delivery_date' => '2024-08-07 12:00:00',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $args = http_build_query([
            "from_delivery_date" => "2024-08-06",
            "to_delivery_date" => "2024-08-08",
            "from_dispatch_date" => "2024-07-06T00:00",
            "to_dispatch_date" => "2024-07-08T00:00",
            "order_id" => 1234,
            "customer_id" => 4321,
            "city_state_country" => "Curitiba",
            "status" => "In transit",
            "min_cost" => 250,
            "max_cost" => 350,
        ]);

        $response = $this->get('/api/v1/deliveries?'.$args);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        $response = $this->get('/api/v1/deliveries?city_state_country=Paris');
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    public function testGetStatistics()
    {
        $this::_seed(1, [
            'order_id' => 1,
            'customer_id' => 1,
            'city' => 'Curitiba',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 300,
            'status' => 'In transit',
            'dispatch_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'estimated_delivery_date' => Carbon::now()->subMonth()->format('Y-m-d H:i:s'),
            'notes' => 'Lorem ipsum'
        ]);

        $this::_seed(1, [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
            'estimated_delivery_date' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
            'notes' => 'Lorem ipsum'
        ]);

        $this::_seed(1, [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subMonth()->format('Y-m-d H:i:s'),
            'estimated_delivery_date' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
            'notes' => 'Lorem ipsum'
        ]);

        $response = $this->get('/api/v1/dashboard/statistics');

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('total_deliveries'));
        $this->assertEquals(2, $response->json('completed_deliveries'));
        $this->assertEquals(2, $response->json('last_7_days_deliveries'));
        $this->assertEquals(200, $response->json('increase_deliveries_last_week'));
        $this->assertEquals(1, $response->json('today_new_customers'));
        $this->assertEquals(0, $response->json('new_customer_increase_from_yesterday'));
        $this->assertEquals(2, $response->json('total_cities'));
        $this->assertEquals(1, $response->json('total_countries'));
    }

    public function testGetChartOrdersLastWeek()
    {
        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
            'estimated_delivery_date' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $response = $this->get('/api/v1/dashboard/charts/orders-last-week');

        $expected=[0,0,0,0,0,0,0];
        $expected[Carbon::now()->dayOfWeek()] = 1;

        $response->assertStatus(200);
        $this->assertEquals($expected, $response->json());
    }

    public function testGetChartCustomersByMonth()
    {
        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subMonths(2)->format('Y-m-d H:i:s'),
            'estimated_delivery_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subMonths(4)->format('Y-m-d H:i:s'),
            'estimated_delivery_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $response = $this->get('/api/v1/dashboard/charts/customers-by-month');

        $response->assertStatus(200);
        $this->assertCount(6, $response->json('labels'));
        $this->assertEquals([0,1,0,1,0,0], $response->json('values'));
    }

    public function testGetChartAverageDeliveryDaysByMonth()
    {
        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-8',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-10',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subMonth()->format('Y-m').'-1',
            'estimated_delivery_date' => Carbon::now()->subMonth()->format('Y-m').'-5',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Cacelled',
            'dispatch_date' => Carbon::now()->format('Y-m').'-1',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-1',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $response = $this->get('/api/v1/dashboard/charts/average-delivery-days-by-month');

        $response->assertStatus(200);
        $this->assertCount(6, $response->json('labels'));
        $this->assertEquals([0,0,0,0,4,7], $response->json('values'));
    }

    public function testGetChartRevenueByMonth()
    {
        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-8',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-10',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subMonth()->format('Y-m').'-1',
            'estimated_delivery_date' => Carbon::now()->subMonth()->format('Y-m').'-5',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $response = $this->get('/api/v1/dashboard/charts/revenue-by-month');

        $response->assertStatus(200);
        $this->assertCount(6, $response->json('labels'));
        $this->assertEquals([0,0,0,0,250,500], $response->json('values'));
    }

    public function testGetChartTop5Cities()
    {
        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Curitiba',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-8',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-10',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subMonth()->format('Y-m').'-1',
            'estimated_delivery_date' => Carbon::now()->subMonth()->format('Y-m').'-5',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);
        $response = $this->get('/api/v1/dashboard/charts/top-5-cities');

        $response->assertStatus(200);
        $this->assertEquals(['Londrina', "Curitiba"], $response->json('labels'));
        $this->assertEquals([2,1], $response->json('values'));
    }

    public function testGetChartCostAverage()
    {
        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 100,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-8',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 300,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->format('Y-m').'-10',
            'estimated_delivery_date' => Carbon::now()->format('Y-m').'-16',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $data = [
            'order_id' => 2,
            'customer_id' => 2,
            'city' => 'Londrina',
            'state' => 'Paraná',
            'country' => 'Brazil',
            'cost' => 250,
            'status' => 'Delivered',
            'dispatch_date' => Carbon::now()->subMonth()->format('Y-m').'-1',
            'estimated_delivery_date' => Carbon::now()->subMonth()->format('Y-m').'-5',
            'notes' => 'Lorem ipsum'
        ];
        $this::_seed(1, $data);

        $response = $this->get('/api/v1/dashboard/charts/cost-average');

        $response->assertStatus(200);
        $this->assertCount(6, $response->json('labels'));
        $this->assertEquals([0,0,0,0,250,200], $response->json('values'));
    }
}
