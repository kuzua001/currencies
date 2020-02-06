<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Model;


interface CurrenciesConfigInterface
{
    public function getRefreshPeriod(): int;

    public function getCachePrefix(): string;

    public function getSupportedCurrencies(): array;
}