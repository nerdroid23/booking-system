<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EmployeeData;
use App\Data\ServiceData;
use App\Models\Employee;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeShowController extends Controller
{
    public function __invoke(Employee $employee): Response
    {
        $services = $employee->services()->get();

        return Inertia::render('employees/Show', [
            'employee' => EmployeeData::from($employee),
            'services' => ServiceData::collect($services),
        ]);
    }
}
