<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\CurrencyRepository;
use App\Repository\DateValueRepository;
use App\Services\Interfaces\ParserInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class EcbParser implements ParserInterface
{
    private const URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * @var int
     */
    private $importedValuesCount = 0;
    /**
     * @var \GuzzleHttp\Client
     */
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EcbParser constructor.
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

        $date = new \DateTime((string)$xml->Cube->Cube->attributes()->time);
        $data = $this->prepareData($xml);
        foreach ($data as $code => $row) {
            $currency = $this->currencyRepository->createCurrency($code);
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

    public function getParserType(): string
    {
        return self::PARSER_TYPE_ECB;
    }

    public function getImportedValuesCount(): int
    {
        return $this->importedValuesCount;
    }

    private function prepareData(\SimpleXMLElement $xml): array
    {
        $data = $xml->Cube->Cube->Cube;
        /** @var $row
         * <Cube currency="USD" rate="1.1117"/>
         */

        $result = [];
        foreach ($data as $row) {
            $attributes = $row->attributes();
            $result[(string)$attributes->currency] = [
                'value' => (float)$attributes->rate,
            ];
        }

        return $result;
    }
}
