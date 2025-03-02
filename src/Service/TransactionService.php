<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Enums\TransactionReason;
use App\Enums\TransactionType;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Wallet $wallet
     * @param string $type
     * @param string $amount
     * @param string $currency
     * @param string $reason
     * @return Transaction
     */
    public function createTransaction(
        Wallet $wallet,
        string $type,
        string $amount,
        string $currency,
        string $reason
    ): Transaction
    {
        $transaction = (new Transaction())
            ->setWallet($wallet)
            ->setType($type)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setReason(TransactionReason::from($reason))
            ->setCreatedAt(new \DateTime());

        $this->em->persist($transaction);
        $this->em->flush();

        return $transaction;
    }
}