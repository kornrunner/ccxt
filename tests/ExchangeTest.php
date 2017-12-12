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
            'coingi',
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
            '_1broker', // apiKey required
            'bter',     // array issue @63
            'ccex',     // not accessible
            'flowbtc',  // bad offset in response
            'gdax',     // UserAgent is required
            'xbtce',    // apiKey required
            'yunbi',    // not accessible
        ],
        'testFetchTrades' => [
            'allcoin',      // not accessible
            'bitcoincoid',  // not accessible
            'bitstamp1',    // array to string @142
            'btcexchange',  // bad offset in response
            'btctradeua',   // array issue @206
            'btcx',         // bad offset in response
            'coincheck',    // supports BTC/JPY only
            'coingi',       // not accessible
            'coinspot',     // apiKey required
            'huobi',        // not accessible
            'huobicny',     // bad offset in response
            'jubi',         // not accessible
            'kraken',       // bad offset in response
            'okcoincny',    // not accessible
            // empty response:
            'btcchina',
            'livecoin',
        ],
        'testFetchOrderBook' => [
            'allcoin',      // not accessible
            'anxpro',       // not accessible
            'bitcoincoid',  // not accessible
            'bitstamp1',    // array to string @74
            'bittrex',      // null in Exchange @959
            'btcexchange',  // bad offset in response
            'btcx',         // bad offset in response
            'coincheck',    // supports BTC/JPY only
            'coingi',       // not accessible
            'coinspot',     // apiKey required
            'huobi',        // not accessible
            'huobicny',     // bad offset in response
            'jubi',         // not accessible
            'kraken',       // string instead of array @336
            'okcoincny',    // not accessible
            'virwox',       // not implemented
        ],
    ];

    private static $config = [];
    private static $markets = [];

    public static function setUpBeforeClass () {
        $keys_global = __DIR__ . '/keys.dist.json';
        $keys_local = __DIR__ . '/keys.json';
        $keys_file = file_exists ($keys_local) ? $keys_local : $keys_global;
        self::$config = json_decode (file_get_contents ($keys_file), true);
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testDescribe($exchange) {
        $this->assertArrayHasKey('name', $exchange->describe());
    }

    /**
     * @dataProvider getExchangeClasses
     * @vcr fetchTicker
     */
    public function testFetchTicker($exchange) {
        if (in_array($exchange->id, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$exchange->id}: fetch ticker skipped");
        }

        if ($exchange->hasFetchTickers) {
            $tickers = $exchange->fetch_tickers();
            $this->assertNotEmpty($tickers);
        } else {
            $this->assertFalse($exchange->hasFetchTickers);
        }
    }

    /**
     * @dataProvider getExchangeClasses
     * @vcr loadMarkets
     */
    public function testLoadMarkets($exchange) {
        if (in_array($exchange->id, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$exchange->id}: load markets skipped");
        }

        $markets = $exchange->load_markets();
        $this->assertNotEmpty($markets);
        self::$markets[$exchange->id] = $markets;
    }

    /**
     * @dataProvider getExchangeClasses
     * @depends testLoadMarkets
     * @vcr fetchTrades
     */
    public function testFetchTrades($exchange) {
        if (in_array($exchange->id, self::$skip[__FUNCTION__]) || !array_key_exists($exchange->id, self::$markets)) {
            return $this->markTestSkipped("{$exchange->id}: fetch trades skipped");
        }

        if ($exchange->hasFetchTrades) {
            $market = current(self::$markets[$exchange->id]);
            $trades = $exchange->fetch_trades($market);
            $this->assertNotEmpty($trades);
        } else {
            $this->assertFalse($exchange->hasFetchTrades);
        }
    }

    /**
     * @dataProvider getExchangeClasses
     * @depends testLoadMarkets
     * @vcr fetchOrderBook
     */
    public function testFetchOrderBook($exchange) {
        if (in_array($exchange->id, self::$skip[__FUNCTION__]) || !array_key_exists($exchange->id, self::$markets)) {
            return $this->markTestSkipped("{$exchange->id}: fetch fetch order book skipped");
        }

        if ($exchange->hasFetchOrderBook) {
            $market = current(self::$markets[$exchange->id]);
            $order_book = $exchange->fetch_order_book($market);
            $this->assertNotEmpty($order_book);
        } else {
            $this->assertFalse($exchange->hasFetchOrderBook);
        }
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

            if (array_key_exists($exchange->id, self::$config)) {
                $params = self::$config[$exchange->id];

                foreach($params as $key => $value) {
                    $exchange->key = $value;
                }
            }

            $classes[] = [$exchange];
        }
        return $classes;
    }
}
