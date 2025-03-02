<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $wallet1 = $this->getReference(WalletFixtures::WALLET_REFERENCE_1, Wallet::class);
        $wallet2 = $this->getReference(WalletFixtures::WALLET_REFERENCE_2, Wallet::class);
        $wallet3 = $this->getReference(WalletFixtures::WALLET_REFERENCE_3, Wallet::class);

        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setWallet($wallet1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setWallet($wallet2);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('user3@example.com');
        $user3->setWallet($wallet3);
        $manager->persist($user3);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WalletFixtures::class,
        ];
    }
}