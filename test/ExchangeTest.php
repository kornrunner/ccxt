<?php

use ccxt\Exchange;
use PHPUnit\Framework\TestCase;

class ExchangeTest extends TestCase {

    private static $skip = [
        'testFetchTicker' => [
            'bleutrade',
            'btcexchange',
            'bter',
            'ccex',
            'dsx',
            'gateio',
            'jubi',
            'southxchange',
            'qryptos',
            'quoine',
            'xbtce',
            'yunbi',
        ],
        'testLoadMarkets' => [
            '_1broker',
            'bter',
            'flowbtc',
            'xbtce',
            'yunbi',
        ],
    ];

    /**
     * @dataProvider getExchangeClasses
     */
    public function testDescribe($exchange) {
        $this->assertArrayHasKey('name', $exchange->describe());
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testFetchTicker($exchange) {
        if (in_array($exchange->id, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$exchange->id}: fetch ticker skipped");
        }

        $delay = $exchange->rateLimit * 1000;
        usleep($delay);

        if ($exchange->hasFetchTickers) {
            $tickers = $exchange->fetch_tickers();
            $this->assertNotEmpty($tickers);
        } else {
            $this->assertFalse($exchange->hasFetchTickers);
        }
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testLoadMarkets($exchange) {
        if (in_array($exchange->id, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$exchange->id}: load markets skipped");
        }

        $tickers = $exchange->load_markets();
        $this->assertNotEmpty($tickers);
    }

    public static function getExchangeClasses(): array {
        $classes = [];
        foreach (Exchange::$exchanges as $name) {
            $class = "ccxt\\{$name}";
            $exchange = new $class;
            $exchange->timeout = 15000;

            if ($name === 'gdax') {
                $exchange->urls['api'] = 'https://api-public.sandbox.gdax.com';
            }

            $classes[] = [$exchange];
        }
        return $classes;
    }
}
