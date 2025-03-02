<?php

namespace App\DataFixtures;

use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WalletFixtures extends Fixture
{
    public const WALLET_REFERENCE_1 = 'wallet-reference-1';
    public const WALLET_REFERENCE_2 = 'wallet-reference-2';
    public const WALLET_REFERENCE_3 = 'wallet-reference-3';

    public function load(ObjectManager $manager): void
    {
        $wallet1 = new Wallet();
        $wallet1->setCurrency('USD');
        $wallet1->setBalance('1000.00');
        $manager->persist($wallet1);
        $this->addReference(self::WALLET_REFERENCE_1, $wallet1);

        $wallet2 = new Wallet();
        $wallet2->setCurrency('RUB');
        $wallet2->setBalance('500.00');
        $manager->persist($wallet2);
        $this->addReference(self::WALLET_REFERENCE_2, $wallet2);

        $wallet3 = new Wallet();
        $wallet3->setCurrency('USD');
        $wallet3->setBalance('750.00');
        $manager->persist($wallet3);
        $this->addReference(self::WALLET_REFERENCE_3, $wallet3);

        $manager->flush();
    }
}