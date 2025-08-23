<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),

            'starts_at' => null,
            'ends_at' => null,
            'canceled_at' => null,
            'name' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),

            'service_id' => Service::factory(),
            'employee_id' => Employee::factory(),
        ];
    }
}
