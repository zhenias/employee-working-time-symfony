<?php

namespace App\Dto\WorkTime;

use Symfony\Component\Validator\Constraints as Assert;

class WorkTimeDto
{
    #[
        Assert\NotBlank,
        Assert\Length(max: 255),
        Assert\Uuid,
    ]
    public string $employeeId;

    #[
        Assert\NotBlank,
        Assert\DateTime(format: 'd.m.Y H:i'),
    ]
    public string $dateTimeStart;

    #[
        Assert\NotBlank,
        Assert\DateTime(format: 'd.m.Y H:i')
    ]
    public string $dateTimeEnd;
}