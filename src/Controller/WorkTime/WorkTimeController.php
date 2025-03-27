<?php

namespace App\Controller\WorkTime;

use App\Service\WorkTime\WorkTimeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/workTime', name: 'app_work_time', format: 'json')]
final class WorkTimeController extends AbstractController
{
    public function __construct(private WorkTimeService $workTimeService)
    {
    }

    #[Route(name: 'create', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $this->workTimeService->create($request);

        return $this->json([
            'response' => ['Work time is created!'],
        ]);
    }

    #[Route('/dailySummary', name: 'daily_summary', methods: ['GET'])]
    public function dailySummary(Request $request): JsonResponse
    {
        $workTimeSummaryDaily = $this->workTimeService->calculateDailySummary($request);

        return $this->json([
            'response' => $workTimeSummaryDaily,
        ]);
    }

    #[Route('/monthlySummary', name: 'monthly_summary', methods: ['GET'])]
    public function monthlySummary(Request $request): JsonResponse
    {
        $workTimeSummaryMonthly = $this->workTimeService->calculateMonthlySummary($request);

        return $this->json([
            'response' => $workTimeSummaryMonthly,
        ]);
    }
}
