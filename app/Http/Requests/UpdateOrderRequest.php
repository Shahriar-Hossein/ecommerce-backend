<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\ValidationRule;

class UpdateOrderRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'bail|sometimes|required|string|in:pending,completed,cancelled',
            'contact_no' => 'bail|sometimes|required|string|max:255',
            'total_price' => 'bail|sometimes|required|numeric',
        ];
    }
}
