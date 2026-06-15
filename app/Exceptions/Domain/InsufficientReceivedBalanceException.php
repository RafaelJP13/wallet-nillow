<?php

namespace App\Exceptions\Domain;

class InsufficientReceivedBalanceException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'Saldo recebido insuficiente para reverter esta transação.'
        );
    }
}