<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript(name: 'Employee')]
final class EmployeeData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        #[MapOutputName('avatar_url')]
        public string $avatarUrl,
    ) {}
}
