<?php

namespace ccxt;

use Exception as Exception; // a common import

class nova extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'nova',
            'name' => 'Novaexchange',
            'countries' => array ( 'TZ' ), // Tanzania
            'rateLimit' => 2000,
            'version' => 'v2',
            'has' => array (
                'CORS' => false,
                'createMarketOrder' => false,
                'createDepositAddress' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchWithdrawals' => true,
            ),
            'urls' => array (
                'referral' => 'https://novaexchange.com/signup/?re=is8vz2hsl3qxewv1uawd',
                'logo' => 'https://user-images.githubusercontent.com/1294454/30518571-78ca0bca-9b8a-11e7-8840-64b83a4a94b2.jpg',
                'api' => 'https://novaexchange.com/remote',
                'www' => 'https://novaexchange.com',
                'doc' => 'https://novaexchange.com/remote/faq',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'markets/',
                        'markets/{basecurrency}/',
                        'market/info/{pair}/',
                        'market/orderhistory/{pair}/',
                        'market/openorders/{pair}/buy/',
                        'market/openorders/{pair}/sell/',
                        'market/openorders/{pair}/both/',
                        'market/openorders/{pair}/{ordertype}/',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'getbalances/',
                        'getbalance/{currency}/',
                        'getdeposits/',
                        'getwithdrawals/',
                        'getnewdepositaddress/{currency}/',
                        'getdepositaddress/{currency}/',
                        'myopenorders/',
                        'myopenorders_market/{pair}/',
                        'cancelorder/{orderid}/',
                        'withdraw/{currency}/',
                        'trade/{pair}/',
                        'tradehistory/',
                        'getdeposithistory/',
                        'getwithdrawalhistory/',
                        'walletstatus/',
                        'walletstatus/{currency}/',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.2 / 100,
                    'taker' => 0.2 / 100,
                ),
            ),
            'commonCurrencies' => array (
                'FAIR' => 'FairCoin',
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetMarkets ($params);
        $markets = $response['markets'];
        $result = array();
        for ($i = 0; $i < count ($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'marketname');
            list($quoteId, $baseId) = explode('_', $id);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $disabled = $this->safe_value($market, 'disabled', false);
            $active = !$disabled;
            $result[] = array_merge ($this->fees['trading'], array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'info' => $market,
            ));
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'pair' => $this->market_id($symbol),
        );
        $response = $this->publicGetMarketOpenordersPairBoth (array_merge ($request, $params));
        return $this->parse_order_book($response, null, 'buyorders', 'sellorders', 'price', 'amount');
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $request = array (
            'pair' => $this->market_id($symbol),
        );
        $response = $this->publicGetMarketInfoPair (array_merge ($request, $params));
        $ticker = $response['markets'][0];
        $timestamp = $this->milliseconds ();
        $last = $this->safe_float($ticker, 'last_price');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high24h'),
            'low' => $this->safe_float($ticker, 'low24h'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => $this->safe_float($ticker, 'change24h'),
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => $this->safe_float($ticker, 'volume24h'),
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market = null) {
        //
        // fetchMyTrades
        //
        //    {
        //        basecurrency => 'BTC',
        //        $fee => '0.00000026',
        //        fromamount => '1079.13354707',
        //        fromcurrency => 'LINX',
        //        orig_orderid => 42906337,
        //        $price => '0.00000012',
        //        toamount => '0.0.00012924',
        //        tocurrency => 'BTC',
        //        trade_time => '2019-07-28 13:36',
        //        tradeid => 21715234,
        //        tradetype => 'SELL',
        //        unix_t_trade_time => 1564313790,
        //    }
        //
        $timestamp = $this->safe_timestamp_2($trade, 'unix_t_datestamp', 'unix_t_trade_time');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $type = null;
        $side = $this->safe_string_lower($trade, 'tradetype');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $feeCost = $this->safe_float($trade, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrency = ($market === null) ? null : $market['quote'];
            $fee = array (
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        $order = $this->safe_integer($trade, 'orig_orderid');
        $id = $this->safe_integer($trade, 'tradeid');
        $cost = null;
        if ($price !== null && $amount !== null) {
            $cost = $amount * $price;
        }
        return array (
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'order' => $order,
            'type' => $type,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'pair' => $market['id'],
        );
        $response = $this->publicGetMarketOrderhistoryPair (array_merge ($request, $params));
        return $this->parse_trades($response['items'], $market, $since, $limit);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        //
        // privatePostTradehistory $response
        //
        //    {
        //        items => array (
        //            array (
        //                basecurrency => 'BTC',
        //                fee => '0.00000026',
        //                fromamount => '1079.13354707',
        //                fromcurrency => 'LINX',
        //                orig_orderid => 42906337,
        //                price => '0.00000012',
        //                toamount => '0.0.00012924',
        //                tocurrency => 'BTC',
        //                trade_time => '2019-07-28 13:36',
        //                tradeid => 21715234,
        //                tradetype => 'SELL',
        //                unix_t_trade_time => 1564313790,
        //            ),
        //        ),
        //        message => 'Your trade history with recent first',
        //        page => 1,
        //        pages => 1,
        //        perpage => 100,
        //        status => 'success',
        //        total_items => 1
        //    }
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
        }
        $response = $this->privatePostTradehistory ($params);
        return $this->parse_trades($response['items'], $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetbalances ($params);
        $balances = $this->safe_value($response, 'balances');
        $result = array( 'info' => $response );
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $lockbox = $this->safe_float($balance, 'amount_lockbox');
            $trades = $this->safe_float($balance, 'amount_trades');
            $account = array (
                'free' => $this->safe_float($balance, 'amount'),
                'used' => $this->sum ($lockbox, $trades),
                'total' => $this->safe_float($balance, 'amount_total'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $amount = (string) $amount;
        $price = (string) $price;
        $market = $this->market ($symbol);
        $request = array (
            'tradetype' => strtoupper($side),
            'tradeamount' => $amount,
            'tradeprice' => $price,
            'tradebase' => 1,
            'pair' => $market['id'],
        );
        $response = $this->privatePostTradePair (array_merge ($request, $params));
        $tradeItems = $this->safe_value($response, 'tradeitems', array());
        $tradeItemsByType = $this->index_by($tradeItems, 'type');
        $created = $this->safe_value($tradeItemsByType, 'created', array());
        $orderId = $this->safe_string($created, 'orderid');
        return array (
            'info' => $response,
            'id' => $orderId,
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $request = array (
            'orderid' => $id,
        );
        return $this->privatePostCancelorder (array_merge ($request, $params));
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        //
        // privatePostMyopenorders $response
        //
        //    {
        //        items => array (
        //            array (
        //                fromamount => 1079.13354707,
        //                fromcurrency => 'LINX',
        //                $market => 'BTC_LINX',
        //                orderdate => '2019-07-28 10:50',
        //                orderid => 43102690,
        //                ordertype => 'SELL',
        //                price => '0.00000015',
        //                toamount => '0.00016187',
        //                tocurrency => 'BTC',
        //                unix_t_orderdate => 1564303847
        //            ),
        //        ),
        //        message => 'Your open $orders with recent first',
        //        page => 1,
        //        pages => 1,
        //        perpage => 100,
        //        status => 'success',
        //        total_items => 1
        //    }
        $this->load_markets();
        $market = null;
        $response = $this->privatePostMyopenorders ($params);
        $orders = $this->safe_value($response, 'items', array());
        return $this->parse_orders($orders, $market, $since, $limit, array (
            'status' => 'open',
        ));
    }

    public function parse_order ($order, $market = null) {
        //
        // fetchOpenOrders
        //
        //    {
        //        fromamount => 1079.13354707,
        //        fromcurrency => 'LINX',
        //        $market => 'BTC_LINX',
        //        orderdate => '2019-07-28 10:50',
        //        orderid => 43102690,
        //        ordertype => 'SELL',
        //        price => '0.00000015',
        //        toamount => '0.00016187',
        //        tocurrency => 'BTC',
        //        unix_t_orderdate => 1564303847
        //    }
        //
        $orderId = $this->safe_string($order, 'orderid');
        $symbol = null;
        $marketId = $this->safe_string($order, 'market');
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $symbol = $market['symbol'];
            } else {
                $baseId = $this->safe_string($order, 'fromcurrency');
                $quoteId = $this->safe_string($order, 'tocurrency');
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        $status = $this->safe_string($order, 'status');
        $timestamp = $this->safe_timestamp($order, 'unix_t_orderdate');
        $amount = $this->safe_float($order, 'fromamount');
        $side = $this->safe_string_lower($order, 'ordertype');
        return array (
            'id' => $orderId,
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'type' => null,
            'side' => $side,
            'price' => null,
            'cost' => null,
            'amount' => $amount,
            'remaining' => null,
            'filled' => null,
            'status' => $status,
            'fee' => null,
            'trades' => null,
            'info' => $order,
        );
    }

    public function create_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privatePostGetnewdepositaddressCurrency (array_merge ($request, $params));
        $address = $this->safe_string($response, 'address');
        $this->check_address($address);
        $tag = $this->safe_string($response, 'tag');
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privatePostGetdepositaddressCurrency (array_merge ($request, $params));
        $address = $this->safe_string($response, 'address');
        $this->check_address($address);
        $tag = $this->safe_string($response, 'tag');
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function parse_transaction ($transaction, $currency = null) {
        $timestamp = $this->safe_timestamp_2($transaction, 'unix_t_time_seen', 'unix_t_daterequested');
        $updated = $this->safe_timestamp($transaction, 'unix_t_datesent');
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $status = $this->parse_transaction_status ($this->safe_string($transaction, 'status'));
        $amount = $this->safe_float($transaction, 'tx_amount');
        $addressTo = $this->safe_string($transaction, 'tx_address');
        $fee = null;
        $txid = $this->safe_string($transaction, 'tx_txid');
        $type = $this->safe_string($transaction, 'type');
        return array (
            'info' => $transaction,
            'id' => null,
            'currency' => $code,
            'amount' => $amount,
            'addressFrom' => null,
            'address' => $addressTo,
            'addressTo' => $addressTo,
            'tagFrom' => null,
            'tag' => null,
            'tagTo' => null,
            'status' => $status,
            'type' => $type,
            'updated' => $updated,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'fee' => $fee,
        );
    }

    public function parse_transaction_status ($status) {
        $statuses = array (
            'Accounted' => 'ok',
            'Confirmed' => 'ok',
            'Incoming' => 'pending',
            'Approved' => 'pending',
            'Sent' => 'pending',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function fetch_deposits ($code = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->privatePostGetdeposithistory ($params);
        for ($i = 0; $i < count ($response['items']); $i++) {
            $response['items'][$i]['type'] = 'deposit';
        }
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency ($code);
        }
        $deposits = $this->safe_value($response, 'items', array());
        return $this->parseTransactions ($deposits, $currency, $since, $limit);
    }

    public function fetch_withdrawals ($code = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->privatePostGetwithdrawalhistory ($params);
        for ($i = 0; $i < count ($response['items']); $i++) {
            $response['items'][$i]['type'] = 'withdrawal';
        }
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency ($code);
        }
        $withdrawals = $this->safe_value($response, 'items', array());
        return $this->parseTransactions ($withdrawals, $currency, $since, $limit);
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/';
        if ($api === 'private') {
            $url .= $api . '/';
        }
        $url .= $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce ();
            $url .= '?' . $this->urlencode (array( 'nonce' => $nonce ));
            $signature = $this->hmac ($this->encode ($url), $this->encode ($this->secret), 'sha512', 'base64');
            $body = $this->urlencode (array_merge (array (
                'apikey' => $this->apiKey,
                'signature' => $signature,
            ), $query));
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('status', $response)) {
            if ($response['status'] !== 'success') {
                throw new ExchangeError($this->id . ' ' . $this->json ($response));
            }
        }
        return $response;
    }
}
