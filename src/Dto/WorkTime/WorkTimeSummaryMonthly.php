<?php

namespace App\Dto\WorkTime;

use Symfony\Component\Validator\Constraints as Assert;

class WorkTimeSummaryMonthly
{
    #[
        Assert\NotBlank,
        Assert\Length(max: 255),
        Assert\Uuid,
    ]
    public string $employeeId;

    #[
        Assert\NotBlank,
        Assert\DateTime(format: 'm.Y'),
    ]
    public string $date;
}