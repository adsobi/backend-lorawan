<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gateway>
 */
class GatewayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'gateway_eui' => $this->faker->regexify('[A-Z0-9]{32}'),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(50),
        ];
    }
}
