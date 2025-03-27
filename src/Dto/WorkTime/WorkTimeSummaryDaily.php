<?php

namespace App\Dto\WorkTime;

use Symfony\Component\Validator\Constraints as Assert;

class WorkTimeSummaryDaily
{
    #[
        Assert\NotBlank,
        Assert\Length(max: 255),
        Assert\Uuid,
    ]
    public string $employeeId;

    #[
        Assert\NotBlank,
        Assert\DateTime(format: 'd.m.Y'),
    ]
    public string $date;
}