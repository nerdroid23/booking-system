<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Employee;
use App\Models\ScheduleExclusion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduleExclusion> */
class ScheduleExclusionFactory extends Factory
{
    protected $model = ScheduleExclusion::class;

    public function definition(): array
    {
        return [
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now(),

            'employee_id' => Employee::factory(),
        ];
    }
}
