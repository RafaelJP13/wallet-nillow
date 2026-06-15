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
}