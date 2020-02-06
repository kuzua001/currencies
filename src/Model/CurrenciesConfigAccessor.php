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

    /** @var array */
    private $config = null;

    public function getRefreshPeriod(): int
    {
        return $this->getConfig()[static::NODE_REFRESH_PERIOD] ?? static::NODE_REFRESH_PERIOD_DEFAULT;
    }

    public function getCachePrefix(): string
    {
        return $this->getConfig()[static::NODE_CACHE_PREFIX] ?? static::NODE_CACHE_PREFIX_DEFAULT;
    }

    public function getSupportedCurrencies(): array
    {
        $supportedCurrencies = $this->getConfig()[static::NODE_SUPPORTED_CURRENCIES] ?? static::NODE_SUPPORTED_CURRENCIES_DEFAULT;
        $supportedCurrenciesHashMap = array_fill_keys($supportedCurrencies, true);

        return $supportedCurrenciesHashMap;
    }

    protected function getConfigFilename(): string
    {
        return realpath(__DIR__ . '/../../config/currencies.yaml');
    }

    private function readConfigData()
    {
        return Yaml::parseFile($this->getConfigFilename());
    }

    private function getConfig()
    {
        if ($this->config === null) {
            $this->config = $this->readConfigData();
        }

        return $this->config;
    }
}