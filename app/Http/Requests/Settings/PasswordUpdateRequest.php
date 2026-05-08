<?php

namespace App\Http\Requests\Settings;

use App\Concerns\PasswordValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ];
    }
    public function messages(): array
    {
        return [
            'current_password.required' => 'The current password field is required.',
            'current_password.string' => 'The current password field must be a string.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password field must be a string.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
