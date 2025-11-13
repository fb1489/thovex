<?php declare(strict_types=1);

namespace App\Validation;

class RequestValidationResponse
{
    public function __construct(
        private array $listOfErrors,
    ) {}

    public function isValid(): bool
    {
        return count($this->listOfErrors) === 0;
    }

    public function errors(): array
    {
        return $this->listOfErrors;
    }
}