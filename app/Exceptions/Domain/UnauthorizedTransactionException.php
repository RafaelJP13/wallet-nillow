<?php

namespace App\Exceptions\Domain;

class UnauthorizedTransactionException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'Você não possui permissão para executar esta operação.'
        );
    }
}