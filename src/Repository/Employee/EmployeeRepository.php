<?php

namespace App\Repository\Employee;

use App\Entity\Employee\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $em)
    {
        parent::__construct($registry, Employee::class);
    }

    public function add(Employee $entity, bool $flush = true): void
    {
        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }
    }
}
