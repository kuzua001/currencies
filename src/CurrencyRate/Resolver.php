<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\CurrencyRate;


use App\Model\CurrenciesConfigInterface;
use App\Model\CurrencyPair;
use App\Model\CurrencyRate;
use App\Model\CurrencyRateInterface;
use App\Model\Exception\CurrencyIsNotSupported;
use App\Model\Exception\CurrencyRateIsNotFound;
use App\Service\CacheInterface;
use App\Service\CurrenciesRatesApiInterface;
use App\Service\CurrenciesRatesRepositoryInterface;

class Resolver
{
    /** @var CurrenciesConfigInterface */
    private $config;

    /** @var CurrenciesRatesRepositoryInterface[] */
    private $sourcecs;

    public function __construct(
        CurrenciesConfigInterface $config,
        CacheInterface $cache,
        CurrenciesRatesRepositoryInterface $database,
        CurrenciesRatesApiInterface $api
    )
    {
        $this->config = $config;
        $this->sourcecs = [
            new Cache($cache, $config->getCachePrefix()),
            $database,
            $api
        ];
    }

    /**
     * @param CurrencyPair $pair
     * @return CurrencyRateInterface|null
     * @throws CurrencyIsNotSupported
     * @throws CurrencyRateIsNotFound
     */
    public function getRate(CurrencyPair $pair): ?CurrencyRateInterface
    {
        $supportedCurrencies = $this->config->getSupportedCurrencies();

        if (!isset($supportedCurrencies[$pair->code])) {
            throw new CurrencyIsNotSupported($pair->code);
        }

        if (!isset($supportedCurrencies[$pair->baseCurrencyCode])) {
            throw new CurrencyIsNotSupported($pair->baseCurrencyCode);
        }

        $timestamp = time();
        /** @var CurrenciesRatesRepositoryInterface[] $outdatedSources */
        $outdatedSources = [];
        /** @var CurrencyRate|null $rate */
        $rate = null;

        foreach ($this->sourcecs as $source) {
            $rate = $source->getCurrencyRate($pair, $timestamp);
            if ($rate !== null) {
                break;
            }
            $outdatedSources[] = $source;
        }

        if ($rate === null) {
            throw new CurrencyRateIsNotFound($pair);
        }

        foreach ($outdatedSources as $source) {
            $source->setCurrencyRate($rate, $rate->getExpiresAt());
        }

        return $rate;
    }
}