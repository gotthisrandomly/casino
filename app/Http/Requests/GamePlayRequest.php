<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GamePlayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|string|in:bet,state',
            'amount' => 'required_if:action,bet|numeric|min:0',
            'bet_data' => 'sometimes|array',
            'bet_data.*' => 'sometimes|string|numeric|boolean|array',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'The action field is required.',
            'action.in' => 'Invalid action. Allowed actions are: bet, state',
            'amount.required_if' => 'The amount field is required when placing a bet.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be greater than or equal to 0.',
        ];
    }
}