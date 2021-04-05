<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\ArgumentsRequired;

class coinspot extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinspot',
            'name' => 'CoinSpot',
            'countries' => array( 'AU' ), // Australia
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => false,
                'CORS' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/28208429-3cacdf9a-6896-11e7-854e-4c79a772a30f.jpg',
                'api' => array(
                    'public' => 'https://www.coinspot.com.au/pubapi',
                    'private' => 'https://www.coinspot.com.au/api',
                ),
                'www' => 'https://www.coinspot.com.au',
                'doc' => 'https://www.coinspot.com.au/api',
                'referral' => 'https://www.coinspot.com.au/register?code=PJURCU',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'latest',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'orders',
                        'orders/history',
                        'my/coin/deposit',
                        'my/coin/send',
                        'quote/buy',
                        'quote/sell',
                        'my/balances',
                        'my/orders',
                        'my/buy',
                        'my/sell',
                        'my/buy/cancel',
                        'my/sell/cancel',
                        'ro/my/balances',
                        'ro/my/balances/{cointype}',
                        'ro/my/deposits',
                        'ro/my/withdrawals',
                        'ro/my/transactions',
                        'ro/my/transactions/{cointype}',
                        'ro/my/transactions/open',
                        'ro/my/transactions/{cointype}/open',
                        'ro/my/sendreceive',
                        'ro/my/affiliatepayments',
                        'ro/my/referralpayments',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/AUD' => array( 'id' => 'btc', 'symbol' => 'BTC/AUD', 'base' => 'BTC', 'quote' => 'AUD', 'baseId' => 'btc', 'quoteId' => 'aud' ),
                'ETH/AUD' => array( 'id' => 'eth', 'symbol' => 'ETH/AUD', 'base' => 'ETH', 'quote' => 'AUD', 'baseId' => 'eth', 'quoteId' => 'aud' ),
                'XRP/AUD' => array( 'id' => 'xrp', 'symbol' => 'XRP/AUD', 'base' => 'XRP', 'quote' => 'AUD', 'baseId' => 'xrp', 'quoteId' => 'aud' ),
                'LTC/AUD' => array( 'id' => 'ltc', 'symbol' => 'LTC/AUD', 'base' => 'LTC', 'quote' => 'AUD', 'baseId' => 'ltc', 'quoteId' => 'aud' ),
                'DOGE/AUD' => array( 'id' => 'doge', 'symbol' => 'DOGE/AUD', 'base' => 'DOGE', 'quote' => 'AUD', 'baseId' => 'doge', 'quoteId' => 'aud' ),
                'RFOX/AUD' => array( 'id' => 'rfox', 'symbol' => 'RFOX/AUD', 'base' => 'RFOX', 'quote' => 'AUD', 'baseId' => 'rfox', 'quoteId' => 'aud' ),
                'POWR/AUD' => array( 'id' => 'powr', 'symbol' => 'POWR/AUD', 'base' => 'POWR', 'quote' => 'AUD', 'baseId' => 'powr', 'quoteId' => 'aud' ),
                'NEO/AUD' => array( 'id' => 'neo', 'symbol' => 'NEO/AUD', 'base' => 'NEO', 'quote' => 'AUD', 'baseId' => 'neo', 'quoteId' => 'aud' ),
                'TRX/AUD' => array( 'id' => 'trx', 'symbol' => 'TRX/AUD', 'base' => 'TRX', 'quote' => 'AUD', 'baseId' => 'trx', 'quoteId' => 'aud' ),
                'EOS/AUD' => array( 'id' => 'eos', 'symbol' => 'EOS/AUD', 'base' => 'EOS', 'quote' => 'AUD', 'baseId' => 'eos', 'quoteId' => 'aud' ),
                'XLM/AUD' => array( 'id' => 'xlm', 'symbol' => 'XLM/AUD', 'base' => 'XLM', 'quote' => 'AUD', 'baseId' => 'xlm', 'quoteId' => 'aud' ),
                'RHOC/AUD' => array( 'id' => 'rhoc', 'symbol' => 'RHOC/AUD', 'base' => 'RHOC', 'quote' => 'AUD', 'baseId' => 'rhoc', 'quoteId' => 'aud' ),
                'GAS/AUD' => array( 'id' => 'gas', 'symbol' => 'GAS/AUD', 'base' => 'GAS', 'quote' => 'AUD', 'baseId' => 'gas', 'quoteId' => 'aud' ),
            ),
            'commonCurrencies' => array(
                'DRK' => 'DASH',
            ),
            'options' => array(
                'fetchBalance' => 'private_post_my_balances',
            ),
        ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $method = $this->safe_string($this->options, 'fetchBalance', 'private_post_my_balances');
        $response = $this->$method ($params);
        //
        // read-write api keys
        //
        //     ...
        //
        // read-only api keys
        //
        //     {
        //         "status":"ok",
        //         "$balances":array(
        //             {
        //                 "LTC":array("$balance":0.1,"audbalance":16.59,"rate":165.95)
        //             }
        //         )
        //     }
        //
        $result = array( 'info' => $response );
        $balances = $this->safe_value_2($response, 'balance', 'balances');
        if (gettype($balances) === 'array' && count(array_filter(array_keys($balances), 'is_string')) == 0) {
            for ($i = 0; $i < count($balances); $i++) {
                $currencies = $balances[$i];
                $currencyIds = is_array($currencies) ? array_keys($currencies) : array();
                for ($j = 0; $j < count($currencyIds); $j++) {
                    $currencyId = $currencyIds[$j];
                    $balance = $currencies[$currencyId];
                    $code = $this->safe_currency_code($currencyId);
                    $account = $this->account();
                    $account['total'] = $this->safe_number($balance, 'balance');
                    $result[$code] = $account;
                }
            }
        } else {
            $currencyIds = is_array($balances) ? array_keys($balances) : array();
            for ($i = 0; $i < count($currencyIds); $i++) {
                $currencyId = $currencyIds[$i];
                $code = $this->safe_currency_code($currencyId);
                $account = $this->account();
                $account['total'] = $this->safe_number($balances, $currencyId);
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'cointype' => $market['id'],
        );
        $orderbook = $this->privatePostOrders (array_merge($request, $params));
        return $this->parse_order_book($orderbook, null, 'buyorders', 'sellorders', 'rate', 'amount');
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetLatest ($params);
        $id = $this->market_id($symbol);
        $id = strtolower($id);
        $ticker = $response['prices'][$id];
        $timestamp = $this->milliseconds();
        $last = $this->safe_number($ticker, 'last');
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
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'cointype' => $market['id'],
        );
        $response = $this->privatePostOrdersHistory (array_merge($request, $params));
        //
        //     {
        //         "status":"ok",
        //         "orders":array(
        //             array("amount":0.00102091,"rate":21549.09999991,"total":21.99969168,"coin":"BTC","solddate":1604890646143,"$market":"BTC/AUD"),
        //         ),
        //     }
        //
        $trades = $this->safe_value($response, 'orders', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        //
        // public fetchTrades
        //
        //     {
        //         "$amount":0.00102091,
        //         "rate":21549.09999991,
        //         "total":21.99969168,
        //         "coin":"BTC",
        //         "solddate":1604890646143,
        //         "$market":"BTC/AUD"
        //     }
        //
        $price = $this->safe_number($trade, 'rate');
        $amount = $this->safe_number($trade, 'amount');
        $cost = $this->safe_number($trade, 'total');
        if (($cost === null) && ($price !== null) && ($amount !== null)) {
            $cost = $price * $amount;
        }
        $timestamp = $this->safe_integer($trade, 'solddate');
        $marketId = $this->safe_string($trade, 'market');
        $symbol = $this->safe_symbol($marketId, $market, '/');
        return array(
            'info' => $trade,
            'id' => null,
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'order' => null,
            'type' => null,
            'side' => null,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $method = 'privatePostMy' . $this->capitalize($side);
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $request = array(
            'cointype' => $this->market_id($symbol),
            'amount' => $amount,
            'rate' => $price,
        );
        return $this->$method (array_merge($request, $params));
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $side = $this->safe_string($params, 'side');
        if ($side !== 'buy' && $side !== 'sell') {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a $side parameter, "buy" or "sell"');
        }
        $params = $this->omit($params, 'side');
        $method = 'privatePostMy' . $this->capitalize($side) . 'Cancel';
        $request = array(
            'id' => $id,
        );
        return $this->$method (array_merge($request, $params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        if (!$this->apiKey) {
            throw new AuthenticationError($this->id . ' requires apiKey for all requests');
        }
        $url = $this->urls['api'][$api] . '/' . $path;
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->json(array_merge(array( 'nonce' => $nonce ), $params));
            $headers = array(
                'Content-Type' => 'application/json',
                'key' => $this->apiKey,
                'sign' => $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}
