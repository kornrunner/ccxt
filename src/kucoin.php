<?php

namespace ccxt;

use Exception as Exception; // a common import

class kucoin extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'kucoin',
            'name' => 'Kucoin',
            'countries' => array ( 'HK' ), // Hong Kong
            'version' => 'v1',
            'rateLimit' => 2000,
            'userAgent' => $this->userAgents['chrome'],
            'has' => array (
                'CORS' => false,
                'cancelOrders' => true,
                'createMarketOrder' => false,
                'fetchDepositAddress' => true,
                'fetchTickers' => true,
                'fetchOHLCV' => true, // see the method implementation below
                'fetchOrder' => true,
                'fetchOrders' => false,
                'fetchClosedOrders' => true,
                'fetchOpenOrders' => true,
                'fetchMyTrades' => 'emulated', // this method is to be deleted, see implementation and comments below
                'fetchCurrencies' => true,
                'withdraw' => true,
            ),
            'timeframes' => array (
                '1m' => 1,
                '5m' => 5,
                '15m' => 15,
                '30m' => 30,
                '1h' => 60,
                '8h' => 480,
                '1d' => 'D',
                '1w' => 'W',
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/33795655-b3c46e48-dcf6-11e7-8abe-dc4588ba7901.jpg',
                'api' => array (
                    'public' => 'https://api.kucoin.com',
                    'private' => 'https://api.kucoin.com',
                    'kitchen' => 'https://kitchen.kucoin.com',
                    'kitchen-2' => 'https://kitchen-2.kucoin.com',
                ),
                'www' => 'https://www.kucoin.com',
                'referral' => 'https://www.kucoin.com/?r=E5wkqe',
                'doc' => 'https://kucoinapidocs.docs.apiary.io',
                'fees' => 'https://news.kucoin.com/en/fee',
            ),
            'api' => array (
                'kitchen' => array (
                    'get' => array (
                        'open/chart/history',
                    ),
                ),
                'public' => array (
                    'get' => array (
                        'open/chart/config',
                        'open/chart/history',
                        'open/chart/symbol',
                        'open/currencies',
                        'open/deal-orders',
                        'open/kline',
                        'open/lang-list',
                        'open/orders',
                        'open/orders-buy',
                        'open/orders-sell',
                        'open/tick',
                        'market/open/coin-info',
                        'market/open/coins',
                        'market/open/coins-trending',
                        'market/open/symbols',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'account/balance',
                        'account/{coin}/wallet/address',
                        'account/{coin}/wallet/records',
                        'account/{coin}/balance',
                        'account/promotion/info',
                        'account/promotion/sum',
                        'deal-orders',
                        'order/active',
                        'order/active-map',
                        'order/dealt',
                        'order/detail',
                        'referrer/descendant/count',
                        'user/info',
                    ),
                    'post' => array (
                        'account/{coin}/withdraw/apply',
                        'account/{coin}/withdraw/cancel',
                        'account/promotion/draw',
                        'cancel-order',
                        'order',
                        'order/cancel-all',
                        'user/change-lang',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.001,
                    'taker' => 0.001,
                ),
                'funding' => array (
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array (
                        'KCS' => 2.0,
                        'BTC' => 0.0005,
                        'USDT' => 10.0,
                        'ETH' => 0.01,
                        'LTC' => 0.001,
                        'NEO' => 0.0,
                        'GAS' => 0.0,
                        'KNC' => 0.5,
                        'BTM' => 5.0,
                        'QTUM' => 0.1,
                        'EOS' => 0.5,
                        'CVC' => 3.0,
                        'OMG' => 0.1,
                        'PAY' => 0.5,
                        'SNT' => 20.0,
                        'BHC' => 1.0,
                        'HSR' => 0.01,
                        'WTC' => 0.1,
                        'VEN' => 2.0,
                        'MTH' => 10.0,
                        'RPX' => 1.0,
                        'REQ' => 20.0,
                        'EVX' => 0.5,
                        'MOD' => 0.5,
                        'NEBL' => 0.1,
                        'DGB' => 0.5,
                        'CAG' => 2.0,
                        'CFD' => 0.5,
                        'RDN' => 0.5,
                        'UKG' => 5.0,
                        'BCPT' => 5.0,
                        'PPT' => 0.1,
                        'BCH' => 0.0005,
                        'STX' => 2.0,
                        'NULS' => 1.0,
                        'GVT' => 0.1,
                        'HST' => 2.0,
                        'PURA' => 0.5,
                        'SUB' => 2.0,
                        'QSP' => 5.0,
                        'POWR' => 1.0,
                        'FLIXX' => 10.0,
                        'LEND' => 20.0,
                        'AMB' => 3.0,
                        'RHOC' => 2.0,
                        'R' => 2.0,
                        'DENT' => 50.0,
                        'DRGN' => 1.0,
                        'ACT' => 0.1,
                    ),
                    'deposit' => array (),
                ),
            ),
            // exchange-specific options
            'options' => array (
                'fetchOrderBookWarning' => true, // raises a warning on null response in fetchOrderBook
                'timeDifference' => 0, // the difference between system clock and Kucoin clock
                'adjustForTimeDifference' => false, // controls the adjustment logic upon instantiation
                'limits' => array (
                    'amount' => array (
                        'min' => array (
                            'ABT' => 1,
                            'ACAT' => 1,
                            'ACT' => 1,
                            'ADB' => 1,
                            'AGI' => 10,
                            'AION' => 1,
                            'AIX' => 1,
                            'AMB' => 1,
                            'AOA' => 1,
                            'APH' => 1,
                            'ARN' => 1,
                            'ARY' => 1,
                            'AXP' => 1,
                            'BAX' => 1,
                            'BCD' => 0.001,
                            'BCH' => 0.00001,
                            'BCPT' => 1,
                            'BNTY' => 1,
                            'BOS' => 1,
                            'BPT' => 1,
                            'BRD' => 1,
                            'BTC' => 0.00001,
                            'BTG' => 0.001,
                            'BTM' => 1,
                            'CAG' => 1,
                            'CanYaCoin' => 1,
                            'CAPP' => 1,
                            'CAT' => 1,
                            'CBC' => 1,
                            'CHP' => 1,
                            'CHSB' => 1,
                            'COFI' => 1,
                            'COV' => 1,
                            'CPC' => 1,
                            'CS' => 1,
                            'CV' => 10,
                            'CVC' => 0.1,
                            'CXO' => 1,
                            'DACC' => 1,
                            'DADI' => 1,
                            'DAG' => 1,
                            'DASH' => 0.01,
                            'DAT' => 1,
                            'DATX' => 1,
                            'DBC' => 1,
                            'DCC' => 1,
                            'DEB' => 1,
                            'DENT' => 1,
                            'DGB' => 1,
                            'DNA' => 1,
                            'DOCK' => 1,
                            'DRGN' => 1,
                            'DTA' => 1,
                            'EBTC' => 1,
                            'EDR' => 1,
                            'EGT' => 1,
                            'ELA' => 1,
                            'ELEC' => 1,
                            'ELF' => 1,
                            'ELIX' => 1,
                            'ENJ' => 1,
                            'EOS' => 0.1,
                            'ETC' => 0.1,
                            'ETH' => 0.00001,
                            'ETN' => 1,
                            'EXY' => 1,
                            'FLIXX' => 0.1,
                            'FOTA' => 1,
                            'GAS' => 0.1,
                            'GAT' => 1,
                            'GLA' => 1,
                            'GO' => 1,
                            'GVT' => 0.1,
                            'HAV' => 1,
                            'HKN' => 1,
                            'HPB' => 1,
                            'HST' => 0.1,
                            'IHT' => 1,
                            'ING' => 1,
                            'INS' => 1,
                            'IOST' => 1,
                            'IOTX' => 1,
                            'ITC' => 1,
                            'J8T' => 1,
                            'JNT' => 1,
                            'KCS' => 1,
                            'KEY' => 1,
                            'KICK' => 1,
                            'KNC' => 0.001,
                            'LA' => 1,
                            'LALA' => 1,
                            'LEND' => 1,
                            'LOCI' => 1,
                            'LOOM' => 1,
                            'LTC' => 1,
                            'LYM' => 1,
                            'MAN' => 1,
                            'MANA' => 1,
                            'MOBI' => 1,
                            'MOD' => 0.1,
                            'MTH' => 1,
                            'MTN' => 1,
                            'MWAT' => 1,
                            'NANO' => 0.1,
                            'NEBL' => 0.1,
                            'NEO' => 0.01,
                            'NULS' => 0.1,
                            'NUSD' => 1,
                            'OCN' => 10,
                            'OLT' => 1,
                            'OMG' => 0.1,
                            'OMX' => 1,
                            'ONION' => 1,
                            'ONT' => 1,
                            'OPEN' => 1,
                            'PARETO' => 1,
                            'PAY' => 0.1,
                            'PBL' => 1,
                            'PLAY' => 1,
                            'POLL' => 1,
                            'POLY' => 1,
                            'POWR' => 0.1,
                            'PPT' => 0.1,
                            'PRL' => 1,
                            'PURA' => 0.1,
                            'QKC' => 1,
                            'QLC' => 1,
                            'QSP' => 0.1,
                            'QTUM' => 0.1,
                            'R' => 1,
                            'RDN' => 1,
                            'REQ' => 1,
                            'RHOC' => 1,
                            'RPX' => 1,
                            'SHL' => 1,
                            'SNC' => 1,
                            'SNM' => 1,
                            'SNOV' => 1,
                            'SNT' => 0.1,
                            'SOUL' => 1,
                            'SPF' => 1,
                            'SPHTX' => 1,
                            'SRN' => 1,
                            'STK' => 1,
                            'SUB' => 0.1,
                            'TEL' => 10,
                            'TFD' => 1,
                            'TFL' => 1,
                            'TIME' => 1,
                            'TIO' => 1,
                            'TKY' => 1,
                            'TMT' => 1,
                            'TNC' => 1,
                            'TOMO' => 1,
                            'TRAC' => 1,
                            'UKG' => 1,
                            'UTK' => 1,
                            'WAN' => 1,
                            'WAX' => 1,
                            'WPR' => 1,
                            'WTC' => 0.1,
                            'XAS' => 0.1,
                            'XLM' => 1,
                            'XLR' => 1,
                            'ZIL' => 1,
                            'ZINC' => 1,
                            'ZPT' => 1,
                        ),
                    ),
                ),
            ),
            'commonCurrencies' => array (
                'CAN' => 'CanYaCoin',
                'XRB' => 'NANO',
            ),
        ));
    }

    public function nonce () {
        return $this->milliseconds () - $this->options['timeDifference'];
    }

    public function load_time_difference () {
        $response = $this->publicGetOpenTick ();
        $after = $this->milliseconds ();
        $this->options['timeDifference'] = intval ($after - $response['timestamp']);
        return $this->options['timeDifference'];
    }

    public function calculate_fee ($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $key = 'quote';
        $rate = $market[$takerOrMaker];
        $cost = floatval ($this->cost_to_precision($symbol, $amount * $rate));
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
        }
        return array (
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => floatval ($this->fee_to_precision($symbol, $cost)),
        );
    }

    public function fetch_markets () {
        $response = $this->publicGetMarketOpenSymbols ();
        if ($this->options['adjustForTimeDifference'])
            $this->load_time_difference ();
        $markets = $response['data'];
        $result = array ();
        for ($i = 0; $i < count ($markets); $i++) {
            $market = $markets[$i];
            $id = $market['symbol'];
            $baseId = $market['coinType'];
            $quoteId = $market['coinTypePair'];
            $base = $this->common_currency_code($baseId);
            $quote = $this->common_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => 8,
                'price' => 8,
            );
            $defaultMinAmount = pow (10, -$precision['amount']);
            $minAmount = $this->safe_float($this->options['limits']['amount']['min'], $base, $defaultMinAmount);
            $active = $market['trading'];
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'taker' => $this->safe_float($market, 'feeRate'),
                'maker' => $this->safe_float($market, 'feeRate'),
                'info' => $market,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => $minAmount,
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $response = $this->privateGetAccountCoinWalletAddress (array_merge (array (
            'coin' => $currency['id'],
        ), $params));
        $data = $response['data'];
        $address = $this->safe_string($data, 'address');
        $this->check_address($address);
        $tag = $this->safe_string($data, 'userOid');
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_currencies ($params = array ()) {
        $response = $this->publicGetMarketOpenCoins ($params);
        $currencies = $response['data'];
        $result = array ();
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $currency['coin'];
            // todo => will need to rethink the fees
            // to add support for multiple withdrawal/$deposit methods and
            // differentiated fees for each particular method
            $code = $this->common_currency_code($id);
            $precision = $currency['tradePrecision'];
            $deposit = $currency['enableDeposit'];
            $withdraw = $currency['enableWithdraw'];
            $active = ($deposit && $withdraw);
            $defaultMinAmount = pow (10, -$precision);
            $minAmount = $this->safe_float($this->options['limits']['amount']['min'], $code, $defaultMinAmount);
            $result[$code] = array (
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'name' => $currency['name'],
                'active' => $active,
                'fee' => $currency['withdrawMinFee'], // todo => redesign
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => $minAmount,
                        'max' => pow (10, $precision),
                    ),
                    'price' => array (
                        'min' => pow (10, -$precision),
                        'max' => pow (10, $precision),
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array (
                        'min' => $currency['withdrawMinAmount'],
                        'max' => pow (10, $precision),
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetAccountBalance (array_merge (array (
        ), $params));
        $balances = $response['data'];
        $result = array ( 'info' => $balances );
        $indexed = $this->index_by($balances, 'coinType');
        $keys = is_array ($indexed) ? array_keys ($indexed) : array ();
        for ($i = 0; $i < count ($keys); $i++) {
            $id = $keys[$i];
            $currency = $this->common_currency_code($id);
            $account = $this->account ();
            $balance = $indexed[$id];
            $used = floatval ($balance['freezeBalance']);
            $free = floatval ($balance['balance']);
            $total = $this->sum ($free, $used);
            $account['free'] = $free;
            $account['used'] = $used;
            $account['total'] = $total;
            $result[$currency] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetOpenOrders (array_merge ($request, $params));
        $orderbook = null;
        $timestamp = null;
        // sometimes kucoin returns this:
        // array ("success":true,"code":"OK","msg":"Operation succeeded.","$timestamp":xxxxxxxxxxxxx,"data":null)
        if (!(is_array ($response) && array_key_exists ('data', $response)) || !$response['data']) {
            if ($this->options['fetchOrderBookWarning'])
                throw new ExchangeError ($this->id . " fetchOrderBook returned an null reply. Set exchange.options['fetchOrderBookWarning'] = false to silence this warning");
            $orderbook = array (
                'BUY' => array (),
                'SELL' => array (),
            );
        } else {
            $orderbook = $response['data'];
            $timestamp = $this->safe_integer($response, 'timestamp');
            $timestamp = $this->safe_integer($response['data'], 'timestamp', $timestamp);
        }
        return $this->parse_order_book($orderbook, $timestamp, 'BUY', 'SELL');
    }

    public function parse_order ($order, $market = null) {
        $side = $this->safe_value($order, 'direction');
        if ($side === null)
            $side = $order['type'];
        if ($side !== null)
            $side = strtolower ($side);
        $orderId = $this->safe_string($order, 'orderOid');
        if ($orderId === null)
            $orderId = $this->safe_string($order, 'oid');
        // do not confuse $trades with orders
        $trades = null;
        if (is_array ($order) && array_key_exists ('dealOrders', $order))
            $trades = $this->safe_value($order['dealOrders'], 'datas');
        if ($trades !== null) {
            $trades = $this->parse_trades($trades, $market);
            for ($i = 0; $i < count ($trades); $i++) {
                $trades[$i]['side'] = $side;
                $trades[$i]['order'] = $orderId;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        } else {
            $symbol = $order['coinType'] . '/' . $order['coinTypePair'];
        }
        $timestamp = $this->safe_value($order, 'createdAt');
        $remaining = $this->safe_float($order, 'pendingAmount');
        $status = null;
        if (is_array ($order) && array_key_exists ('status', $order)) {
            $status = $order['status'];
        } else {
            if ($this->safe_value($order, 'isActive', true)) {
                $status = 'open';
            } else {
                $status = 'closed';
            }
        }
        $filled = $this->safe_float($order, 'dealAmount');
        $amount = $this->safe_float($order, 'amount');
        $cost = $this->safe_float($order, 'dealValue');
        if ($cost === null)
            $cost = $this->safe_float($order, 'dealValueTotal');
        if ($status === null) {
            if ($remaining !== null)
                if ($remaining > 0)
                    $status = 'open';
                else
                    $status = 'closed';
        }
        if ($filled === null) {
            if ($status !== null)
                if ($status === 'closed')
                    $filled = $this->safe_float($order, 'amount');
        } else if ($filled === 0.0) {
            if ($trades !== null) {
                $cost = 0;
                for ($i = 0; $i < count ($trades); $i++) {
                    $filled .= $trades[$i]['amount'];
                    $cost .= $trades[$i]['cost'];
                }
            }
        }
        // kucoin $price and $amount fields have varying names
        // thus the convoluted spaghetti code below
        $price = null;
        if ($filled !== null) {
            // if the $order was $filled at least for some part
            if ($filled > 0.0) {
                $price = $this->safe_float($order, 'price');
                if ($price === null)
                    $price = $this->safe_float($order, 'dealPrice');
                if ($price === null)
                    $price = $this->safe_float($order, 'dealPriceAverage');
            } else {
                // it's an open $order, not $filled yet, use the initial $price
                $price = $this->safe_float($order, 'orderPrice');
                if ($price === null)
                    $price = $this->safe_float($order, 'price');
            }
            if ($price !== null) {
                if ($cost === null)
                    $cost = $price * $filled;
            }
            if ($amount === null) {
                if ($remaining !== null)
                    $amount = $this->sum ($filled, $remaining);
            } else if ($remaining === null) {
                $remaining = $amount - $filled;
            }
        }
        if ($status === 'open') {
            if (($cost === null) || ($cost === 0.0))
                if ($price !== null)
                    if ($amount !== null)
                        $cost = $amount * $price;
        }
        $feeCurrency = null;
        if ($market !== null) {
            $feeCurrency = ($side === 'sell') ? $market['quote'] : $market['base'];
        } else {
            $feeCurrencyField = ($side === 'sell') ? 'coinTypePair' : 'coinType';
            $feeCurrency = $this->safe_string($order, $feeCurrencyField);
            if ($feeCurrency !== null) {
                if (is_array ($this->currencies_by_id) && array_key_exists ($feeCurrency, $this->currencies_by_id))
                    $feeCurrency = $this->currencies_by_id[$feeCurrency]['code'];
            }
        }
        $feeCost = $this->safe_float($order, 'fee');
        $fee = array (
            'cost' => $this->safe_float($order, 'feeTotal', $feeCost),
            'rate' => $this->safe_float($order, 'feeRate'),
            'currency' => $feeCurrency,
        );
        $result = array (
            'info' => $order,
            'id' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => $trades,
        );
        return $result;
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        if ($symbol === null)
            throw new ExchangeError ($this->id . ' fetchOrder requires a $symbol argument');
        $orderType = $this->safe_value($params, 'type');
        if ($orderType === null)
            throw new ExchangeError ($this->id . ' fetchOrder requires a type parameter ("BUY" or "SELL")');
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
            'type' => $orderType,
            'orderOid' => $id,
        );
        $response = $this->privateGetOrderDetail (array_merge ($request, $params));
        if (!$response['data'])
            throw new OrderNotFound ($this->id . ' ' . $this->json ($response));
        //
        // the caching part to be removed
        //
        //     $order = $this->parse_order($response['data'], $market);
        //     $orderId = $order['id'];
        //     if (is_array ($this->orders) && array_key_exists ($orderId, $this->orders))
        //         $order['status'] = $this->orders[$orderId]['status'];
        //     $this->orders[$orderId] = $order;
        //
        return $this->parse_order($response['data'], $market);
    }

    public function parse_orders_by_status ($orders, $market, $since, $limit, $status) {
        $result = array ();
        for ($i = 0; $i < count ($orders); $i++) {
            $order = $this->parse_order(array_merge ($orders[$i], array (
                'status' => $status,
            )), $market);
            $result[] = $order;
        }
        $symbol = ($market !== null) ? $market['symbol'] : null;
        return $this->filter_by_symbol_since_limit($result, $symbol, $since, $limit);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $marketId = null;
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $marketId = $market['id'];
        } else {
            $marketId = '';
        }
        $request = array (
            'symbol' => $marketId,
        );
        $response = $this->privateGetOrderActiveMap (array_merge ($request, $params));
        $sell = $this->safe_value($response['data'], 'SELL');
        if ($sell === null)
            $sell = array ();
        $buy = $this->safe_value($response['data'], 'BUY');
        if ($buy === null)
            $buy = array ();
        $orders = $this->array_concat($sell, $buy);
        //
        // the caching part to be removed
        //
        //     for ($i = 0; $i < count ($orders); $i++) {
        //         $order = $this->parse_order(array_merge ($orders[$i], array (
        //             'status' => 'open',
        //         )), $market);
        //         $orderId = $order['id'];
        //         if (is_array ($this->orders) && array_key_exists ($orderId, $this->orders))
        //             if ($this->orders[$orderId]['status'] !== 'open')
        //                 $order['status'] = $this->orders[$orderId]['status'];
        //         $this->orders[$order['id']] = $order;
        //     }
        //     $openOrders = $this->filter_by($this->orders, 'status', 'open');
        //     return $this->filter_by_symbol_since_limit($openOrders, $symbol, $since, $limit);
        //
        return $this->parse_orders_by_status ($orders, $market, $since, $limit, 'open');
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = 20, $params = array ()) {
        $request = array ();
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null)
            $request['since'] = $since;
        if ($limit !== null)
            $request['limit'] = $limit;
        $response = $this->privateGetOrderDealt (array_merge ($request, $params));
        $orders = $response['data']['datas'];
        //
        // the caching part to be removed
        //
        //     for ($i = 0; $i < count ($orders); $i++) {
        //         $order = $this->parse_order(array_merge ($orders[$i], array (
        //             'status' => 'closed',
        //         )), $market);
        //         $orderId = $order['id'];
        //         if (is_array ($this->orders) && array_key_exists ($orderId, $this->orders))
        //             if ($this->orders[$orderId]['status'] === 'canceled')
        //                 $order['status'] = $this->orders[$orderId]['status'];
        //         $this->orders[$order['id']] = $order;
        //     }
        //     $closedOrders = $this->filter_by($this->orders, 'status', 'closed');
        //     return $this->filter_by_symbol_since_limit($closedOrders, $symbol, $since, $limit);
        //
        return $this->parse_orders_by_status ($orders, $market, $since, $limit, 'closed');
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type !== 'limit')
            throw new ExchangeError ($this->id . ' allows limit orders only');
        $this->load_markets();
        $market = $this->market ($symbol);
        $quote = $market['quote'];
        $base = $market['base'];
        $request = array (
            'symbol' => $market['id'],
            'type' => strtoupper ($side),
            'price' => $this->truncate ($price, $this->currencies[$quote]['precision']),
            'amount' => $this->truncate ($amount, $this->currencies[$base]['precision']),
        );
        $price = floatval ($price);
        $amount = floatval ($amount);
        $cost = $price * $amount;
        $response = $this->privatePostOrder (array_merge ($request, $params));
        $orderId = $this->safe_string($response['data'], 'orderOid');
        $timestamp = $this->safe_integer($response, 'timestamp');
        $iso8601 = null;
        if ($timestamp !== null)
            $iso8601 = $this->iso8601 ($timestamp);
        $order = array (
            'info' => $response,
            'id' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $iso8601,
            'lastTradeTimestamp' => null,
            'symbol' => $market['symbol'],
            'type' => $type,
            'side' => $side,
            'amount' => $amount,
            'filled' => null,
            'remaining' => null,
            'price' => $price,
            'cost' => $cost,
            'status' => 'open',
            'fee' => null,
            'trades' => null,
        );
        $this->orders[$orderId] = $order;
        return $order;
    }

    public function cancel_orders ($symbol = null, $params = array ()) {
        // https://kucoinapidocs.docs.apiary.io/#reference/0/trading/cancel-all-orders
        // docs say $symbol is required, but it seems to be optional
        // you can cancel all orders, or filter by $symbol or type or both
        $request = array ();
        if ($symbol !== null) {
            $this->load_markets();
            $market = $this->market ($symbol);
            $request['symbol'] = $market['id'];
        }
        if (is_array ($params) && array_key_exists ('type', $params)) {
            $request['type'] = strtoupper ($params['type']);
            $params = $this->omit ($params, 'type');
        }
        //
        // the caching part to be removed
        //
        //     $response = $this->privatePostOrderCancelAll (array_merge ($request, $params));
        //     $openOrders = $this->filter_by($this->orders, 'status', 'open');
        //     for ($i = 0; $i < count ($openOrders); $i++) {
        //         $order = $openOrders[$i];
        //         $orderId = $order['id'];
        //         $this->orders[$orderId]['status'] = 'canceled';
        //     }
        //     return $response;
        //
        return $this->privatePostOrderCancelAll (array_merge ($request, $params));
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        if ($symbol === null)
            throw new ExchangeError ($this->id . ' cancelOrder requires a symbol');
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
            'orderOid' => $id,
        );
        if (is_array ($params) && array_key_exists ('type', $params)) {
            $request['type'] = strtoupper ($params['type']);
            $params = $this->omit ($params, 'type');
        } else {
            throw new ExchangeError ($this->id . ' cancelOrder requires parameter type=["BUY"|"SELL"]');
        }
        //
        // the caching part to be removed
        //
        //     $response = $this->privatePostCancelOrder (array_merge ($request, $params));
        //     if (is_array ($this->orders) && array_key_exists ($id, $this->orders)) {
        //         $this->orders[$id]['status'] = 'canceled';
        //     } else {
        //         // store it in cache for further references
        //         $timestamp = $this->milliseconds ();
        //         $side = strtolower ($request['type']);
        //         $this->orders[$id] = array (
        //             'id' => $id,
        //             'timestamp' => $timestamp,
        //             'datetime' => $this->iso8601 ($timestamp),
        //             'type' => null,
        //             'side' => $side,
        //             'symbol' => $symbol,
        //             'status' => 'canceled',
        //         );
        //     }
        //     return $response;
        //
        return $this->privatePostCancelOrder (array_merge ($request, $params));
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $ticker['datetime'];
        $symbol = null;
        if ($market === null) {
            $marketId = $ticker['coinType'] . '-' . $ticker['coinTypePair'];
            if (is_array ($this->markets_by_id) && array_key_exists ($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        // TNC coin doesn't have changerate for some reason
        $change = $this->safe_float($ticker, 'change');
        $last = $this->safe_float($ticker, 'lastDealPrice');
        $open = null;
        if ($last !== null)
            if ($change !== null)
                $open = $last - $change;
        $changePercentage = $this->safe_float($ticker, 'changeRate');
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $changePercentage,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => $this->safe_float($ticker, 'volValue'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetMarketOpenSymbols ($params);
        $tickers = $response['data'];
        $result = array ();
        for ($t = 0; $t < count ($tickers); $t++) {
            $ticker = $this->parse_ticker($tickers[$t]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $result;
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetOpenTick (array_merge (array (
            'symbol' => $market['id'],
        ), $params));
        $ticker = $response['data'];
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade ($trade, $market = null) {
        $id = null;
        $order = null;
        $info = $trade;
        $timestamp = null;
        $type = null;
        $side = null;
        $price = null;
        $cost = null;
        $amount = null;
        $fee = null;
        if (gettype ($trade) === 'array' && count (array_filter (array_keys ($trade), 'is_string')) == 0) {
            $timestamp = $trade[0];
            $type = 'limit';
            if ($trade[1] === 'BUY') {
                $side = 'buy';
            } else if ($trade[1] === 'SELL') {
                $side = 'sell';
            }
            $price = $trade[2];
            $amount = $trade[3];
        } else {
            $timestamp = $this->safe_value($trade, 'createdAt');
            $order = $this->safe_string($trade, 'orderOid');
            $id = $this->safe_string($trade, 'oid');
            $side = $this->safe_string($trade, 'direction');
            if ($side !== null)
                $side = strtolower ($side);
            $price = $this->safe_float($trade, 'dealPrice');
            $amount = $this->safe_float($trade, 'amount');
            $cost = $this->safe_float($trade, 'dealValue');
            $feeCurrency = null;
            if ($market !== null) {
                $feeCurrency = ($side === 'sell') ? $market['quote'] : $market['base'];
            } else {
                $feeCurrencyField = ($side === 'sell') ? 'coinTypePair' : 'coinType';
                $feeCurrency = $this->safe_string($order, $feeCurrencyField);
                if ($feeCurrency !== null) {
                    if (is_array ($this->currencies_by_id) && array_key_exists ($feeCurrency, $this->currencies_by_id))
                        $feeCurrency = $this->currencies_by_id[$feeCurrency]['code'];
                }
            }
            $fee = array (
                'cost' => $this->safe_float($trade, 'fee'),
                'currency' => $feeCurrency,
            );
        }
        $symbol = null;
        if ($market !== null)
            $symbol = $market['symbol'];
        return array (
            'id' => $id,
            'order' => $order,
            'info' => $info,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($limit === null) {
            $limit = 100; // default to 100 even if it was explicitly set to null by the user
        }
        $market = $this->market ($symbol);
        $response = $this->publicGetOpenDealOrders (array_merge (array (
            'symbol' => $market['id'],
            'limit' => $limit,
        ), $params));
        return $this->parse_trades($response['data'], $market, $since, $limit);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        // todo => this method is deprecated and to be deleted shortly
        // it improperly mimics fetchMyTrades with closed orders
        // kucoin does not have any means of fetching personal trades at all
        // this will effectively simplify current convoluted implementations of parseOrder and parseTrade
        if ($symbol === null)
            throw new ExchangeError ($this->id . ' fetchMyTrades is deprecated and requires a $symbol argument');
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        if ($limit !== null)
            $request['limit'] = $limit;
        $response = $this->privateGetDealOrders (array_merge ($request, $params));
        return $this->parse_trades($response['data']['datas'], $market, $since, $limit);
    }

    public function parse_trading_view_ohlcv ($ohlcvs, $market = null, $timeframe = '1m', $since = null, $limit = null) {
        $result = $this->convert_trading_view_to_ohlcv($ohlcvs);
        return $this->parse_ohlcvs($result, $market, $timeframe, $since, $limit);
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $end = $this->seconds ();
        $resolution = $this->timeframes[$timeframe];
        // convert 'resolution' to $minutes in order to calculate 'from' later
        $minutes = $resolution;
        if ($minutes === 'D') {
            if ($limit === null)
                $limit = 30; // 30 days, 1 month
            $minutes = 1440;
        } else if ($minutes === 'W') {
            if ($limit === null)
                $limit = 52; // 52 weeks, 1 year
            $minutes = 10080;
        } else if ($limit === null) {
            // last 1440 periods, whatever the duration of the period is
            // for 1m it equals 1 day (24 hours)
            // for 5m it equals 5 days
            // ...
            $limit = 1440;
        }
        $start = $end - $limit * $minutes * 60;
        // if 'since' has been supplied by user
        if ($since !== null) {
            $start = intval ($since / 1000); // convert milliseconds to seconds
            $end = min ($end, $this->sum ($start, $limit * $minutes * 60));
        }
        $request = array (
            'symbol' => $market['id'],
            'resolution' => $resolution,
            'from' => $start,
            'to' => $end,
        );
        $response = $this->publicGetOpenChartHistory (array_merge ($request, $params));
        return $this->parse_trading_view_ohlcv ($response, $market, $timeframe, $since, $limit);
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency ($code);
        $this->check_address($address);
        $response = $this->privatePostAccountCoinWithdrawApply (array_merge (array (
            'coin' => $currency['id'],
            'amount' => $amount,
            'address' => $address,
        ), $params));
        return array (
            'info' => $response,
            'id' => null,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $endpoint = '/' . $this->version . '/' . $this->implode_params($path, $params);
        $url = $this->urls['api'][$api] . $endpoint;
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'private') {
            $this->check_required_credentials();
            // their $nonce is always a calibrated synched milliseconds-timestamp
            $nonce = $this->nonce ();
            $queryString = '';
            $nonce = (string) $nonce;
            if ($query) {
                $queryString = $this->rawencode ($this->keysort ($query));
                $url .= '?' . $queryString;
                if ($method !== 'GET') {
                    $body = $queryString;
                }
            }
            $auth = $endpoint . '/' . $nonce . '/' . $queryString;
            $payload = base64_encode ($this->encode ($auth));
            // $payload should be "encoded" as returned from stringToBase64
            $signature = $this->hmac ($payload, $this->encode ($this->secret), 'sha256');
            $headers = array (
                'KC-API-KEY' => $this->apiKey,
                'KC-API-NONCE' => $nonce,
                'KC-API-SIGNATURE' => $signature,
            );
        } else {
            if ($query)
                $url .= '?' . $this->urlencode ($query);
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function throw_exception_on_error ($response) {
        //
        // API endpoints return the following formats
        //     array ( success => false, $code => "ERROR", msg => "Min price:100.0" )
        //     array ( success => true,  $code => "OK",    msg => "Operation succeeded." )
        //
        // Web OHLCV endpoint returns this:
        //     array ( s => "ok", o => array (), h => array (), l => array (), c => array (), v => array () )
        //
        // This particular method handles API responses only
        //
        if (!(is_array ($response) && array_key_exists ('success', $response)))
            return;
        if ($response['success'] === true)
            return; // not an error
        if (!(is_array ($response) && array_key_exists ('code', $response)) || !(is_array ($response) && array_key_exists ('msg', $response)))
            throw new ExchangeError ($this->id . ' => malformed $response => ' . $this->json ($response));
        $code = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'msg');
        $feedback = $this->id . ' ' . $this->json ($response);
        if ($code === 'UNAUTH') {
            if ($message === 'Invalid nonce')
                throw new InvalidNonce ($feedback);
            throw new AuthenticationError ($feedback);
        } else if ($code === 'ERROR') {
            if (mb_strpos ($message, 'The precision of amount') !== false)
                throw new InvalidOrder ($feedback); // amount violates precision.amount
            if (mb_strpos ($message, 'Min amount each order') !== false)
                throw new InvalidOrder ($feedback); // amount < limits.amount.min
            if (mb_strpos ($message, 'Min price:') !== false)
                throw new InvalidOrder ($feedback); // price < limits.price.min
            if (mb_strpos ($message, 'Max price:') !== false)
                throw new InvalidOrder ($feedback); // price > limits.price.max
            if (mb_strpos ($message, 'The precision of price') !== false)
                throw new InvalidOrder ($feedback); // price violates precision.price
        } else if ($code === 'NO_BALANCE') {
            if (mb_strpos ($message, 'Insufficient balance') !== false)
                throw new InsufficientFunds ($feedback);
        }
        throw new ExchangeError ($this->id . ' => unknown $response => ' . $this->json ($response));
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response = null) {
        if ($response !== null) {
            // JS callchain parses $body beforehand
            $this->throw_exception_on_error($response);
        } else if ($body && ($body[0] === '{')) {
            // Python/PHP callchains don't have json available at this step
            $this->throw_exception_on_error(json_decode ($body, $as_associative_array = true));
        }
    }
}
