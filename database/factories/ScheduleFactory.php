<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule> */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),

            'starts_at' => null,
            'ends_at' => null,

            'sunday_starts_at' => '09:00:00',
            'sunday_ends_at' => '17:00:00',
            'monday_starts_at' => '09:00:00',
            'monday_ends_at' => '17:00:00',
            'tuesday_starts_at' => '09:00:00',
            'tuesday_ends_at' => '17:00:00',
            'wednesday_starts_at' => '09:00:00',
            'wednesday_ends_at' => '17:00:00',
            'thursday_starts_at' => '09:00:00',
            'thursday_ends_at' => '17:00:00',
            'friday_starts_at' => '09:00:00',
            'friday_ends_at' => '17:00:00',
            'saturday_starts_at' => '09:00:00',
            'saturday_ends_at' => '17:00:00',
        ];
    }
}
