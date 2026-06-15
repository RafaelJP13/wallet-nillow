<?php

namespace App\Exceptions\Domain;

class TransactionAlreadyReversedException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'A transação já foi revertida.'
        );
    }
}