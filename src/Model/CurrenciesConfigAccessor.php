<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Model;

use Symfony\Component\Yaml\Yaml;


class CurrenciesConfigAccessor implements CurrenciesConfigInterface
{
    const NODE_REFRESH_PERIOD = 'refresh_period';
    const NODE_CACHE_PREFIX = 'cache_prefix';
    const NODE_SUPPORTED_CURRENCIES = 'supported_currencies';

    const NODE_REFRESH_PERIOD_DEFAULT = 3600;
    const NODE_CACHE_PREFIX_DEFAULT = 'currencies_';
    const NODE_SUPPORTED_CURRENCIES_DEFAULT = [];
    /** @var null|static */
    private static $instance = null;

    /** @var array */
    private $config;

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            $instance = new static();
            $instance->config = $instance->readConfigData();
            static::$instance = $instance;
        }

        return static::$instance;
    }

    public function getRefreshPeriod(): int
    {
        return $this->config[static::NODE_REFRESH_PERIOD] ?? static::NODE_REFRESH_PERIOD_DEFAULT;
    }

    public function getCachePrefix(): string
    {
        return $this->config[static::NODE_CACHE_PREFIX] ?? static::NODE_CACHE_PREFIX_DEFAULT;
    }

    public function getSupportedCurrencies(): array
    {
        return $this->config[static::NODE_SUPPORTED_CURRENCIES] ?? static::NODE_SUPPORTED_CURRENCIES_DEFAULT;
    }

    protected function readConfigData()
    {
        return Yaml::parseFile($this->getConfigFilename());
    }

    private function __construct()
    {
    }

    private function getConfigFilename(): string
    {
        return realpath(__DIR__ . '/../../config/currencies.yaml');
    }
}