<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\UpdateBalanceDTO;
use App\DTO\Response\BalanceResponseDTO;
use App\Exception\FinancialException;
use App\Service\WalletService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WalletController extends AbstractController
{
    private WalletService $walletService;
    private LoggerInterface $logger;

    public function __construct(
        WalletService   $walletService,
        LoggerInterface $logger
    )
    {
        $this->walletService = $walletService;
        $this->logger = $logger;
    }

    #[Route('/wallet/update', methods: ['POST'])]
    public function updateBalance(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $dto = new UpdateBalanceDTO(
                walletId: $data['walletId'],
                type: $data['type'],
                amount: $data['amount'],
                currency: $data['currency'],
                reason: $data['reason']
            );

            $this->walletService->updateBalance($dto);

            return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
        } catch (FinancialException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return new JsonResponse(['status' => 'error', 'message' => 'internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/wallet/balance/{walletId}', methods: ['GET'])]
    public function getBalance(int $walletId): JsonResponse
    {
        try {
            $responseDTO = $this->walletService->getBalance($walletId);

            return new JsonResponse(['balance' => $responseDTO->balance], Response::HTTP_OK);
        } catch (FinancialException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return new JsonResponse(['status' => 'error', 'message' => 'internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}