<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domains\User\Enums\UserRole;
use App\Rules\DocumentRule;
use App\ValueObjects\Document\Factory\DocumentFactory;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Foundation\Http\FormRequest;

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
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
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

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        $validated['document'] = DocumentFactory::create($validated['document']);
        $validated['email']    = Email::fromString($validated['email']);
        $validated['password'] = Password::fromString($validated['password']);

        return $validated;
    }
}
