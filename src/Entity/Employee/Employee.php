<?php

namespace App\Entity\Employee;

use App\Repository\Employee\EmployeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[
    ORM\Entity(repositoryClass: EmployeeRepository::class),
    ORM\Table(name: 'employees')
]
class Employee
{
    #[
        ORM\Id,
        ORM\GeneratedValue(strategy: 'NONE'),
        ORM\Column(name: 'id', type: Types::STRING, length: 255, unique: true)
    ]
    private string $id;

    #[ORM\Column(name: 'user_name', type: Types::STRING, length: 255)]
    private string $userName;

    public function __construct()
    {
        $this->id = Uuid::v1();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }
}
