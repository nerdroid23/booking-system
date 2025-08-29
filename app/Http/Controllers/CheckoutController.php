<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\AvailabilityData;
use App\Data\EmployeeData;
use App\Data\ServiceData;
use App\Models\Employee;
use App\Models\Service;
use App\Services\Bookings\DateCollection;
use App\Services\Bookings\ServiceSlotAvailability;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __invoke(Service $service, ?Employee $employee = null): Response
    {
        $employees = $employee ? collect([$employee]) : $service->employees;

        /** @var DateCollection $availability */
        $availability = new ServiceSlotAvailability(employees: $employees, service: $service)
            ->forPeriod(start: now()->startOfDay(), end: now()->addMonth()->endOfDay())
            ->hasSlots()
            ->values();

        return inertia()->render('checkout/Index', [
            'service' => ServiceData::from($service),
            'employee' => $employee ? EmployeeData::from($employee) : null,
            'availability' => AvailabilityData::collect($availability),
            'date' => $availability->firstAvailableDate()?->date->toDateString(),
        ]);
    }
}
