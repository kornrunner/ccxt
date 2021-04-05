<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\BadSymbol;

class coincheck extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coincheck',
            'name' => 'coincheck',
            'countries' => array( 'JP', 'ID' ),
            'rateLimit' => 1500,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMyTrades' => true,
                'fetchOrderBook' => true,
                'fetchOpenOrders' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87182088-1d6d6380-c2ec-11ea-9c64-8ab9f9b289f5.jpg',
                'api' => 'https://coincheck.com/api',
                'www' => 'https://coincheck.com',
                'doc' => 'https://coincheck.com/documents/exchange/api',
                'fees' => array(
                    'https://coincheck.com/exchange/fee',
                    'https://coincheck.com/info/fee',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'exchange/orders/rate',
                        'order_books',
                        'rate/{pair}',
                        'ticker',
                        'trades',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts',
                        'accounts/balance',
                        'accounts/leverage_balance',
                        'bank_accounts',
                        'deposit_money',
                        'exchange/orders/opens',
                        'exchange/orders/transactions',
                        'exchange/orders/transactions_pagination',
                        'exchange/leverage/positions',
                        'lending/borrows/matches',
                        'send_money',
                        'withdraws',
                    ),
                    'post' => array(
                        'bank_accounts',
                        'deposit_money/{id}/fast',
                        'exchange/orders',
                        'exchange/transfers/to_leverage',
                        'exchange/transfers/from_leverage',
                        'lending/borrows',
                        'lending/borrows/{id}/repay',
                        'send_money',
                        'withdraws',
                    ),
                    'delete' => array(
                        'bank_accounts/{id}',
                        'exchange/orders/{id}',
                        'withdraws/{id}',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/JPY' => array( 'id' => 'btc_jpy', 'symbol' => 'BTC/JPY', 'base' => 'BTC', 'quote' => 'JPY', 'baseId' => 'btc', 'quoteId' => 'jpy' ), // the only real pair
                // 'ETH/JPY' => array( 'id' => 'eth_jpy', 'symbol' => 'ETH/JPY', 'base' => 'ETH', 'quote' => 'JPY', 'baseId' => 'eth', 'quoteId' => 'jpy' ),
                'ETC/JPY' => array( 'id' => 'etc_jpy', 'symbol' => 'ETC/JPY', 'base' => 'ETC', 'quote' => 'JPY', 'baseId' => 'etc', 'quoteId' => 'jpy' ),
                // 'DAO/JPY' => array( 'id' => 'dao_jpy', 'symbol' => 'DAO/JPY', 'base' => 'DAO', 'quote' => 'JPY', 'baseId' => 'dao', 'quoteId' => 'jpy' ),
                // 'LSK/JPY' => array( 'id' => 'lsk_jpy', 'symbol' => 'LSK/JPY', 'base' => 'LSK', 'quote' => 'JPY', 'baseId' => 'lsk', 'quoteId' => 'jpy' ),
                'FCT/JPY' => array( 'id' => 'fct_jpy', 'symbol' => 'FCT/JPY', 'base' => 'FCT', 'quote' => 'JPY', 'baseId' => 'fct', 'quoteId' => 'jpy' ),
                'MONA/JPY' => array( 'id' => 'mona_jpy', 'symbol' => 'MONA/JPY', 'base' => 'MONA', 'quote' => 'JPY', 'baseId' => 'mona', 'quoteId' => 'jpy' ),
                // 'XMR/JPY' => array( 'id' => 'xmr_jpy', 'symbol' => 'XMR/JPY', 'base' => 'XMR', 'quote' => 'JPY', 'baseId' => 'xmr', 'quoteId' => 'jpy' ),
                // 'REP/JPY' => array( 'id' => 'rep_jpy', 'symbol' => 'REP/JPY', 'base' => 'REP', 'quote' => 'JPY', 'baseId' => 'rep', 'quoteId' => 'jpy' ),
                // 'XRP/JPY' => array( 'id' => 'xrp_jpy', 'symbol' => 'XRP/JPY', 'base' => 'XRP', 'quote' => 'JPY', 'baseId' => 'xrp', 'quoteId' => 'jpy' ),
                // 'ZEC/JPY' => array( 'id' => 'zec_jpy', 'symbol' => 'ZEC/JPY', 'base' => 'ZEC', 'quote' => 'JPY', 'baseId' => 'zec', 'quoteId' => 'jpy' ),
                // 'XEM/JPY' => array( 'id' => 'xem_jpy', 'symbol' => 'XEM/JPY', 'base' => 'XEM', 'quote' => 'JPY', 'baseId' => 'xem', 'quoteId' => 'jpy' ),
                // 'LTC/JPY' => array( 'id' => 'ltc_jpy', 'symbol' => 'LTC/JPY', 'base' => 'LTC', 'quote' => 'JPY', 'baseId' => 'ltc', 'quoteId' => 'jpy' ),
                // 'DASH/JPY' => array( 'id' => 'dash_jpy', 'symbol' => 'DASH/JPY', 'base' => 'DASH', 'quote' => 'JPY', 'baseId' => 'dash', 'quoteId' => 'jpy' ),
                // 'ETH/BTC' => array( 'id' => 'eth_btc', 'symbol' => 'ETH/BTC', 'base' => 'ETH', 'quote' => 'BTC', 'baseId' => 'eth', 'quoteId' => 'btc' ),
                'ETC/BTC' => array( 'id' => 'etc_btc', 'symbol' => 'ETC/BTC', 'base' => 'ETC', 'quote' => 'BTC', 'baseId' => 'etc', 'quoteId' => 'btc' ),
                // 'LSK/BTC' => array( 'id' => 'lsk_btc', 'symbol' => 'LSK/BTC', 'base' => 'LSK', 'quote' => 'BTC', 'baseId' => 'lsk', 'quoteId' => 'btc' ),
                // 'FCT/BTC' => array( 'id' => 'fct_btc', 'symbol' => 'FCT/BTC', 'base' => 'FCT', 'quote' => 'BTC', 'baseId' => 'fct', 'quoteId' => 'btc' ),
                // 'XMR/BTC' => array( 'id' => 'xmr_btc', 'symbol' => 'XMR/BTC', 'base' => 'XMR', 'quote' => 'BTC', 'baseId' => 'xmr', 'quoteId' => 'btc' ),
                // 'REP/BTC' => array( 'id' => 'rep_btc', 'symbol' => 'REP/BTC', 'base' => 'REP', 'quote' => 'BTC', 'baseId' => 'rep', 'quoteId' => 'btc' ),
                // 'XRP/BTC' => array( 'id' => 'xrp_btc', 'symbol' => 'XRP/BTC', 'base' => 'XRP', 'quote' => 'BTC', 'baseId' => 'xrp', 'quoteId' => 'btc' ),
                // 'ZEC/BTC' => array( 'id' => 'zec_btc', 'symbol' => 'ZEC/BTC', 'base' => 'ZEC', 'quote' => 'BTC', 'baseId' => 'zec', 'quoteId' => 'btc' ),
                // 'XEM/BTC' => array( 'id' => 'xem_btc', 'symbol' => 'XEM/BTC', 'base' => 'XEM', 'quote' => 'BTC', 'baseId' => 'xem', 'quoteId' => 'btc' ),
                // 'LTC/BTC' => array( 'id' => 'ltc_btc', 'symbol' => 'LTC/BTC', 'base' => 'LTC', 'quote' => 'BTC', 'baseId' => 'ltc', 'quoteId' => 'btc' ),
                // 'DASH/BTC' => array( 'id' => 'dash_btc', 'symbol' => 'DASH/BTC', 'base' => 'DASH', 'quote' => 'BTC', 'baseId' => 'dash', 'quoteId' => 'btc' ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0,
                    'taker' => 0,
                ),
            ),
        ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $balances = $this->privateGetAccountsBalance ($params);
        $result = array( 'info' => $balances );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $currencyId = $this->currency_id($code);
            if (is_array($balances) && array_key_exists($currencyId, $balances)) {
                $account = $this->account();
                $reserved = $currencyId . '_reserved';
                $account['free'] = $this->safe_number($balances, $currencyId);
                $account['used'] = $this->safe_number($balances, $reserved);
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // Only BTC/JPY is meaningful
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $response = $this->privateGetExchangeOrdersOpens ($params);
        $rawOrders = $this->safe_value($response, 'orders', array());
        $parsedOrders = $this->parse_orders($rawOrders, $market, $since, $limit);
        $result = array();
        for ($i = 0; $i < count($parsedOrders); $i++) {
            $result[] = array_merge($parsedOrders[$i], array( 'status' => 'open' ));
        }
        return $result;
    }

    public function parse_order($order, $market = null) {
        //
        // fetchOpenOrders
        //
        //     {                        $id =>  202835,
        //                      order_type => "buy",
        //                            rate =>  26890,
        //                            pair => "btc_jpy",
        //                  pending_amount => "0.5527",
        //       pending_market_buy_amount =>  null,
        //                  stop_loss_rate =>  null,
        //                      created_at => "2015-01-10T05:55:38.000Z" }
        //
        // todo => add formats for fetchOrder, fetchClosedOrders here
        //
        $id = $this->safe_string($order, 'id');
        $side = $this->safe_string($order, 'order_type');
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $amount = $this->safe_number($order, 'pending_amount');
        $remaining = $this->safe_number($order, 'pending_amount');
        $price = $this->safe_number($order, 'rate');
        $status = null;
        $marketId = $this->safe_string($order, 'pair');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        return $this->safe_order(array(
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'amount' => $amount,
            'remaining' => $remaining,
            'filled' => null,
            'side' => $side,
            'type' => null,
            'timeInForce' => null,
            'postOnly' => null,
            'status' => $status,
            'symbol' => $symbol,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'fee' => null,
            'info' => $order,
            'average' => null,
            'trades' => null,
        ));
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        $response = $this->publicGetOrderBooks (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        if ($symbol !== 'BTC/JPY') {
            throw new BadSymbol($this->id . ' fetchTicker () supports BTC/JPY only');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        $ticker = $this->publicGetTicker (array_merge($request, $params));
        $timestamp = $this->safe_timestamp($ticker, 'timestamp');
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        $id = $this->safe_string($trade, 'id');
        $price = $this->safe_number($trade, 'rate');
        $marketId = $this->safe_string($trade, 'pair');
        $market = $this->safe_value($this->markets_by_id, $marketId, $market);
        $symbol = null;
        $baseId = null;
        $quoteId = null;
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $baseId = $market['baseId'];
                $quoteId = $market['quoteId'];
                $symbol = $market['symbol'];
            } else {
                $ids = explode('_', $marketId);
                $baseId = $ids[0];
                $quoteId = $ids[1];
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
        $takerOrMaker = null;
        $amount = null;
        $cost = null;
        $side = null;
        $fee = null;
        $orderId = null;
        if (is_array($trade) && array_key_exists('liquidity', $trade)) {
            if ($this->safe_string($trade, 'liquidity') === 'T') {
                $takerOrMaker = 'taker';
            } else if ($this->safe_string($trade, 'liquidity') === 'M') {
                $takerOrMaker = 'maker';
            }
            $funds = $this->safe_value($trade, 'funds', array());
            $amount = $this->safe_number($funds, $baseId);
            $cost = $this->safe_number($funds, $quoteId);
            $fee = array(
                'currency' => $this->safe_string($trade, 'fee_currency'),
                'cost' => $this->safe_number($trade, 'fee'),
            );
            $side = $this->safe_string($trade, 'side');
            $orderId = $this->safe_string($trade, 'order_id');
        } else {
            $amount = $this->safe_number($trade, 'amount');
            $side = $this->safe_string($trade, 'order_type');
        }
        if ($cost === null) {
            if ($amount !== null) {
                if ($price !== null) {
                    $cost = $amount * $price;
                }
            }
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'order' => $orderId,
            'takerOrMaker' => $takerOrMaker,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $response = $this->privateGetExchangeOrdersTransactions (array_merge(array(), $params));
        $transactions = $this->safe_value($response, 'transactions', array());
        return $this->parse_trades($transactions, $market, $since, $limit);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetTrades (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair' => $this->market_id($symbol),
        );
        if ($type === 'market') {
            $order_type = $type . '_' . $side;
            $request['order_type'] = $order_type;
            $prefix = ($side === 'buy') ? ($order_type . '_') : '';
            $request[$prefix . 'amount'] = $amount;
        } else {
            $request['order_type'] = $side;
            $request['rate'] = $price;
            $request['amount'] = $amount;
        }
        $response = $this->privatePostExchangeOrders (array_merge($request, $params));
        $id = $this->safe_string($response, 'id');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => $id,
        );
        return $this->privateDeleteExchangeOrdersId (array_merge($request, $params));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $queryString = '';
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode($this->keysort($query));
                }
            } else {
                if ($query) {
                    $body = $this->urlencode($this->keysort($query));
                    $queryString = $body;
                }
            }
            $auth = $nonce . $url . $queryString;
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'ACCESS-KEY' => $this->apiKey,
                'ACCESS-NONCE' => $nonce,
                'ACCESS-SIGNATURE' => $this->hmac($this->encode($auth), $this->encode($this->secret)),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if ($api === 'public') {
            return $response;
        }
        if (is_array($response) && array_key_exists('success', $response)) {
            if ($response['success']) {
                return $response;
            }
        }
        throw new ExchangeError($this->id . ' ' . $this->json($response));
    }
}
