<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domains\User\Enums\UserRole;
use App\Rules\DocumentRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $options = implode(',', UserRole::values());

        return [
            'name'     => ['required', 'string', 'max:255'],
            'document' => [
                'required',
                'string',
                new DocumentRule(),
                'unique:users,document',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => ['required', 'string', 'in:'.$options],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string'   => 'The name must be a string.',
            'name.max'      => 'The name may not be greater than 255 characters.',

            'document.required' => 'The document field is required.',
            'document.unique'   => 'This document is already registered.',

            'email.required' => 'The email field is required.',
            'email.email'    => 'The email must be a valid email address.',
            'email.unique'   => 'This email is already registered.',

            'password.required' => 'The password field is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'document' => preg_replace('/[^0-9]/', '', $this->document ?? ''),
        ]);
    }
}
