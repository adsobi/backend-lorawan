<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EndNode>
 */
class EndNodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'app_id' => random_int(
                DB::table('apps')->min('id'),
                DB::table('apps')->max('id'),
            ),
            'dev_addr' => $this->faker->regexify('[A-Z0-9]{8}') ,
            'name' => $this->faker->name(),
            'nwk_s_key' => $this->faker->regexify('[A-Z0-9]{32}'),
            'app_s_key' => $this->faker->regexify('[A-Z0-9]{32}'),
            'dev_eui' => $this->faker->regexify('[A-Z0-9]{32}'),
            'join_eui' => $this->faker->regexify('[A-Z0-9]{32}'),
        ];
    }
}
