<?php

namespace App\Service\Employee;

use App\Dto\Employee\EmployeeDto;
use App\Entity\Employee\Employee;
use App\Exception\ValidationException;
use App\Repository\Employee\EmployeeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class EmployeeService
{
    public function __construct(
        private EmployeeRepository  $employeeRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator
    ) { }

    public function create(Request $request): Employee
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), EmployeeDto::class, 'json');
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid JSON.');
        }
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $employee = new Employee();
        $employee->setUserName($dto->firstName.' '.$dto->lastName);

        $this->employeeRepository->add($employee);

        return $employee;
    }
}