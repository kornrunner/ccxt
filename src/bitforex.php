<?php

namespace ccxt;

use Exception as Exception; // a common import

class bitforex extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bitforex',
            'name' => 'Bitforex',
            'countries' => array ( 'CN' ),
            'version' => 'v1',
            'has' => array (
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'createOrder' => true,
                'cancelOrder' => true,
                'fetchTicker' => true,
                'fetchTickers' => false,
                'fetchMyTrades' => false,
                'fetchTrades' => true,
                'fetchOrder' => true,
                'fetchOrders' => false,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/44310033-69e9e600-a3d8-11e8-873d-54d74d1bc4e4.jpg',
                'api' => 'https://api.bitforex.com',
                'www' => 'https://www.bitforex.com',
                'doc' => 'https://github.com/bitforexapi/API_Docs/wiki',
                'fees' => 'https://help.bitforex.com/en_us/?cat=13',
                'referral' => 'https://www.bitforex.com/registered?inviterId=1867438',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'api/v1/market/symbols',
                        'api/v1/market/ticker',
                        'api/v1/market/depth',
                        'api/v1/market/trades',
                        'api/v1/market/kline',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'api/v1/fund/mainAccount',
                        'api/v1/fund/allAccount',
                        'api/v1/trade/placeOrder',
                        'api/v1/trade/cancelOrder',
                        'api/v1/trade/orderInfo',
                        'api/v1/trade/orderInfos',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.0,
                    'taker' => 0.05 / 100,
                ),
                'funding' => array (
                    'tierBased' => false,
                    'percentage' => true,
                    'deposit' => array (),
                    'withdraw' => array (
                        'BTC' => 0.0005,
                        'ETH' => 0.01,
                        'BCH' => 0.0001,
                        'LTC' => 0.001,
                        'ETC' => 0.005,
                        'USDT' => 5,
                        'CMCT' => 30,
                        'AION' => 3,
                        'LVT' => 0,
                        'DATA' => 40,
                        'RHP' => 50,
                        'NEO' => 0,
                        'AIDOC' => 10,
                        'BQT' => 2,
                        'R' => 2,
                        'DPY' => 0.8,
                        'GTC' => 40,
                        'AGI' => 30,
                        'DENT' => 100,
                        'SAN' => 1,
                        'SPANK' => 8,
                        'AID' => 5,
                        'OMG' => 0.1,
                        'BFT' => 5,
                        'SHOW' => 150,
                        'TRX' => 20,
                        'ABYSS' => 10,
                        'THM' => 25,
                        'ZIL' => 20,
                        'PPT' => 0.2,
                        'WTC' => 0.4,
                        'LRC' => 7,
                        'BNT' => 1,
                        'CTXC' => 1,
                        'MITH' => 20,
                        'TRUE' => 4,
                        'LYM' => 10,
                        'VEE' => 100,
                        'AUTO' => 200,
                        'REN' => 50,
                        'TIO' => 2.5,
                        'NGC' => 1.5,
                        'PST' => 10,
                        'CRE' => 200,
                        'IPC' => 5,
                        'PTT' => 1000,
                        'XMCT' => 20,
                        'ATMI' => 40,
                        'TERN' => 40,
                        'XLM' => 0.01,
                        'ODE' => 15,
                        'FTM' => 100,
                        'RTE' => 100,
                        'DCC' => 100,
                        'IMT' => 500,
                        'GOT' => 3,
                        'EGT' => 500,
                        'DACC' => 1000,
                        'UBEX' => 500,
                        'ABL' => 100,
                        'OLT' => 100,
                        'DAV' => 40,
                        'THRT' => 10,
                        'RMESH' => 3,
                        'UPP' => 20,
                        'SDT' => 0,
                        'SHR' => 10,
                        'MTV' => 3,
                        'ESS' => 100,
                        'MET' => 3,
                        'TTC' => 20,
                        'LXT' => 10,
                        'XCLP' => 100,
                        'LUK' => 100,
                        'UBC' => 100,
                        'DTX' => 10,
                        'BEAT' => 20,
                        'DEED' => 2,
                        'BGX' => 3000,
                        'PRL' => 20,
                        'ELY' => 50,
                        'CARD' => 300,
                        'SQR' => 15,
                        'VRA' => 400,
                        'BWX' => 3500,
                        'MAS' => 75,
                        'FLP' => 0.6,
                        'UNC' => 300,
                        'CRNC' => 15,
                        'MFG' => 70,
                        'ZXC' => 70,
                        'TRT' => 30,
                        'ZIX' => 35,
                        'XRA' => 10,
                        'AMO' => 1600,
                        'IPG' => 3,
                        'uDoo' => 50,
                        'URB' => 30,
                        'ARCONA' => 3,
                        'CRAD' => 5,
                        'NOBS' => 1000,
                        'ADF' => 2,
                        'ELF' => 5,
                        'LX' => 20,
                        'PATH' => 15,
                        'SILK' => 120,
                        'SKYFT' => 50,
                        'EDN' => 50,
                        'ADE' => 50,
                        'EDR' => 10,
                        'TIME' => 0.25,
                        'SPRK' => 20,
                        'QTUM' => 0.01,
                        'BF' => 5,
                        'ZPR' => 100,
                        'HYB' => 10,
                        'CAN' => 30,
                        'CEL' => 10,
                        'ATS' => 50,
                        'KCASH' => 1,
                        'ACT' => 0.01,
                        'MT' => 300,
                        'DXT' => 30,
                        'WAB' => 4000,
                        'HYDRO' => 400,
                        'LQD' => 5,
                        'OPTC' => 200,
                        'EQUAD' => 80,
                        'LATX' => 50,
                        'LEDU' => 100,
                        'RIT' => 70,
                        'ACDC' => 500,
                        'FSN' => 2,
                    ),
                ),
            ),
            'exceptions' => array (
                '4004' => '\\ccxt\\OrderNotFound',
                '1013' => '\\ccxt\\AuthenticationError',
                '1016' => '\\ccxt\\AuthenticationError',
                '3002' => '\\ccxt\\InsufficientFunds',
                '10204' => '\\ccxt\\DDoSProtection',
            ),
        ));
    }

    public function fetch_markets () {
        $response = $this->publicGetApiV1MarketSymbols ();
        $data = $response['data'];
        $result = array ();
        for ($i = 0; $i < count ($data); $i++) {
            $market = $data[$i];
            $id = $market['symbol'];
            $symbolParts = explode ('-', $id);
            $baseId = $symbolParts[2];
            $quoteId = $symbolParts[1];
            $base = strtoupper ($baseId);
            $quote = strtoupper ($quoteId);
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $active = true;
            $precision = array (
                'amount' => $market['amountPrecision'],
                'price' => $market['pricePrecision'],
            );
            $limits = array (
                'amount' => array (
                    'min' => $market['minOrderAmount'],
                    'max' => null,
                ),
                'price' => array (
                    'min' => null,
                    'max' => null,
                ),
                'cost' => array (
                    'min' => null,
                    'max' => null,
                ),
            );
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function parse_trade ($trade, $market = null) {
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($trade, 'time');
        $id = $this->safe_string($trade, 'tid');
        $orderId = null;
        $amount = $this->safe_float($trade, 'amount');
        $price = $this->safe_float($trade, 'price');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $amount * $price;
            }
        }
        $sideId = $this->safe_integer($trade, 'direction');
        $side = $this->parse_side ($sideId);
        return array (
            'info' => $trade,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'order' => $orderId,
            'fee' => null,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'symbol' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $market = $this->market ($symbol);
        $response = $this->publicGetApiV1MarketTrades (array_merge ($request, $params));
        return $this->parse_trades($response['data'], $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostApiV1FundAllAccount ($params);
        $data = $response['data'];
        $result = array ( 'info' => $response );
        for ($i = 0; $i < count ($data); $i++) {
            $current = $data[$i];
            $currencyId = $current['currency'];
            $code = strtoupper ($currencyId);
            if (is_array ($this->currencies_by_id) && array_key_exists ($currencyId, $this->currencies_by_id)) {
                $code = $this->currencies_by_id[$currencyId]['code'];
            } else {
                $code = $this->common_currency_code($code);
            }
            $account = $this->account ();
            $result[$code] = $account;
            $result[$code]['used'] = $this->safe_float($current, 'frozen');
            $result[$code]['free'] = $this->safe_float($current, 'active');
            $result[$code]['total'] = $this->safe_float($current, 'fix');
        }
        return $this->parse_balance($result);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->markets[$symbol];
        $request = array (
            'symbol' => $market['id'],
        );
        $response = $this->publicGetApiV1MarketTicker (array_merge ($request, $params));
        $data = $response['data'];
        $timestamp = $this->safe_integer($data, 'date');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($data, 'high'),
            'low' => $this->safe_float($data, 'low'),
            'bid' => $this->safe_float($data, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($data, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $this->safe_float($data, 'last'),
            'last' => $this->safe_float($data, 'last'),
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($data, 'vol'),
            'quoteVolume' => null,
            'info' => $response,
        );
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $marketId = $this->market_id($symbol);
        $request = array (
            'symbol' => $marketId,
        );
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $response = $this->publicGetApiV1MarketDepth (array_merge ($request, $params));
        $data = $response['data'];
        $timestamp = $response['time'];
        $bidsKey = 'bids';
        $asksKey = 'asks';
        $priceKey = 'price';
        $amountKey = 'amount';
        $orderbook = $this->parse_order_book($data, $timestamp, $bidsKey, $asksKey, $priceKey, $amountKey);
        return $orderbook;
    }

    public function parse_order_status ($status) {
        $statuses = array (
            '0' => 'open',
            '1' => 'open',
            '2' => 'closed',
            '3' => 'canceled',
            '4' => 'canceled',
        );
        return (is_array ($statuses) && array_key_exists ($status, $statuses)) ? $statuses[$status] : $status;
    }

    public function parse_side ($sideId) {
        if ($sideId === 1) {
            return 'buy';
        } else if ($sideId === 2) {
            return 'sell';
        } else {
            return null;
        }
    }

    public function parse_order ($order, $market = null) {
        $id = $this->safe_string($order, 'orderId');
        $timestamp = $this->safe_float($order, 'createTime');
        $lastTradeTimestamp = $this->safe_float($order, 'lastTime');
        $symbol = $market['symbol'];
        $sideId = $this->safe_integer($order, 'tradeType');
        $side = $this->parse_side ($sideId);
        $type = null;
        $price = $this->safe_float($order, 'orderPrice');
        $average = $this->safe_float($order, 'avgPrice');
        $amount = $this->safe_float($order, 'orderAmount');
        $filled = $this->safe_float($order, 'dealAmount');
        $remaining = $amount - $filled;
        $status = $this->parse_order_status($this->safe_string($order, 'orderState'));
        $cost = $filled * $price;
        $fee = $this->safe_float($order, 'tradeFee');
        $result = array (
            'info' => $order,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'average' => $average,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
        );
        return $result;
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $this->market_id($symbol),
            'orderId' => $id,
        );
        $response = $this->privatePostApiV1TradeOrderInfo (array_merge ($request, $params));
        $order = $this->parse_order($response['data'], $market);
        return $order;
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $this->market_id($symbol),
            'state' => 0,
        );
        $response = $this->privatePostApiV1TradeOrderInfos (array_merge ($request, $params));
        return $this->parse_orders($response['data'], $market, $since, $limit);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $this->market_id($symbol),
            'state' => 1,
        );
        $response = $this->privatePostApiV1TradeOrderInfos (array_merge ($request, $params));
        return $this->parse_orders($response['data'], $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $sideId = null;
        if ($side === 'buy') {
            $sideId = 1;
        } else if ($side === 'sell') {
            $sideId = 2;
        }
        $request = array (
            'symbol' => $this->market_id($symbol),
            'price' => $price,
            'amount' => $amount,
            'tradeType' => $sideId,
        );
        $response = $this->privatePostApiV1TradePlaceOrder (array_merge ($request, $params));
        $data = $response['data'];
        return array (
            'info' => $response,
            'id' => $this->safe_string($data, 'orderId'),
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'orderId' => $id,
        );
        if ($symbol !== null) {
            $request['symbol'] = $this->market_id($symbol);
        }
        $results = $this->privatePostApiV1TradeCancelOrder (array_merge ($request, $params));
        $success = $results['success'];
        $returnVal = array ( 'info' => $results, 'success' => $success );
        return $returnVal;
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        } else {
            $this->check_required_credentials();
            $payload = $this->urlencode (array ( 'accessKey' => $this->apiKey ));
            $query['nonce'] = $this->milliseconds ();
            if ($query) {
                $payload .= '&' . $this->urlencode ($this->keysort ($query));
            }
            // $message = '/' . 'api/' . $this->version . '/' . $path . '?' . $payload;
            $message = '/' . $path . '?' . $payload;
            $signature = $this->hmac ($this->encode ($message), $this->encode ($this->secret));
            $body = $payload . '&signData=' . $signature;
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body) {
        if (gettype ($body) !== 'string') {
            return; // fallback to default error handler
        }
        if (($body[0] === '{') || ($body[0] === '[')) {
            $response = json_decode ($body, $as_associative_array = true);
            $feedback = $this->id . ' ' . $body;
            $success = $this->safe_value($response, 'success');
            if ($success !== null) {
                if (!$success) {
                    $code = $this->safe_string($response, 'code');
                    if (is_array ($this->exceptions) && array_key_exists ($code, $this->exceptions)) {
                        throw new $this->exceptions[$code] ($feedback);
                    } else {
                        throw new ExchangeError ($feedback);
                    }
                }
            }
        }
    }
}
