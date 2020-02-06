<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Model\Exception;


class MethodNotApplicable extends \Exception
{
    public function __construct($method, $class)
    {
        parent::__construct(sprintf('Method "%s" call is not applicable for repository "%s".', $method, $class));
    }
}