<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayOfWeek;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_date',
            'ends_at' => 'immutable_date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkingHoursForDate(CarbonImmutable $date): ?array
    {
        $hours = match ($date->dayOfWeek) {
            DayOfWeek::Sunday->value => [$this->sunday_starts_at, $this->sunday_ends_at],
            DayOfWeek::Monday->value => [$this->monday_starts_at, $this->monday_ends_at],
            DayOfWeek::Tuesday->value => [$this->tuesday_starts_at, $this->tuesday_ends_at],
            DayOfWeek::Wednesday->value => [$this->wednesday_starts_at, $this->wednesday_ends_at],
            DayOfWeek::Thursday->value => [$this->thursday_starts_at, $this->thursday_ends_at],
            DayOfWeek::Friday->value => [$this->friday_starts_at, $this->friday_ends_at],
            DayOfWeek::Saturday->value => [$this->saturday_starts_at, $this->saturday_ends_at],
            default => [null, null],
        };

        $hours = array_filter($hours);

        return empty($hours) ? null : $hours;
    }
}
