<?php
declare(strict_types=1);

namespace App\Controller\API;

use App\Services\Validators\CurrencyValidator;
use App\Services\ValueConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConvertController extends AbstractController
{
    /**
     * @var ValueConverter
     */
    private $valueConverter;
    /**
     * @var CurrencyValidator
     */
    private $currencyValidator;

    /**
     * ConvertController constructor.
     * @param ValueConverter $valueConverter
     * @param CurrencyValidator $currencyValidator
     */
    public function __construct(
        ValueConverter $valueConverter,
        CurrencyValidator $currencyValidator
    )
    {
        $this->valueConverter = $valueConverter;
        $this->currencyValidator = $currencyValidator;
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return JsonResponse
     */
    public function convert(string $from, string $to, float $amount): JsonResponse
    {
        $success = $this->currencyValidator->validate($from);
        $errors = $this->currencyValidator->getErrors();
        $success &= $this->currencyValidator->validate($to);
        $success = (bool)$success;
        $errors = \array_merge($errors, $this->currencyValidator->getErrors());
        if ($success === false) {
            return $this->json([
                'success' => false,
                'errors' => $errors
            ]);
        }

        try {
            $value = $this->valueConverter->convert($amount, $from, $to);

        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }

        return $this->json(\compact('success', 'from', 'to', 'value'));
    }
}
