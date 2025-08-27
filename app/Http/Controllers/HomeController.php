<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EmployeeData;
use App\Data\ServiceData;
use App\Models\Employee;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class HomeController
{
    public function __invoke(): Response
    {
        $employees = Employee::query()->orderBy('name')->get();
        $services = Service::query()->orderBy('title')->get();

        return Inertia::render('Welcome', [
            'employees' => EmployeeData::collect($employees),
            'services' => ServiceData::collect($services),
        ]);
    }
}
