<?php

namespace App\Data;

use App\Services\Bookings\Date;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript(name: 'Availability')]
final class AvailabilityData extends Data
{
    public function __construct(
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $date,
        #[DataCollectionOf(SlotData::class)]
        public Collection $slots,
    ) {}

    public static function fromDate(Date $date): self
    {
        return new self(date: $date->date->toMutable(), slots: SlotData::collect($date->slots));
    }
}
