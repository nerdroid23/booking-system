<?php

declare(strict_types=1);

namespace App\Services\Bookings;

use App\Models\Employee;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

final class Slot
{
    /** @var Collection<int, Employee> */
    public Collection $employees;

    public function __construct(public CarbonImmutable $time)
    {
        $this->employees = new Collection;
    }

    public function addEmployee(Employee $employee): void
    {
        $this->employees->push($employee);
    }

    public function hasEmployees(): bool
    {
        return $this->employees->isNotEmpty();
    }
}
