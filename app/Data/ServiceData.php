<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript(name: 'Service')]
final class ServiceData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $duration,
        public string $price,
    ) {}
}
