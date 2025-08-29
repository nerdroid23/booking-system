<?php

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Service;
use App\Services\Bookings\ServiceSlotAvailability;
use Carbon\Carbon;

test('it shows available time slots for a service', function () {
    // given
    Carbon::setTestNow(Carbon::parse('1st Jan 2010'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    // when
    $availability = new ServiceSlotAvailability(collect([$employee]), $service)
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // then
    expect($availability->first()->date->toDateString())
        ->toEqual(now()->toDateString())
        ->and($availability->first()->slots)
        ->toHaveCount(16);
});

test('it lists multiple slots over more than one day', function () {
    // given
    Carbon::setTestNow(Carbon::parse('1st Jan 2010'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfYear(),
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    // when
    $availability = new ServiceSlotAvailability(collect([$employee]), $service)
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    // then
    $dates = $availability->pluck('date')->map(fn ($date) => $date->toDateString());

    expect($dates)
        ->toContain(
            now()->toDateString(),
            now()->addDay()->toDateString(),
        )
        ->and($availability->get(0)->slots)
        ->toHaveCount(16)
        ->and($availability->get(1)->slots)
        ->toHaveCount(16);
});

test('it excludes booked appointments for the employee', function () {
    // given
    Carbon::setTestNow(Carbon::parse('1st Jan 2010'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    Appointment::factory()->for($employee)
        ->for($service)
        ->create([
            'starts_at' => now()->setTimeFromTimeString('12:00'),
            'ends_at' => now()->setTimeFromTimeString('12:45'),
        ]);

    // when
    $availability = new ServiceSlotAvailability(collect([$employee]), $service)
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // then
    expect($availability->first()->slots->pluck('time')->map(fn ($time) => $time->toTimeString()))
        ->not->toContain('12:00:00', '12:30:00')
        ->toContain('11:30:00', '13:00:00');
});

test('it ignores canceled appointments', function () {
    // given
    Carbon::setTestNow(Carbon::parse('1st Jan 2010'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    Appointment::factory()->for($employee)
        ->for($service)
        ->create([
            'starts_at' => now()->setTimeFromTimeString('12:00'),
            'ends_at' => now()->setTimeFromTimeString('12:45'),
            'canceled_at' => now()->setTimeFromTimeString('11:30'),
        ]);

    // when
    $availability = new ServiceSlotAvailability(collect([$employee]), $service)
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // then
    expect($availability->first()->slots->pluck('time')->map(fn ($time) => $time->toTimeString()))
        ->toContain('11:30:00', '12:00:00', '12:30:00', '13:00:00');
});

test('it shows multiple employees available for a service', function () {
    // given
    Carbon::setTestNow(Carbon::parse('1st Jan 2010'));

    $service = Service::factory()
        ->create(['duration' => 30]);

    $employees = Employee::factory()
        ->count(2)
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]))
        ->create();

    // when
    $availability = new ServiceSlotAvailability($employees, $service)
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // then
    expect($availability->first()->slots->first()->employees)->toHaveCount(2);
});
