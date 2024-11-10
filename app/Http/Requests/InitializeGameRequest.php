<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitializeGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'config' => 'sometimes|array',
            'config.*' => 'sometimes|string|numeric|boolean|array',
        ];
    }
}