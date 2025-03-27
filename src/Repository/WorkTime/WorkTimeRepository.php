<?php

namespace App\Repository\WorkTime;

use App\Entity\Employee\Employee;
use App\Entity\WorkTime\WorkTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkTime>
 */
class WorkTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $em)
    {
        parent::__construct($registry, WorkTime::class);
    }

    public function add(WorkTime $entity, bool $flush = true): void
    {
        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }
    }

    public function existsForEmployeeOnDate(Employee $employee, \DateTimeInterface $date): bool
    {
        return null !== $this->findOneBy(['employee' => $employee, 'date' => $date]);
    }

    public function findWorkTimeBetweenDates(string $employeeId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('ws')
            ->andWhere('ws.employee = :employeeId')
            ->andWhere('ws.dateTimeStart >= :start')
            ->andWhere('ws.dateTimeEnd <= :end')
            ->setParameter('employeeId', $employeeId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();
    }
}
