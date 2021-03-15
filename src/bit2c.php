<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class bit2c extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bit2c',
            'name' => 'Bit2C',
            'countries' => array( 'IL' ), // Israel
            'rateLimit' => 3000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766119-3593220e-5ece-11e7-8b3a-5a041f6bcc3f.jpg',
                'api' => 'https://bit2c.co.il',
                'www' => 'https://www.bit2c.co.il',
                'referral' => 'https://bit2c.co.il/Aff/63bfed10-e359-420c-ab5a-ad368dab0baf',
                'doc' => array(
                    'https://www.bit2c.co.il/home/api',
                    'https://github.com/OferE/bit2c',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'Exchanges/{pair}/Ticker',
                        'Exchanges/{pair}/orderbook',
                        'Exchanges/{pair}/trades',
                        'Exchanges/{pair}/lasttrades',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'Merchant/CreateCheckout',
                        'Order/AddCoinFundsRequest',
                        'Order/AddFund',
                        'Order/AddOrder',
                        'Order/AddOrderMarketPriceBuy',
                        'Order/AddOrderMarketPriceSell',
                        'Order/CancelOrder',
                        'Order/AddCoinFundsRequest',
                        'Order/AddStopOrder',
                        'Payment/GetMyId',
                        'Payment/Send',
                        'Payment/Pay',
                    ),
                    'get' => array(
                        'Account/Balance',
                        'Account/Balance/v2',
                        'Order/MyOrders',
                        'Order/GetById',
                        'Order/AccountHistory',
                        'Order/OrderHistory',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/NIS' => array( 'id' => 'BtcNis', 'symbol' => 'BTC/NIS', 'base' => 'BTC', 'quote' => 'NIS', 'baseId' => 'Btc', 'quoteId' => 'Nis' ),
                'ETH/NIS' => array( 'id' => 'EthNis', 'symbol' => 'ETH/NIS', 'base' => 'ETH', 'quote' => 'NIS', 'baseId' => 'Eth', 'quoteId' => 'Nis' ),
                'BCH/NIS' => array( 'id' => 'BchabcNis', 'symbol' => 'BCH/NIS', 'base' => 'BCH', 'quote' => 'NIS', 'baseId' => 'Bchabc', 'quoteId' => 'Nis' ),
                'LTC/NIS' => array( 'id' => 'LtcNis', 'symbol' => 'LTC/NIS', 'base' => 'LTC', 'quote' => 'NIS', 'baseId' => 'Ltc', 'quoteId' => 'Nis' ),
                'ETC/NIS' => array( 'id' => 'EtcNis', 'symbol' => 'ETC/NIS', 'base' => 'ETC', 'quote' => 'NIS', 'baseId' => 'Etc', 'quoteId' => 'Nis' ),
                'BTG/NIS' => array( 'id' => 'BtgNis', 'symbol' => 'BTG/NIS', 'base' => 'BTG', 'quote' => 'NIS', 'baseId' => 'Btg', 'quoteId' => 'Nis' ),
                'BSV/NIS' => array( 'id' => 'BchsvNis', 'symbol' => 'BSV/NIS', 'base' => 'BSV', 'quote' => 'NIS', 'baseId' => 'Bchsv', 'quoteId' => 'Nis' ),
                'GRIN/NIS' => array( 'id' => 'GrinNis', 'symbol' => 'GRIN/NIS', 'base' => 'GRIN', 'quote' => 'NIS', 'baseId' => 'Grin', 'quoteId' => 'Nis' ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.5 / 100,
                    'taker' => 0.5 / 100,
                ),
            ),
            'options' => array(
                'fetchTradesMethod' => 'public_get_exchanges_pair_trades',
            ),
            'exceptions' => array(
                'exact' => array(
                    'Please provide valid APIkey' => '\\ccxt\\AuthenticationError', // array( "error" : "Please provide valid APIkey" )
                ),
                'broad' => array(
                    // array( "error" => "Please provide valid nonce in Request Nonce (1598218490) is not bigger than last nonce (1598218490).")
                    // array( "error" => "Please provide valid nonce in Request UInt64.TryParse failed for nonce :" )
                    'Please provide valid nonce' => '\\ccxt\\InvalidNonce',
                    'please approve new terms of use on site' => '\\ccxt\\PermissionDenied', // array( "error" : "please approve new terms of use on site." )
                ),
            ),
        ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $balance = $this->privateGetAccountBalanceV2 ($params);
        //
        //     {
        //         "AVAILABLE_NIS" => 0.0,
        //         "NIS" => 0.0,
        //         "LOCKED_NIS" => 0.0,
        //         "AVAILABLE_BTC" => 0.0,
        //         "BTC" => 0.0,
        //         "LOCKED_BTC" => 0.0,
        //         "AVAILABLE_ETH" => 0.0,
        //         "ETH" => 0.0,
        //         "LOCKED_ETH" => 0.0,
        //         "AVAILABLE_BCHSV" => 0.0,
        //         "BCHSV" => 0.0,
        //         "LOCKED_BCHSV" => 0.0,
        //         "AVAILABLE_BCHABC" => 0.0,
        //         "BCHABC" => 0.0,
        //         "LOCKED_BCHABC" => 0.0,
        //         "AVAILABLE_LTC" => 0.0,
        //         "LTC" => 0.0,
        //         "LOCKED_LTC" => 0.0,
        //         "AVAILABLE_ETC" => 0.0,
        //         "ETC" => 0.0,
        //         "LOCKED_ETC" => 0.0,
        //         "AVAILABLE_BTG" => 0.0,
        //         "BTG" => 0.0,
        //         "LOCKED_BTG" => 0.0,
        //         "AVAILABLE_GRIN" => 0.0,
        //         "GRIN" => 0.0,
        //         "LOCKED_GRIN" => 0.0,
        //         "Fees" => {
        //             "BtcNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "EthNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "BchabcNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "LtcNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "EtcNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "BtgNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "LtcBtc" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "BchsvNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 ),
        //             "GrinNis" => array( "FeeMaker" => 1.0, "FeeTaker" => 1.0 )
        //         }
        //     }
        //
        $result = array( 'info' => $balance );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $account = $this->account();
            $currencyId = $this->currency_id($code);
            $uppercase = strtoupper($currencyId);
            if (is_array($balance) && array_key_exists($uppercase, $balance)) {
                $account['free'] = $this->safe_float($balance, 'AVAILABLE_' . $uppercase);
                $account['total'] = $this->safe_float($balance, $uppercase);
            }
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair' => $this->market_id($symbol),
        );
        $orderbook = $this->publicGetExchangesPairOrderbook (array_merge($request, $params));
        return $this->parse_order_book($orderbook);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair' => $this->market_id($symbol),
        );
        $ticker = $this->publicGetExchangesPairTicker (array_merge($request, $params));
        $timestamp = $this->milliseconds();
        $averagePrice = $this->safe_float($ticker, 'av');
        $baseVolume = $this->safe_float($ticker, 'a');
        $quoteVolume = null;
        if ($baseVolume !== null && $averagePrice !== null) {
            $quoteVolume = $baseVolume * $averagePrice;
        }
        $last = $this->safe_float($ticker, 'll');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => null,
            'low' => null,
            'bid' => $this->safe_float($ticker, 'h'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'l'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $averagePrice,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $method = $this->options['fetchTradesMethod'];
        $request = array(
            'pair' => $market['id'],
        );
        if ($since !== null) {
            $request['date'] = intval($since);
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // max 100000
        }
        $response = $this->$method (array_merge($request, $params));
        if (gettype($response) === 'string') {
            throw new ExchangeError($response);
        }
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $method = 'privatePostOrderAddOrder';
        $request = array(
            'Amount' => $amount,
            'Pair' => $this->market_id($symbol),
        );
        if ($type === 'market') {
            $method .= 'MarketPrice' . $this->capitalize($side);
        } else {
            $request['Price'] = $price;
            $request['Total'] = $amount * $price;
            $request['IsBid'] = ($side === 'buy');
        }
        $response = $this->$method (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['NewOrder']['id'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => $id,
        );
        return $this->privatePostOrderCancelOrder (array_merge($request, $params));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        $response = $this->privateGetOrderMyOrders (array_merge($request, $params));
        $orders = $this->safe_value($response, $market['id'], array());
        $asks = $this->safe_value($orders, 'ask', array());
        $bids = $this->safe_value($orders, 'bid', array());
        return $this->parse_orders($this->array_concat($asks, $bids), $market, $since, $limit);
    }

    public function parse_order($order, $market = null) {
        $timestamp = $this->safe_integer($order, 'created');
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'amount');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $side = $this->safe_value($order, 'type');
        if ($side === 0) {
            $side = 'buy';
        } else if ($side === 1) {
            $side = 'sell';
        }
        $id = $this->safe_string($order, 'id');
        $status = $this->safe_string($order, 'status');
        return $this->safe_order(array(
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => null,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'filled' => null,
            'remaining' => null,
            'cost' => null,
            'trades' => null,
            'fee' => null,
            'info' => $order,
            'average' => null,
        ));
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($limit !== null) {
            $request['take'] = $limit;
        }
        $request['take'] = $limit;
        if ($since !== null) {
            $request['toTime'] = $this->ymd($this->milliseconds(), '.');
            $request['fromTime'] = $this->ymd($since, '.');
        }
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
        }
        $response = $this->privateGetOrderOrderHistory (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = null;
        $id = null;
        $price = null;
        $amount = null;
        $orderId = null;
        $feeCost = null;
        $side = null;
        $reference = $this->safe_string($trade, 'reference');
        if ($reference !== null) {
            $timestamp = $this->safe_timestamp($trade, 'ticks');
            $price = $this->safe_float($trade, 'price');
            $amount = $this->safe_float($trade, 'firstAmount');
            $reference_parts = explode('|', $reference); // $reference contains 'pair|$orderId|tradeId'
            if ($market === null) {
                $marketId = $this->safe_string($trade, 'pair');
                if (is_array($this->markets_by_id[$marketId]) && array_key_exists($marketId, $this->markets_by_id[$marketId])) {
                    $market = $this->markets_by_id[$marketId];
                } else if (is_array($this->markets_by_id) && array_key_exists($reference_parts[0], $this->markets_by_id)) {
                    $market = $this->markets_by_id[$reference_parts[0]];
                }
            }
            $orderId = $reference_parts[1];
            $id = $reference_parts[2];
            $side = $this->safe_integer($trade, 'action');
            if ($side === 0) {
                $side = 'buy';
            } else if ($side === 1) {
                $side = 'sell';
            }
            $feeCost = $this->safe_float($trade, 'feeAmount');
        } else {
            $timestamp = $this->safe_timestamp($trade, 'date');
            $id = $this->safe_string($trade, 'tid');
            $price = $this->safe_float($trade, 'price');
            $amount = $this->safe_float($trade, 'amount');
            $side = $this->safe_value($trade, 'isBid');
            if ($side !== null) {
                if ($side) {
                    $side = 'buy';
                } else {
                    $side = 'sell';
                }
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $price * $amount,
            'fee' => array(
                'cost' => $feeCost,
                'currency' => 'NIS',
                'rate' => null,
            ),
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        if ($api === 'public') {
            $url .= '.json';
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $query = array_merge(array(
                'nonce' => $nonce,
            ), $params);
            $auth = $this->urlencode($query);
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $auth;
                }
            } else {
                $body = $auth;
            }
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha512', 'base64');
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'key' => $this->apiKey,
                'sign' => $signature,
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        //
        //     array( "$error" : "please approve new terms of use on site." )
        //     array( "$error" => "Please provide valid nonce in Request Nonce (1598218490) is not bigger than last nonce (1598218490).")
        //
        $error = $this->safe_string($response, 'error');
        if ($error !== null) {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $error, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $error, $feedback);
            throw new ExchangeError($feedback); // unknown message
        }
    }
}
