<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\DateValue;
use App\Repository\CurrencyRepository;

class ValueConverter
{
    private CurrencyRepository $currencyRepository;

    public function __construct(
        CurrencyRepository $currencyRepository
    )
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function convert(float $value, string $fromCode, string $toCode): float
    {
        $fromValue = $this->currencyRepository->getLastValue($fromCode);
        $toValue = $this->currencyRepository->getLastValue($toCode);

        /** @var DateValue $firstValueFrom */
        if ($fromValue === null || ($firstValueFrom = $fromValue->getValues()->first()) === null) {
            throw new \Exception("No data for currency '{$fromCode}'");
        }
        /** @var DateValue $firstValueTo */
        if ($toValue === null || ($firstValueTo = $toValue->getValues()->first()) === null) {
            throw new \Exception("No data for currency '{$toCode}'");
        }

        $multiplier = $firstValueTo->getValue() / $firstValueFrom->getValue();
        return \round($value * $multiplier, 5);
    }
}
