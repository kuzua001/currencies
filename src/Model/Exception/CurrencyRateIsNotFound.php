<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Model\Exception;


use App\Model\CurrencyPair;

class CurrencyRateIsNotFound extends \Exception
{
    public function __construct(CurrencyPair $pair)
    {
        parent::__construct(sprintf('Currency rate "%s" to "%s" is not found.', $pair->code, $pair->baseCurrencyCode));
    }
}