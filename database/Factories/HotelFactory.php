<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HotelFactory extends Factory {
    protected $model = Hotel::class;

    public function definition(): array {
        return [
            'name'        => $this->faker->name(),
            'image'       => $this->faker->word(),
            'description' => $this->faker->text(),
            'address'     => $this->faker->address(),
            'stars'       => $this->faker->randomNumber(),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),

            'city_id' => City::factory(),
        ];
    }
}
