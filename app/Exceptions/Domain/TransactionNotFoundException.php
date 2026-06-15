<?php

namespace App\Exceptions\Domain;

class TransactionNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'Transação não encontrada.'
        );
    }
}