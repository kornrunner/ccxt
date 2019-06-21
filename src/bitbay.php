<?php

namespace ccxt;

use Exception as Exception; // a common import

class bitbay extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bitbay',
            'name' => 'BitBay',
            'countries' => array ( 'MT', 'EU' ), // Malta
            'rateLimit' => 1000,
            'has' => array (
                'CORS' => true,
                'withdraw' => true,
                'fetchMyTrades' => true,
            ),
            'urls' => array (
                'referral' => 'https://auth.bitbay.net/ref/jHlbB4mIkdS1',
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766132-978a7bd8-5ece-11e7-9540-bc96d1e9bbb8.jpg',
                'www' => 'https://bitbay.net',
                'api' => array (
                    'public' => 'https://bitbay.net/API/Public',
                    'private' => 'https://bitbay.net/API/Trading/tradingApi.php',
                    'v1_01Public' => 'https://api.bitbay.net/rest',
                    'v1_01Private' => 'https://api.bitbay.net/rest',
                ),
                'doc' => array (
                    'https://bitbay.net/public-api',
                    'https://bitbay.net/en/private-api',
                    'https://bitbay.net/account/tab-api',
                    'https://github.com/BitBayNet/API',
                    'https://docs.bitbay.net/v1.0.1-en/reference',
                ),
                'fees' => 'https://bitbay.net/en/fees',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        '{id}/all',
                        '{id}/market',
                        '{id}/orderbook',
                        '{id}/ticker',
                        '{id}/trades',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'info',
                        'trade',
                        'cancel',
                        'orderbook',
                        'orders',
                        'transfer',
                        'withdraw',
                        'history',
                        'transactions',
                    ),
                ),
                'v1_01Public' => array (
                    'get' => array (
                        'trading/ticker',
                        'trading/ticker/{symbol}',
                        'trading/stats',
                        'trading/orderbook/{symbol}',
                        'trading/transactions/{symbol}',
                        'trading/candle/history/{symbol}/{resolution}',
                    ),
                ),
                'v1_01Private' => array (
                    'get' => array (
                        'payments/withdrawal/{detailId}',
                        'payments/deposit/{detailId}',
                        'trading/offer',
                        'trading/config/{symbol}',
                        'trading/history/transactions',
                        'balances/BITBAY/history',
                        'balances/BITBAY/balance',
                        'fiat_cantor/rate/{baseId}/{quoteId}',
                        'fiat_cantor/history',
                    ),
                    'post' => array (
                        'trading/offer/{symbol}',
                        'trading/config/{symbol}',
                        'balances/BITBAY/balance',
                        'balances/BITBAY/balance/transfer/{source}/{destination}',
                        'fiat_cantor/exchange',
                    ),
                    'delete' => array (
                        'trading/offer/{symbol}/{id}/{side}/{price}',
                    ),
                    'put' => array (
                        'balances/BITBAY/balance/{id}',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.3 / 100,
                    'taker' => 0.0043,
                ),
                'funding' => array (
                    'withdraw' => array (
                        'BTC' => 0.0009,
                        'LTC' => 0.005,
                        'ETH' => 0.00126,
                        'LSK' => 0.2,
                        'BCH' => 0.0006,
                        'GAME' => 0.005,
                        'DASH' => 0.001,
                        'BTG' => 0.0008,
                        'PLN' => 4,
                        'EUR' => 1.5,
                    ),
                ),
            ),
            'exceptions' => array (
                '400' => '\\ccxt\\ExchangeError', // At least one parameter wasn't set
                '401' => '\\ccxt\\InvalidOrder', // Invalid order type
                '402' => '\\ccxt\\InvalidOrder', // No orders with specified currencies
                '403' => '\\ccxt\\InvalidOrder', // Invalid payment currency name
                '404' => '\\ccxt\\InvalidOrder', // Error. Wrong transaction type
                '405' => '\\ccxt\\InvalidOrder', // Order with this id doesn't exist
                '406' => '\\ccxt\\InsufficientFunds', // No enough money or crypto
                // code 407 not specified are not specified in their docs
                '408' => '\\ccxt\\InvalidOrder', // Invalid currency name
                '501' => '\\ccxt\\AuthenticationError', // Invalid public key
                '502' => '\\ccxt\\AuthenticationError', // Invalid sign
                '503' => '\\ccxt\\InvalidNonce', // Invalid moment parameter. Request time doesn't match current server time
                '504' => '\\ccxt\\ExchangeError', // Invalid method
                '505' => '\\ccxt\\AuthenticationError', // Key has no permission for this action
                '506' => '\\ccxt\\AuthenticationError', // Account locked. Please contact with customer service
                // codes 507 and 508 are not specified in their docs
                '509' => '\\ccxt\\ExchangeError', // The BIC/SWIFT is required for this currency
                '510' => '\\ccxt\\ExchangeError', // Invalid market name
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->v1_01PublicGetTradingTicker ($params);
        //
        //     {
        //         status => 'Ok',
        //         $items => array (
        //             'BSV-USD' => array (
        //                 $market => array (
        //                     code => 'BSV-USD',
        //                     $first => array( currency => 'BSV', minOffer => '0.00035', scale => 8 ),
        //                     $second => array( currency => 'USD', minOffer => '5', scale => 2 )
        //                 ),
        //                 time => '1557569762154',
        //                 highestBid => '52.31',
        //                 lowestAsk => '62.99',
        //                 rate => '63',
        //                 previousRate => '51.21',
        //             ),
        //         ),
        //     }
        //
        $result = array();
        $items = $this->safe_value($response, 'items');
        $keys = is_array($items) ? array_keys($items) : array();
        for ($i = 0; $i < count ($keys); $i++) {
            $key = $keys[$i];
            $item = $items[$key];
            $market = $this->safe_value($item, 'market', array());
            $first = $this->safe_value($market, 'first', array());
            $second = $this->safe_value($market, 'second', array());
            $baseId = $this->safe_string($first, 'currency');
            $quoteId = $this->safe_string($second, 'currency');
            $id = $baseId . $quoteId;
            $base = $this->common_currency_code($baseId);
            $quote = $this->common_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => $this->safe_integer($first, 'scale'),
                'price' => $this->safe_integer($second, 'scale'),
            );
            // todo => check that the limits have ben interpreted correctly
            // todo => parse the fees page
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'precision' => $precision,
                'active' => null,
                'fee' => null,
                'limits' => array (
                    'amount' => array (
                        'min' => $this->safe_float($first, 'minOffer'),
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => $this->safe_float($second, 'minOffer'),
                        'max' => null,
                    ),
                ),
                'info' => $item,
            );
        }
        return $result;
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $markets = $symbol ? array ( $this->market_id($symbol) ) : array();
        $request = array (
            'markets' => $markets,
        );
        $response = $this->v1_01PrivateGetTradingHistoryTransactions (array_merge (array( 'query' => $this->json ($request) ), $params));
        //
        //     {
        //         status => 'Ok',
        //         totalRows => '67',
        //         $items => array (
        //             array (
        //                 id => 'b54659a0-51b5-42a0-80eb-2ac5357ccee2',
        //                 market => 'BTC-EUR',
        //                 time => '1541697096247',
        //                 amount => '0.00003',
        //                 rate => '4341.44',
        //                 initializedBy => 'Sell',
        //                 wasTaker => false,
        //                 userAction => 'Buy',
        //                 offerId => 'bd19804a-6f89-4a69-adb8-eb078900d006',
        //                 commissionValue => null
        //             ),
        //         )
        //     }
        //
        $items = $this->safe_value($response, 'items');
        $result = $this->parse_trades($items, null, $since, $limit);
        if ($symbol === null) {
            return $result;
        }
        return $this->filter_by_symbol($result, $symbol);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostInfo ($params);
        $balances = $this->safe_value($response, 'balances');
        if ($balances === null) {
            throw new ExchangeError($this->id . ' empty $balance $response ' . $this->json ($response));
        }
        $result = array( 'info' => $response );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count ($codes); $i++) {
            $code = $codes[$i];
            $currencyId = $this->currencyId ($code);
            $balance = $this->safe_value($balances, $currencyId);
            if ($balance !== null) {
                $account = $this->account ();
                $account['free'] = $this->safe_float($balance, 'available');
                $account['used'] = $this->safe_float($balance, 'locked');
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'id' => $this->market_id($symbol),
        );
        $orderbook = $this->publicGetIdOrderbook (array_merge ($request, $params));
        return $this->parse_order_book($orderbook);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $request = array (
            'id' => $this->market_id($symbol),
        );
        $ticker = $this->publicGetIdTicker (array_merge ($request, $params));
        $timestamp = $this->milliseconds ();
        $baseVolume = $this->safe_float($ticker, 'volume');
        $vwap = $this->safe_float($ticker, 'vwap');
        $quoteVolume = null;
        if ($baseVolume !== null && $vwap !== null) {
            $quoteVolume = $baseVolume * $vwap;
        }
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'max'),
            'low' => $this->safe_float($ticker, 'min'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $this->safe_float($ticker, 'average'),
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market) {
        if (is_array($trade) && array_key_exists('tid', $trade)) {
            return $this->parse_public_trade ($trade, $market);
        } else {
            return $this->parse_my_trade ($trade, $market);
        }
    }

    public function parse_my_trade ($trade, $market) {
        //
        //     {
        //         id => '5b6780e2-5bac-4ac7-88f4-b49b5957d33a',
        //         $market => 'BTC-EUR',
        //         time => '1520719374684',
        //         $amount => '0.3',
        //         rate => '7502',
        //         initializedBy => 'Sell',
        //         $wasTaker => true,
        //         $userAction => 'Sell',
        //         offerId => 'd093b0aa-b9c9-4a52-b3e2-673443a6188b',
        //         $commissionValue => null
        //     }
        //
        $timestamp = $this->safe_integer($trade, 'time');
        $userAction = $this->safe_string($trade, 'userAction');
        $side = ($userAction === 'Buy') ? 'buy' : 'sell';
        $wasTaker = $this->safe_value($trade, 'wasTaker');
        $takerOrMaker = $wasTaker ? 'taker' : 'maker';
        $price = $this->safe_float($trade, 'rate');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
            }
        }
        $commissionValue = $this->safe_float($trade, 'commissionValue');
        $fee = null;
        if ($commissionValue !== null) {
            // it always seems to be null so don't know what currency to use
            $fee = array (
                'currency' => null,
                'cost' => $commissionValue,
            );
        }
        $marketId = $this->safe_string($trade, 'market');
        $order = $this->safe_string($trade, 'offerId');
        // todo => check this logic
        $type = $order ? 'limit' : 'market';
        return array (
            'id' => $this->safe_string($trade, 'id'),
            'order' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $this->find_symbol(str_replace('-', '', $marketId)),
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'takerOrMaker' => $takerOrMaker,
            'fee' => $fee,
            'info' => $trade,
        );
    }

    public function parse_public_trade ($trade, $market = null) {
        //
        //     {
        //         "date":1459608665,
        //         "$price":0.02722571,
        //         "$type":"sell",
        //         "$amount":1.08112001,
        //         "tid":"0"
        //     }
        //
        $timestamp = $this->safe_integer($trade, 'date');
        if ($timestamp !== null) {
            $timestamp *= 1000;
        }
        $id = $this->safe_string($trade, 'tid');
        $type = null;
        $side = $this->safe_string($trade, 'type');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array (
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'order' => null,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'id' => $market['id'],
        );
        $response = $this->publicGetIdTrades (array_merge ($request, $params));
        //
        //     array (
        //         array (
        //             "date":1459608665,
        //             "price":0.02722571,
        //             "type":"sell",
        //             "amount":1.08112001,
        //             "tid":"0"
        //         ),
        //         array (
        //             "date":1459698930,
        //             "price":0.029,
        //             "type":"buy",
        //             "amount":0.444188,
        //             "tid":"1"
        //         ),
        //         {
        //             "date":1459726670,
        //             "price":0.029,
        //             "type":"buy",
        //             "amount":0.25459599,
        //             "tid":"2"
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type !== 'limit') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $market = $this->market ($symbol);
        $request = array (
            'type' => $side,
            'currency' => $market['baseId'],
            'amount' => $amount,
            'payment_currency' => $market['quoteId'],
            'rate' => $price,
        );
        return $this->privatePostTrade (array_merge ($request, $params));
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $request = array (
            'id' => $id,
        );
        return $this->privatePostCancel (array_merge ($request, $params));
    }

    public function is_fiat ($currency) {
        $fiatCurrencies = array (
            'USD' => true,
            'EUR' => true,
            'PLN' => true,
        );
        return $this->safe_value($fiatCurrencies, $currency, false);
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $method = null;
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
            'quantity' => $amount,
        );
        if ($this->is_fiat ($code)) {
            $method = 'privatePostWithdraw';
            // $request['account'] = $params['account']; // they demand an account number
            // $request['express'] = $params['express']; // whatever it means, they don't explain
            // $request['bic'] = '';
        } else {
            $method = 'privatePostTransfer';
            if ($tag !== null) {
                $address .= '?dt=' . (string) $tag;
            }
            $request['address'] = $address;
        }
        $response = $this->$method (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => null,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        if ($api === 'public') {
            $query = $this->omit ($params, $this->extract_params($path));
            $url .= '/' . $this->implode_params($path, $params) . '.json';
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        } else if ($api === 'v1_01Public') {
            $query = $this->omit ($params, $this->extract_params($path));
            $url .= '/' . $this->implode_params($path, $params);
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        } else if ($api === 'v1_01Private') {
            $this->check_required_credentials();
            $query = $this->omit ($params, $this->extract_params($path));
            $url .= '/' . $this->implode_params($path, $params);
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
            $nonce = $this->now ();
            $payload = $this->apiKey . $nonce;
            if ($body !== null) {
                $body = $this->json ($body);
            }
            $headers = array (
                'Request-Timestamp' => $nonce,
                'Operation-Id' => $this->uuid (),
                'API-Key' => $this->apiKey,
                'API-Hash' => $this->hmac ($this->encode ($payload), $this->encode ($this->secret), 'sha512'),
                'Content-Type' => 'application/json',
            );
        } else {
            $this->check_required_credentials();
            $body = $this->urlencode (array_merge (array (
                'method' => $path,
                'moment' => $this->nonce (),
            ), $params));
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
                'API-Key' => $this->apiKey,
                'API-Hash' => $this->hmac ($this->encode ($body), $this->encode ($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($httpCode, $reason, $url, $method, $headers, $body, $response) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        if (is_array($response) && array_key_exists('code', $response)) {
            //
            // bitbay returns the integer 'success' => 1 key from their private API
            // or an integer 'code' value from 0 to 510 and an error message
            //
            //      array( 'success' => 1, ... )
            //      array( 'code' => 502, 'message' => 'Invalid sign' )
            //      array( 'code' => 0, 'message' => 'offer funds not exceeding minimums' )
            //
            //      400 At least one parameter wasn't set
            //      401 Invalid order type
            //      402 No orders with specified currencies
            //      403 Invalid payment currency name
            //      404 Error. Wrong transaction type
            //      405 Order with this id doesn't exist
            //      406 No enough money or crypto
            //      408 Invalid currency name
            //      501 Invalid public key
            //      502 Invalid sign
            //      503 Invalid moment parameter. Request time doesn't match current server time
            //      504 Invalid $method
            //      505 Key has no permission for this action
            //      506 Account locked. Please contact with customer service
            //      509 The BIC/SWIFT is required for this currency
            //      510 Invalid market name
            //
            $code = $this->safe_string($response, 'code'); // always an integer
            $feedback = $this->id . ' ' . $body;
            $exceptions = $this->exceptions;
            if (is_array($this->exceptions) && array_key_exists($code, $this->exceptions)) {
                throw new $exceptions[$code]($feedback);
            } else {
                throw new ExchangeError($feedback);
            }
        }
    }
}
