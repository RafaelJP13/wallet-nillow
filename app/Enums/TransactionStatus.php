<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'Pendente';
    case COMPLETED = 'Concluída';
    case REVERSED = 'Estornada';
    case FAILED = 'Falha';
}