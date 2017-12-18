<?php

use ccxt\Exchange;
use VCR\VCR;
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
            'bter',     // array issue @63
            'flowbtc',  // bad offset in response
            'yunbi',    // not accessible
            'bitso',    // not accessible
            'kraken',   // timeout
            'xbtce',    // travis
        ],
        'testFetchBalance' => [
            'anxpro',       // not accessible
            'binance',      // bad apiKey
            'bitcoincoid',  // nonce
            'btctradeua',   // not accessible
            'bitfinex',     // nonce
            'bitfinex2',    // not accessible
            'bitlish',      // not accessible
            'btcx',         // bad offset in response
            'bter',         // not countable @63
            'bxinth',       // no apiKey
            'chbtc',        // bad offset in response
            'coingi',       // not accessible
            'coincheck',    // bad apiKey
            'dsx',          // bad apiKey
            'flowbtc',      // bad offset in response
            'huobi',        // bad apiKey
            'huobipro',     // bad apiKey
            'jubi',         // not accessible
            'liqui',        // null instead of array
            'okcoinusd',    // not accessible
            'okex',         // not accessible
            'southxchange', // not countable @71
            'virwox',       // no method parameter?!
            'yobit',        // bad apiKey
            'zaif',         // signature mismatch
            'fybse',        // travis
            'virwox',       // travis
            'vaultoro',     // travis
            'bitso',        // travis
            'bitbay',       // empty response
            'bitmarket',    // undefined index data
            'btcmarkets',   // bad timestamp
        ],
        'testFetchTrades' => [
            'bitcoincoid',  // not accessible
            'bitstamp1',    // array to string @142
            'btcexchange',  // bad offset in response
            'btctradeua',   // array issue @206
            'btcx',         // bad offset in response
            'coincheck',    // supports BTC/JPY only
            'coingi',       // not accessible
            'huobi',        // not accessible
            'huobicny',     // bad offset in response
            'jubi',         // not accessible
            'kraken',       // bad offset in response
            'okcoincny',    // not accessible
            'bit2c',        // travis kills
            'fybse',        // travis kills
            // empty response:
            'btcchina',
            'livecoin',
            'paymium',
            'xbtce',
        ],
        'testFetchOrderBook' => [
            'anxpro',       // not accessible
            'bitcoincoid',  // not accessible
            'bitstamp1',    // array to string @74
            'btcexchange',  // bad offset in response
            'btcx',         // bad offset in response
            'coincheck',    // supports BTC/JPY only
            'coingi',       // not accessible
            'huobi',        // not accessible
            'huobicny',     // bad offset in response
            'jubi',         // not accessible
            'kraken',       // string instead of array @336
            'okcoincny',    // not accessible
            'virwox',       // not implemented
            'xbtce',        // travis
        ],
        'testFetchOHLCV' => [
            'btcexchange',  // bad offset in response
            'huobi',        // not accessible
            'okcoincny',    // not accessible
            'huobicny',     // empty response
            'poloniex',     // travis
            'bitlish',      // travis
            'allcoin',      // travis
        ],
    ];

