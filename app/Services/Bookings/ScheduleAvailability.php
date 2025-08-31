<?php

declare(strict_types=1);

namespace App\Services\Bookings;

use App\Models\Employee;
use App\Models\ScheduleExclusion;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriodImmutable;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;

final class ScheduleAvailability
{
    public function __construct(
        private readonly Employee $employee,
        private readonly Service $service,
        private PeriodCollection $periods = new PeriodCollection,
    ) {}

    public function forPeriod(Carbon $start, Carbon $end): PeriodCollection
    {
        collect(CarbonPeriodImmutable::create($start, $end)->floorDays())
            ->each($this->addAvailabilityFromSchedule(...));

        return $this->periods;
    }

    private function addAvailabilityFromSchedule(CarbonImmutable $date): void
    {
        if ($date->isPast()) {
            return;
        }

        $schedule = $this->employee->schedules
            ->where('starts_at', '<=', $date)
            ->where('ends_at', '>=', $date)
            ->first();

        if (! $schedule) {
            return;
        }

        if (! [$startsAt, $endsAt] = $schedule->getWorkingHoursForDate($date)) {
            return;
        }

        $this->periods = $this->periods->add(
            Period::make(
                $date->setTimeFromTimeString($startsAt),
                $date->setTimeFromTimeString($endsAt)
                    ->subMinutes($this->service->duration),
                Precision::MINUTE(),
            )
        );

        $this->employee->scheduleExclusions
            ->each($this->subtractScheduleExclusion(...));

        $this->subtractTimePassedToday();
    }

    private function subtractScheduleExclusion(ScheduleExclusion $exclusion): void
    {
        $this->periods = $this->periods->subtract(
            Period::make(
                $exclusion->starts_at,
                $exclusion->ends_at,
                Precision::MINUTE(),
                Boundaries::EXCLUDE_END(),
            )
        );
    }

    private function subtractTimePassedToday(): void
    {
        $this->periods = $this->periods->subtract(
            Period::make(
                now()->startOfDay(),
                now()->endOfHour(),
                Precision::MINUTE(),
                Boundaries::EXCLUDE_START(),
            )
        );
    }
}
