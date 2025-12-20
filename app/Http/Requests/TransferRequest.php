<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domains\Transaction\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('transfer', Transaction::class);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'integer'],
            'payee' => ['required', 'integer'],
            'payer' => ['required', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'value.required' => 'The amount field is required.',
            'value.integer'  => 'The amount must be an integer.',
            'payee.required' => 'The payee field is required.',
            'payer.required' => 'The payer field is required.',
        ];
    }
}
