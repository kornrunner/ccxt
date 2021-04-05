<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\BadRequest;
use \ccxt\InvalidAddress;
use \ccxt\NotSupported;

class idex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'idex',
            'name' => 'IDEX',
            'countries' => array( 'US' ),
            'rateLimit' => 1500,
            'version' => 'v2',
            'certified' => true,
            'pro' => true,
            'requiresWeb3' => true,
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchCurrencies' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchOrders' => false,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTransactions' => false,
                'fetchDeposits' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '6h' => '6h',
                '1d' => '1d',
            ),
            'urls' => array(
                'test' => array(
                    'public' => 'https://api-sandbox.idex.io',
                    'private' => 'https://api-sandbox.idex.io',
                ),
                'logo' => 'https://user-images.githubusercontent.com/51840849/94481303-2f222100-01e0-11eb-97dd-bc14c5943a86.jpg',
                'api' => array(
                    'ETH' => 'https://api-eth.idex.io',
                    'BSC' => 'https://api-bsc.idex.io',
                ),
                'www' => 'https://idex.io',
                'doc' => array(
                    'https://docs.idex.io/',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ping',
                        'time',
                        'exchange',
                        'assets',
                        'markets',
                        'tickers',
                        'candles',
                        'trades',
                        'orderbook',
                        'wsToken',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'user',
                        'wallets',
                        'balances',
                        'orders',
                        'fills',
                        'deposits',
                        'withdrawals',
                    ),
                    'post' => array(
                        'wallets',
                        'orders',
                        'orders/test',
                        'withdrawals',
                    ),
                    'delete' => array(
                        'orders',
                    ),
                ),
            ),
            'options' => array(
                'defaultTimeInForce' => 'gtc',
                'defaultSelfTradePrevention' => 'cn',
                'network' => 'ETH', // also supports BSC
            ),
            'exceptions' => array(
                'INVALID_ORDER_QUANTITY' => '\\ccxt\\InvalidOrder',
                'INSUFFICIENT_FUNDS' => '\\ccxt\\InsufficientFunds',
                'SERVICE_UNAVAILABLE' => '\\ccxt\\ExchangeNotAvailable',
                'EXCEEDED_RATE_LIMIT' => '\\ccxt\\DDoSProtection',
                'INVALID_PARAMETER' => '\\ccxt\\BadRequest',
                'WALLET_NOT_ASSOCIATED' => '\\ccxt\\InvalidAddress',
                'INVALID_WALLET_SIGNATURE' => '\\ccxt\\AuthenticationError',
            ),
            'requiredCredentials' => array(
                'walletAddress' => true,
                'privateKey' => true,
                'apiKey' => true,
                'secret' => true,
            ),
            'paddingMode' => PAD_WITH_ZERO,
            'commonCurrencies' => array(),
            'requireCredentials' => array(
                'privateKey' => true,
                'apiKey' => true,
                'secret' => true,
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        // array(
        //   array(
        //     market => 'DIL-ETH',
        //     $status => 'active',
        //     baseAsset => 'DIL',
        //     baseAssetPrecision => 8,
        //     quoteAsset => 'ETH',
        //     quoteAssetPrecision => 8
        //   ), ...
        // )
        $response = $this->publicGetMarkets ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $entry = $response[$i];
            $marketId = $this->safe_string($entry, 'market');
            $baseId = $this->safe_string($entry, 'baseAsset');
            $quoteId = $this->safe_string($entry, 'quoteAsset');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $basePrecision = $this->safe_integer($entry, 'baseAssetPrecision');
            $quotePrecision = $this->safe_integer($entry, 'quoteAssetPrecision');
            $status = $this->safe_string($entry, 'status');
            $active = $status === 'active';
            $precision = array(
                'amount' => $basePrecision,
                'price' => $quotePrecision,
            );
            $result[] = array(
                'symbol' => $symbol,
                'id' => $marketId,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'info' => $entry,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        // array(
        //   {
        //     $market => 'DIL-ETH',
        //     time => 1598367493008,
        //     open => '0.09695361',
        //     high => '0.10245881',
        //     low => '0.09572507',
        //     close => '0.09917079',
        //     closeQuantity => '0.71320950',
        //     baseVolume => '309.17380612',
        //     quoteVolume => '30.57633981',
        //     percentChange => '2.28',
        //     numTrades => 205,
        //     ask => '0.09910476',
        //     bid => '0.09688340',
        //     sequence => 3902
        //   }
        // )
        $response = $this->publicGetTickers (array_merge($request, $params));
        $ticker = $this->safe_value($response, 0);
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        // array(
        //   array(
        //     market => 'DIL-ETH',
        //     time => 1598367493008,
        //     open => '0.09695361',
        //     high => '0.10245881',
        //     low => '0.09572507',
        //     close => '0.09917079',
        //     closeQuantity => '0.71320950',
        //     baseVolume => '309.17380612',
        //     quoteVolume => '30.57633981',
        //     percentChange => '2.28',
        //     numTrades => 205,
        //     ask => '0.09910476',
        //     bid => '0.09688340',
        //     sequence => 3902
        //   ), ...
        // )
        $response = $this->publicGetTickers ($params);
        return $this->parse_tickers($response, $symbols);
    }

    public function parse_ticker($ticker, $market = null) {
        // {
        //   $market => 'DIL-ETH',
        //   time => 1598367493008,
        //   $open => '0.09695361',
        //   $high => '0.10245881',
        //   $low => '0.09572507',
        //   $close => '0.09917079',
        //   closeQuantity => '0.71320950',
        //   $baseVolume => '309.17380612',
        //   $quoteVolume => '30.57633981',
        //   percentChange => '2.28',
        //   numTrades => 205,
        //   $ask => '0.09910476',
        //   $bid => '0.09688340',
        //   sequence => 3902
        // }
        $marketId = $this->safe_string($ticker, 'market');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $baseVolume = $this->safe_number($ticker, 'baseVolume');
        $quoteVolume = $this->safe_number($ticker, 'quoteVolume');
        $timestamp = $this->safe_integer($ticker, 'time');
        $open = $this->safe_number($ticker, 'open');
        $high = $this->safe_number($ticker, 'high');
        $low = $this->safe_number($ticker, 'low');
        $close = $this->safe_number($ticker, 'close');
        $ask = $this->safe_number($ticker, 'ask');
        $bid = $this->safe_number($ticker, 'bid');
        $percentage = $this->safe_number($ticker, 'percentChange');
        if ($percentage !== null) {
            $percentage = 1 . $percentage / 100;
        }
        $change = null;
        if (($close !== null) && ($open !== null)) {
            $change = $close - $open;
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $high,
            'low' => $low,
            'bid' => $bid,
            'bidVolume' => null,
            'ask' => $ask,
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $close,
            'last' => $close,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
            'interval' => $timeframe,
        );
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetCandles (array_merge($request, $params));
        if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) == 0) {
            // array(
            //   array(
            //     start => 1598345580000,
            //     open => '0.09771286',
            //     high => '0.09771286',
            //     low => '0.09771286',
            //     close => '0.09771286',
            //     volume => '1.45340410',
            //     sequence => 3853
            //   ), ...
            // )
            return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
        } else {
            //  array("nextTime":1595536440000)
            return array();
        }
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        // {
        //   start => 1598345580000,
        //   $open => '0.09771286',
        //   $high => '0.09771286',
        //   $low => '0.09771286',
        //   $close => '0.09771286',
        //   $volume => '1.45340410',
        //   sequence => 3853
        // }
        $timestamp = $this->safe_integer($ohlcv, 'start');
        $open = $this->safe_number($ohlcv, 'open');
        $high = $this->safe_number($ohlcv, 'high');
        $low = $this->safe_number($ohlcv, 'low');
        $close = $this->safe_number($ohlcv, 'close');
        $volume = $this->safe_number($ohlcv, 'volume');
        return array( $timestamp, $open, $high, $low, $close, $volume );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        // array(
        //   array(
        //     fillId => 'b5467d00-b13e-3fa9-8216-dd66735550fc',
        //     price => '0.09771286',
        //     quantity => '1.45340410',
        //     quoteQuantity => '0.14201627',
        //     time => 1598345638994,
        //     makerSide => 'buy',
        //     sequence => 3853
        //   ), ...
        // )
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        // public trades
        // {
        //   fillId => 'b5467d00-b13e-3fa9-8216-dd66735550fc',
        //   $price => '0.09771286',
        //   quantity => '1.45340410',
        //   quoteQuantity => '0.14201627',
        //   time => 1598345638994,
        //   $makerSide => 'buy',
        //   sequence => 3853
        // }
        // private trades
        // {
        //   fillId => '48582d10-b9bb-3c4b-94d3-e67537cf2472',
        //   $price => '0.09905990',
        //   quantity => '0.40000000',
        //   quoteQuantity => '0.03962396',
        //   time => 1598873478762,
        //   $makerSide => 'sell',
        //   sequence => 5053,
        //   $market => 'DIL-ETH',
        //   $orderId => '7cdc8e90-eb7d-11ea-9e60-4118569f6e63',
        //   $side => 'buy',
        //   $fee => '0.00080000',
        //   feeAsset => 'DIL',
        //   gas => '0.00857497',
        //   liquidity => 'taker',
        //   txId => '0xeaa02b112c0b8b61bc02fa1776a2b39d6c614e287c1af90df0a2e591da573e65',
        //   txStatus => 'mined'
        // }
        $id = $this->safe_string($trade, 'fillId');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'quantity');
        $cost = $this->safe_number($trade, 'quoteQuantity');
        $timestamp = $this->safe_integer($trade, 'time');
        $marketId = $this->safe_string($trade, 'market');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        // this code handles the duality of public vs private trades
        $makerSide = $this->safe_string($trade, 'makerSide');
        $oppositeSide = ($makerSide === 'buy') ? 'sell' : 'buy';
        $side = $this->safe_string($trade, 'side', $oppositeSide);
        $takerOrMaker = $this->safe_string($trade, 'liquidity', 'taker');
        $feeCost = $this->safe_number($trade, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'feeAsset');
            $fee = array(
                'cost' => $feeCost,
                'currency' => $this->safe_currency_code($feeCurrencyId),
            );
        }
        $orderId = $this->safe_string($trade, 'orderId');
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => $orderId,
            'type' => 'limit',
            'side' => $side,
            'takerOrMaker' => $takerOrMaker,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
            'level' => 2,
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        // {
        //   sequence => 36416753,
        //   bids => array(
        //     array( '0.09672815', '8.22284267', 1 ),
        //     array( '0.09672814', '1.83685554', 1 ),
        //     array( '0.09672143', '4.10962617', 1 ),
        //     array( '0.09658884', '4.03863759', 1 ),
        //     array( '0.09653781', '3.35730684', 1 ),
        //     array( '0.09624660', '2.54163586', 1 ),
        //     array( '0.09617490', '1.93065030', 1 )
        //   ),
        //   asks => array(
        //     array( '0.09910476', '3.22840154', 1 ),
        //     array( '0.09940587', '3.39796593', 1 ),
        //     array( '0.09948189', '4.25088898', 1 ),
        //     array( '0.09958362', '2.42195784', 1 ),
        //     array( '0.09974393', '4.25234367', 1 ),
        //     array( '0.09995250', '3.40192141', 1 )
        //   )
        // }
        $response = $this->publicGetOrderbook (array_merge($request, $params));
        $nonce = $this->safe_integer($response, 'sequence');
        return array(
            'timestamp' => null,
            'datetime' => null,
            'nonce' => $nonce,
            'bids' => $this->parse_side($response, 'bids'),
            'asks' => $this->parse_side($response, 'asks'),
        );
    }

    public function parse_side($book, $side) {
        $bookSide = $this->safe_value($book, $side, array());
        $result = array();
        for ($i = 0; $i < count($bookSide); $i++) {
            $order = $bookSide[$i];
            $price = $this->safe_number($order, 0);
            $amount = $this->safe_number($order, 1);
            $orderCount = $this->safe_integer($order, 2);
            $result[] = array( $price, $amount, $orderCount );
        }
        $descending = $side === 'bids';
        return $this->sort_by($result, 0, $descending);
    }

    public function fetch_currencies($params = array ()) {
        // array(
        //   array(
        //     $name => 'Ether',
        //     symbol => 'ETH',
        //     contractAddress => '0x0000000000000000000000000000000000000000',
        //     assetDecimals => 18,
        //     exchangeDecimals => 8
        //   ), ..
        // )
        $response = $this->publicGetAssets ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $entry = $response[$i];
            $name = $this->safe_string($entry, 'name');
            $currencyId = $this->safe_string($entry, 'symbol');
            $precision = $this->safe_integer($entry, 'exchangeDecimals');
            $code = $this->safe_currency_code($currencyId);
            $lot = pow(-10, $precision);
            $result[$code] = array(
                'id' => $currencyId,
                'code' => $code,
                'info' => $entry,
                'type' => null,
                'name' => $name,
                'active' => null,
                'fee' => null,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array( 'min' => $lot, 'max' => null ),
                    'price' => array( 'min' => $lot, 'max' => null ),
                    'cost' => array( 'min' => null, 'max' => null ),
                    'withdraw' => array( 'min' => $lot, 'max' => null ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->check_required_credentials();
        $this->load_markets();
        $nonce1 = $this->uuidv1();
        $request = array(
            'nonce' => $nonce1,
            'wallet' => $this->walletAddress,
        );
        // array(
        //   array(
        //     asset => 'DIL',
        //     quantity => '0.00000000',
        //     availableForTrade => '0.00000000',
        //     locked => '0.00000000',
        //     usdValue => null
        //   ), ...
        // )
        $extendedRequest = array_merge($request, $params);
        if ($extendedRequest['wallet'] === null) {
            throw new BadRequest($this->id . ' wallet is null, set $this->walletAddress or "address" in params');
        }
        $response = null;
        try {
            $response = $this->privateGetBalances ($extendedRequest);
        } catch (Exception $e) {
            if ($e instanceof InvalidAddress) {
                $walletAddress = $extendedRequest['wallet'];
                $this->associate_wallet($walletAddress);
                $response = $this->privateGetBalances ($extendedRequest);
            } else {
                throw $e;
            }
        }
        $result = array(
            'info' => $response,
        );
        for ($i = 0; $i < count($response); $i++) {
            $entry = $response[$i];
            $currencyId = $this->safe_string($entry, 'asset');
            $code = $this->safe_currency_code($currencyId);
            $total = $this->safe_number($entry, 'quantity');
            $free = $this->safe_number($entry, 'availableForTrade');
            $used = $this->safe_number($entry, 'locked');
            $result[$code] = array(
                'free' => $free,
                'used' => $used,
                'total' => $total,
            );
        }
        return $this->parse_balance($result);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->check_required_credentials();
        $this->load_markets();
        $market = null;
        $request = array(
            'nonce' => $this->uuidv1(),
            'wallet' => $this->walletAddress,
        );
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['market'] = $market['id'];
        }
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        // array(
        //   {
        //     fillId => '48582d10-b9bb-3c4b-94d3-e67537cf2472',
        //     price => '0.09905990',
        //     quantity => '0.40000000',
        //     quoteQuantity => '0.03962396',
        //     time => 1598873478762,
        //     makerSide => 'sell',
        //     sequence => 5053,
        //     $market => 'DIL-ETH',
        //     orderId => '7cdc8e90-eb7d-11ea-9e60-4118569f6e63',
        //     side => 'buy',
        //     fee => '0.00080000',
        //     feeAsset => 'DIL',
        //     gas => '0.00857497',
        //     liquidity => 'taker',
        //     txId => '0xeaa02b112c0b8b61bc02fa1776a2b39d6c614e287c1af90df0a2e591da573e65',
        //     txStatus => 'mined'
        //   }
        // )
        $extendedRequest = array_merge($request, $params);
        if ($extendedRequest['wallet'] === null) {
            throw new BadRequest($this->id . ' $walletAddress is null, set $this->walletAddress or "address" in params');
        }
        $response = null;
        try {
            $response = $this->privateGetFills ($extendedRequest);
        } catch (Exception $e) {
            if ($e instanceof InvalidAddress) {
                $walletAddress = $extendedRequest['wallet'];
                $this->associate_wallet($walletAddress);
                $response = $this->privateGetFills ($extendedRequest);
            } else {
                throw $e;
            }
        }
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'orderId' => $id,
        );
        return $this->fetch_orders_helper($symbol, null, null, array_merge($request, $params));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'closed' => false,
        );
        return $this->fetch_orders_helper($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'closed' => true,
        );
        return $this->fetch_orders_helper($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_orders_helper($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'nonce' => $this->uuidv1(),
            'wallet' => $this->walletAddress,
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['market'] = $market['id'];
        }
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetOrders (array_merge($request, $params));
        // fetchClosedOrders / fetchOpenOrders
        // array(
        //   {
        //     "$market" => "DIL-ETH",
        //     "orderId" => "7cdc8e90-eb7d-11ea-9e60-4118569f6e63",
        //     "wallet" => "0x0AB991497116f7F5532a4c2f4f7B1784488628e1",
        //     "time" => 1598873478650,
        //     "status" => "filled",
        //     "type" => "$limit",
        //     "side" => "buy",
        //     "originalQuantity" => "0.40000000",
        //     "executedQuantity" => "0.40000000",
        //     "cumulativeQuoteQuantity" => "0.03962396",
        //     "avgExecutionPrice" => "0.09905990",
        //     "price" => "1.00000000",
        //     "fills" => array(
        //       {
        //         "fillId" => "48582d10-b9bb-3c4b-94d3-e67537cf2472",
        //         "price" => "0.09905990",
        //         "quantity" => "0.40000000",
        //         "quoteQuantity" => "0.03962396",
        //         "time" => 1598873478650,
        //         "makerSide" => "sell",
        //         "sequence" => 5053,
        //         "fee" => "0.00080000",
        //         "feeAsset" => "DIL",
        //         "gas" => "0.00857497",
        //         "liquidity" => "taker",
        //         "txId" => "0xeaa02b112c0b8b61bc02fa1776a2b39d6c614e287c1af90df0a2e591da573e65",
        //         "txStatus" => "mined"
        //       }
        //     )
        //   }
        // )
        // fetchOrder
        // { $market => 'DIL-ETH',
        //   orderId => '7cdc8e90-eb7d-11ea-9e60-4118569f6e63',
        //   wallet => '0x0AB991497116f7F5532a4c2f4f7B1784488628e1',
        //   time => 1598873478650,
        //   status => 'filled',
        //   type => 'limit',
        //   side => 'buy',
        //   originalQuantity => '0.40000000',
        //   executedQuantity => '0.40000000',
        //   cumulativeQuoteQuantity => '0.03962396',
        //   avgExecutionPrice => '0.09905990',
        //   price => '1.00000000',
        //   fills:
        //    array( { fillId => '48582d10-b9bb-3c4b-94d3-e67537cf2472',
        //        price => '0.09905990',
        //        quantity => '0.40000000',
        //        quoteQuantity => '0.03962396',
        //        time => 1598873478650,
        //        makerSide => 'sell',
        //        sequence => 5053,
        //        fee => '0.00080000',
        //        feeAsset => 'DIL',
        //        gas => '0.00857497',
        //        liquidity => 'taker',
        //        txId => '0xeaa02b112c0b8b61bc02fa1776a2b39d6c614e287c1af90df0a2e591da573e65',
        //        txStatus => 'mined' } ) }
        if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) == 0) {
            return $this->parse_orders($response, $market, $since, $limit);
        } else {
            return $this->parse_order($response, $market);
        }
    }

    public function parse_order_status($status) {
        // https://docs.idex.io/#order-states-amp-lifecycle
        $statuses = array(
            'active' => 'open',
            'partiallyFilled' => 'open',
            'rejected' => 'canceled',
            'filled' => 'closed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "$market" => "DIL-ETH",
        //         "orderId" => "7cdc8e90-eb7d-11ea-9e60-4118569f6e63",
        //         "wallet" => "0x0AB991497116f7F5532a4c2f4f7B1784488628e1",
        //         "time" => 1598873478650,
        //         "$status" => "$filled",
        //         "$type" => "limit",
        //         "$side" => "buy",
        //         "originalQuantity" => "0.40000000",
        //         "executedQuantity" => "0.40000000",
        //         "cumulativeQuoteQuantity" => "0.03962396",
        //         "avgExecutionPrice" => "0.09905990",
        //         "$price" => "1.00000000",
        //         "$fills" => array(
        //             {
        //             "fillId" => "48582d10-b9bb-3c4b-94d3-e67537cf2472",
        //             "$price" => "0.09905990",
        //             "quantity" => "0.40000000",
        //             "quoteQuantity" => "0.03962396",
        //             "time" => 1598873478650,
        //             "makerSide" => "sell",
        //             "sequence" => 5053,
        //             "$fee" => "0.00080000",
        //             "feeAsset" => "DIL",
        //             "gas" => "0.00857497",
        //             "liquidity" => "taker",
        //             "txId" => "0xeaa02b112c0b8b61bc02fa1776a2b39d6c614e287c1af90df0a2e591da573e65",
        //             "txStatus" => "mined"
        //             }
        //         )
        //     }
        //
        $timestamp = $this->safe_integer($order, 'time');
        $fills = $this->safe_value($order, 'fills', array());
        $id = $this->safe_string($order, 'orderId');
        $clientOrderId = $this->safe_string($order, 'clientOrderId');
        $marketId = $this->safe_string($order, 'market');
        $side = $this->safe_string($order, 'side');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $trades = $this->parse_trades($fills, $market);
        $type = $this->safe_string($order, 'type');
        $amount = $this->safe_number($order, 'originalQuantity');
        $filled = $this->safe_number($order, 'executedQuantity');
        $average = $this->safe_number($order, 'avgExecutionPrice');
        $price = $this->safe_number($order, 'price');
        $rawStatus = $this->safe_string($order, 'status');
        $status = $this->parse_order_status($rawStatus);
        $fee = array(
            'currency' => null,
            'cost' => null,
        );
        $lastTrade = null;
        for ($i = 0; $i < count($trades); $i++) {
            $lastTrade = $trades[$i];
            $fee['currency'] = $lastTrade['fee']['currency'];
            $fee['cost'] = $this->sum($fee['cost'], $lastTrade['fee']['cost']);
        }
        $lastTradeTimestamp = $this->safe_integer($lastTrade, 'timestamp');
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'cost' => null,
            'average' => $average,
            'filled' => $filled,
            'remaining' => null,
            'status' => $status,
            'fee' => $fee,
            'trades' => $trades,
        ));
    }

    public function associate_wallet($walletAddress, $params = array ()) {
        $nonce = $this->uuidv1();
        $noPrefix = $this->remove0x_prefix($walletAddress);
        $byteArray = array(
            $this->base16_to_binary($nonce),
            $this->base16_to_binary($noPrefix),
        );
        $binary = $this->binary_concat_array($byteArray);
        $hash = $this->hash($binary, 'keccak', 'hex');
        $signature = $this->sign_message_string($hash, $this->privateKey);
        // {
        //   address => '0x0AB991497116f7F5532a4c2f4f7B1784488628e1',
        //   totalPortfolioValueUsd => '0.00',
        //   time => 1598468353626
        // }
        $request = array(
            'parameters' => array(
                'nonce' => $nonce,
                'wallet' => $walletAddress,
            ),
            'signature' => $signature,
        );
        $result = $this->privatePostWallets ($request);
        return $result;
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        // https://docs.idex.io/#create-order
        $this->check_required_credentials();
        $this->load_markets();
        $market = $this->market($symbol);
        $nonce = $this->uuidv1();
        $typeEnum = null;
        $stopLossTypeEnums = array(
            'stopLoss' => 3,
            'stopLossLimit' => 4,
            'takeProfit' => 5,
            'takeProfitLimit' => 6,
        );
        $stopPriceString = null;
        if (($type === 'stopLossLimit') || ($type === 'takeProfitLimit') || (is_array($params) && array_key_exists('stopPrice', $params))) {
            if (!(is_array($params) && array_key_exists('stopPrice', $params))) {
                throw new BadRequest($this->id . ' stopPrice is a required parameter for ' . $type . 'orders');
            }
            $stopPriceString = $this->price_to_precision($symbol, $params['stopPrice']);
        }
        $limitTypeEnums = array(
            'limit' => 1,
            'limitMaker' => 2,
        );
        $priceString = null;
        $typeLower = strtolower($type);
        $limitOrder = mb_strpos($typeLower, 'limit') > -1;
        if (is_array($limitTypeEnums) && array_key_exists($type, $limitTypeEnums)) {
            $typeEnum = $limitTypeEnums[$type];
            $priceString = $this->price_to_precision($symbol, $price);
        } else if (is_array($stopLossTypeEnums) && array_key_exists($type, $stopLossTypeEnums)) {
            $typeEnum = $stopLossTypeEnums[$type];
            $priceString = $this->price_to_precision($symbol, $price);
        } else if ($type === 'market') {
            $typeEnum = 0;
        } else {
            throw new BadRequest($this->id . ' ' . $type . ' is not a valid order type');
        }
        $amountEnum = 0; // base quantity
        if (is_array($params) && array_key_exists('quoteOrderQuantity', $params)) {
            if ($type !== 'market') {
                throw new NotSupported($this->id . ' quoteOrderQuantity is not supported for ' . $type . ' orders, only supported for $market orders');
            }
            $amountEnum = 1;
            $amount = $this->safe_number($params, 'quoteOrderQuantity');
        }
        $sideEnum = ($side === 'buy') ? 0 : 1;
        $walletBytes = $this->remove0x_prefix($this->walletAddress);
        $network = $this->safe_string($this->options, 'network', 'ETH');
        $orderVersion = ($network === 'ETH') ? 1 : 2;
        $amountString = $this->amount_to_precision($symbol, $amount);
        // https://docs.idex.io/#time-in-force
        $timeInForceEnums = array(
            'gtc' => 0,
            'ioc' => 2,
            'fok' => 3,
        );
        $defaultTimeInForce = $this->safe_string($this->options, 'defaultTimeInForce', 'gtc');
        $timeInForce = $this->safe_string($params, 'timeInForce', $defaultTimeInForce);
        $timeInForceEnum = null;
        if (is_array($timeInForceEnums) && array_key_exists($timeInForce, $timeInForceEnums)) {
            $timeInForceEnum = $timeInForceEnums[$timeInForce];
        } else {
            $allOptions = is_array($timeInForceEnums) ? array_keys($timeInForceEnums) : array();
            $asString = implode(', ', $allOptions);
            throw new BadRequest($this->id . ' ' . $timeInForce . ' is not a valid $timeInForce, please choose one of ' . $asString);
        }
        // https://docs.idex.io/#self-trade-prevention
        $selfTradePreventionEnums = array(
            'dc' => 0,
            'co' => 1,
            'cn' => 2,
            'cb' => 3,
        );
        $defaultSelfTradePrevention = $this->safe_string($this->options, 'defaultSelfTradePrevention', 'cn');
        $selfTradePrevention = $this->safe_string($params, 'selfTradePrevention', $defaultSelfTradePrevention);
        $selfTradePreventionEnum = null;
        if (is_array($selfTradePreventionEnums) && array_key_exists($selfTradePrevention, $selfTradePreventionEnums)) {
            $selfTradePreventionEnum = $selfTradePreventionEnums[$selfTradePrevention];
        } else {
            $allOptions = is_array($selfTradePreventionEnums) ? array_keys($selfTradePreventionEnums) : array();
            $asString = implode(', ', $allOptions);
            throw new BadRequest($this->id . ' ' . $selfTradePrevention . ' is not a valid $selfTradePrevention, please choose one of ' . $asString);
        }
        $byteArray = [
            $this->number_to_be($orderVersion, 1),
            $this->base16_to_binary($nonce),
            $this->base16_to_binary($walletBytes),
            $this->encode($market['id']),  // TODO => refactor to remove either encode or stringToBinary
            $this->number_to_be($typeEnum, 1),
            $this->number_to_be($sideEnum, 1),
            $this->encode($amountString),
            $this->number_to_be($amountEnum, 1),
        ];
        if ($limitOrder) {
            $encodedPrice = $this->encode($priceString);
            $byteArray[] = $encodedPrice;
        }
        if (is_array($stopLossTypeEnums) && array_key_exists($type, $stopLossTypeEnums)) {
            $encodedPrice = $this->encode($stopPriceString || $priceString);
            $byteArray[] = $encodedPrice;
        }
        $clientOrderId = $this->safe_string($params, 'clientOrderId');
        if ($clientOrderId !== null) {
            $byteArray[] = $this->encode($clientOrderId);
        }
        $after = array(
            $this->number_to_be($timeInForceEnum, 1),
            $this->number_to_be($selfTradePreventionEnum, 1),
            $this->number_to_be(0, 8), // unused
        );
        $allBytes = $this->array_concat($byteArray, $after);
        $binary = $this->binary_concat_array($allBytes);
        $hash = $this->hash($binary, 'keccak', 'hex');
        $signature = $this->sign_message_string($hash, $this->privateKey);
        $request = array(
            'parameters' => array(
                'nonce' => $nonce,
                'market' => $market['id'],
                'side' => $side,
                'type' => $type,
                'wallet' => $this->walletAddress,
                'timeInForce' => $timeInForce,
                'selfTradePrevention' => $selfTradePrevention,
            ),
            'signature' => $signature,
        );
        if ($limitOrder) {
            $request['parameters']['price'] = $priceString;
        }
        if (is_array($stopLossTypeEnums) && array_key_exists($type, $stopLossTypeEnums)) {
            $request['parameters']['stopPrice'] = $stopPriceString || $priceString;
        }
        if ($amountEnum === 0) {
            $request['parameters']['quantity'] = $amountString;
        } else {
            $request['parameters']['quoteOrderQuantity'] = $amountString;
        }
        if ($clientOrderId !== null) {
            $request['parameters']['clientOrderId'] = $clientOrderId;
        }
        // {
        //   $market => 'DIL-ETH',
        //   orderId => '7cdc8e90-eb7d-11ea-9e60-4118569f6e63',
        //   wallet => '0x0AB991497116f7F5532a4c2f4f7B1784488628e1',
        //   time => 1598873478650,
        //   status => 'filled',
        //   $type => 'limit',
        //   $side => 'buy',
        //   originalQuantity => '0.40000000',
        //   executedQuantity => '0.40000000',
        //   cumulativeQuoteQuantity => '0.03962396',
        //   $price => '1.00000000',
        //   fills => array(
        //     {
        //       fillId => '48582d10-b9bb-3c4b-94d3-e67537cf2472',
        //       $price => '0.09905990',
        //       quantity => '0.40000000',
        //       quoteQuantity => '0.03962396',
        //       time => 1598873478650,
        //       makerSide => 'sell',
        //       sequence => 5053,
        //       fee => '0.00080000',
        //       feeAsset => 'DIL',
        //       gas => '0.00857497',
        //       liquidity => 'taker',
        //       txStatus => 'pending'
        //     }
        //   ),
        //   avgExecutionPrice => '0.09905990'
        // }
        // we don't use extend here because it is a signed endpoint
        $response = $this->privatePostOrders ($request);
        return $this->parse_order($response, $market);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_required_credentials();
        $this->load_markets();
        $nonce = $this->uuidv1();
        $amountString = $this->currency_to_precision($code, $amount);
        $currency = $this->currency($code);
        $walletBytes = $this->remove0x_prefix($this->walletAddress);
        $byteArray = [
            $this->base16_to_binary($nonce),
            $this->base16_to_binary($walletBytes),
            $this->encode($currency['id']),
            $this->encode($amountString),
            $this->number_to_be(1, 1), // bool set to true
        ];
        $binary = $this->binary_concat_array($byteArray);
        $hash = $this->hash($binary, 'keccak', 'hex');
        $signature = $this->sign_message_string($hash, $this->privateKey);
        $request = array(
            'parameters' => array(
                'nonce' => $nonce,
                'wallet' => $address,
                'asset' => $currency['id'],
                'quantity' => $amountString,
            ),
            'signature' => $signature,
        );
        // {
        //   withdrawalId => 'a61dcff0-ec4d-11ea-8b83-c78a6ecb3180',
        //   asset => 'ETH',
        //   assetContractAddress => '0x0000000000000000000000000000000000000000',
        //   quantity => '0.20000000',
        //   time => 1598962883190,
        //   fee => '0.00024000',
        //   txStatus => 'pending',
        //   txId => null
        // }
        $response = $this->privatePostWithdrawals ($request);
        $id = $this->safe_string($response, 'withdrawalId');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->check_required_credentials();
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $nonce = $this->uuidv1();
        $walletBytes = $this->remove0x_prefix($this->walletAddress);
        $byteArray = array(
            $this->base16_to_binary($nonce),
            $this->base16_to_binary($walletBytes),
            $this->encode($id),
        );
        $binary = $this->binary_concat_array($byteArray);
        $hash = $this->hash($binary, 'keccak', 'hex');
        $signature = $this->sign_message_string($hash, $this->privateKey);
        $request = array(
            'parameters' => array(
                'nonce' => $nonce,
                'wallet' => $this->walletAddress,
                'orderId' => $id,
            ),
            'signature' => $signature,
        );
        // array( array( orderId => '688336f0-ec50-11ea-9842-b332f8a34d0e' ) )
        $response = $this->privateDeleteOrders (array_merge($request, $params));
        $canceledOrder = $this->safe_value($response, 0);
        return $this->parse_order($canceledOrder, $market);
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        $errorCode = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'message');
        if (is_array($this->exceptions) && array_key_exists($errorCode, $this->exceptions)) {
            $Exception = $this->exceptions[$errorCode];
            throw new $Exception($this->id . ' ' . $message);
        }
        if ($errorCode !== null) {
            throw new ExchangeError($this->id . ' ' . $message);
        }
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $params = array_merge(array(
            'method' => 'privateGetDeposits',
        ), $params);
        return $this->fetch_transactions_helper($code, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $params = array_merge(array(
            'method' => 'privateGetWithdrawals',
        ), $params);
        return $this->fetch_transactions_helper($code, $since, $limit, $params);
    }

    public function fetch_transactions_helper($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $nonce = $this->uuidv1();
        $request = array(
            'nonce' => $nonce,
            'wallet' => $this->walletAddress,
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['asset'] = $currency['id'];
        }
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        // array(
        //   {
        //     depositId => 'e9970cc0-eb6b-11ea-9e89-09a5ebc1f98e',
        //     asset => 'ETH',
        //     quantity => '1.00000000',
        //     txId => '0xcd4aac3171d7131cc9e795568c67938675185ac17641553ef54c8a7c294c8142',
        //     txTime => 1598865853000,
        //     confirmationTime => 1598865930231
        //   }
        // )
        $method = $params['method'];
        $params = $this->omit($params, 'method');
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'mined' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        // fetchDeposits
        // {
        //   depositId => 'e9970cc0-eb6b-11ea-9e89-09a5ebc1f98f',
        //   asset => 'ETH',
        //   quantity => '1.00000000',
        //   txId => '0xcd4aac3171d7131cc9e795568c67938675185ac17641553ef54c8a7c294c8142',
        //   txTime => 1598865853000,
        //   confirmationTime => 1598865930231
        // }
        // fetchWithdrwalas
        // {
        //   withdrawalId => 'a62d8760-ec4d-11ea-9fa6-47904c19499b',
        //   asset => 'ETH',
        //   assetContractAddress => '0x0000000000000000000000000000000000000000',
        //   quantity => '0.20000000',
        //   time => 1598962883288,
        //   $fee => '0.00024000',
        //   txId => '0x305e9cdbaa85ad029f50578d13d31d777c085de573ed5334d95c19116d8c03ce',
        //   txStatus => 'mined'
        //  }
        $type = null;
        if (is_array($transaction) && array_key_exists('depositId', $transaction)) {
            $type = 'deposit';
        } else if (is_array($transaction) && array_key_exists('withdrawalId', $transaction)) {
            $type = 'withdrawal';
        }
        $id = $this->safe_string_2($transaction, 'depositId', 'withdrawId');
        $code = $this->safe_currency_code($this->safe_string($transaction, 'asset'), $currency);
        $amount = $this->safe_number($transaction, 'quantity');
        $txid = $this->safe_string($transaction, 'txId');
        $timestamp = $this->safe_integer($transaction, 'txTime');
        $fee = null;
        if (is_array($transaction) && array_key_exists('fee', $transaction)) {
            $fee = array(
                'cost' => $this->safe_number($transaction, 'fee'),
                'currency' => 'ETH',
            );
        }
        $rawStatus = $this->safe_string($transaction, 'txStatus');
        $status = $this->parse_transaction_status($rawStatus);
        $updated = $this->safe_integer($transaction, 'confirmationTime');
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => null,
            'tag' => null,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $network = $this->safe_string($this->options, 'network', 'ETH');
        $version = $this->safe_string($this->options, 'version', 'v1');
        $url = $this->urls['api'][$network] . '/' . $version . '/' . $path;
        $keys = is_array($params) ? array_keys($params) : array();
        $length = is_array($keys) ? count($keys) : 0;
        $query = null;
        if ($length > 0) {
            if ($method === 'GET') {
                $query = $this->urlencode($params);
                $url = $url . '?' . $query;
            } else {
                $body = $this->json($params);
            }
        }
        $headers = array(
            'Content-Type' => 'application/json',
        );
        if ($this->apiKey !== null) {
            $headers['IDEX-API-Key'] = $this->apiKey;
        }
        if ($api === 'private') {
            $payload = null;
            if ($method === 'GET') {
                $payload = $query;
            } else {
                $payload = $body;
            }
            $headers['IDEX-HMAC-Signature'] = $this->hmac($this->encode($payload), $this->encode($this->secret), 'sha256', 'hex');
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}
