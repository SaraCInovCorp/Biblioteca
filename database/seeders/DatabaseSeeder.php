<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->withProfilePhoto()->withPersonalTeam()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
        ]);

        User::factory()->count(10)->withProfilePhoto()->withPersonalTeam()->create();

        if (env('SEEDER_TYPE', 'faker') === 'api') {
            $this->call(DatabaseSeederApi::class);
        } else {
            $this->call(DatabaseSeederFaker::class);
        }
    }
}