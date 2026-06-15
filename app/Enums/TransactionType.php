<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposito';
    case WITHDRAW = 'saque';
    case TRANSFER = 'transferência';
    case REVERSAL = 'estorno';
}