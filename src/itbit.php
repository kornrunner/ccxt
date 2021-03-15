<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\ArgumentsRequired;

class itbit extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'itbit',
            'name' => 'itBit',
            'countries' => array( 'US' ),
            'rateLimit' => 2000,
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
                'fetchTransactions' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27822159-66153620-60ad-11e7-89e7-005f6d7f3de0.jpg',
                'api' => 'https://api.itbit.com',
                'www' => 'https://www.itbit.com',
                'doc' => array(
                    'https://api.itbit.com/docs',
                    'https://www.itbit.com/api',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'markets/{symbol}/ticker',
                        'markets/{symbol}/order_book',
                        'markets/{symbol}/trades',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'wallets',
                        'wallets/{walletId}',
                        'wallets/{walletId}/balances/{currencyCode}',
                        'wallets/{walletId}/funding_history',
                        'wallets/{walletId}/trades',
                        'wallets/{walletId}/orders',
                        'wallets/{walletId}/orders/{id}',
                    ),
                    'post' => array(
                        'wallet_transfers',
                        'wallets',
                        'wallets/{walletId}/cryptocurrency_deposits',
                        'wallets/{walletId}/cryptocurrency_withdrawals',
                        'wallets/{walletId}/orders',
                        'wire_withdrawal',
                    ),
                    'delete' => array(
                        'wallets/{walletId}/orders/{id}',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/USD' => array( 'id' => 'XBTUSD', 'symbol' => 'BTC/USD', 'base' => 'BTC', 'quote' => 'USD', 'baseId' => 'XBT', 'quoteId' => 'USD' ),
                'BTC/SGD' => array( 'id' => 'XBTSGD', 'symbol' => 'BTC/SGD', 'base' => 'BTC', 'quote' => 'SGD', 'baseId' => 'XBT', 'quoteId' => 'SGD' ),
                'BTC/EUR' => array( 'id' => 'XBTEUR', 'symbol' => 'BTC/EUR', 'base' => 'BTC', 'quote' => 'EUR', 'baseId' => 'XBT', 'quoteId' => 'EUR' ),
                'ETH/USD' => array( 'id' => 'ETHUSD', 'symbol' => 'ETH/USD', 'base' => 'ETH', 'quote' => 'USD', 'baseId' => 'ETH', 'quoteId' => 'USD' ),
                'ETH/EUR' => array( 'id' => 'ETHEUR', 'symbol' => 'ETH/EUR', 'base' => 'ETH', 'quote' => 'EUR', 'baseId' => 'ETH', 'quoteId' => 'EUR' ),
                'ETH/SGD' => array( 'id' => 'ETHSGD', 'symbol' => 'ETH/SGD', 'base' => 'ETH', 'quote' => 'SGD', 'baseId' => 'ETH', 'quoteId' => 'SGD' ),
                'PAXGUSD' => array( 'id' => 'PAXGUSD', 'symbol' => 'PAXG/USD', 'base' => 'PAXG', 'quote' => 'USD', 'baseId' => 'PAXG', 'quoteId' => 'USD' ),
                'BCHUSD' => array( 'id' => 'BCHUSD', 'symbol' => 'BCH/USD', 'base' => 'BCH', 'quote' => 'USD', 'baseId' => 'BCH', 'quoteId' => 'USD' ),
                'LTCUSD' => array( 'id' => 'LTCUSD', 'symbol' => 'LTC/USD', 'base' => 'LTC', 'quote' => 'USD', 'baseId' => 'LTC', 'quoteId' => 'USD' ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => -0.03 / 100,
                    'taker' => 0.35 / 100,
                ),
            ),
            'commonCurrencies' => array(
                'XBT' => 'BTC',
            ),
        ));
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        $orderbook = $this->publicGetMarketsSymbolOrderBook (array_merge($request, $params));
        return $this->parse_order_book($orderbook);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        $ticker = $this->publicGetMarketsSymbolTicker (array_merge($request, $params));
        $serverTimeUTC = $this->safe_string($ticker, 'serverTimeUTC');
        if (!$serverTimeUTC) {
            throw new ExchangeError($this->id . ' fetchTicker returned a bad response => ' . $this->json($ticker));
        }
        $timestamp = $this->parse8601($serverTimeUTC);
        $vwap = $this->safe_float($ticker, 'vwap24h');
        $baseVolume = $this->safe_float($ticker, 'volume24h');
        $quoteVolume = null;
        if ($baseVolume !== null && $vwap !== null) {
            $quoteVolume = $baseVolume * $vwap;
        }
        $last = $this->safe_float($ticker, 'lastPrice');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high24h'),
            'low' => $this->safe_float($ticker, 'low24h'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => $this->safe_float($ticker, 'openToday'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         $timestamp => "2015-05-22T17:45:34.7570000Z",
        //         matchNumber => "5CR1JEUBBM8J",
        //         $price => "351.45000000",
        //         $amount => "0.00010000"
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         "$orderId" => "248ffda4-83a0-4033-a5bb-8929d523f59f",
        //         "$timestamp" => "2015-05-11T14:48:01.9870000Z",
        //         "instrument" => "XBTUSD",
        //         "direction" => "buy",                      // buy or sell
        //         "currency1" => "XBT",                      // $base currency
        //         "currency1Amount" => "0.00010000",         // order $amount in $base currency
        //         "currency2" => "USD",                      // $quote currency
        //         "currency2Amount" => "0.0250530000000000", // order $cost in $quote currency
        //         "rate" => "250.53000000",
        //         "commissionPaid" => "0.00000000",   // net $trade fee paid after using any available rebate balance
        //         "commissionCurrency" => "USD",
        //         "$rebatesApplied" => "-0.000125265", // negative values represent $amount of rebate balance used for trades removing liquidity from order book; positive values represent $amount of rebate balance earned from trades adding liquidity to order book
        //         "$rebateCurrency" => "USD",
        //         "executionId" => "23132"
        //     }
        //
        $id = $this->safe_string_2($trade, 'executionId', 'matchNumber');
        $timestamp = $this->parse8601($this->safe_string($trade, 'timestamp'));
        $side = $this->safe_string($trade, 'direction');
        $orderId = $this->safe_string($trade, 'orderId');
        $feeCost = $this->safe_float($trade, 'commissionPaid');
        $feeCurrencyId = $this->safe_string($trade, 'commissionCurrency');
        $feeCurrency = $this->safe_currency_code($feeCurrencyId);
        $rebatesApplied = $this->safe_float($trade, 'rebatesApplied');
        if ($rebatesApplied !== null) {
            $rebatesApplied = -$rebatesApplied;
        }
        $rebateCurrencyId = $this->safe_string($trade, 'rebateCurrency');
        $rebateCurrency = $this->safe_currency_code($rebateCurrencyId);
        $price = $this->safe_float_2($trade, 'price', 'rate');
        $amount = $this->safe_float_2($trade, 'currency1Amount', 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $symbol = null;
        $marketId = $this->safe_string($trade, 'instrument');
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                $baseId = $this->safe_string($trade, 'currency1');
                $quoteId = $this->safe_string($trade, 'currency2');
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if ($symbol === null) {
            if ($market !== null) {
                $symbol = $market['symbol'];
            }
        }
        $result = array(
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
            'cost' => $cost,
            'fee' => null,
        );
        if ($feeCost !== null) {
            if ($rebatesApplied !== null) {
                if ($feeCurrency === $rebateCurrency) {
                    $feeCost = $this->sum($feeCost, $rebatesApplied);
                    $result['fee'] = array(
                        'cost' => $feeCost,
                        'currency' => $feeCurrency,
                    );
                } else {
                    $result['fees'] = array(
                        array(
                            'cost' => $feeCost,
                            'currency' => $feeCurrency,
                        ),
                        array(
                            'cost' => $rebatesApplied,
                            'currency' => $rebateCurrency,
                        ),
                    );
                }
            } else {
                $result['fee'] = array(
                    'cost' => $feeCost,
                    'currency' => $feeCurrency,
                );
            }
        }
        if (!(is_array($result) && array_key_exists('fee', $result))) {
            if (!(is_array($result) && array_key_exists('fees', $result))) {
                $result['fee'] = null;
            }
        }
        return $result;
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $walletId = $this->safe_string($params, 'walletId');
        if ($walletId === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $walletId parameter');
        }
        $request = array(
            'walletId' => $walletId,
        );
        if ($limit !== null) {
            $request['perPage'] = $limit; // default 50, max 50
        }
        $response = $this->privateGetWalletsWalletIdFundingHistory (array_merge($request, $params));
        //     array( bankName => 'USBC (usd)',
        //         withdrawalId => 94740,
        //         holdingPeriodCompletionDate => '2018-04-16T07:57:05.9606869',
        //         $time => '2018-04-16T07:57:05.9600000',
        //         $currency => 'USD',
        //         $transactionType => 'Withdrawal',
        //         amount => '2186.72000000',
        //         walletName => 'Wallet',
        //         $status => 'completed' ),
        //
        //     { "$time" => "2018-01-02T19:52:22.4176503",
        //     "amount" => "0.50000000",
        //     "$status" => "completed",
        //     "$txnHash" => "1b6fff67ed83cb9e9a38ca4976981fc047322bc088430508fe764a127d3ace95",
        //     "$currency" => "XBT",
        //     "walletName" => "Wallet",
        //     "$transactionType" => "Deposit",
        //     "$destinationAddress" => "3AAWTH9et4e8o51YKp9qPpmujrNXKwHWNX"}
        $items = $response['fundingHistory'];
        $result = array();
        for ($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            $time = $this->safe_string($item, 'time');
            $timestamp = $this->parse8601($time);
            $currency = $this->safe_string($item, 'currency');
            $destinationAddress = $this->safe_string($item, 'destinationAddress');
            $txnHash = $this->safe_string($item, 'txnHash');
            $transactionType = $this->safe_string_lower($item, 'transactionType');
            $transactionStatus = $this->safe_string($item, 'status');
            $status = $this->parse_transfer_status($transactionStatus);
            $result[] = array(
                'id' => $this->safe_string($item, 'withdrawalId'),
                'timestamp' => $timestamp,
                'datetime' => $this->iso8601($timestamp),
                'currency' => $this->safe_currency_code($currency),
                'address' => $destinationAddress,
                'tag' => null,
                'txid' => $txnHash,
                'type' => $transactionType,
                'status' => $status,
                'amount' => $this->safe_float($item, 'amount'),
                'fee' => null,
                'info' => $item,
            );
        }
        return $result;
    }

    public function parse_transfer_status($status) {
        $options = array(
            'cancelled' => 'canceled',
            'completed' => 'ok',
        );
        return $this->safe_string($options, $status, 'pending');
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $walletId = $this->safe_string($params, 'walletId');
        if ($walletId === null) {
            throw new ExchangeError($this->id . ' fetchMyTrades() requires a $walletId parameter');
        }
        $request = array(
            'walletId' => $walletId,
        );
        if ($since !== null) {
            $request['rangeStart'] = $this->ymdhms($since, 'T');
        }
        if ($limit !== null) {
            $request['perPage'] = $limit; // default 50, max 50
        }
        $response = $this->privateGetWalletsWalletIdTrades (array_merge($request, $params));
        //
        //     {
        //         "totalNumberOfRecords" => "2",
        //         "currentPageNumber" => "1",
        //         "latestExecutionId" => "332", // most recent execution at time of $response
        //         "recordsPerPage" => "50",
        //         "tradingHistory" => array(
        //             array(
        //                 "orderId" => "248ffda4-83a0-4033-a5bb-8929d523f59f",
        //                 "timestamp" => "2015-05-11T14:48:01.9870000Z",
        //                 "instrument" => "XBTUSD",
        //                 "direction" => "buy",                      // buy or sell
        //                 "currency1" => "XBT",                      // base currency
        //                 "currency1Amount" => "0.00010000",         // order amount in base currency
        //                 "currency2" => "USD",                      // quote currency
        //                 "currency2Amount" => "0.0250530000000000", // order cost in quote currency
        //                 "rate" => "250.53000000",
        //                 "commissionPaid" => "0.00000000",   // net trade fee paid after using any available rebate balance
        //                 "commissionCurrency" => "USD",
        //                 "rebatesApplied" => "-0.000125265", // negative values represent amount of rebate balance used for $trades removing liquidity from order book; positive values represent amount of rebate balance earned from $trades adding liquidity to order book
        //                 "rebateCurrency" => "USD",
        //                 "executionId" => "23132"
        //             ),
        //         ),
        //     }
        //
        $trades = $this->safe_value($response, 'tradingHistory', array());
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetMarketsSymbolTrades (array_merge($request, $params));
        //
        //     {
        //         count => 3,
        //         recentTrades => array(
        //             array(
        //                 timestamp => "2015-05-22T17:45:34.7570000Z",
        //                 matchNumber => "5CR1JEUBBM8J",
        //                 price => "351.45000000",
        //                 amount => "0.00010000"
        //             ),
        //         )
        //     }
        //
        $trades = $this->safe_value($response, 'recentTrades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->fetch_wallets($params);
        $balances = $response[0]['balances'];
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'availableBalance');
            $account['total'] = $this->safe_float($balance, 'totalBalance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_wallets($params = array ()) {
        $this->load_markets();
        if (!$this->uid) {
            throw new AuthenticationError($this->id . ' fetchWallets() requires uid API credential');
        }
        $request = array(
            'userId' => $this->uid,
        );
        return $this->privateGetWallets (array_merge($request, $params));
    }

    public function fetch_wallet($walletId, $params = array ()) {
        $this->load_markets();
        $request = array(
            'walletId' => $walletId,
        );
        return $this->privateGetWalletsWalletId (array_merge($request, $params));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'open',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'filled',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $walletIdInParams = (is_array($params) && array_key_exists('walletId', $params));
        if (!$walletIdInParams) {
            throw new ExchangeError($this->id . ' fetchOrders() requires a $walletId parameter');
        }
        $walletId = $params['walletId'];
        $request = array(
            'walletId' => $walletId,
        );
        $response = $this->privateGetWalletsWalletIdOrders (array_merge($request, $params));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'submitted' => 'open', // order pending book entry
            'open' => 'open',
            'filled' => 'closed',
            'cancelled' => 'canceled',
            'rejected' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "$id" => "13d6af57-8b0b-41e5-af30-becf0bcc574d",
        //         "walletId" => "7e037345-1288-4c39-12fe-d0f99a475a98",
        //         "$side" => "buy",
        //         "instrument" => "XBTUSD",
        //         "$type" => "limit",
        //         "currency" => "XBT",
        //         "$amount" => "2.50000000",
        //         "displayAmount" => "2.50000000",
        //         "$price" => "650.00000000",
        //         "volumeWeightedAveragePrice" => "0.00000000",
        //         "amountFilled" => "0.00000000",
        //         "createdTime" => "2014-02-11T17:05:15Z",
        //         "status" => "submitted",
        //         "funds" => null,
        //         "metadata" => array(),
        //         "clientOrderIdentifier" => null,
        //         "$postOnly" => "False"
        //     }
        //
        $side = $this->safe_string($order, 'side');
        $type = $this->safe_string($order, 'type');
        $symbol = $this->markets_by_id[$order['instrument']]['symbol'];
        $timestamp = $this->parse8601($order['createdTime']);
        $amount = $this->safe_float($order, 'amount');
        $filled = $this->safe_float($order, 'amountFilled');
        $fee = null;
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'volumeWeightedAveragePrice');
        $clientOrderId = $this->safe_string($order, 'clientOrderIdentifier');
        $id = $this->safe_string($order, 'id');
        $postOnlyString = $this->safe_string($order, 'postOnly');
        $postOnly = ($postOnlyString === 'True');
        return $this->safe_order(array(
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $this->parse_order_status($this->safe_string($order, 'status')),
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => $postOnly,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'average' => $average,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => null,
            'fee' => $fee,
            // 'trades' => $this->parse_trades($order['trades'], $market),
            'trades' => null,
        ));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $walletIdInParams = (is_array($params) && array_key_exists('walletId', $params));
        if (!$walletIdInParams) {
            throw new ExchangeError($this->id . ' createOrder() requires a walletId parameter');
        }
        $amount = (string) $amount;
        $price = (string) $price;
        $market = $this->market($symbol);
        $request = array(
            'side' => $side,
            'type' => $type,
            'currency' => str_replace($market['quote'], '', $market['id']),
            'amount' => $amount,
            'display' => $amount,
            'price' => $price,
            'instrument' => $market['id'],
        );
        $response = $this->privatePostWalletsWalletIdOrders (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['id'],
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $walletIdInParams = (is_array($params) && array_key_exists('walletId', $params));
        if (!$walletIdInParams) {
            throw new ExchangeError($this->id . ' fetchOrder() requires a walletId parameter');
        }
        $request = array(
            'id' => $id,
        );
        $response = $this->privateGetWalletsWalletIdOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $walletIdInParams = (is_array($params) && array_key_exists('walletId', $params));
        if (!$walletIdInParams) {
            throw new ExchangeError($this->id . ' cancelOrder() requires a walletId parameter');
        }
        $request = array(
            'id' => $id,
        );
        return $this->privateDeleteWalletsWalletIdOrdersId (array_merge($request, $params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($method === 'GET' && $query) {
            $url .= '?' . $this->urlencode($query);
        }
        if ($method === 'POST' && $query) {
            $body = $this->json($query);
        }
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $timestamp = $nonce;
            $authBody = ($method === 'POST') ? $body : '';
            $auth = array( $method, $url, $authBody, $nonce, $timestamp );
            $message = $nonce . str_replace('\\/', '/', $this->json($auth));
            $hash = $this->hash($this->encode($message), 'sha256', 'binary');
            $binaryUrl = $this->encode($url);
            $binhash = $this->binary_concat($binaryUrl, $hash);
            $signature = $this->hmac($binhash, $this->encode($this->secret), 'sha512', 'base64');
            $headers = array(
                'Authorization' => $this->apiKey . ':' . $signature,
                'Content-Type' => 'application/json',
                'X-Auth-Timestamp' => $timestamp,
                'X-Auth-Nonce' => $nonce,
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('code', $response)) {
            throw new ExchangeError($this->id . ' ' . $this->json($response));
        }
        return $response;
    }
}