/**
 * https://api-public.sandbox.gdax.com
 * https://crossorigin.me/
 */
    private static $proxy = [
        'ccex' => 'https://cors-anywhere.herokuapp.com/',
        'allcoin' => 'https://cors-anywhere.herokuapp.com/',      // not accessible
    ];

    private static $api_url = [
        'gdax' => 'https://api-public.sandbox.gdax.com',
    ];

    private static $config = [];

    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        VCR::turnOff();
    }

    public function setUp() {
        parent::setUp();

        $keys_global = __DIR__ . '/keys.dist.json';
        $keys_local = __DIR__ . '/keys.json';
        $keys_file = file_exists($keys_local) ? $keys_local : $keys_global;
        self::$config = json_decode(file_get_contents ($keys_file), true);
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testDescribe(string $name) {
        $exchange = self::exchangeFactory($name);
        $this->assertArrayHasKey('name', $exchange->describe());
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testFetchTicker(string $name) {
        $exchange = self::exchangeFactory($name);
        if (in_array($exchange->id, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$exchange->id}: fetch ticker skipped");
        }

        if ($exchange->hasFetchTickers) {
            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $tickers = $exchange->fetch_tickers();
            VCR::eject();
            $this->assertNotEmpty($tickers);

            $ticker = current($tickers);
            $this->assertArrayHasKey('symbol', $ticker);
            $this->assertArrayHasKey('baseVolume', $ticker);
            $this->assertArrayHasKey('info', $ticker);
        } else {
            $this->assertFalse($exchange->hasFetchTickers);
        }
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testLoadMarkets(string $name) {
        $exchange = self::exchangeFactory($name);
        if (in_array($exchange->id, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$exchange->id}: load markets skipped");
        }

        VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
        $markets = $exchange->load_markets();
        VCR::eject();
        $this->assertNotEmpty($markets);
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testFetchBalance(string $name) {
        $exchange = self::exchangeFactory($name);
        if (in_array($exchange->id, self::$skip[__FUNCTION__])) {
            return $this->markTestSkipped("{$exchange->id}: fetch balance skipped");
        }

        if (!empty($exchange->apiKey)) {
            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $balance = $exchange->fetch_balance();
            VCR::eject();
            $this->assertNotEmpty($balance);
        } else {
            $this->assertEmpty($exchange->apiKey);
        }
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testFetchTrades(string $name) {
        $exchange = self::exchangeFactory($name);
        if (in_array($exchange->id, array_merge(self::$skip[__FUNCTION__], self::$skip['testLoadMarkets']))) {
            return $this->markTestSkipped("{$exchange->id}: fetch trades skipped");
        }

        if ($exchange->hasFetchTrades) {
            VCR::insertCassette('testLoadMarkets@' . $exchange->id . '.json');
            $markets = $exchange->load_markets();
            VCR::eject();
            $market = current($markets);

            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $trades = $exchange->fetch_trades($market);
            VCR::eject();
            $this->assertNotEmpty($trades);
        } else {
            $this->assertFalse($exchange->hasFetchTrades);
        }
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testFetchOrderBook(string $name) {
        $exchange = self::exchangeFactory($name);
        if (in_array($exchange->id, array_merge(self::$skip[__FUNCTION__], self::$skip['testLoadMarkets']))) {
            return $this->markTestSkipped("{$exchange->id}: fetch order book skipped");
        }

        if ($exchange->hasFetchOrderBook) {
            VCR::insertCassette('testLoadMarkets@' . $exchange->id . '.json');
            $markets = $exchange->load_markets();
            VCR::eject();
            $market = current($markets);

            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $order_book = $exchange->fetch_order_book($market);
            VCR::eject();
            $this->assertNotEmpty($order_book);
        } else {
            $this->assertFalse($exchange->hasFetchOrderBook);
        }
    }

    /**
     * @dataProvider getExchangeClasses
     */
    public function testFetchOHLCV(string $name) {
        $exchange = self::exchangeFactory($name);
        if (in_array($exchange->id, array_merge(self::$skip[__FUNCTION__], self::$skip['testLoadMarkets']))) {
            return $this->markTestSkipped("{$exchange->id}: fetch OHLCV skipped");
        }

        if ($exchange->hasFetchOHLCV) {
            VCR::insertCassette('testLoadMarkets@' . $exchange->id . '.json');
            $markets = $exchange->load_markets();
            VCR::eject();
            $market = current($markets);

            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $ohlcv = $exchange->fetch_ohlcv($market);
            VCR::eject();
            $this->assertNotEmpty($ohlcv);
        } else {
            $this->assertFalse($exchange->hasFetchOHLCV);
        }
    }

    private static function exchangeFactory(string $class): Exchange {
        $exchange = new $class;
        $exchange->timeout = 15000;

        if (array_key_exists($exchange->id, self::$api_url)) {
            $exchange->urls['api'] = self::$api_url[$exchange->id];
        }

        if (array_key_exists($exchange->id, self::$proxy)) {
            $exchange->proxy = self::$proxy[$exchange->id];
        }

        if (array_key_exists($exchange->id, self::$config)) {
            $params = self::$config[$exchange->id];

            foreach($params as $key => $value) {
                $exchange->{$key} = $value;
            }
        }

        return $exchange;
    }

    public static function getExchangeClasses(): array {
        $classes = [];
        foreach (Exchange::$exchanges as $name) {
            $classes[] = ["ccxt\\{$name}"];
        }
        return $classes;
    }
}
