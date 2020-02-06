<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Model\Exception;


class CurrencyIsNotSupported extends \Exception
{
    public function __construct($code)
    {
        parent::__construct(sprintf('Currency "%s" is not supported.', $code));
    }
}