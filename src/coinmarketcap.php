<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class coinmarketcap extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinmarketcap',
            'name' => 'CoinMarketCap',
            'rateLimit' => 10000,
            'version' => 'v1',
            'countries' => array( 'US' ),
            'has' => array(
                'cancelOrder' => false,
                'CORS' => true,
                'createLimitOrder' => false,
                'createMarketOrder' => false,
                'createOrder' => false,
                'editOrder' => false,
                'privateAPI' => false,
                'fetchBalance' => false,
                'fetchCurrencies' => true,
                'fetchL2OrderBook' => false,
                'fetchMarkets' => true,
                'fetchOHLCV' => false,
                'fetchOrderBook' => false,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => false,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87182086-1cd4cd00-c2ec-11ea-9ec4-d0cf2a2abf62.jpg',
                'api' => array(
                    'public' => 'https://api.coinmarketcap.com',
                    'files' => 'https://files.coinmarketcap.com',
                    'charts' => 'https://graph.coinmarketcap.com',
                ),
                'www' => 'https://coinmarketcap.com',
                'doc' => 'https://coinmarketcap.com/api',
            ),
            'requiredCredentials' => array(
                'apiKey' => false,
                'secret' => false,
            ),
            'api' => array(
                'files' => array(
                    'get' => array(
                        'generated/stats/global.json',
                    ),
                ),
                'graphs' => array(
                    'get' => array(
                        'currencies/{name}/',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'ticker/',
                        'ticker/{id}/',
                        'global/',
                    ),
                ),
            ),
            'currencyCodes' => array(
                'AUD',
                'BRL',
                'CAD',
                'CHF',
                'CNY',
                'EUR',
                'GBP',
                'HKD',
                'IDR',
                'INR',
                'JPY',
                'KRW',
                'MXN',
                'RUB',
                'USD',
                'BTC',
                'ETH',
                'LTC',
            ),
        ));
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        throw new ExchangeError('Fetching order books is not supported by the API of ' . $this->id);
    }

    public function currency_code($base, $name) {
        $currencies = array(
            'ACChain' => 'ACChain',
            'AdCoin' => 'AdCoin',
            'BatCoin' => 'BatCoin',
            'BigONE Token' => 'BigONE Token', // conflict with Harmony (ONE)
            'Bitgem' => 'Bitgem',
            'BlazeCoin' => 'BlazeCoin',
            'BlockCAT' => 'BlockCAT',
            'Blocktrade Token' => 'Blocktrade Token',
            'BOX Token' => 'BOX Token', // conflict with BOX (ContentBox)
            'Catcoin' => 'Catcoin',
            'CanYaCoin' => 'CanYaCoin', // conflict with CAN (Content and AD Network)
            'CryptoBossCoin' => 'CryptoBossCoin', // conflict with CBC (CashBet Coin)
            'Comet' => 'Comet', // conflict with CMT (CyberMiles)
            'CPChain' => 'CPChain',
            'CrowdCoin' => 'CrowdCoin', // conflict with CRC CryCash
            'Cryptaur' => 'Cryptaur', // conflict with CPT = Contents Protocol https://github.com/ccxt/ccxt/issues/4920 and https://github.com/ccxt/ccxt/issues/6081
            'Cubits' => 'Cubits', // conflict with QBT (Qbao)
            'DAO.Casino' => 'DAO.Casino', // conflict with BET (BetaCoin)
            'DefiBox' => 'DefiBox', // conflict with BOX (ContentBox)
            'E-Dinar Coin' => 'E-Dinar Coin', // conflict with EDR Endor Protocol and EDRCoin
            'EDRcoin' => 'EDRcoin', // conflict with EDR Endor Protocol and E-Dinar Coin
            'ENTCash' => 'ENTCash', // conflict with ENT (Eternity)
            'FairCoin' => 'FairCoin', // conflict with FAIR (FairGame) https://github.com/ccxt/ccxt/pull/5865
            'Fabric Token' => 'Fabric Token',
            // 'GET Protocol' => 'GET Protocol',
            'GHOSTPRISM' => 'GHOSTPRISM', // conflict with GHOST
            'Global Tour Coin' => 'Global Tour Coin', // conflict with GTC (Game.com)
            'GuccioneCoin' => 'GuccioneCoin', // conflict with GCC (Global Cryptocurrency)
            'HarmonyCoin' => 'HarmonyCoin', // conflict with HMC (Hi Mutual Society)
            'Harvest Masternode Coin' => 'Harvest Masternode Coin', // conflict with HC (HyperCash)
            'HOT Token' => 'HOT Token',
            'Hydro Protocol' => 'Hydro Protocol', // conflict with HOT (Holo)
            'Huncoin' => 'Huncoin', // conflict with HNC (Helleniccoin)
            'iCoin' => 'iCoin',
            'Infinity Economics' => 'Infinity Economics', // conflict with XIN (Mixin)
            'IQ.cash' => 'IQ.cash', // conflict with IQ (Everipedia)
            'KingN Coin' => 'KingN Coin', // conflict with KNC (Kyber Network)
            'LiteBitcoin' => 'LiteBitcoin', // conflict with LBTC (LightningBitcoin)
            'Maggie' => 'Maggie',
            'Menlo One' => 'Menlo One', // conflict with Harmony (ONE)
            'Mobilian Coin' => 'Mobilian Coin', // conflict with Membrana (MBN)
            'Monarch' => 'Monarch', // conflict with MyToken (MT)
            'MTC Mesh Network' => 'MTC Mesh Network', // conflict with MTC Docademic doc.com Token https://github.com/ccxt/ccxt/issues/6081 https://github.com/ccxt/ccxt/issues/3025
            'IOTA' => 'IOTA', // a special case, most exchanges list it as IOTA, therefore we change just the Coinmarketcap instead of changing them all
            'NetCoin' => 'NetCoin',
            'PCHAIN' => 'PCHAIN', // conflict with PAI (Project Pai)
            'Penta' => 'Penta', // conflict with PNT (pNetwork)
            'Plair' => 'Plair', // conflict with PLA (PLANET)
            'PlayChip' => 'PlayChip', // conflict with PLA (PLANET)
            'Polcoin' => 'Polcoin',
            'PutinCoin' => 'PutinCoin', // conflict with PUT (Profile Utility Token)
            'Rcoin' => 'Rcoin', // conflict with RCN (Ripio Credit Network)
            // https://github.com/ccxt/ccxt/issues/6081
            // https://github.com/ccxt/ccxt/issues/3365
            // https://github.com/ccxt/ccxt/issues/2873
            'SBTCT' => 'SiamBitcoin', // conflict with sBTC
            'Super Bitcoin' => 'Super Bitcoin', // conflict with sBTC
            'TerraCredit' => 'TerraCredit', // conflict with CREDIT (PROXI)
            'Themis' => 'Themis', // conflict with GET (Guaranteed Entrance Token, GET Protocol)
            'UNI COIN' => 'UNI COIN', // conflict with UNI (Uniswap)
            'UNICORN Token' => 'UNICORN Token', // conflict with UNI (Uniswap)
            'Universe' => 'Universe', // conflict with UNI (Uniswap)
        );
        return $this->safe_value($currencies, $name, $base);
    }

    public function fetch_markets($params = array ()) {
        $request = array(
            'limit' => 0,
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $currencies = $this->currencyCodes;
            for ($j = 0; $j < count($currencies); $j++) {
                $quote = $currencies[$j];
                $quoteId = strtolower($quote);
                $baseId = $market['id'];
                $base = $this->currency_code($market['symbol'], $market['name']);
                $symbol = $base . '/' . $quote;
                $id = $baseId . '/' . $quoteId;
                $result[] = array(
                    'id' => $id,
                    'symbol' => $symbol,
                    'base' => $base,
                    'quote' => $quote,
                    'baseId' => $baseId,
                    'quoteId' => $quoteId,
                    'info' => $market,
                    'active' => null,
                    'precision' => $this->precision,
                    'limits' => $this->limits,
                );
            }
        }
        return $result;
    }

    public function fetch_global($currency = 'USD') {
        $this->load_markets();
        $request = array();
        if ($currency) {
            $request['convert'] = $currency;
        }
        return $this->publicGetGlobal ($request);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->safe_timestamp($ticker, 'last_updated');
        if ($timestamp === null) {
            $timestamp = $this->milliseconds();
        }
        $change = $this->safe_number($ticker, 'percent_change_24h');
        $last = null;
        $symbol = null;
        $volume = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            $priceKey = 'price_' . $market['quoteId'];
            $last = $this->safe_number($ticker, $priceKey);
            $volumeKey = '24h_volume_' . $market['quoteId'];
            $volume = $this->safe_number($ticker, $volumeKey);
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => null,
            'low' => null,
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => $change,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => $volume,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($currency = 'USD', $params = array ()) {
        $this->load_markets();
        $request = array(
            'limit' => 10000,
        );
        if ($currency) {
            $request['convert'] = $currency;
        }
        $response = $this->publicGetTicker (array_merge($request, $params));
        $result = array();
        for ($t = 0; $t < count($response); $t++) {
            $ticker = $response[$t];
            $currencyId = strtolower($currency);
            $id = $ticker['id'] . '/' . $currencyId;
            $symbol = $id;
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            }
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $result;
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'convert' => $market['quote'],
            'id' => $market['baseId'],
        );
        $response = $this->publicGetTickerId (array_merge($request, $params));
        $ticker = $response[0];
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_currencies($params = array ()) {
        $request = array(
            'limit' => 0,
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'symbol');
            $name = $this->safe_string($currency, 'name');
            // todo => will need to rethink the fees
            // to add support for multiple withdrawal/deposit methods and
            // differentiated fees for each particular method
            $precision = 8; // default $precision, todo => fix "magic constants"
            $code = $this->currency_code($id, $name);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'name' => $name,
                'active' => true,
                'fee' => null, // todo => redesign
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision),
                        'max' => pow(10, $precision),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision),
                        'max' => pow(10, $precision),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($query) {
            $url .= '?' . $this->urlencode($query);
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('error', $response)) {
            if ($response['error']) {
                throw new ExchangeError($this->id . ' ' . $this->json($response));
            }
        }
        return $response;
    }
}
