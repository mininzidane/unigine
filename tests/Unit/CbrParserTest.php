<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\CbrParser;
use PHPUnit\Framework\TestCase;

class CbrParserTest extends TestCase
{
    public function testParse(CbrParser $parser): void
    {
        $parser->parse();
    }
}
