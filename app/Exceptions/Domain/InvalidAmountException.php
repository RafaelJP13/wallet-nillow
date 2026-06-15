<?php

namespace App\Exceptions\Domain;

class InvalidAmountException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'O valor informado deve ser maior que zero.'
        );
    }
}