<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'jdoe@mail.com',
        ]);

        $employees = Employee::factory()->count(2)
            ->create();

        $services = Service::factory()->count(2)
            ->create();

        $employees[0]->services()
            ->attach([$services[0]->id, $services[1]->id]);

        $employees[1]->services()
            ->attach([$services[1]->id]);

        $employees[0]->schedules()
            ->create([
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'monday_starts_at' => '09:00:00',
                'monday_ends_at' => '17:00:00',
            ]);
    }
}
