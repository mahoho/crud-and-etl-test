<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HotelControllerTest extends TestCase {
    use RefreshDatabase;
    use WithFaker;

    #[Test]
    public function it_can_list_hotels() {
        $city = City::factory()->create();
        $hotels = Hotel::factory()->count(5)->create(['city_id' => $city->id]);

        $response = $this->getJson('/api/hotels/list');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'stars',
                        'image',
                        'city' => [
                            'id',
                            'name',
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function it_can_show_a_single_hotel() {
        $city = City::factory()->create();
        $hotel = Hotel::factory()->create(['city_id' => $city->id]);

        $response = $this->getJson("/api/hotels/show/{$hotel->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id'          => $hotel->id,
                'name'        => $hotel->name,
                'description' => $hotel->description,
                'image'       => $hotel->image,
                'stars'       => $hotel->stars,
                'city'        => [
                    'id'   => $city->id,
                    'name' => $city->name,
                ]
            ]);
    }

    #[Test]
    public function it_can_create_and_update_a_hotel() {
        $city = City::factory()->create();

        $hotelData = [
            'address'     => $this->faker->address,
            'description' => $this->faker->realText(),
            'name'        => $this->faker->company,
            'stars'       => $this->faker->numberBetween(1, 5),
            'image'       => $this->faker->imageUrl(),
            'city_id'     => $city->id,
        ];

        $response = $this->postJson('/api/hotels/save', $hotelData);

        $statusCode = $response->getStatusCode();

        $this->assertContains($statusCode, [200, 201]);

        $response->assertJson([
            'name'        => $hotelData['name'],
            'stars'       => $hotelData['stars'],
            'address'     => $hotelData['address'],
            'description' => $hotelData['description'],
            'city'        => [
                'id'   => $city->id,
                'name' => $city->name,
            ]
        ]);

        $this->assertDatabaseHas('hotels', ['name' => $hotelData['name']]);

        $updatedData = [
            'id'      => $response->json('id'),
            'name'    => 'Updated Hotel Name',
            'address' => 'Updated Address',
            'stars'   => $this->faker->numberBetween(1, 5),
            'city_id' => $city->id,
        ];

        $updateResponse = $this->postJson('/api/hotels/save', $updatedData);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'name'    => 'Updated Hotel Name',
                'address' => 'Updated Address',
                'stars'   => $updatedData['stars'],
                'city'    => [
                    'id'   => $city->id,
                    'name' => $city->name,
                ]
            ]);

        $this->assertDatabaseHas('hotels', [
            'name'    => 'Updated Hotel Name',
            'address' => 'Updated Address',
            'stars'   => $updatedData['stars'],
            'city_id' => $city->id
        ]);
    }

    #[Test]
    public function it_can_delete_a_hotel() {
        $hotel = Hotel::factory()->create();

        $response = $this->postJson("/api/hotels/delete/{$hotel->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('hotels', ['id' => $hotel->id]);
    }
}
