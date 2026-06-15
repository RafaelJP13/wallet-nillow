<?php

namespace App\Exceptions\Domain;

class WalletNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'Carteira não encontrada.'
        );
    }
}