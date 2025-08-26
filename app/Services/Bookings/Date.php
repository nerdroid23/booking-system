<?php

declare(strict_types=1);

namespace App\Services\Bookings;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class Date
{
    /** @var Collection<int, Slot> */
    public Collection $slots;

    public function __construct(public CarbonImmutable $date)
    {
        $this->slots = collect();
    }

    public function addSlot(Slot $slot): void
    {
        $this->slots->push($slot);
    }
}
