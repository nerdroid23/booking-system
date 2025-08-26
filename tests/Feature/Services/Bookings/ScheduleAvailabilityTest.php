<?php

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\ScheduleExclusion;
use App\Models\Service;
use App\Services\Bookings\ScheduleAvailability;
use Illuminate\Support\Carbon;

test('it lists employee availability', function () {
    // given
    Carbon::setTestNow(Carbon::parse('1st Jan 2010'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    // when
    $availability = new ScheduleAvailability($employee, $service)
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // then
    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('09:00:00'))
        ->toBeTrue()
        ->endsAt(now()->setTimeFromTimeString('16:30:00'))
        ->toBeTrue();
});

test('it accounts for different daily schedule times', function () {
    // given
    Carbon::setTestNow(Carbon::parse('Monday Jan 2010'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
            'monday_starts_at' => '10:00:00',
            'monday_ends_at' => '15:00:00',
            'tuesday_starts_at' => '08:00:00',
            'tuesday_ends_at' => '16:00:00',
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    // when
    $availability = new ScheduleAvailability($employee, $service)
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    // then
    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('10:00:00'))
        ->toBeTrue()
        ->endsAt(now()->setTimeFromTimeString('14:30:00'))
        ->toBeTrue();

    $availability->next();

    expect($availability->current())
        ->startsAt(now()->addDay()->setTimeFromTimeString('08:00:00'))
        ->toBeTrue()
        ->endsAt(now()->addDay()->setTimeFromTimeString('15:30:00'))
        ->toBeTrue();
});

test('it does not show availability for schedule exclusions', function () {
    // given
    Carbon::setTestNow(Carbon::parse('1st Jan 2010'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    // exclude all day tomorrow
    ScheduleExclusion::factory()
        ->for($employee)
        ->create([
            'starts_at' => now()->addDay()->startOfDay(),
            'ends_at' => now()->addDay()->endOfDay(),
        ]);

    // exclude lunch break today
    ScheduleExclusion::factory()
        ->for($employee)
        ->create([
            'starts_at' => now()->setTimeFromTimeString('13:00:00'),
            'ends_at' => now()->setTimeFromTimeString('14:00:00'),
        ]);

    // when
    $availability = new ScheduleAvailability($employee, $service)
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    // then
    // first period is before lunch break
    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('09:00:00'))
        ->toBeTrue()
        ->endsAt(now()->setTimeFromTimeString('12:59:00'))
        ->toBeTrue();

    $availability->next();

    // second period is after lunch break
    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('14:00:00'))
        ->toBeTrue()
        ->endsAt(now()->setTimeFromTimeString('16:30:00'))
        ->toBeTrue();

    $availability->next();

    // tomorrow is fully excluded, so no more availability
    expect($availability->valid())->toBeFalse();
});

test('availability starts at the next hour of the current time', function () {
    // given
    Carbon::setTestNow(Carbon::parse('12 Jan 2010 11:11:00'));

    $employee = Employee::factory()->create();

    Schedule::factory()->for($employee)
        ->create([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addYear()->endOfDay(),
        ]);

    $service = Service::factory()->hasAttached($employee)
        ->create(['duration' => 30]);

    // when
    $availability = new ScheduleAvailability($employee, $service)
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    // then
    expect($availability->current())
        ->startsAt(now()->setTimeFromTimeString('12:00:00'))
        ->toBeTrue();
});
