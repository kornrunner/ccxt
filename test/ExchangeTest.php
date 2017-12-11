<?php

use ccxt\Exchange;
use PHPUnit\Framework\TestCase;

class ExchangeTest extends TestCase {

    private static $skip = [
        'testFetchTicker' => [
            'ccxt\\bleutrade',
            'ccxt\\btcexchange',
            'ccxt\\bter',
            'ccxt\\ccex',
            'ccxt\\dsx',
            'ccxt\\gateio',
            'ccxt\\jubi',
            'ccxt\\southxchange',
            'ccxt\\qryptos',
            'ccxt\\quoine',
            'ccxt\\xbtce',
            'ccxt\\yunbi',
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
        $class_name = get_class($exchange);
        if (in_array($class_name, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$class_name}: fetch ticker skipped");
        }

        switch ($class_name) {
            case 'ccxt\\gdax':
                $exchange->urls['api'] = 'https://api-public.sandbox.gdax.com';
                break;
        }

        $delay = $exchange->rateLimit * 1000;
        usleep($delay);

        if ($exchange->hasFetchTickers) {
            $exchange->timeout = 30000;
            $tickers = $exchange->fetch_tickers();
            $this->assertNotEmpty($tickers);
        } else {
            $this->assertFalse($exchange->hasFetchTickers);
        }
    }

    public static function getExchangeClasses(): array {
        $classes = [];
        foreach (Exchange::$exchanges as $name) {
            $class = "ccxt\\{$name}";
            $classes[] = [new $class];
        }
        return $classes;
    }
}
