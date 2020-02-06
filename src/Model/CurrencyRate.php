<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Model;


class CurrencyRate implements CurrencyRateInterface
{
    /** @var CurrencyPair */
    private $pair;

    /** @var float */
    private $rate = 1;

    /** @var null|int */
    private $expiresAt = null;

    public function __construct(CurrencyPair $pair, ?int $expiresAt = null)
    {
        $this->pair = $pair;
        $this->expiresAt = $expiresAt;
    }

    public function getPair(): CurrencyPair
    {
        return $this->pair;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    public function getCode(): string
    {
        return $this->pair->code;
    }

    public function getBaseCurrencyCode(): string
    {
        return $this->pair->baseCurrencyCode;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate)
    {
        $this->rate = $rate;
    }
}