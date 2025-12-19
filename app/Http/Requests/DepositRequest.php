<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('deposit', Transaction::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'integer', 'min:1'],
            'payee' => ['required', 'integer', 'exists:users,id', 'different:payer'],
            'payer' => ['required', 'integer', 'exists:users,id', 'different:payee'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'value.required'  => 'The amount field is required.',
            'value.integer'   => 'The amount must be an integer.',
            'value.min'       => 'The amount must be at least 1.',
            'payee.required'  => 'The payee field is required.',
            'payee.exists'    => 'The selected payee user does not exist.',
            'payee.different' => 'The payee must be different from the payer.',
            'payer.required'  => 'The payer field is required.',
            'payer.exists'    => 'The selected payer user does not exist.',
            'payer.different' => 'The payer must be different from the payee.',
        ];
    }
}
