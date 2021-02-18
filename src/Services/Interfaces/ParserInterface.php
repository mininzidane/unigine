<?php
declare(strict_types=1);

namespace App\Services\Interfaces;

interface ParserInterface
{
    public const PARSER_TYPE_CBR = 'CBR';
    public const PARSER_TYPE_ECB = 'ECB';

    /**
     * Parses data from external API to DB storage
     */
    public function parse(): void;

    public function getParserType(): string;

    public function getImportedValuesCount(): int;
}
