<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\CurrencyRepository;

class ValueConverter
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * ValueConverter constructor.
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(
        CurrencyRepository $currencyRepository
    )
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @param float $value
     * @param string $fromCode
     * @param string $toCode
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function convert(float $value, string $fromCode, string $toCode): float
    {
        $fromValue = $this->currencyRepository->getLastValue($fromCode);
        $toValue = $this->currencyRepository->getLastValue($toCode);

        if ($fromValue === null || empty($fromValue->getValues())) {
            throw new \Exception("No data for currency '{$fromCode}'");
        }
        if ($toValue === null || empty($toValue->getValues())) {
            throw new \Exception("No data for currency '{$toCode}'");
        }

        $multiplier = $toValue->getValues()[0]->getValue() / $fromValue->getValues()[0]->getValue();
        return \round($value * $multiplier, 5);
    }
}
