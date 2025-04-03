<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateProductRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'title' => 'bail|sometimes|required|unique:products|string|max:255',
            'images.*' => 'bail|sometimes|required|image|mimes:png,jpg,jpeg|max:2048',
            'stock_quantity' => 'bail|sometimes|required|numeric|integer',
            'price' => 'bail|sometimes|required|numeric|integer',
            'currency' => 'bail|sometimes|required|string|max:10',
            'trending' => 'bail|sometimes|nullable|boolean',
            'description' => 'bail|sometimes|required|string',
        ];
    }
}
