<?php

declare(strict_types=1);

namespace App\DTO\Request;

final readonly class UpdateBalanceDTO
{
    public function __construct(
        public int    $walletId,
        public string $type,
        public string $amount,
        public string $currency,
        public string $reason
    )
    {
    }
}