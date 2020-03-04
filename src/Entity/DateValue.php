<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DateValueRepository")
 */
class DateValue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5)
     */
    private $value;

    /**
     * @var Currency
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="values")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency;

    /**
     * @var string
     * @ORM\Column(type="string", name="parser_type")
     */
    private $parserType;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     * @return $this
     */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     * @return DateValue
     */
    public function setCurrency(Currency $currency): DateValue
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getParserType(): string
    {
        return $this->parserType;
    }

    /**
     * @param string $parserType
     * @return DateValue
     */
    public function setParserType(string $parserType): DateValue
    {
        $this->parserType = $parserType;
        return $this;
    }
}
