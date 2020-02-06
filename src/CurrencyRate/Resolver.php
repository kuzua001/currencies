<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\CurrencyRate;


use App\Model\CurrenciesConfigInterface;
use App\Model\CurrencyPair;
use App\Model\CurrencyRate;
use App\Model\CurrencyRateInterface;
use App\Service\Cache;
use App\Service\CacheInterface;
use App\Service\CurrenciesRatesApiInterface;
use App\Service\CurrenciesRatesRepositoryInterface;

class Resolver
{
    /** @var CurrenciesRatesRepositoryInterface[] */
    private $sourcecs;

    public function __construct(
        CurrenciesConfigInterface $config,
        CacheInterface $cache,
        CurrenciesRatesRepositoryInterface $database,
        CurrenciesRatesApiInterface $api
    )
    {
        $this->sourcecs = [
            new Cache($cache, $config->getCachePrefix()),
            $database,
            $api
        ];
    }

    public function getRate(CurrencyPair $pair): ?CurrencyRateInterface
    {
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
            return null;
        }

        foreach ($outdatedSources as $source) {
            $source->setCurrencyRate($rate, $rate->getExpiresAt());
        }

        return $rate;
    }
}