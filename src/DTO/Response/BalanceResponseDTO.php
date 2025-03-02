<?php

declare(strict_types=1);

namespace App\DTO\Response;

final readonly class BalanceResponseDTO
{
    public function __construct(public string $balance)
    {
    }
}