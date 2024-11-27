<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Update this to true to allow validation for now.
        // You can add specific authorization logic here later.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id', // Ensure user_id exists in the users table
//            'percentage' => 'required|numeric|min:0|max:100', // Validate percentage within 0-100 range
            'percentage' => [
                'required',
                'regex:/^\d{1,3}(\.\d{1,4})?$/', // Allows numbers with up to 4 decimal places
                'numeric',
                'min:0',
                'max:100',
            ],

        ];
    }
}
