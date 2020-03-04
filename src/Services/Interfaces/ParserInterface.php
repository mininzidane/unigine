<?php
declare(strict_types=1);

namespace App\Services\Interfaces;

interface ParserInterface
{
    public const PARSER_TYPE_CBR = 'CBR';
    public const PARSER_TYPE_ECB = 'ECB';

    public const CODE_EURO = 'EUR';
    public const CODE_RUB = 'RUB';

    /**
     * Parses data from external API to DB storage
     */
    public function parse(): void;

    /**
     * @return string
     */
    public function getParserType(): string;

    /**
     * @return int
     */
    public function getImportedValuesCount(): int;
}
