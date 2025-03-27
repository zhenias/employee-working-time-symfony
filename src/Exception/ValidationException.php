<?php

namespace App\Exception;

use AllowDynamicProperties;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[AllowDynamicProperties]
class ValidationException extends HttpException
{
    public function __construct(ConstraintViolationListInterface $violations)
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'invalidValue' => $violation->getInvalidValue()
            ];
        }

        parent::__construct(400, 'Validation failed', null, [], 0);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}