<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\CurrencyRate;

use App\Model\CurrencyPair;
use App\Model\CurrencyRate;
use App\Model\CurrencyRateInterface;
use App\Service\CacheInterface;
use App\Service\CurrenciesRatesRepositoryInterface;

class Cache implements CurrenciesRatesRepositoryInterface
{
    /** @var CacheInterface */
    private $cache;

    /** @var string */
    private $prefix;

    public function __construct(CacheInterface $cache, string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function getCurrencyRate(CurrencyPair $pair, int $timestamp): ?CurrencyRateInterface
    {
        $key = $this->formatKey($pair);
        $expiresAt = $this->cache->expiresAt($key);

        if ($expiresAt > $timestamp) {
            $rateValue = (float)$this->cache->get($key);
            $rate = new CurrencyRate($pair);
            $rate->setRate($rateValue);

            return $rate;
        }

        return null;
    }

    public function setCurrencyRate(CurrencyRateInterface $rate, int $expiresAt): bool
    {
        $key = $this->formatKey($rate->getPair());
        $ttl = $rate->getExpiresAt() - $expiresAt;
        $this->cache->set($key, $rate->getRate(), $ttl);
    }

    private function formatKey(CurrencyPair $pair)
    {
        return sprintf('%s_%s_to_%s', $this->prefix, $pair->code, $pair->baseCurrencyCode);
    }
}