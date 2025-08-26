<?php

declare(strict_types=1);

namespace App\Services\Bookings;

use App\Models\Employee;
use Carbon\CarbonImmutable;

final class Slot
{
    /** @var Employee[] */
    public array $availableEmployees = [];

    public function __construct(public CarbonImmutable $time) {}

    public function addEmployee(Employee $employee): void
    {
        $this->availableEmployees[] = $employee;
    }

    public function hasEmployees(): bool
    {
        return ! empty($this->availableEmployees);
    }
}
