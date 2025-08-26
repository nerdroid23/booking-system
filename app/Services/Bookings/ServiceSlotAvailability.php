<?php

declare(strict_types=1);

namespace App\Services\Bookings;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;

final readonly class ServiceSlotAvailability
{
    public function __construct(private Collection $employees, private Service $service) {}

    public function forPeriod(Carbon $start, Carbon $end): DateCollection
    {
        // TODO: fix slot not starting at the beginning of the period
        // e.g. service duration is 45 minutes, if there's an exclusion that ends at 14:00,
        // the next available slot should be 14:00, not 14:15

        $timeSlots = new TimeSlotGenerator($start, $end)->generate($this->service->duration);

        $this->employees->each(function (Employee $employee) use ($start, $end, &$timeSlots) {
            // get employee availability for the given period
            $periods = new ScheduleAvailability($employee, $this->service)
                ->forPeriod(start: $start, end: $end);

            // remove appointments from the period collection
            $periods = $this->removeAppointmentsFromPeriods($employee, $periods);

            // add available employees to the slots
            foreach ($periods as $period) {
                $this->addAvailableEmployeeForPeriod($employee, $period, $timeSlots);
            }
        });

        // remove empty slots
        $timeSlots = $this->removeEmptySlots($timeSlots);

        return $timeSlots;
    }

    private function addAvailableEmployeeForPeriod(Employee $employee, Period $period, Collection $timeSlots): void
    {
        $timeSlots->each(function (Date $date) use ($period, $employee) {
            $date->slots->each(function (Slot $slot) use ($period, $employee) {
                if ($period->contains($slot->time)) {
                    $slot->addEmployee($employee);
                }
            });
        });
    }

    private function removeEmptySlots(DateCollection $timeSlots): DateCollection
    {
        return $timeSlots->filter(function (Date $date) {
            $date->slots = $date->slots->filter(function (Slot $slot) {
                return $slot->hasEmployees();
            })->values();

            return true;
        });
    }

    private function removeAppointmentsFromPeriods(Employee $employee, PeriodCollection $periods): PeriodCollection
    {
        $employee->appointments->whereNull('canceled_at')
            ->each(function (Appointment $appointment) use (&$periods) {
                $periods = $periods->subtract(
                    Period::make(
                        $appointment->starts_at->subMinutes($this->service->duration)->addMinute(),
                        $appointment->ends_at,
                        Precision::MINUTE(),
                        Boundaries::EXCLUDE_ALL(),
                    )
                );
            });

        return $periods;
    }
}
