<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App;

use App\CurrencyRate\Cache as CurrencyCache;
use App\CurrencyRate\Resolver;
use App\Model\CurrenciesConfigAccessor;
use App\Model\CurrencyPair;
use App\Model\CurrencyRate;
use App\Service\CacheInterface;
use App\Service\CurrenciesRatesApiInterface;
use Mocks\Cache;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    private $source = null;

    public function testGetRate()
    {
        $expectedRate = 70;

        $apiRates = [
            'USD' => [
                'value' => $expectedRate,
                'ttl' => 1,
                'base' => 'RUB'
            ]
        ];

        $config = $this->createConfig('currencies');
        $api = $this->createApi($apiRates);
        $database = $this->createDatabase();
        $cache = $this->createCache();

        $resolver = new Resolver($config, $cache, $database, $api);
        $pair = new CurrencyPair();
        $pair->code = 'USD';
        $pair->baseCurrencyCode = 'RUB';

        $rate = $resolver->getRate($pair);

        $this->assertEquals($expectedRate, $rate->getRate());
    }

    private function createCache()
    {
        return $this->getCommonSource();
    }

    private function createApi($rateItems)
    {
        $apiMock = $this->getMockBuilder(CurrenciesRatesApiInterface::class)
            ->onlyMethods(['getCurrencyRate','setCurrencyRate'])
            ->getMock();

        $apiMock->method('getCurrencyRate')
            ->will($this->returnCallback(function() {
                $source = $this->getCommonSource();
                $apiSource = new \App\CurrencyRate\Cache($source, 'api');
                $args = func_get_args();
                return $apiSource->getCurrencyRate(...$args);
            }));
        $apiMock->method('setCurrencyRate')
            ->will($this->returnCallback(function() {
                $source = $this->getCommonSource();
                $apiSource = new \App\CurrencyRate\Cache($source, 'api');
                $args = func_get_args();
                return $apiSource->setCurrencyRate(...$args);
            }));

        foreach ($rateItems as $code => $item) {
            $pair = new CurrencyPair();
            $pair->code = $code;
            $pair->baseCurrencyCode = $item['base'];
            $rate = new CurrencyRate($pair);
            $rate->setRate($item['value']);
            $apiMock->setCurrencyRate($rate, time() + $item['ttl']);
        }

        return $apiMock;
    }

    private function createDatabase()
    {
        return new CurrencyCache($this->getCommonSource(), 'database');
    }

    private function createConfig($configName)
    {
        $configMock = $this->getMockBuilder(CurrenciesConfigAccessor::class)
            ->onlyMethods(['getConfigFilename'])
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->method('getConfigFilename')
            ->willReturn($this->getConfigPath($configName . '.yaml'));

        return $configMock;
    }

    private  function getCommonSource(): CacheInterface
    {
        if ($this->source === null) {
            $source = new Cache();
            $this->source = $source;
        }

        return $this->source;
    }

    private function getConfigPath($fileName)
    {
        return __DIR__ . '/../../providers/' . $fileName;
    }
}