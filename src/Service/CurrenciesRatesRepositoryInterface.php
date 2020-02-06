<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Service;
use App\Model\CurrencyPair;
use App\Model\CurrencyRateInterface;

interface CurrenciesRatesRepositoryInterface
{
    public function getCurrencyRate(CurrencyPair $pair, int $timestamp): ?CurrencyRateInterface;

    public function setCurrencyRate(CurrencyRateInterface $rate, int $expiresAt): bool;
}