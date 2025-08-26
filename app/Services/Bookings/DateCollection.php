<?php

declare(strict_types=1);

namespace App\Services\Bookings;

use Illuminate\Support\Collection;

/** @extends Collection<int, Date> */
class DateCollection extends Collection
{
    public function firstAvailableDate(): ?Date
    {
        return $this->firstWhere(fn (Date $date) => $date->slots->count() >= 1);
    }
}
