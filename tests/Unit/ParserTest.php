<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\CbrParser;
use App\Services\EcbParser;
use App\Services\Interfaces\ParserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ParserTest extends KernelTestCase
{
    /**
     * @var CbrParser
     */
    private $cbrParser;
    /**
     * @var EcbParser
     */
    private $ecbParser;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected function setUp(): void
    {
        parent::setUp();
        static::bootKernel();
        $this->cbrParser = static::$kernel->getContainer()->get('cbr_parser');
        $this->ecbParser = static::$kernel->getContainer()->get('ecb_parser');
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        parent::tearDown();
    }

    public function testCbrParse(): void
    {
        $this->cbrParser->parse();
        self::assertEquals(ParserInterface::PARSER_TYPE_CBR, $this->cbrParser->getParserType());
        self::assertGreaterThan(0, $this->cbrParser->getImportedValuesCount());
    }

    public function testEcbParse(): void
    {
        $this->ecbParser->parse();
        self::assertEquals(ParserInterface::PARSER_TYPE_ECB, $this->ecbParser->getParserType());
        self::assertGreaterThan(0, $this->ecbParser->getImportedValuesCount());
    }
}
