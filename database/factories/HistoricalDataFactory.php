<?php

namespace Database\Factories;

use App\Models\HistoricalData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoricalData>
 */
class HistoricalDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'end_node_id'=> random_int(
                DB::table('end_nodes')->min('id'),
                DB::table('end_nodes')->max('id'),
            ),
            'gateway_id'=> random_int(
                DB::table('gateways')->min('id'),
                DB::table('gateways')->max('id'),
            ),
            'data' => $this->faker->randomElement(['dupa1', 'dupa2', 'dupa3']),
            'snr'=> $this->faker->randomFloat(1,7,12),
            'rssi'=> random_int(-50,-20),
            'type'=> $this->faker->randomElement(HistoricalData::TYPES),
        ];
    }
}
