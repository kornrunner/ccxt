<?php

namespace ccxt;

use Exception as Exception; // a common import

class kuna extends acx {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'kuna',
            'name' => 'Kuna',
            'countries' => array ( 'UA' ),
            'rateLimit' => 1000,
            'version' => 'v2',
            'has' => array (
                'CORS' => false,
                'fetchTickers' => true,
                'fetchOpenOrders' => true,
                'fetchMyTrades' => true,
                'withdraw' => false,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/31697638-912824fa-b3c1-11e7-8c36-cf9606eb94ac.jpg',
                'api' => 'https://kuna.io',
                'www' => 'https://kuna.io',
                'doc' => 'https://kuna.io/documents/api',
                'fees' => 'https://kuna.io/documents/api',
            ),
            'fees' => array (
                'trading' => array (
                    'taker' => 0.25 / 100,
                    'maker' => 0.25 / 100,
                ),
                'funding' => array (
                    'withdraw' => array (
                        'UAH' => '1%',
                        'BTC' => 0.001,
                        'BCH' => 0.001,
                        'ETH' => 0.01,
                        'WAVES' => 0.01,
                        'GOL' => 0.0,
                        'GBG' => 0.0,
                        // 'RMC' => 0.001 BTC
                        // 'ARN' => 0.01 ETH
                        // 'R' => 0.01 ETH
                        // 'EVR' => 0.01 ETH
                    ),
                    'deposit' => array (
                        // 'UAH' => (amount) => amount * 0.001 . 5
                    ),
                ),
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $quotes = array ( 'btc', 'eth', 'eurs', 'gbg', 'uah' );
        $pricePrecisions = array (
            'UAH' => 0,
        );
        $markets = array ();
        $tickers = $this->publicGetTickers ();
        $ids = is_array ($tickers) ? array_keys ($tickers) : array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            for ($j = 0; $j < count ($quotes); $j++) {
                $quoteId = $quotes[$j];
                if (mb_strpos ($id, $quoteId) > 0) {
                    $baseId = str_replace ($quoteId, '', $id);
                    $base = strtoupper ($baseId);
                    $quote = strtoupper ($quoteId);
                    $base = $this->common_currency_code($base);
                    $quote = $this->common_currency_code($quote);
                    $symbol = $base . '/' . $quote;
                    $precision = array (
                        'amount' => 6,
                        'price' => $this->safe_integer($pricePrecisions, $quote, 6),
                    );
                    $markets[] = array (
                        'id' => $id,
                        'symbol' => $symbol,
                        'base' => $base,
                        'quote' => $quote,
                        'baseId' => $baseId,
                        'quoteId' => $quoteId,
                        'precision' => $precision,
                        'limits' => array (
                            'amount' => array (
                                'min' => pow (10, -$precision['amount']),
                                'max' => pow (10, $precision['amount']),
                            ),
                            'price' => array (
                                'min' => pow (10, -$precision['price']),
                                'max' => pow (10, $precision['price']),
                            ),
                            'cost' => array (
                                'min' => null,
                                'max' => null,
                            ),
                        ),
                    );
                    break;
                }
            }
        }
        return $markets;
    }

    public function fetch_l3_order_book ($symbol, $limit = null, $params = array ()) {
        return $this->fetch_order_book($symbol, $limit, $params);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null)
            throw new ArgumentsRequired ($this->id . ' fetchOpenOrders requires a $symbol argument');
        $this->load_markets();
        $market = $this->market ($symbol);
        $orders = $this->privateGetOrders (array_merge (array (
            'market' => $market['id'],
        ), $params));
        // todo emulation of fetchClosedOrders, fetchOrders, fetchOrder
        // with order cache . fetchOpenOrders
        // as in BTC-e, Liqui, Yobit, DSX, Tidex, WEX
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->parse8601 ($trade['created_at']);
        $symbol = null;
        if ($market)
            $symbol = $market['symbol'];
        $side = $this->safe_string($trade, 'side');
        if ($side !== null) {
            $sideMap = array (
                'ask' => 'sell',
                'bid' => 'buy',
            );
            $side = $this->safe_string($sideMap, $side);
        }
        $cost = $this->safe_float($trade, 'funds');
        $order = $this->safe_string($trade, 'order_id');
        return array (
            'id' => (string) $trade['id'],
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'price' => $this->safe_float($trade, 'price'),
            'amount' => $this->safe_float($trade, 'volume'),
            'cost' => $cost,
            'order' => $order,
            'info' => $trade,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetTrades (array_merge (array (
            'market' => $market['id'],
        ), $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null)
            throw new ArgumentsRequired ($this->id . ' fetchOpenOrders requires a $symbol argument');
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->privateGetTradesMy (array ( 'market' => $market['id'] ));
        return $this->parse_trades($response, $market, $since, $limit);
    }
}
