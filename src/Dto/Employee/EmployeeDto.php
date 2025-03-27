<?php

namespace App\Dto\Employee;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

class EmployeeDto
{
    #[
        Assert\NotBlank,
        Assert\Length(max: 126),
        Assert\Type(Types::STRING)
    ]
    public string $firstName;

    #[
        Assert\NotBlank,
        Assert\Length(max: 126),
        Assert\Type(Types::STRING)
    ]
    public string $lastName;
}