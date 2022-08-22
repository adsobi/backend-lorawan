<?php

namespace Database\Seeders;

use App\Models\App;
use App\Models\Gateway;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();
        App::factory()->count(2)->createOneQuietly(['user_id'=> $user->id]);
        Gateway::factory()->count(2)->create();

    }
}
