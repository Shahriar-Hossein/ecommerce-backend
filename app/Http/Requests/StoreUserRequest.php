<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreUserRequest extends BaseFormRequest
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
            'name' => 'bail|required|max:255',
            'email' => 'bail|required|email:rfc,dns|unique:users|max:255',
            'password' => 'bail|required|min:8|max:255',
            'confirm_password' => 'required|same:password',
        ];
    }
}
