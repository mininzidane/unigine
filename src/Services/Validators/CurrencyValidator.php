<?php
declare(strict_types=1);

namespace App\Services\Validators;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

class CurrencyValidator
{
    private array $errors = [];

    public function validate(string $currency): bool
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($currency, [
            new Length(['min' => 3, 'max' => 3]),
        ]);

        $success = true;
        $this->errors = [];
        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $this->errors[] = "{$violation->getInvalidValue()}: {$violation->getMessage()}";
            $success = false;
        }

        return $success;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
