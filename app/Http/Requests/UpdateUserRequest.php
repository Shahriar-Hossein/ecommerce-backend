<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateUserRequest extends BaseFormRequest
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
            'first_name' => 'bail|sometimes|nullable|string|max:255',
            'last_name' => 'bail|sometimes|nullable|string|max:255',
            'contact_no' => 'bail|sometimes|nullable|numeric|digits:11',
            'date_of_birth' => 'bail|sometimes|nullable|date',
            'gender' => 'bail|sometimes|nullable|in:Male,Female,Other',
            'address' => 'bail|sometimes|nullable|string|max:255',
            'email' => 'bail|sometimes|required|email:rfc,dns|unique:users|max:255',
            'password' => 'bail|sometimes|required|min:8|max:255',
            'new_password' => 'bail|sometimes|required|min:8|max:255',
            'confirm_password' => 'bail|sometimes|required|min:8|max:255|same:new_password',
        ];
    }
}
