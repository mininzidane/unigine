<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\CurrencyRepository;
use App\Repository\DateValueRepository;
use App\Services\Interfaces\ParserInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class CbrParser implements ParserInterface
{
    private const URL = 'https://www.cbr.ru/scripts/XML_daily.asp';

    private $client;
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;
    /**
     * @var DateValueRepository
     */
    private $dateValueRepository;
    /**
     * @var int
     */
    private $importedValuesCount = 0;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CbrParser constructor.
     * @param \GuzzleHttp\Client $client
     * @param CurrencyRepository $currencyRepository
     * @param DateValueRepository $dateValueRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        \GuzzleHttp\Client $client,
        CurrencyRepository $currencyRepository,
        DateValueRepository $dateValueRepository,
        LoggerInterface $logger
    )
    {
        $this->client = $client;
        $this->currencyRepository = $currencyRepository;
        $this->dateValueRepository = $dateValueRepository;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getParserType(): string
    {
        return self::PARSER_TYPE_CBR;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function parse(): void
    {
        $this->importedValuesCount = 0;

        try {
            $response = $this->client->get(self::URL);
            $body = $response->getBody();
            $xml = new \SimpleXMLElement((string)$body);

        } catch (GuzzleException $e) {
            $this->logger->error("CBR parser guzzle exception: {$e->getMessage()}");
            return;

        } catch (\Throwable $e) {
            $this->logger->error("CBR parser exception: {$e->getMessage()}");
            return;
        }

        $date = new \DateTime((string)$xml->attributes()->Date);
        $data = $this->calculateEuroBasedValues($xml);
        foreach ($data as $code => $row) {
            $currency = $this->currencyRepository->createCurrency($code, $row['name']);
            if ($this->dateValueRepository->saveDateValueForCurrency(
                $currency,
                $date,
                $row['value'],
                $this->getParserType()
            )) {
                $this->importedValuesCount++;
            }
        }
    }

    /**
     * @return int
     */
    public function getImportedValuesCount(): int
    {
        return $this->importedValuesCount;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array [code<string> => ['name' => <string>, 'value' => <float>], ... ]
     */
    private function calculateEuroBasedValues(\SimpleXMLElement $xml): array
    {
        $data = [];
        /** @var $valute
          * <Valute ID="R01010">
          * <NumCode>036</NumCode>
          * <CharCode>AUD</CharCode>
          * <Nominal>1</Nominal>
          * <Name>Австралийский доллар</Name>
          * <Value>43,5373</Value>
          * </Valute>
         */
        foreach ($xml->Valute as $valute) {
            $data[(string)$valute->CharCode] = [
                'name' => (string)$valute->Name,
                'value' => (float)\str_replace(',', '.', $valute->Value) / (int)$valute->Nominal
            ];
        }

        $rubleToEuroMultiplier = $data[self::CODE_EURO]['value'];
        $data = \array_map(function (array $row) use ($rubleToEuroMultiplier) {
            $row['value'] = \round($rubleToEuroMultiplier / $row['value'], 5);
            return $row;
        }, $data);
        $data[self::CODE_RUB] = [
            'name' => 'Рубль',
            'value' => $rubleToEuroMultiplier
        ];

        return $data;
    }
}
