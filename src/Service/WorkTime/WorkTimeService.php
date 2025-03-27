<?php

namespace App\Service\WorkTime;

use App\Dto\WorkTime\WorkTimeDto;
use App\Dto\WorkTime\WorkTimeSummaryDaily;
use App\Dto\WorkTime\WorkTimeSummaryMonthly;
use App\Entity\WorkTime\WorkTime;
use App\Exception\ValidationException;
use App\Repository\Employee\EmployeeRepository;
use App\Repository\WorkTime\WorkTimeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class WorkTimeService
{
    public function __construct(
        private WorkTimeRepository  $workTimeRepository,
        private EmployeeRepository  $employeeRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator
    ) { }

    public function create(Request $request): WorkTime
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(false), WorkTimeDto::class, 'json');
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid JSON.');
        }
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $employee = $this->employeeRepository->findOneBy([
            'id' => $dto->employeeId,
        ]);

        if (!$employee) {
            throw new BadRequestHttpException('User not found.');
        }

        $dateTimeStart = \DateTime::createFromFormat('d.m.Y H:i', $dto->dateTimeStart);
        $dateTimeEnd = \DateTime::createFromFormat('d.m.Y H:i', $dto->dateTimeEnd);

        match (true) {
            $dateTimeStart >= $dateTimeEnd => throw new BadRequestHttpException('End time must be after start time.'),
            $dateTimeStart->diff($dateTimeEnd)->h > 12 => throw new BadRequestHttpException('Work time cannot exceed 12 hours.'),
            $this->workTimeRepository->existsForEmployeeOnDate($employee, $dateTimeStart) => throw new ConflictHttpException('Work time already exists for this date.'),
            default => null
        };

        $workTime = new WorkTime();
        $workTime->setEmployee($employee);
        $workTime->setDateTimeStart($dateTimeStart);
        $workTime->setDateTimeEnd($dateTimeEnd);
        $workTime->setDate($dateTimeStart);

        $this->workTimeRepository->add($workTime);

        return $workTime;
    }

    private function roundHours(int $minutes): float
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes <= 15) {
            return $hours;
        } elseif ($remainingMinutes <= 45) {
            return $hours + 0.5;
        } else {
            return $hours + 1;
        }
    }

    public function calculateDailySummary(Request $request): array
    {
        parse_str($request->getQueryString(), $queryParams);

        $dto = $this->serializer->deserialize(json_encode($queryParams), WorkTimeSummaryDaily::class, 'json');
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $employee = $this->employeeRepository->findOneBy([
            'id' => $dto->employeeId,
        ]);

        if (!$employee) {
            throw new BadRequestHttpException('User not found.');
        }

        $workTimes = $this->workTimeRepository->findBy([
            'employee' => $employee,
            'date' => \DateTime::createFromFormat('d.m.Y', $dto->date),
        ]);

        $totalMinutes = 0;
        foreach ($workTimes as $workTime) {
            $diff = $workTime->getDateTimeStart()->diff($workTime->getDateTimeEnd());

            $totalMinutes += ($diff->h * 60) + $diff->i;
        }

        $totalHours = $this->roundHours($totalMinutes);
        $rate = $_ENV['RATE'];
        $total = $totalHours * $rate;

        return [
            'totalHours' => $totalHours,
            'totalAmount' => $total . ' PLN',
            'hourlyRate' => $rate . ' PLN',
        ];
    }

    public function calculateMonthlySummary(Request $request): array
    {
        parse_str($request->getQueryString(), $queryParams);

        $dto = $this->serializer->deserialize(json_encode($queryParams), WorkTimeSummaryMonthly::class, 'json');
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $employee = $this->employeeRepository->findOneBy([
            'id' => $dto->employeeId,
        ]);

        if (!$employee) {
            throw new BadRequestHttpException('User not found.');
        }

        [$month, $year] = explode('.', $dto->date);
        $startDate = new \DateTime("01.$month.$year");
        $endDate = (clone $startDate)->modify('last day of this month');

        $workTimes = $this->workTimeRepository->findWorkTimeBetweenDates(
            $dto->employeeId,
            $startDate,
            $endDate
        );

        $normalRate = $_ENV['RATE'];
        $overtimeRate = $normalRate * ($_ENV['OVERTIME_RATE_PERCENT'] / 100);
        $monthlyLimit = $_ENV['NORMAL_MONTHLY_HOURS'];

        $totalNormalHours = 0;
        $totalOvertimeHours = 0;

        foreach ($workTimes as $workTime) {
            $diff = $workTime->getDateTimeStart()->diff($workTime->getDateTimeEnd());
            $minutes = ($diff->h * 60) + $diff->i;
            $hours = $this->roundHours($minutes);

            if ($totalNormalHours + $hours <= $monthlyLimit) {
                $totalNormalHours += $hours;
            } else {
                $overtimeHours = ($totalNormalHours + $hours) - $monthlyLimit;
                $totalNormalHours = $monthlyLimit;
                $totalOvertimeHours += $overtimeHours;
            }
        }

        $normalSalary = $totalNormalHours * $normalRate;
        $overtimeSalary = $totalOvertimeHours * $overtimeRate;
        $totalSalary = $normalSalary + $overtimeSalary;

        return [
            'totalNormalHours' => $totalNormalHours,
            'normalRate' => "$normalRate PLN",
            'totalOvertimeHours' => $totalOvertimeHours,
            'overtimeRate' => "$overtimeRate PLN",
            'totalSalary' => "$totalSalary PLN"
        ];
    }
}