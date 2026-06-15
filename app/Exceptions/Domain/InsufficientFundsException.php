<?php

namespace App\Exceptions\Domain;

class InsufficientFundsException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'Saldo insuficiente para realizar a operação.'
        );
    }
}