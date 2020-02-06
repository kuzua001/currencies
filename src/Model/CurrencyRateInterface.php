<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Model;


interface CurrencyRateInterface
{
    public function getCode(): string;

    public function getBaseCurrencyCode(): string;

    public function getRate(): float;

    public function getPair(): CurrencyPair;

    public function getExpiresAt(): ?int;
}