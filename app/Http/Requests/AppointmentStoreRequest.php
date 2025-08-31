<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],
            'service_id' => [
                'required',
                'exists:services,id',
            ],
            'datetime' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'after:now',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
        ];
    }
}
