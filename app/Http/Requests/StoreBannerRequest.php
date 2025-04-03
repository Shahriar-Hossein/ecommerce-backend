<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreBannerRequest extends BaseFormRequest
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
            'title' => 'bail|required|string|max:255',
            'subtitle' => 'bail|required|string|max:255',
            'description' => 'bail|required|string',
            'image' => 'bail|required|image|mimes:png,jpg,jpeg,webp',
            'button_url' => 'bail|required|string|max:255',
            'button_text' => 'bail|required|string|max:255',
        ];
    }
}
