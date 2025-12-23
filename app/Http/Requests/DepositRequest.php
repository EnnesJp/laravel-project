<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domains\Transaction\Models\Transaction;
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
            'value' => ['required', 'numeric', 'min:0.01'],
            'payee' => ['required', 'integer'],
            'payer' => ['required', 'integer'],
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
            'value.required' => 'The amount field is required.',
            'value.numeric'  => 'The amount must be a valid number.',
            'value.min'      => 'The amount must be at least 0.01.',
            'payee.required' => 'The payee field is required.',
            'payer.required' => 'The payer field is required.',
        ];
    }

    /**
     * Get the validated data from the request with amount converted to cents.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if ($key === 'value') {
            return (int) round($validated * 100);
        }

        if ($key === null && isset($validated['value'])) {
            $validated['value'] = (int) round($validated['value'] * 100);
        }

        return $validated;
    }
}
