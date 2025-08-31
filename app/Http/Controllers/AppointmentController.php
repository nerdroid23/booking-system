<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\AppointmentData;
use App\Http\Requests\AppointmentStoreRequest;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use App\Services\Bookings\ServiceSlotAvailability;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function show(Appointment $appointment): Response
    {
        $appointment->load('employee', 'service');

        return inertia()->render('appointments/Show', [
            'appointment' => AppointmentData::from($appointment),
        ]);
    }

    public function store(AppointmentStoreRequest $request): RedirectResponse
    {
        $data = $request->only(['employee_id', 'service_id', 'name', 'email']);
        $startsAt = $request->date('datetime')->toImmutable();

        $employee = Employee::query()->find($data['employee_id']);
        $service = Service::query()->find($request->input('service_id'));

        $availability = new ServiceSlotAvailability(employees: collect([$employee]), service: $service)
            ->forPeriod(
                start: $startsAt->startOfDay()->toMutable(),
                end: $startsAt->endOfDay()->toMutable()
            );

        if (! $availability->firstAvailableDate()->containsSlot($startsAt->toTimeString())) {
            return back()->withErrors(['datetime' => 'The selected time is no longer available. Please choose another time.']);
        }

        $appointment = Appointment::query()
            ->create(array_merge($data, [
                'starts_at' => $startsAt,
                'ends_at' => $startsAt->addMinutes($service->duration),
            ]));

        return to_route('appointments.show', $appointment->uuid);
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->update([
            'canceled_at' => now(),
        ]);

        return to_route('appointments.show', $appointment->uuid);
    }
}
