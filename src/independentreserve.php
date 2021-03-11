<?php

namespace ccxt;

use Exception; // a common import

class independentreserve extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'independentreserve',
            'name' => 'Independent Reserve',
            'countries' => array( 'AU', 'NZ' ), // Australia, New Zealand
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87182090-1e9e9080-c2ec-11ea-8e49-563db9a38f37.jpg',
                'api' => array(
                    'public' => 'https://api.independentreserve.com/Public',
                    'private' => 'https://api.independentreserve.com/Private',
                ),
                'www' => 'https://www.independentreserve.com',
                'doc' => 'https://www.independentreserve.com/API',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'GetValidPrimaryCurrencyCodes',
                        'GetValidSecondaryCurrencyCodes',
                        'GetValidLimitOrderTypes',
                        'GetValidMarketOrderTypes',
                        'GetValidOrderTypes',
                        'GetValidTransactionTypes',
                        'GetMarketSummary',
                        'GetOrderBook',
                        'GetAllOrders',
                        'GetTradeHistorySummary',
                        'GetRecentTrades',
                        'GetFxRates',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'PlaceLimitOrder',
                        'PlaceMarketOrder',
                        'CancelOrder',
                        'GetOpenOrders',
                        'GetClosedOrders',
                        'GetClosedFilledOrders',
                        'GetOrderDetails',
                        'GetAccounts',
                        'GetTransactions',
                        'GetDigitalCurrencyDepositAddress',
                        'GetDigitalCurrencyDepositAddresses',
                        'SynchDigitalCurrencyDepositAddressWithBlockchain',
                        'WithdrawDigitalCurrency',
                        'RequestFiatWithdrawal',
                        'GetTrades',
                        'GetBrokerageFees',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'taker' => 0.5 / 100,
                    'maker' => 0.5 / 100,
                    'percentage' => true,
                    'tierBased' => false,
                ),
            ),
            'commonCurrencies' => array(
                'PLA' => 'PlayChip',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $baseCurrencies = $this->publicGetGetValidPrimaryCurrencyCodes ($params);
        $quoteCurrencies = $this->publicGetGetValidSecondaryCurrencyCodes ($params);
        $result = array();
        for ($i = 0; $i < count($baseCurrencies); $i++) {
            $baseId = $baseCurrencies[$i];
            $base = $this->safe_currency_code($baseId);
            for ($j = 0; $j < count($quoteCurrencies); $j++) {
                $quoteId = $quoteCurrencies[$j];
                $quote = $this->safe_currency_code($quoteId);
                $id = $baseId . '/' . $quoteId;
                $symbol = $base . '/' . $quote;
                $result[] = array(
                    'id' => $id,
                    'symbol' => $symbol,
                    'base' => $base,
                    'quote' => $quote,
                    'baseId' => $baseId,
                    'quoteId' => $quoteId,
                    'info' => $id,
                    'active' => null,
                    'precision' => $this->precision,
                    'limits' => $this->limits,
                );
            }
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $balances = $this->privatePostGetAccounts ($params);
        $result = array( 'info' => $balances );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'CurrencyCode');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'AvailableBalance');
            $account['total'] = $this->safe_float($balance, 'TotalBalance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'primaryCurrencyCode' => $market['baseId'],
            'secondaryCurrencyCode' => $market['quoteId'],
        );
        $response = $this->publicGetGetOrderBook (array_merge($request, $params));
        $timestamp = $this->parse8601($this->safe_string($response, 'CreatedTimestampUtc'));
        return $this->parse_order_book($response, $timestamp, 'BuyOrders', 'SellOrders', 'Price', 'Volume');
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->parse8601($this->safe_string($ticker, 'CreatedTimestampUtc'));
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'LastPrice');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'DayHighestPrice'),
            'low' => $this->safe_float($ticker, 'DayLowestPrice'),
            'bid' => $this->safe_float($ticker, 'CurrentHighestBidPrice'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'CurrentLowestOfferPrice'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $this->safe_float($ticker, 'DayAvgPrice'),
            'baseVolume' => $this->safe_float($ticker, 'DayVolumeXbtInSecondaryCurrrency'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'primaryCurrencyCode' => $market['baseId'],
            'secondaryCurrencyCode' => $market['quoteId'],
        );
        $response = $this->publicGetGetMarketSummary (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_order($order, $market = null) {
        //
        // fetchOrder
        //
        //     {
        //         "OrderGuid" => "c7347e4c-b865-4c94-8f74-d934d4b0b177",
        //         "CreatedTimestampUtc" => "2014-09-23T12:39:34.3817763Z",
        //         "Type" => "MarketBid",
        //         "VolumeOrdered" => 5.0,
        //         "VolumeFilled" => 5.0,
        //         "Price" => null,
        //         "AvgPrice" => 100.0,
        //         "ReservedAmount" => 0.0,
        //         "Status" => "Filled",
        //         "PrimaryCurrencyCode" => "Xbt",
        //         "SecondaryCurrencyCode" => "Usd"
        //     }
        //
        // fetchOpenOrders & fetchClosedOrders
        //
        //     {
        //         "OrderGuid" => "b8f7ad89-e4e4-4dfe-9ea3-514d38b5edb3",
        //         "CreatedTimestampUtc" => "2020-09-08T03:04:18.616367Z",
        //         "OrderType" => "LimitOffer",
        //         "Volume" => 0.0005,
        //         "Outstanding" => 0.0005,
        //         "Price" => 113885.83,
        //         "AvgPrice" => 113885.83,
        //         "Value" => 56.94,
        //         "Status" => "Open",
        //         "PrimaryCurrencyCode" => "Xbt",
        //         "SecondaryCurrencyCode" => "Usd",
        //         "FeePercent" => 0.005,
        //     }
        //
        $symbol = null;
        $baseId = $this->safe_string($order, 'PrimaryCurrencyCode');
        $quoteId = $this->safe_string($order, 'SecondaryCurrencyCode');
        $base = null;
        $quote = null;
        if (($baseId !== null) && ($quoteId !== null)) {
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
        } else if ($market !== null) {
            $symbol = $market['symbol'];
            $base = $market['base'];
            $quote = $market['quote'];
        }
        $orderType = $this->safe_string_2($order, 'Type', 'OrderType');
        $side = null;
        if (mb_strpos($orderType, 'Bid') !== false) {
            $side = 'buy';
        } else if (mb_strpos($orderType, 'Offer') !== false) {
            $side = 'sell';
        }
        if (mb_strpos($orderType, 'Market') !== false) {
            $orderType = 'market';
        } else if (mb_strpos($orderType, 'Limit') !== false) {
            $orderType = 'limit';
        }
        $timestamp = $this->parse8601($this->safe_string($order, 'CreatedTimestampUtc'));
        $amount = $this->safe_float_2($order, 'VolumeOrdered', 'Volume');
        $filled = $this->safe_float($order, 'VolumeFilled');
        $remaining = $this->safe_float($order, 'Outstanding');
        if ($filled === null) {
            if (($remaining !== null) && ($amount !== null)) {
                $filled = max (0, $amount - $remaining);
            }
        }
        if ($remaining === null) {
            if (($filled !== null) && ($amount !== null)) {
                $remaining = max (0, $amount - $filled);
            }
        }
        $feeRate = $this->safe_float($order, 'FeePercent');
        $feeCost = null;
        if ($feeRate !== null) {
            $feeCost = $feeRate * $filled;
        }
        $fee = array(
            'rate' => $feeRate,
            'cost' => $feeCost,
            'currency' => $base,
        );
        $id = $this->safe_string($order, 'OrderGuid');
        $status = $this->parse_order_status($this->safe_string($order, 'Status'));
        $cost = $this->safe_float($order, 'Value');
        $average = $this->safe_float($order, 'AvgPrice');
        $price = $this->safe_float($order, 'Price', $average);
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $orderType,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'average' => $average,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => null,
        );
    }

    public function parse_order_status($status) {
        $statuses = array(
            'Open' => 'open',
            'PartiallyFilled' => 'open',
            'Filled' => 'closed',
            'PartiallyFilledAndCancelled' => 'canceled',
            'Cancelled' => 'canceled',
            'PartiallyFilledAndExpired' => 'canceled',
            'Expired' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetOrderDetails (array_merge(array(
            'orderGuid' => $id,
        ), $params));
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        return $this->parse_order($response, $market);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = $this->ordered(array());
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['primaryCurrencyCode'] = $market['baseId'];
            $request['secondaryCurrencyCode'] = $market['quoteId'];
        }
        if ($limit === null) {
            $limit = 50;
        }
        $request['pageIndex'] = 1;
        $request['pageSize'] = $limit;
        $response = $this->privatePostGetOpenOrders (array_merge($request, $params));
        $data = $this->safe_value($response, 'Data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = $this->ordered(array());
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['primaryCurrencyCode'] = $market['baseId'];
            $request['secondaryCurrencyCode'] = $market['quoteId'];
        }
        if ($limit === null) {
            $limit = 50;
        }
        $request['pageIndex'] = 1;
        $request['pageSize'] = $limit;
        $response = $this->privatePostGetClosedOrders (array_merge($request, $params));
        $data = $this->safe_value($response, 'Data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = 50, $params = array ()) {
        $this->load_markets();
        $pageIndex = $this->safe_integer($params, 'pageIndex', 1);
        if ($limit === null) {
            $limit = 50;
        }
        $request = $this->ordered(array(
            'pageIndex' => $pageIndex,
            'pageSize' => $limit,
        ));
        $response = $this->privatePostGetTrades (array_merge($request, $params));
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        return $this->parse_trades($response['Data'], $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse8601($trade['TradeTimestampUtc']);
        $id = $this->safe_string($trade, 'TradeGuid');
        $orderId = $this->safe_string($trade, 'OrderGuid');
        $price = $this->safe_float_2($trade, 'Price', 'SecondaryCurrencyTradePrice');
        $amount = $this->safe_float_2($trade, 'VolumeTraded', 'PrimaryCurrencyAmount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $baseId = $this->safe_string($trade, 'PrimaryCurrencyCode');
        $quoteId = $this->safe_string($trade, 'SecondaryCurrencyCode');
        $marketId = null;
        if (($baseId !== null) && ($quoteId !== null)) {
            $marketId = $baseId . '/' . $quoteId;
        }
        $symbol = $this->safe_symbol($marketId, $market, '/');
        $side = $this->safe_string($trade, 'OrderType');
        if ($side !== null) {
            if (mb_strpos($side, 'Bid') !== false) {
                $side = 'buy';
            } else if (mb_strpos($side, 'Offer') !== false) {
                $side = 'sell';
            }
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'primaryCurrencyCode' => $market['baseId'],
            'secondaryCurrencyCode' => $market['quoteId'],
            'numberOfRecentTradesToRetrieve' => 50, // max = 50
        );
        $response = $this->publicGetGetRecentTrades (array_merge($request, $params));
        return $this->parse_trades($response['Trades'], $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $capitalizedOrderType = $this->capitalize($type);
        $method = 'privatePostPlace' . $capitalizedOrderType . 'Order';
        $orderType = $capitalizedOrderType;
        $orderType .= ($side === 'sell') ? 'Offer' : 'Bid';
        $request = $this->ordered(array(
            'primaryCurrencyCode' => $market['baseId'],
            'secondaryCurrencyCode' => $market['quoteId'],
            'orderType' => $orderType,
        ));
        if ($type === 'limit') {
            $request['price'] = $price;
        }
        $request['volume'] = $amount;
        $response = $this->$method (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['OrderGuid'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderGuid' => $id,
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $auth = array(
                $url,
                'apiKey=' . $this->apiKey,
                'nonce=' . (string) $nonce,
            );
            $keys = is_array($params) ? array_keys($params) : array();
            for ($i = 0; $i < count($keys); $i++) {
                $key = $keys[$i];
                $value = (string) $params[$key];
                $auth[] = $key . '=' . $value;
            }
            $message = implode(',', $auth);
            $signature = $this->hmac($this->encode($message), $this->encode($this->secret));
            $query = $this->ordered(array());
            $query['apiKey'] = $this->apiKey;
            $query['nonce'] = $nonce;
            $query['signature'] = strtoupper($signature);
            for ($i = 0; $i < count($keys); $i++) {
                $key = $keys[$i];
                $query[$key] = $params[$key];
            }
            $body = $this->json($query);
            $headers = array( 'Content-Type' => 'application/json' );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}
