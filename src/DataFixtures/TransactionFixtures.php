<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Enums\TransactionReason;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $wallet1 = $this->getReference(WalletFixtures::WALLET_REFERENCE_1, Wallet::class);
        $wallet2 = $this->getReference(WalletFixtures::WALLET_REFERENCE_2, Wallet::class);
        $wallet3 = $this->getReference(WalletFixtures::WALLET_REFERENCE_3, Wallet::class);

        $this->createTransaction($manager, $wallet1, 'deposit', '100.00', 'USD', TransactionReason::TRANSFER);
        $this->createTransaction($manager, $wallet1, 'withdraw', '50.00', 'USD', TransactionReason::BONUS);
        $this->createTransaction($manager, $wallet2, 'deposit', '200.00', 'RUB', TransactionReason::REFUND);
        $this->createTransaction($manager, $wallet2, 'withdraw', '100.00', 'RUB', TransactionReason::TRANSFER);
        $this->createTransaction($manager, $wallet3, 'deposit', '150.00', 'USD', TransactionReason::TRANSFER);
        $this->createTransaction($manager, $wallet3, 'withdraw', '75.00', 'RUB', TransactionReason::REFUND);

        $manager->flush();
    }

    private function createTransaction(ObjectManager $manager, Wallet $wallet, string $type, string $amount, string $currency, TransactionReason $reason): void
    {
        $transaction = new Transaction();
        $transaction->setWallet($wallet);
        $transaction->setType($type);
        $transaction->setAmount($amount);
        $transaction->setCurrency($currency);
        $transaction->setReason($reason);
        $transaction->setCreatedAt(new \DateTime());
        $manager->persist($transaction);
    }

    public function getDependencies(): array
    {
        return [
            WalletFixtures::class,
        ];
    }
}