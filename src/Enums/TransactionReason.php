<?php

namespace App\Enums;

enum TransactionReason: string
{
    case STOCK = 'stock';
    case REFUND = 'refund';
    case BONUS = 'bonus';
    case FEE = 'fee';
    case TRANSFER = 'transfer';
}
