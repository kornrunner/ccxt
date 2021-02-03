<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ArgumentsRequired;

class kuna extends acx {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'kuna',
            'name' => 'Kuna',
            'countries' => array( 'UA' ),
            'rateLimit' => 1000,
            'version' => 'v2',
            'has' => array(
                'CORS' => false,
                'fetchTickers' => true,
                'fetchOHLCV' => 'emulated',
                'fetchOpenOrders' => true,
                'fetchMyTrades' => true,
                'withdraw' => false,
            ),
            'timeframes' => null,
            'urls' => array(
                'referral' => 'https://kuna.io?r=kunaid-gvfihe8az7o4',
                'logo' => 'https://user-images.githubusercontent.com/51840849/87153927-f0578b80-c2c0-11ea-84b6-74612568e9e1.jpg',
                'api' => 'https://kuna.io',
                'www' => 'https://kuna.io',
                'doc' => 'https://kuna.io/documents/api',
                'fees' => 'https://kuna.io/documents/api',
            ),
            'fees' => array(
                'trading' => array(
                    'taker' => 0.25 / 100,
                    'maker' => 0.25 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
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
                    'deposit' => array(
                        // 'UAH' => (amount) => amount * 0.001 + 5
                    ),
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $quotes = array( 'btc', 'eth', 'eurs', 'rub', 'uah', 'usd', 'usdt', 'gol' );
        $pricePrecisions = array(
            'UAH' => 0,
        );
        $markets = array();
        $response = $this->publicGetTickers ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            for ($j = 0; $j < count($quotes); $j++) {
                $quoteId = $quotes[$j];
                $index = mb_strpos($id, $quoteId);
                $slice = mb_substr($id, $index);
                if (($index > 0) && ($slice === $quoteId)) {
                    $baseId = str_replace($quoteId, '', $id);
                    $base = $this->safe_currency_code($baseId);
                    $quote = $this->safe_currency_code($quoteId);
                    $symbol = $base . '/' . $quote;
                    $precision = array(
                        'amount' => 6,
                        'price' => $this->safe_integer($pricePrecisions, $quote, 6),
                    );
                    $markets[] = array(
                        'id' => $id,
                        'symbol' => $symbol,
                        'base' => $base,
                        'quote' => $quote,
                        'baseId' => $baseId,
                        'quoteId' => $quoteId,
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
                        'active' => null,
                        'info' => null,
                    );
                    break;
                }
            }
        }
        return $markets;
    }

    public function fetch_l3_order_book($symbol, $limit = null, $params = array ()) {
        return $this->fetch_order_book($symbol, $limit, $params);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->privateGetOrders (array_merge($request, $params));
        // todo emulation of fetchClosedOrders, fetchOrders, fetchOrder
        // with order cache . fetchOpenOrders
        // as in BTC-e, Liqui, Yobit, DSX, Tidex, WEX
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $side = $this->safe_string_2($trade, 'side', 'trend');
        if ($side !== null) {
            $sideMap = array(
                'ask' => 'sell',
                'bid' => 'buy',
            );
            $side = $this->safe_string($sideMap, $side, $side);
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'volume');
        $cost = $this->safe_float($trade, 'funds');
        $orderId = $this->safe_string($trade, 'order_id');
        $id = $this->safe_string($trade, 'id');
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'order' => $orderId,
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
            'market' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->privateGetTradesMy (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $trades = $this->fetch_trades($symbol, $since, $limit, $params);
        $ohlcvc = $this->build_ohlcvc($trades, $timeframe, $since, $limit);
        $result = array();
        for ($i = 0; $i < count($ohlcvc); $i++) {
            $ohlcv = $ohlcvc[$i];
            $result[] = [
                $ohlcv[0],
                $ohlcv[1],
                $ohlcv[2],
                $ohlcv[3],
                $ohlcv[4],
                $ohlcv[5],
            ];
        }
        return $result;
    }
}
