<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_wallet_id' => ['nullable', 'integer', 'exists:wallets,id'],
            'to_wallet_id'   => ['required', 'integer', 'exists:wallets,id'],
            'type'           => ['required', 'string', 'in:deposit,withdraw,transfer,reversal'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'description'    => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_wallet_id.required' => 'A carteira de destino é obrigatória.',
            'to_wallet_id.exists' => 'A carteira de destino não existe.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.min' => 'O valor mínimo é 0.01.',
            'type.in' => 'O tipo de transação é inválido.',
        ];
    }
}