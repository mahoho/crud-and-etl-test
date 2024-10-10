<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelControllerTest extends TestCase {
    use RefreshDatabase;

    /**
     * Test the index route returns a list of hotels.
     *
     * @return void
     */
    public function testIndexReturnsListOfHotels(): void {
        $city = City::factory()->create();
        Hotel::factory()->count(3)->create(['city_id' => $city->id]);

        $response = $this->getJson('/api/hotels');

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

    /**
     * Test the store route creates a hotel and returns it.
     *
     * @return void
     */
    public function testStoreCreatesHotel(): void {
        /** @var City $city */
        $city = City::factory()->create();

        $data = [
            'name'        => 'Hotel Test',
            'city_id'     => $city->id,
            'address'     => '123 Test Street',
            'stars'       => 5,
            'description' => 'A wonderful place',
        ];

        $response = $this->postJson('/api/hotels', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'hotel' => [
                    'id',
                    'name',
                    'city_id',
                    'address',
                    'stars',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Hotel created successfully',
                'hotel'   => $data,
            ]);

        $this->assertDatabaseHas('hotels', $data);
    }

    /**
     * Test the show route returns a specific hotel.
     *
     * @return void
     */
    public function testShowReturnsHotel(): void {
        /** @var City $city */
        $city = City::factory()->create();
        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create(['city_id' => $city->id]);

        $response = $this->getJson("/api/hotels/$hotel->id");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'city_id',
                'address',
                'stars',
                'description',
                'created_at',
                'updated_at',
                'city' => ['id', 'name']
            ])
            ->assertJson([
                'id'          => $hotel->id,
                'name'        => $hotel->name,
                'city_id'     => $city->id,
                'address'     => $hotel->address,
                'stars'       => $hotel->stars,
                'description' => $hotel->description
            ]);
    }

    /**
     * Test the update route updates a hotel.
     *
     * @return void
     */
    public function testUpdateModifiesHotel(): void {
        /** @var City $city */
        $city = City::factory()->create();
        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create(['city_id' => $city->id]);

        $updateData = [
            'name'        => 'Updated Hotel',
            'city_id'     => $city->id,
            'address'     => '456 Updated Street',
            'stars'       => 4,
            'description' => 'An updated description',
        ];

        $response = $this->putJson("/api/hotels/$hotel->id", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'hotel' => [
                    'id',
                    'name',
                    'city_id',
                    'address',
                    'stars',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Hotel updated successfully',
                'hotel'   => $updateData,
            ]);

        $this->assertDatabaseHas('hotels', $updateData);
    }

    /**
     * Test the destroy route soft deletes a hotel.
     *
     * @return void
     */
    public function testDestroyDeletesHotel(): void {
        /** @var City $city */
        $city = City::factory()->create();
        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create(['city_id' => $city->id]);

        $response = $this->deleteJson("/api/hotels/$hotel->id");

        $response->assertStatus(204);

        $this->assertSoftDeleted('hotels', ['id' => $hotel->id]);
    }

    /**
     * Test the restore route restores a soft-deleted hotel.
     *
     * @return void
     */
    public function testRestoreRestoresHotel(): void {
        /** @var City $city */
        $city = City::factory()->create();
        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create(['city_id' => $city->id]);

        $hotel->delete();

        $response = $this->postJson("/api/hotels/$hotel->id/restore");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'hotel' => [
                    'id',
                    'name',
                    'city_id',
                    'address',
                    'stars',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Hotel restored successfully',
                'hotel'   => [
                    'id'          => $hotel->id,
                    'name'        => $hotel->name,
                    'city_id'     => $hotel->city_id,
                    'address'     => $hotel->address,
                    'stars'       => $hotel->stars,
                    'description' => $hotel->description,
                ]
            ]);

        $this->assertDatabaseHas('hotels', ['id' => $hotel->id]);
    }
}
