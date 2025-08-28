<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EmployeeData;
use App\Data\ServiceData;
use App\Models\Employee;
use App\Models\Service;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __invoke(Service $service, Employee $employee): Response
    {
        return inertia()->render('checkout/Index', [
            'service' => ServiceData::from($service),
            'employee' => EmployeeData::from($employee),
        ]);
    }
}
