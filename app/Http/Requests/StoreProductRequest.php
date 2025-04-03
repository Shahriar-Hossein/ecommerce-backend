<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreProductRequest extends BaseFormRequest
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
            'title' => 'bail|required|unique:products|string|max:255',
            'images.*' => 'bail|required|image|mimes:png,jpg,jpeg|max:2048',
            'stock_quantity' => 'bail|required|numeric|integer',
            'price' => 'bail|required|numeric|integer',
            'currency' => 'bail|required|string|max:10',
            'trending' => 'bail|nullable|boolean',
            'description' => 'bail|required|string',
        ];
    }
}
