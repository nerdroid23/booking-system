<?php

declare(strict_types=1);

namespace App\Services\Bookings;

use Carbon\Carbon;
use Carbon\CarbonPeriodImmutable;

final readonly class TimeSlotGenerator
{
    public function __construct(
        private Carbon $start,
        private Carbon $end,
    ) {}

    public function generate(int $interval): DateCollection
    {
        $collection = new DateCollection;

        $days = CarbonPeriodImmutable::create($this->start, '1 day', $this->end);

        foreach ($days as $day) {
            $date = new Date($day);

            $slots = CarbonPeriodImmutable::create(
                $day->startOfDay(),
                "{$interval} minutes",
                $day->endOfDay(),
            );

            foreach ($slots as $slot) {
                $date->addSlot(new Slot($slot));
            }

            $collection->push($date);
        }

        return $collection;
    }
}
