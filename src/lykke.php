<?php

namespace ccxt;

use Exception; // a common import

class lykke extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'lykke',
            'name' => 'Lykke',
            'countries' => array( 'CH' ),
            'version' => 'v1',
            'rateLimit' => 200,
            'has' => array(
                'CORS' => false,
                'fetchOHLCV' => false,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchOrder' => true,
                'fetchOrders' => true,
                'fetchTrades' => true,
                'fetchMyTrades' => true,
                'createOrder' => true,
                'cancelOrder' => true,
                'cancelAllOrders' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
            ),
            'timeframes' => array(
                '1m' => 'Minute',
                '5m' => 'Min5',
                '15m' => 'Min15',
                '30m' => 'Min30',
                '1h' => 'Hour',
                '4h' => 'Hour4',
                '6h' => 'Hour6',
                '12h' => 'Hour12',
                '1d' => 'Day',
                '1w' => 'Week',
                '1M' => 'Month',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => false,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/34487620-3139a7b0-efe6-11e7-90f5-e520cef74451.jpg',
                'api' => array(
                    'mobile' => 'https://public-api.lykke.com/api',
                    'public' => 'https://hft-api.lykke.com/api',
                    'private' => 'https://hft-api.lykke.com/api',
                ),
                'test' => array(
                    'mobile' => 'https://public-api.lykke.com/api',
                    'public' => 'https://hft-service-dev.lykkex.net/api',
                    'private' => 'https://hft-service-dev.lykkex.net/api',
                ),
                'www' => 'https://www.lykke.com',
                'doc' => array(
                    'https://hft-api.lykke.com/swagger/ui/',
                    'https://www.lykke.com/lykke_api',
                ),
                'fees' => 'https://www.lykke.com/trading-conditions',
            ),
            'api' => array(
                'mobile' => array(
                    'get' => array(
                        'AssetPairs/rate',
                        'AssetPairs/rate/{assetPairId}',
                        'AssetPairs/dictionary/{market}',
                        'Assets/dictionary',
                        'Candles/history/{market}/available',
                        'Candles/history/{market}/{assetPair}/{period}/{type}/{from}/{to}',
                        'Company/ownershipStructure',
                        'Company/registrationsCount',
                        'IsAlive',
                        'Market',
                        'Market/{market}',
                        'Market/capitalization/{market}',
                        'OrderBook',
                        'OrderBook/{assetPairId}',
                        'Trades/{AssetPairId}',
                        'Trades/Last/{assetPair}/{n}',
                    ),
                    'post' => array(
                        'AssetPairs/rate/history',
                        'AssetPairs/rate/history/{assetPairId}',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'AssetPairs',
                        'AssetPairs/{id}',
                        'IsAlive',
                        'OrderBooks',
                        'OrderBooks/{AssetPairId}',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'Orders',
                        'Orders/{id}',
                        'Wallets',
                        'History/trades',
                    ),
                    'post' => array(
                        'Orders/limit',
                        'Orders/market',
                        'Orders/{id}/Cancel',
                        'Orders/v2/market',
                        'Orders/v2/limit',
                        'Orders/stoplimit',
                        'Orders/bulk',
                    ),
                    'delete' => array(
                        'Orders',
                        'Orders/{id}',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.0, // as of 7 Feb 2018, see https://github.com/ccxt/ccxt/issues/1863
                    'taker' => 0.0, // https://www.lykke.com/cp/wallet-fees-and-limits
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(
                        'BTC' => 0.001,
                    ),
                    'deposit' => array(
                        'BTC' => 0,
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'XPD' => 'Lykke XPD',
            ),
        ));
    }

    public function parse_trade($trade, $market) {
        //
        //  public fetchTrades
        //
        //   {
        //     "$id" => "d5983ab8-e9ec-48c9-bdd0-1b18f8e80a71",
        //     "assetPairId" => "BTCUSD",
        //     "dateTime" => "2019-05-15T06:52:02.147Z",
        //     "volume" => 0.00019681,
        //     "index" => 0,
        //     "$price" => 8023.333,
        //     "action" => "Buy"
        //   }
        //
        //  private fetchMyTrades
        //     array(
        //         Id => '3500b83c-9963-4349-b3ee-b3e503073cea',
        //         OrderId => '83b50feb-8615-4dc6-b606-8a4168ecd708',
        //         DateTime => '2020-05-19T11:17:39.31+00:00',
        //         Timestamp => '2020-05-19T11:17:39.31+00:00',
        //         State => null,
        //         Amount => -0.004,
        //         BaseVolume => -0.004,
        //         QuotingVolume => 39.3898,
        //         Asset => 'BTC',
        //         BaseAssetId => 'BTC',
        //         QuotingAssetId => 'USD',
        //         AssetPair => 'BTCUSD',
        //         AssetPairId => 'BTCUSD',
        //         Price => 9847.427,
        //         Fee => array( Amount => null, Type => 'Unknown', FeeAssetId => null )
        //     ),
        $marketId = $this->safe_string($trade, 'AssetPairId');
        $symbol = $this->safe_symbol($marketId, $market);
        $id = $this->safe_string_2($trade, 'id', 'Id');
        $orderId = $this->safe_string($trade, 'OrderId');
        $timestamp = $this->parse8601($this->safe_string_2($trade, 'dateTime', 'DateTime'));
        $price = $this->safe_number_2($trade, 'price', 'Price');
        $amount = $this->safe_number_2($trade, 'volume', 'Amount');
        $side = $this->safe_string_lower($trade, 'action');
        if ($side === null) {
            if ($amount < 0) {
                $side = 'sell';
            } else {
                $side = 'buy';
            }
        }
        $amount = abs($amount);
        $cost = $price * $amount;
        $fee = array(
            'cost' => 0, // There are no fees for trading. https://www.lykke.com/wallet-fees-and-limits/
            'currency' => $market['quote'],
        );
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'order' => $orderId,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($limit === null) {
            $limit = 100;
        }
        $request = array(
            'AssetPairId' => $market['id'],
            'skip' => 0,
            'take' => $limit,
        );
        $response = $this->mobileGetTradesAssetPairId (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($limit !== null) {
            $request['take'] = $limit; // How many maximum items have to be returned, max 1000 default 100.
        }
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['assetPairId'] = $market['id'];
        }
        $response = $this->privateGetHistoryTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetWallets ($params);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'AssetId');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['total'] = $this->safe_number($balance, 'Balance');
            $account['used'] = $this->safe_number($balance, 'Reserved');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array( 'id' => $id );
        return $this->privateDeleteOrdersId (array_merge($request, $params));
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['assetPairId'] = $market['id'];
        }
        return $this->privateDeleteOrders (array_merge($request, $params));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $query = array(
            'AssetPairId' => $market['id'],
            'OrderAction' => $this->capitalize($side),
            'Volume' => $amount,
            'Asset' => $market['baseId'],
        );
        if ($type === 'limit') {
            $query['Price'] = $price;
        }
        $method = 'privatePostOrdersV2' . $this->capitalize($type);
        $result = $this->$method (array_merge($query, $params));
        //
        // $market
        //
        //     {
        //         "Price" => 0
        //     }
        //
        // limit
        //
        //     {
        //         "Id":"xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
        //     }
        //
        $id = $this->safe_string($result, 'Id');
        $price = $this->safe_number($result, 'Price');
        return array(
            'id' => $id,
            'info' => $result,
            'clientOrderId' => null,
            'timestamp' => null,
            'datetime' => null,
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => null,
            'average' => null,
            'filled' => null,
            'remaining' => null,
            'status' => null,
            'fee' => null,
            'trades' => null,
        );
    }

    public function fetch_markets($params = array ()) {
        $markets = $this->publicGetAssetPairs ();
        //
        //     array( array(                Id => "AEBTC",
        //                      Name => "AE/BTC",
        //                  Accuracy =>  6,
        //          InvertedAccuracy =>  8,
        //               BaseAssetId => "6f75280b-a005-4016-a3d8-03dc644e8912",
        //            QuotingAssetId => "BTC",
        //                 MinVolume =>  0.4,
        //         MinInvertedVolume =>  0.0001                                 ),
        //       {                Id => "AEETH",
        //                      Name => "AE/ETH",
        //                  Accuracy =>  6,
        //          InvertedAccuracy =>  8,
        //               BaseAssetId => "6f75280b-a005-4016-a3d8-03dc644e8912",
        //            QuotingAssetId => "ETH",
        //                 MinVolume =>  0.4,
        //         MinInvertedVolume =>  0.001                                  } )
        //
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'Id');
            $name = $this->safe_string($market, 'Name');
            list($baseId, $quoteId) = explode('/', $name);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'price' => $this->safe_integer($market, 'Accuracy'),
                'amount' => $this->safe_integer($market, 'InvertedAccuracy'),
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'active' => true,
                'info' => $market,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => pow(10, $precision['amount']),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => pow(10, $precision['price']),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'baseId' => null,
                'quoteId' => null,
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $close = $this->safe_number($ticker, 'lastPrice');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => null,
            'low' => null,
            'bid' => $this->safe_number($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $close,
            'last' => $close,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => $this->safe_number($ticker, 'volume24H'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $ticker = $this->mobileGetMarketMarket (array_merge($request, $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'Open' => 'open',
            'Pending' => 'open',
            'InOrderBook' => 'open',
            'Processing' => 'open',
            'Matched' => 'closed',
            'Cancelled' => 'canceled',
            'Rejected' => 'rejected',
            'Replaced' => 'canceled',
            'Placed' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "Id" => "string",
        //         "Status" => "Unknown",
        //         "AssetPairId" => "string",
        //         "Volume" => 0,
        //         "Price" => 0,
        //         "RemainingVolume" => 0,
        //         "LastMatchTime" => "2020-03-26T20:58:50.710Z",
        //         "CreatedAt" => "2020-03-26T20:58:50.710Z",
        //         "Type" => "Unknown",
        //         "LowerLimitPrice" => 0,
        //         "LowerPrice" => 0,
        //         "UpperLimitPrice" => 0,
        //         "UpperPrice" => 0
        //     }
        //
        $status = $this->parse_order_status($this->safe_string($order, 'Status'));
        $marketId = $this->safe_string($order, 'AssetPairId');
        $symbol = $this->safe_symbol($marketId, $market);
        $lastTradeTimestamp = $this->parse8601($this->safe_string($order, 'LastMatchTime'));
        $timestamp = null;
        if ((is_array($order) && array_key_exists('Registered', $order)) && ($order['Registered'])) {
            $timestamp = $this->parse8601($order['Registered']);
        } else if ((is_array($order) && array_key_exists('CreatedAt', $order)) && ($order['CreatedAt'])) {
            $timestamp = $this->parse8601($order['CreatedAt']);
        }
        $price = $this->safe_number($order, 'Price');
        $side = null;
        $amount = $this->safe_number($order, 'Volume');
        if ($amount < 0) {
            $side = 'sell';
            $amount = abs($amount);
        } else {
            $side = 'buy';
        }
        $remaining = abs($this->safe_number($order, 'RemainingVolume'));
        $id = $this->safe_string($order, 'Id');
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => null,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'average' => null,
            'amount' => $amount,
            'filled' => null,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'trades' => null,
        ));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privateGetOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privateGetOrders ($params);
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'InOrderBook',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'Matched',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetOrderBooksAssetPairId (array_merge(array(
            'AssetPairId' => $this->market_id($symbol),
        ), $params));
        $orderbook = array(
            'timestamp' => null,
            'bids' => array(),
            'asks' => array(),
        );
        $timestamp = null;
        for ($i = 0; $i < count($response); $i++) {
            $side = $response[$i];
            if ($side['IsBuy']) {
                $orderbook['bids'] = $this->array_concat($orderbook['bids'], $side['Prices']);
            } else {
                $orderbook['asks'] = $this->array_concat($orderbook['asks'], $side['Prices']);
            }
            $sideTimestamp = $this->parse8601($side['Timestamp']);
            $timestamp = ($timestamp === null) ? $sideTimestamp : max ($timestamp, $sideTimestamp);
        }
        return $this->parse_order_book($orderbook, $timestamp, 'bids', 'asks', 'Price', 'Volume');
    }

    public function parse_bid_ask($bidask, $priceKey = 0, $amountKey = 1) {
        $price = $this->safe_number($bidask, $priceKey);
        $amount = $this->safe_number($bidask, $amountKey);
        if ($amount < 0) {
            $amount = -$amount;
        }
        return array( $price, $amount );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'mobile') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($api === 'private') {
            if (($method === 'GET') || ($method === 'DELETE')) {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
            }
            $this->check_required_credentials();
            $headers = array(
                'api-key' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            );
            if ($method === 'POST') {
                if ($params) {
                    $body = $this->json($params);
                }
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}
