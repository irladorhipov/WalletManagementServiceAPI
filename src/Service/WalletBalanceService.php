<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Wallet;
use App\Enums\TransactionType;
use App\Exception\FinancialException;
use Money\Money;
use Money\Currency;

class WalletBalanceService
{
    /**
     * @param Wallet $wallet
     * @param string $amount
     * @param string $currency
     * @param string $type
     * @return void
     * @throws FinancialException
     */
    public function updateBalance(Wallet $wallet, string $amount, string $currency, string $type): void
    {
        $balance = new Money($wallet->getBalance(), new Currency($wallet->getCurrency()));
        $amount = new Money($amount, new Currency($currency));

        if ($type === TransactionType::DEBIT->value) {
            if ($balance->lessThan($amount)) {
                throw new FinancialException("Недостаточно средств на счете");
            }
            $newBalance = $balance->subtract($amount);
        } elseif ($type === TransactionType::CREDIT->value) {
            $newBalance = $balance->add($amount);
        } else {
            throw new FinancialException("Недопустимый тип транзакции");
        }

        $wallet->setBalance($newBalance->getAmount());
    }
}