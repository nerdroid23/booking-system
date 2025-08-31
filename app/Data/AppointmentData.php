<?php

namespace App\Data;

use App\Models\Appointment;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('Appointment')]
class AppointmentData extends Data
{
    public function __construct(
        public string $uuid,
        public EmployeeData $employee,
        public ServiceData $service,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'F d, Y')]
        public Carbon $date,
        #[MapOutputName('starts_at')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'H:i')]
        public Carbon $startsAt,
        #[MapOutputName('ends_at')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'H:i')]
        public Carbon $endsAt,
        public bool $canceled,
    ) {}

    public static function fromModel(Appointment $appointment): self
    {
        return new self(
            uuid: $appointment->uuid,
            employee: EmployeeData::from($appointment->employee),
            service: ServiceData::from($appointment->service),
            date: $appointment->starts_at->toMutable(),
            startsAt: $appointment->starts_at->toMutable(),
            endsAt: $appointment->ends_at->toMutable(),
            canceled: (bool) $appointment->canceled_at,
        );
    }
}
