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
            '_1broker',
            'bter',
            'flowbtc',
            'xbtce',
            'yunbi',
        ],
        'testFetchTrades' => [
            'allcoin',
            'bithumb',
            'bitlish',
            'bitstamp1',
            'btcexchange',
            'btctradeua',
            'btcx',
            'chilebit',
            'coingi',
            'coinspot',
            'gateio',
            'getbtc',
            'foxbit',
            'huobi',
            'huobipro',
            'huobicny',
            'urdubit',
            'jubi',
            'lakebtc',
            'okcoincny',
            'okex',
            'surbitcoin',
            // empty:
            'btcchina',
            'dsx',
            'gatecoin',
            'gdax',
            'livecoin',
            'qryptos',
            'quoine',
            'tidex',
        ],
        'testFetchOrderBook' => [
            'allcoin',
            'anxpro',
            'bithumb',
            'btcchina',
            'bitstamp1',
            'btcexchange',
            'btcx',
            'coingi',
            'coinspot',
            'gdax',
            'getbtc',
            'huobi',
            'huobicny',
            'jubi',
            'okex',
            'okcoincny',
            'virwox',
            'kraken',
            'lakebtc',
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
            $market = array_rand(self::$markets[$exchange->id]);
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
            $market = array_rand(self::$markets[$exchange->id]);
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
