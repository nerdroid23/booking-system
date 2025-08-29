<?php

namespace App\Data;

use App\Services\Bookings\Slot;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript(name: 'Slot')]
final class SlotData extends Data
{
    public function __construct(
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d H:i:s')]
        public CarbonImmutable $datetime,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'H:i')]
        public CarbonImmutable $time,
        /** @var array<int, int> $employees */
        public array $employees,
    ) {}

    public static function fromSlot(Slot $slot): self
    {
        return new self(
            datetime: $slot->time,
            time: $slot->time,
            employees: $slot->employees->pluck('id')->all(),
        );
    }
}
