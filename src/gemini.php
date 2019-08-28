<?php

namespace ccxt;

use Exception as Exception; // a common import

class gemini extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'gemini',
            'name' => 'Gemini',
            'countries' => array ( 'US' ),
            'rateLimit' => 1500, // 200 for private API
            'version' => 'v1',
            'has' => array (
                'fetchDepositAddress' => false,
                'createDepositAddress' => true,
                'CORS' => false,
                'fetchBidsAsks' => false,
                'fetchTickers' => false,
                'fetchMyTrades' => true,
                'fetchOrder' => true,
                'fetchOrders' => false,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => false,
                'createMarketOrder' => false,
                'withdraw' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => false,
                'fetchDeposits' => false,
                'fetchOHLCV' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27816857-ce7be644-6096-11e7-82d6-3c257263229c.jpg',
                'api' => array (
                    'public' => 'https://api.gemini.com',
                    'private' => 'https://api.gemini.com',
                    'web' => 'https://docs.gemini.com',
                ),
                'www' => 'https://gemini.com/',
                'doc' => array (
                    'https://docs.gemini.com/rest-api',
                    'https://docs.sandbox.gemini.com',
                ),
                'test' => 'https://api.sandbox.gemini.com',
                'fees' => array (
                    'https://gemini.com/api-fee-schedule',
                    'https://gemini.com/trading-fees',
                    'https://gemini.com/transfer-fees',
                ),
            ),
            'api' => array (
                'web' => array (
                    'get' => array (
                        'rest-api',
                    ),
                ),
                'public' => array (
                    'get' => array (
                        'v1/symbols',
                        'v1/pubticker/{symbol}',
                        'v1/book/{symbol}',
                        'v1/trades/{symbol}',
                        'v1/auction/{symbol}',
                        'v1/auction/{symbol}/history',
                        'v2/candles/{symbol}/{timeframe}',
                        'v2/ticker/{symbol}',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'v1/order/new',
                        'v1/order/cancel',
                        'v1/order/cancel/session',
                        'v1/order/cancel/all',
                        'v1/order/status',
                        'v1/orders',
                        'v1/mytrades',
                        'v1/tradevolume',
                        'v1/transfers',
                        'v1/balances',
                        'v1/deposit/{currency}/newAddress',
                        'v1/withdraw/{currency}',
                        'v1/heartbeat',
                        'v1/transfers',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'taker' => 0.0035,
                    'maker' => 0.001,
                ),
            ),
            'httpExceptions' => array (
                '400' => '\\ccxt\\BadRequest', // Auction not open or paused, ineligible timing, market not open, or the request was malformed, in the case of a private API request, missing or malformed Gemini private API authentication headers
                '403' => '\\ccxt\\PermissionDenied', // The API key is missing the role necessary to access this private API endpoint
                '404' => '\\ccxt\\OrderNotFound', // Unknown API entry point or Order not found
                '406' => '\\ccxt\\InsufficientFunds', // Insufficient Funds
                '429' => '\\ccxt\\DDoSProtection', // Rate Limiting was applied
                '500' => '\\ccxt\\ExchangeError', // The server encountered an error
                '502' => '\\ccxt\\ExchangeError', // Technical issues are preventing the request from being satisfied
                '503' => '\\ccxt\\ExchangeNotAvailable', // The exchange is down for maintenance
            ),
            'timeframes' => array (
                '1m' => '1m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1hr',
                '6h' => '6hr',
                '1d' => '1day',
            ),
            'exceptions' => array (
                'exact' => array (
                    'AuctionNotOpen' => '\\ccxt\\BadRequest', // Failed to place an auction-only order because there is no current auction open for this symbol
                    'ClientOrderIdTooLong' => '\\ccxt\\BadRequest', // The Client Order ID must be under 100 characters
                    'ClientOrderIdMustBeString' => '\\ccxt\\BadRequest', // The Client Order ID must be a string
                    'ConflictingOptions' => '\\ccxt\\BadRequest', // New orders using a combination of order execution options are not supported
                    'EndpointMismatch' => '\\ccxt\\BadRequest', // The request was submitted to an endpoint different than the one in the payload
                    'EndpointNotFound' => '\\ccxt\\BadRequest', // No endpoint was specified
                    'IneligibleTiming' => '\\ccxt\\BadRequest', // Failed to place an auction order for the current auction on this symbol because the timing is not eligible, new orders may only be placed before the auction begins.
                    'InsufficientFunds' => '\\ccxt\\InsufficientFunds', // The order was rejected because of insufficient funds
                    'InvalidJson' => '\\ccxt\\BadRequest', // The JSON provided is invalid
                    'InvalidNonce' => '\\ccxt\\InvalidNonce', // The nonce was not greater than the previously used nonce, or was not present
                    'InvalidOrderType' => '\\ccxt\\InvalidOrder', // An unknown order type was provided
                    'InvalidPrice' => '\\ccxt\\InvalidOrder', // For new orders, the price was invalid
                    'InvalidQuantity' => '\\ccxt\\InvalidOrder', // A negative or otherwise invalid quantity was specified
                    'InvalidSide' => '\\ccxt\\InvalidOrder', // For new orders, and invalid side was specified
                    'InvalidSignature' => '\\ccxt\\AuthenticationError', // The signature did not match the expected signature
                    'InvalidSymbol' => '\\ccxt\\BadRequest', // An invalid symbol was specified
                    'InvalidTimestampInPayload' => '\\ccxt\\BadRequest', // The JSON payload contained a timestamp parameter with an unsupported value.
                    'Maintenance' => '\\ccxt\\ExchangeNotAvailable', // The system is down for maintenance
                    'MarketNotOpen' => '\\ccxt\\InvalidOrder', // The order was rejected because the market is not accepting new orders
                    'MissingApikeyHeader' => '\\ccxt\\AuthenticationError', // The X-GEMINI-APIKEY header was missing
                    'MissingOrderField' => '\\ccxt\\InvalidOrder', // A required order_id field was not specified
                    'MissingRole' => '\\ccxt\\AuthenticationError', // The API key used to access this endpoint does not have the required role assigned to it
                    'MissingPayloadHeader' => '\\ccxt\\AuthenticationError', // The X-GEMINI-PAYLOAD header was missing
                    'MissingSignatureHeader' => '\\ccxt\\AuthenticationError', // The X-GEMINI-SIGNATURE header was missing
                    'NoSSL' => '\\ccxt\\AuthenticationError', // You must use HTTPS to access the API
                    'OptionsMustBeArray' => '\\ccxt\\BadRequest', // The options parameter must be an array.
                    'OrderNotFound' => '\\ccxt\\OrderNotFound', // The order specified was not found
                    'RateLimit' => '\\ccxt\\DDoSProtection', // Requests were made too frequently. See Rate Limits below.
                    'System' => '\\ccxt\\ExchangeError', // We are experiencing technical issues
                    'UnsupportedOption' => '\\ccxt\\BadRequest', // This order execution option is not supported.
                ),
                'broad' => array(),
            ),
            'options' => array (
                'fetchMarketsMethod' => 'fetch_markets_from_web',
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $method = $this->safe_value($this->options, 'fetchMarketsMethod', 'fetch_markets_from_api');
        return $this->$method ($params);
    }

    public function fetch_markets_from_web ($symbols = null, $params = array ()) {
        $response = $this->webGetRestApi ($params);
        $sections = explode('<h1 $id="$symbols-and-minimums">Symbols and minimums</h1>', $response);
        $numSections = is_array ($sections) ? count ($sections) : 0;
        $error = $this->id . ' the ' . $this->name . ' API doc HTML markup has changed, breaking the parser of order limits and $precision info for ' . $this->name . ' markets.';
        if ($numSections !== 2) {
            throw new NotSupported($error);
        }
        $tables = explode('tbody>', $sections[1]);
        $numTables = is_array ($tables) ? count ($tables) : 0;
        if ($numTables < 2) {
            throw new NotSupported($error);
        }
        // $tables[1] = str_replace("\n", '', $tables[1]); // eslint-disable-line quotes
        $rows = explode("{tr}\n", $tables[1]); // eslint-disable-line quotes
        $numRows = is_array ($rows) ? count ($rows) : 0;
        if ($numRows < 2) {
            throw new NotSupported($error);
        }
        $result = array();
        // skip the first element (empty string)
        for ($i = 1; $i < $numRows; $i++) {
            $row = $rows[$i];
            $cells = explode("</td>\n", $row); // eslint-disable-line quotes
            $numCells = is_array ($cells) ? count ($cells) : 0;
            if ($numCells < 7) {
                throw new NotSupported($error);
            }
            //
            //     array (
            //         '{td}<code class="prettyprint">btcusd</code>',
            //         '{td}USD', // $quote
            //         '{td}BTC', // $base
            //         '{td}0.00001 BTC (1e-5)', // min amount
            //         '{td}0.00000001 BTC (1e-8)', // amount min tick size
            //         '{td}0.01 USD', // price min tick size
            //         '</tr>\n'
            //     )
            //
            $id = str_replace('{td}', '', $cells[0]);
            $id = str_replace('<code class="prettyprint">', '', $id);
            $id = str_replace('</code>', '', $id);
            $baseId = str_replace('{td}', '', $cells[2]);
            $quoteId = str_replace('{td}', '', $cells[1]);
            $minAmountAsString = str_replace('{td}', '', $cells[3]);
            $amountTickSizeAsString = str_replace('{td}', '', $cells[4]);
            $priceTickSizeAsString = str_replace('{td}', '', $cells[5]);
            $minAmount = explode(' ', $minAmountAsString);
            $amountPrecision = explode(' ', $amountTickSizeAsString);
            $pricePrecision = explode(' ', $priceTickSizeAsString);
            $baseId = strtolower($baseId);
            $quoteId = strtolower($quoteId);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => $this->precision_from_string($amountPrecision[0]),
                'price' => $this->precision_from_string($pricePrecision[0]),
            );
            $active = null;
            $result[] = array (
                'id' => $id,
                'info' => $row,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => floatval ($minAmount[0]),
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_markets_from_api ($params = array ()) {
        $response = $this->publicGetV1Symbols ($params);
        $result = array();
        for ($i = 0; $i < count ($response); $i++) {
            $id = $response[$i];
            $market = $id;
            $baseId = mb_substr($id, 0, 3 - 0);
            $quoteId = mb_substr($id, 3, 6 - 3);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => null,
                'price' => null,
            );
            $result[] = array (
                'id' => $id,
                'info' => $market,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'symbol' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['limit_bids'] = $limit;
            $request['limit_asks'] = $limit;
        }
        $response = $this->publicGetV1BookSymbol (array_merge ($request, $params));
        return $this->parse_order_book($response, null, 'bids', 'asks', 'price', 'amount');
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        $ticker = $this->publicGetV1PubtickerSymbol (array_merge ($request, $params));
        $timestamp = $this->safe_integer($ticker['volume'], 'timestamp');
        $baseCurrency = $market['base']; // unified structures are guaranteed to have unified fields
        $quoteCurrency = $market['quote']; // so we don't need safe-methods for unified structures
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => null,
            'low' => null,
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
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker['volume'], $baseCurrency),
            'quoteVolume' => $this->safe_float($ticker['volume'], $quoteCurrency),
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->safe_integer($trade, 'timestampms');
        $id = $this->safe_string($trade, 'tid');
        $orderId = $this->safe_string($trade, 'order_id');
        $feeCurrencyId = $this->safe_string($trade, 'fee_currency');
        $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
        $fee = array (
            'cost' => $this->safe_float($trade, 'fee_amount'),
            'currency' => $feeCurrencyCode,
        );
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $type = null;
        $side = $this->safe_string_lower($trade, 'type');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array (
            'id' => $id,
            'order' => $orderId,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        $response = $this->publicGetV1TradesSymbol (array_merge ($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostV1Balances ($params);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count ($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account ();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['total'] = $this->safe_float($balance, 'amount');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order ($order, $market = null) {
        $timestamp = $this->safe_integer($order, 'timestampms');
        $amount = $this->safe_float($order, 'original_amount');
        $remaining = $this->safe_float($order, 'remaining_amount');
        $filled = $this->safe_float($order, 'executed_amount');
        $status = 'closed';
        if ($order['is_live']) {
            $status = 'open';
        }
        if ($order['is_cancelled']) {
            $status = 'canceled';
        }
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'avg_execution_price');
        $cost = null;
        if ($filled !== null) {
            if ($average !== null) {
                $cost = $filled * $average;
            }
        }
        $type = $this->safe_string($order, 'type');
        if ($type === 'exchange limit') {
            $type = 'limit';
        } else if ($type === 'market buy' || $type === 'market sell') {
            $type = 'market';
        } else {
            $type = $order['type'];
        }
        $fee = null;
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($order, 'symbol');
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $id = $this->safe_string($order, 'order_id');
        $side = $this->safe_string_lower($order, 'side');
        return array (
            'id' => $id,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'average' => $average,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'fee' => $fee,
        );
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => $id,
        );
        $response = $this->privatePostV1OrderStatus (array_merge ($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostV1Orders ($params);
        $orders = $this->parse_orders($response, null, $since, $limit);
        if ($symbol !== null) {
            $market = $this->market ($symbol); // throws on non-existent $symbol
            $orders = $this->filter_by_symbol($orders, $market['symbol']);
        }
        return $orders;
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $nonce = $this->nonce ();
        $request = array (
            'client_order_id' => (string) $nonce,
            'symbol' => $this->market_id($symbol),
            'amount' => (string) $amount,
            'price' => (string) $price,
            'side' => $side,
            'type' => 'exchange limit', // gemini allows limit orders only
        );
        $response = $this->privatePostV1OrderNew (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $response['order_id'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => $id,
        );
        return $this->privatePostV1OrderCancel (array_merge ($request, $params));
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit_trades'] = $limit;
        }
        if ($since !== null) {
            $request['timestamp'] = intval ($since / 1000);
        }
        $response = $this->privatePostV1Mytrades (array_merge ($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
            'amount' => $amount,
            'address' => $address,
        );
        $response = $this->privatePostV1WithdrawCurrency (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $this->safe_string($response, 'txHash'),
        );
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function fetch_transactions ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($limit !== null) {
            $request['limit_transfers'] = $limit;
        }
        if ($since !== null) {
            $request['timestamp'] = $since;
        }
        $response = $this->privatePostV1Transfers (array_merge ($request, $params));
        return $this->parseTransactions ($response);
    }

    public function parse_transaction ($transaction, $currency = null) {
        $timestamp = $this->safe_integer($transaction, 'timestampms');
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $address = $this->safe_string($transaction, 'destination');
        $type = $this->safe_string_lower($transaction, 'type');
        $status = 'pending';
        // When deposits show as Advanced or Complete they are available for trading.
        if ($transaction['status']) {
            $status = 'ok';
        }
        $fee = null;
        $feeAmount = $this->safe_float($transaction, 'feeAmount');
        if ($feeAmount !== null) {
            $fee = array (
                'cost' => $feeAmount,
                'currency' => $code,
            );
        }
        return array (
            'info' => $transaction,
            'id' => $this->safe_string($transaction, 'eid'),
            'txid' => $this->safe_string($transaction, 'txHash'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'address' => $address,
            'tag' => null, // or is it defined?
            'type' => $type, // direction of the $transaction, ('deposit' | 'withdraw')
            'amount' => $this->safe_float($transaction, 'amount'),
            'currency' => $code,
            'status' => $status,
            'updated' => null,
            'fee' => $fee,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $request = array_merge (array (
                'request' => $url,
                'nonce' => $nonce,
            ), $query);
            $payload = $this->json ($request);
            $payload = base64_encode ($this->encode ($payload));
            $signature = $this->hmac ($payload, $this->encode ($this->secret), 'sha384');
            $headers = array (
                'Content-Type' => 'text/plain',
                'X-GEMINI-APIKEY' => $this->apiKey,
                'X-GEMINI-PAYLOAD' => $this->decode ($payload),
                'X-GEMINI-SIGNATURE' => $signature,
            );
        } else {
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        }
        $url = $this->urls['api'][$api] . $url;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        //
        //     {
        //         "$result" => "error",
        //         "$reason" => "BadNonce",
        //         "$message" => "Out-of-sequence nonce {1234} precedes previously used nonce {2345}"
        //     }
        //
        $result = $this->safe_string($response, 'result');
        if ($result === 'error') {
            $reason = $this->safe_string($response, 'reason');
            $message = $this->safe_string($response, 'message');
            $feedback = $this->id . ' ' . $message;
            $exact = $this->exceptions['exact'];
            if (is_array($exact) && array_key_exists($reason, $exact)) {
                throw new $exact[$reason]($feedback);
            } else if (is_array($exact) && array_key_exists($message, $exact)) {
                throw new $exact[$message]($feedback);
            }
            $broad = $this->exceptions['broad'];
            $broadKey = $this->findBroadlyMatchedKey ($broad, $message);
            if ($broadKey !== null) {
                throw new $broad[$broadKey]($feedback);
            }
            throw new ExchangeError($feedback); // unknown $message
        }
    }

    public function create_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privatePostV1DepositCurrencyNewAddress (array_merge ($request, $params));
        $address = $this->safe_string($response, 'address');
        $this->check_address($address);
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => null,
            'info' => $response,
        );
    }

    public function fetch_ohlcv ($symbol, $timeframe = '5m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'timeframe' => $this->timeframes[$timeframe],
            'symbol' => $market['id'],
        );
        $response = $this->publicGetV2CandlesSymbolTimeframe (array_merge ($request, $params));
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }
}
