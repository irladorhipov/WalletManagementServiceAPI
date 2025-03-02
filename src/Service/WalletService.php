<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Request\UpdateBalanceDTO;
use App\DTO\Response\BalanceResponseDTO;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Exception\FinancialException;

class WalletService
{
    private EntityManagerInterface $em;
    private WalletRepository $walletRepository;
    private LoggerInterface $logger;
    private TransactionService $transactionService;
    private CurrencyConverterService $currencyConverterService;
    private WalletBalanceService $walletBalanceService;

    public function __construct(
        EntityManagerInterface   $em,
        WalletRepository         $walletRepository,
        LoggerInterface          $logger,
        TransactionService       $transactionService,
        CurrencyConverterService $currencyConverterService,
        WalletBalanceService     $walletBalanceService
    )
    {
        $this->em = $em;
        $this->walletRepository = $walletRepository;
        $this->logger = $logger;
        $this->transactionService = $transactionService;
        $this->currencyConverterService = $currencyConverterService;
        $this->walletBalanceService = $walletBalanceService;
    }

    /**
     * @param UpdateBalanceDTO $dto
     * @return void
     * @throws FinancialException
     * @throws \Throwable
     */
    public function updateBalance(UpdateBalanceDTO $dto): void
    {
        $this->em->beginTransaction();

        try {
            $wallet = $this->walletRepository->find($dto->walletId);

            if (!$wallet instanceof Wallet) {
                $this->logger->error("Кошелек не найден", ['walletId' => $dto->walletId]);
                throw new FinancialException("Кошелек не найден");
            }

            $convertAmount = $dto->amount;

            if ($dto->currency !== $wallet->getCurrency()) {
                $convertAmount = $this->currencyConverterService->convertCurrency($dto->amount, $dto->currency, $wallet->getCurrency());
                $this->logger->info("Конвертация валюты", [
                    'amount' => $dto->amount,
                    'fromCurrency' => $dto->currency,
                    'toCurrency' => $wallet->getCurrency(),
                    'convertedAmount' => $convertAmount
                ]);
            }

            $this->walletBalanceService->updateBalance($wallet, $convertAmount, $wallet->getCurrency(), $dto->type);

            $transaction = $this->transactionService->createTransaction(
                wallet: $wallet,
                type: $dto->type,
                amount: $convertAmount,
                currency: $dto->currency,
                reason: $dto->reason
            );

            $this->em->commit();

            $this->logger->info("Транзакция успешно создана", [
                'transactionId' => $transaction->getId(),
                'walletId' => $dto->walletId,
                'type' => $dto->type,
                'amount' => $convertAmount,
                'currency' => $dto->currency,
                'reason' => $dto->reason
            ]);

        } catch (\Throwable $e) {
            $this->em->rollback();
            $this->logger->error("Ошибка при обновлении баланса: " . $e->getMessage(), [
                'walletId' => $dto->walletId,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * @param int $walletId
     * @return BalanceResponseDTO
     * @throws FinancialException
     */
    public function getBalance(int $walletId): BalanceResponseDTO
    {
        $wallet = $this->walletRepository->find($walletId);

        if (!$wallet) {
            $this->logger->error("Кошелек не найден", ['walletId' => $walletId]);
            throw new FinancialException("Кошелек не найден");
        }

        $this->logger->info("Запрос баланса", [
            'walletId' => $walletId,
            'balance' => $wallet->getBalance()
        ]);

        return new BalanceResponseDTO($wallet->getBalance());
    }
}