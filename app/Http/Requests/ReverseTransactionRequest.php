<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReverseTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => ['required', 'integer', 'exists:transactions,id'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required' => 'O ID da transação é obrigatório.',
            'transaction_id.exists' => 'A transação informada não existe.',
        ];
    }
}