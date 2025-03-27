<?php

namespace App\Controller\Employee;

use App\Service\Employee\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employees', name: 'app_employee', format: 'json')]
final class EmployeeController extends AbstractController
{
    public function __construct(private EmployeeService $employeeService)
    {
    }

    #[Route(name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $employee = $this->employeeService->create($request);

        return $this->json([
            'response' => [
                'id' => $employee->getId()
            ],
        ]);
    }
}
