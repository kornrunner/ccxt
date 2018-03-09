<?php

use ccxt\Exchange;
use VCR\VCR;
use PHPUnit\Framework\TestCase;

class ExchangeTest extends TestCase {

    private static $skip = [
        'testFetchTicker' => [
            'bibox',
            'bleutrade',
            'btcexchange',
            'coinexchange',
            'bter',
            'ccex',
            'coingi',
            'dsx',
            'jubi',
            'southxchange',
            'qryptos',
            'quoine',
            'xbtce',
            'yunbi',
            'tidex',
            'yobit',
            'gatecoin',
        ],
        'testLoadMarkets' => [
            '_1broker', // requires secret
            'bibox',    // bad apiKey
            'bter',     // bad response
            'coinexchange',  // bad offset in response
            'flowbtc',  // bad offset in response
            'yunbi',    // not accessible
            'bitso',    // not accessible
            'allcoin',  // not accessible
            'kraken',   // timeout
            'xbtce',    // travis
            'coinegg',  // failed for btc?
        ],
        'testFetchBalance' => [
            '_1broker',     // requires secret
            'anxpro',       // not accessible
            'binance',      // bad apiKey
            'bibox',        // bad apiKey
            'bitcoincoid',  // nonce
            'btctradeua',   // not accessible
            'bitfinex',     // nonce
            'bitfinex2',    // not accessible
            'bitlish',      // not accessible
            'bittrex',      // invalid apiKey
            'bitmarket',    // not accessible
            'btcx',         // bad offset in response
            'bter',         // bad response
            'bit2c',        // not accessible
            'bxinth',       // no apiKey
            'cex',          // no apiKey
            'chbtc',        // bad offset in response
            'coingi',       // not accessible
            'coincheck',    // bad apiKey
            'coinspot',     // not accessible
            'coinsecure',   // requires secret
            'dsx',          // bad apiKey
            'flowbtc',      // bad offset in response
            'huobi',        // bad apiKey
            'huobipro',     // bad apiKey
            'hitbtc',       // bad apiKey
            'liqui',        // bad apiKey
            'jubi',         // not accessible
            'okcoinusd',    // not accessible
            'okex',         // not accessible
            'southxchange', // bad response
            'virwox',       // no method parameter?!
            'yobit',        // bad apiKey
            'zaif',         // signature mismatch
            'fybse',        // travis
            'virwox',       // travis
            'vaultoro',     // travis
            'bitso',        // travis
            'xbtce',        // travis
            'bitbay',       // empty response
            'btcmarkets',   // bad timestamp
            'coinmate'      // bad offset in response
        ],
        'testFetchTrades' => [
            '_1broker',     // not implemeneted
            'anxpro',       // not implemeneted
            'coinmarketcap', // not implemeneted
            'lykke',        // not implemeneted
            'bitcoincoid',  // not accessible
            'btcexchange',  // bad offset in response
            'bibox',        // bad apiKey
            'bter',         // bad response
            'btcx',         // bad offset in response
            'coincheck',    // not accessible
            'coinegg',      // no balance?
            'coingi',       // not accessible
            'huobi',        // not accessible
            'huobicny',     // bad offset in response
            'jubi',         // not accessible
            'kraken',       // bad offset in response
            'okcoincny',    // not accessible
            'bit2c',        // travis kills
            'fybse',        // travis kills
            'allcoin',      // not accessible
            // empty response:
            'bitstamp1',
            'btcchina',
            'coinsecure',
            'livecoin',
            'paymium',
            'xbtce',
            'virwox',
        ],
        'testFetchOrderBook' => [
            '_1broker',     // requires secret
            'anxpro',       // not accessible
            'bitcoincoid',  // not accessible
            'btctradeim',   // no balance?
            'bleutrade',    // bad offset in response
            'btcexchange',  // bad offset in response
            'btcx',         // bad offset in response
            'bter',         // bad response
            'ccex',         // not accessible
            'chbtc',        // bad response
            'coinexchange',  // bad offset in response
            'coinmarketcap', // not implemeneted
            'coinegg',      // no balance?
            'coingi',       // not accessible
            'coinspot',     // bad offset in response
            'coolcoin',     // failed?
            'huobi',        // not accessible
            'huobicny',     // bad offset in response
            'jubi',         // not accessible
            'okcoincny',    // not accessible
            'virwox',       // not implemented
            'xbtce',        // travis
            'allcoin',      // not accessible
            'btctradeua',   // not accessible
        ],
        'testFetchOHLCV' => [
            'bibox',        // bad apiKey
            'bit2c',        // not accessible
            'bitstamp1',    // not accessible
            'bleutrade',    // bad response
            'btcchina',     // empty response
            'btcx',         // bad offset in response
            'btcexchange',  // bad offset in response
            'coinmarketcap', // not implemeneted
            'cobinhood',    // candle not found?!
            'ccex',         // not accessible
            'chbtc',        // 'server busy'
            'coinegg',      // no balance?
            'coinspot',     // bad offset in response
            'coinsecure',   // empty response
            'huobi',        // not accessible
            'jubi',         // not allowed
            'okcoincny',    // not accessible
            'kucoin',       // not accessible
            'huobicny',     // empty response
            'poloniex',     // travis
            'bitlish',      // travis
            'allcoin',      // travis
            'zb',           // not accessible
            'nova',         // no such market?
            'livecoin',     // empty response
            'virwox',       // empty response
            'kuna',         // not supported
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

        if ($exchange->has('FetchTickers')) {
            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $tickers = $exchange->fetch_tickers();
            VCR::eject();
            $this->assertNotEmpty($tickers);

            $ticker = current($tickers);
            $this->assertArrayHasKey('symbol', $ticker);
            $this->assertArrayHasKey('baseVolume', $ticker);
            $this->assertArrayHasKey('info', $ticker);
        } else {
            $this->assertFalse($exchange->has('FetchTickers'));
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

        if ($exchange->has('FetchTrades')) {
            VCR::insertCassette('testLoadMarkets@' . $exchange->id . '.json');
            $markets = $exchange->load_markets();
            VCR::eject();
            $symbol = $this->getSymbol($exchange, $markets);

            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $trades = $exchange->fetch_trades($symbol);
            VCR::eject();
            $this->assertNotEmpty($trades);
        } else {
            $this->assertFalse($exchange->has('FetchTrades'));
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

        if ($exchange->has('FetchOrderBook')) {
            VCR::insertCassette('testLoadMarkets@' . $exchange->id . '.json');
            $markets = $exchange->load_markets();
            VCR::eject();
            $symbol = $this->getSymbol($exchange, $markets);

            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $order_book = $exchange->fetch_order_book($symbol);
            VCR::eject();
            $this->assertNotEmpty($order_book);
        } else {
            $this->assertFalse($exchange->has('FetchOrderBook'));
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

        if ($exchange->has('FetchOHLCV')) {
            VCR::insertCassette('testLoadMarkets@' . $exchange->id . '.json');
            $markets = $exchange->load_markets();
            VCR::eject();
            $symbol = $this->getSymbol($exchange, $markets);

            VCR::insertCassette(__FUNCTION__ . '@' . $exchange->id . '.json');
            $ohlcv = $exchange->fetch_ohlcv($symbol);
            VCR::eject();
            $this->assertNotEmpty($ohlcv);
        } else {
            $this->assertFalse($exchange->has('FetchOHLCV'));
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

    private function getSymbol (Exchange $exchange, array $markets): string {
        switch ($exchange->id) {
            case 'bitstamp1':
                return 'BTC/USD';

            case 'coincheck':
                return 'BTC/JPY';

            default:
                return current($markets)['symbol'] ?? '';
        }
    }
}
