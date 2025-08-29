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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __invoke(Request $request, Service $service, ?Employee $employee = null): Response
    {
        $employees = $employee ? collect([$employee]) : $service->employees;

        /** @var DateCollection $availability */
        $availability = new ServiceSlotAvailability(employees: $employees, service: $service)
            ->forPeriod(
                start: Carbon::createFromDate($request->query('start')),
                end: Carbon::createFromDate($request->query('start'))->endOfMonth()
            )
            ->hasSlots()
            ->values();

        return inertia()->render('checkout/Index', [
            'service' => ServiceData::from($service),
            'employee' => $employee ? EmployeeData::from($employee) : null,
            // TODO: Use deferred
            // 'availability' => inertia()->defer(fn () => AvailabilityData::collect($availability)),
            'availability' => AvailabilityData::collect($availability),
            'date' => $availability->firstAvailableDate()?->date->toDateString(),
            'start' => $request->query('start'),
        ]);
    }
}
