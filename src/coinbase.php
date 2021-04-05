<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class coinbase extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinbase',
            'name' => 'Coinbase',
            'countries' => array( 'US' ),
            'rateLimit' => 400, // 10k calls per hour
            'version' => 'v2',
            'userAgent' => $this->userAgents['chrome'],
            'headers' => array(
                'CB-VERSION' => '2018-05-30',
            ),
            'has' => array(
                'CORS' => true,
                'cancelOrder' => false,
                'createDepositAddress' => true,
                'createOrder' => false,
                'deposit' => false,
                'fetchBalance' => true,
                'fetchClosedOrders' => false,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => false,
                'fetchMarkets' => true,
                'fetchMyTrades' => false,
                'fetchOHLCV' => false,
                'fetchOpenOrders' => false,
                'fetchOrder' => false,
                'fetchOrderBook' => false,
                'fetchL2OrderBook' => false,
                'fetchLedger' => true,
                'fetchOrders' => false,
                'fetchTicker' => true,
                'fetchTickers' => false,
                'fetchTime' => true,
                'fetchBidsAsks' => false,
                'fetchTrades' => false,
                'withdraw' => false,
                'fetchTransactions' => false,
                'fetchDeposits' => true,
                'fetchWithdrawals' => true,
                'fetchMySells' => true,
                'fetchMyBuys' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/40811661-b6eceae2-653a-11e8-829e-10bfadb078cf.jpg',
                'api' => 'https://api.coinbase.com',
                'www' => 'https://www.coinbase.com',
                'doc' => 'https://developers.coinbase.com/api/v2',
                'fees' => 'https://support.coinbase.com/customer/portal/articles/2109597-buy-sell-bank-transfer-fees',
                'referral' => 'https://www.coinbase.com/join/58cbe25a355148797479dbd2',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currencies',
                        'time',
                        'exchange-rates',
                        'users/{user_id}',
                        'prices/{symbol}/buy',
                        'prices/{symbol}/sell',
                        'prices/{symbol}/spot',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts',
                        'accounts/{account_id}',
                        'accounts/{account_id}/addresses',
                        'accounts/{account_id}/addresses/{address_id}',
                        'accounts/{account_id}/addresses/{address_id}/transactions',
                        'accounts/{account_id}/transactions',
                        'accounts/{account_id}/transactions/{transaction_id}',
                        'accounts/{account_id}/buys',
                        'accounts/{account_id}/buys/{buy_id}',
                        'accounts/{account_id}/sells',
                        'accounts/{account_id}/sells/{sell_id}',
                        'accounts/{account_id}/deposits',
                        'accounts/{account_id}/deposits/{deposit_id}',
                        'accounts/{account_id}/withdrawals',
                        'accounts/{account_id}/withdrawals/{withdrawal_id}',
                        'payment-methods',
                        'payment-methods/{payment_method_id}',
                        'user',
                        'user/auth',
                    ),
                    'post' => array(
                        'accounts',
                        'accounts/{account_id}/primary',
                        'accounts/{account_id}/addresses',
                        'accounts/{account_id}/transactions',
                        'accounts/{account_id}/transactions/{transaction_id}/complete',
                        'accounts/{account_id}/transactions/{transaction_id}/resend',
                        'accounts/{account_id}/buys',
                        'accounts/{account_id}/buys/{buy_id}/commit',
                        'accounts/{account_id}/sells',
                        'accounts/{account_id}/sells/{sell_id}/commit',
                        'accounts/{account_id}/deposists',
                        'accounts/{account_id}/deposists/{deposit_id}/commit',
                        'accounts/{account_id}/withdrawals',
                        'accounts/{account_id}/withdrawals/{withdrawal_id}/commit',
                    ),
                    'put' => array(
                        'accounts/{account_id}',
                        'user',
                    ),
                    'delete' => array(
                        'accounts/{id}',
                        'accounts/{account_id}/transactions/{transaction_id}',
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'two_factor_required' => '\\ccxt\\AuthenticationError', // 402 When sending money over 2fa limit
                    'param_required' => '\\ccxt\\ExchangeError', // 400 Missing parameter
                    'validation_error' => '\\ccxt\\ExchangeError', // 400 Unable to validate POST/PUT
                    'invalid_request' => '\\ccxt\\ExchangeError', // 400 Invalid request
                    'personal_details_required' => '\\ccxt\\AuthenticationError', // 400 User’s personal detail required to complete this request
                    'identity_verification_required' => '\\ccxt\\AuthenticationError', // 400 Identity verification is required to complete this request
                    'jumio_verification_required' => '\\ccxt\\AuthenticationError', // 400 Document verification is required to complete this request
                    'jumio_face_match_verification_required' => '\\ccxt\\AuthenticationError', // 400 Document verification including face match is required to complete this request
                    'unverified_email' => '\\ccxt\\AuthenticationError', // 400 User has not verified their email
                    'authentication_error' => '\\ccxt\\AuthenticationError', // 401 Invalid auth (generic)
                    'invalid_token' => '\\ccxt\\AuthenticationError', // 401 Invalid Oauth token
                    'revoked_token' => '\\ccxt\\AuthenticationError', // 401 Revoked Oauth token
                    'expired_token' => '\\ccxt\\AuthenticationError', // 401 Expired Oauth token
                    'invalid_scope' => '\\ccxt\\AuthenticationError', // 403 User hasn’t authenticated necessary scope
                    'not_found' => '\\ccxt\\ExchangeError', // 404 Resource not found
                    'rate_limit_exceeded' => '\\ccxt\\RateLimitExceeded', // 429 Rate limit exceeded
                    'internal_server_error' => '\\ccxt\\ExchangeError', // 500 Internal server error
                ),
                'broad' => array(
                    'request timestamp expired' => '\\ccxt\\InvalidNonce', // array("errors":[array("id":"authentication_error","message":"request timestamp expired")])
                ),
            ),
            'commonCurrencies' => array(
                'CGLD' => 'CELO',
            ),
            'options' => array(
                'fetchCurrencies' => array(
                    'expires' => 5000,
                ),
                'accounts' => array(
                    'wallet',
                    'fiat',
                    // 'vault',
                ),
            ),
        ));
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetTime ($params);
        //
        //     {
        //         "$data" => {
        //             "epoch" => 1589295679,
        //             "iso" => "2020-05-12T15:01:19Z"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->safe_timestamp($data, 'epoch');
    }

    public function fetch_accounts($params = array ()) {
        $this->load_markets();
        $request = array(
            'limit' => 100,
        );
        $response = $this->privateGetAccounts (array_merge($request, $params));
        //
        //     {
        //         "id" => "XLM",
        //         "name" => "XLM Wallet",
        //         "primary" => false,
        //         "type" => "wallet",
        //         "$currency" => array(
        //             "$code" => "XLM",
        //             "name" => "Stellar Lumens",
        //             "color" => "#000000",
        //             "sort_index" => 127,
        //             "exponent" => 7,
        //             "type" => "crypto",
        //             "address_regex" => "^G[A-Z2-7]{55}$",
        //             "asset_id" => "13b83335-5ede-595b-821e-5bcdfa80560f",
        //             "destination_tag_name" => "XLM Memo ID",
        //             "destination_tag_regex" => "^[ -~]array(1,28)$"
        //         ),
        //         "balance" => array(
        //             "amount" => "0.0000000",
        //             "$currency" => "XLM"
        //         ),
        //         "created_at" => null,
        //         "updated_at" => null,
        //         "resource" => "$account",
        //         "resource_path" => "/v2/accounts/XLM",
        //         "allow_deposits" => true,
        //         "allow_withdrawals" => true
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $account = $data[$i];
            $currency = $this->safe_value($account, 'currency', array());
            $currencyId = $this->safe_string($currency, 'code');
            $code = $this->safe_currency_code($currencyId);
            $result[] = array(
                'id' => $this->safe_string($account, 'id'),
                'type' => $this->safe_string($account, 'type'),
                'code' => $code,
                'info' => $account,
            );
        }
        return $result;
    }

    public function create_deposit_address($code, $params = array ()) {
        $accountId = $this->safe_string($params, 'account_id');
        $params = $this->omit($params, 'account_id');
        if ($accountId === null) {
            $this->load_accounts();
            for ($i = 0; $i < count($this->accounts); $i++) {
                $account = $this->accounts[$i];
                if ($account['code'] === $code && $account['type'] === 'wallet') {
                    $accountId = $account['id'];
                    break;
                }
            }
        }
        if ($accountId === null) {
            throw new ExchangeError($this->id . ' createDepositAddress could not find the $account with matching currency $code, specify an `account_id` extra param');
        }
        $request = array(
            'account_id' => $accountId,
        );
        $response = $this->privatePostAccountsAccountIdAddresses (array_merge($request, $params));
        //
        //     {
        //         "$data" => {
        //             "id" => "05b1ebbf-9438-5dd4-b297-2ddedc98d0e4",
        //             "$address" => "coinbasebase",
        //             "address_info" => array(
        //                 "$address" => "coinbasebase",
        //                 "destination_tag" => "287594668"
        //             ),
        //             "name" => null,
        //             "created_at" => "2019-07-01T14:39:29Z",
        //             "updated_at" => "2019-07-01T14:39:29Z",
        //             "network" => "eosio",
        //             "uri_scheme" => "eosio",
        //             "resource" => "$address",
        //             "resource_path" => "/v2/accounts/14cfc769-e852-52f3-b831-711c104d194c/addresses/05b1ebbf-9438-5dd4-b297-2ddedc98d0e4",
        //             "warnings" => array(
        //                 array(
        //                     "title" => "Only send EOS (EOS) to this $address",
        //                     "details" => "Sending any other cryptocurrency will result in permanent loss.",
        //                     "image_url" => "https://dynamic-assets.coinbase.com/deaca3d47b10ed4a91a872e9618706eec34081127762d88f2476ac8e99ada4b48525a9565cf2206d18c04053f278f693434af4d4629ca084a9d01b7a286a7e26/asset_icons/1f8489bb280fb0a0fd643c1161312ba49655040e9aaaced5f9ad3eeaf868eadc.png"
        //                 ),
        //                 {
        //                     "title" => "Both an $address and EOS memo are required to receive EOS",
        //                     "details" => "If you send funds without an EOS memo or with an incorrect EOS memo, your funds cannot be credited to your $account->",
        //                     "image_url" => "https://www.coinbase.com/assets/receive-warning-2f3269d83547a7748fb39d6e0c1c393aee26669bfea6b9f12718094a1abff155.png"
        //                 }
        //             ),
        //             "warning_title" => "Only send EOS (EOS) to this $address",
        //             "warning_details" => "Sending any other cryptocurrency will result in permanent loss.",
        //             "destination_tag" => "287594668",
        //             "deposit_uri" => "eosio:coinbasebase?dt=287594668",
        //             "callback_url" => null
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $tag = $this->safe_string($data, 'destination_tag');
        $address = $this->safe_string($data, 'address');
        return array(
            'currency' => $code,
            'tag' => $tag,
            'address' => $address,
            'info' => $response,
        );
    }

    public function fetch_my_sells($symbol = null, $since = null, $limit = null, $params = array ()) {
        // they don't have an endpoint for all historical trades
        $request = $this->prepare_account_request($limit, $params);
        $this->load_markets();
        $query = $this->omit($params, array( 'account_id', 'accountId' ));
        $sells = $this->privateGetAccountsAccountIdSells (array_merge($request, $query));
        return $this->parse_trades($sells['data'], null, $since, $limit);
    }

    public function fetch_my_buys($symbol = null, $since = null, $limit = null, $params = array ()) {
        // they don't have an endpoint for all historical trades
        $request = $this->prepare_account_request($limit, $params);
        $this->load_markets();
        $query = $this->omit($params, array( 'account_id', 'accountId' ));
        $buys = $this->privateGetAccountsAccountIdBuys (array_merge($request, $query));
        return $this->parse_trades($buys['data'], null, $since, $limit);
    }

    public function fetch_transactions_with_method($method, $code = null, $since = null, $limit = null, $params = array ()) {
        $request = $this->prepare_account_request_with_currency_code($code, $limit, $params);
        $this->load_markets();
        $query = $this->omit($params, array( 'account_id', 'accountId' ));
        $response = $this->$method (array_merge($request, $query));
        return $this->parse_transactions($response['data'], null, $since, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        // fiat only, for crypto transactions use fetchLedger
        return $this->fetch_transactions_with_method('privateGetAccountsAccountIdWithdrawals', $code, $since, $limit, $params);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        // fiat only, for crypto transactions use fetchLedger
        return $this->fetch_transactions_with_method('privateGetAccountsAccountIdDeposits', $code, $since, $limit, $params);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'created' => 'pending',
            'completed' => 'ok',
            'canceled' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $market = null) {
        //
        // fiat deposit
        //
        //     {
        //         "$id" => "f34c19f3-b730-5e3d-9f72",
        //         "$status" => "completed",
        //         "payment_method" => array(
        //             "$id" => "a022b31d-f9c7-5043-98f2",
        //             "resource" => "payment_method",
        //             "resource_path" => "/v2/payment-methods/a022b31d-f9c7-5043-98f2"
        //         ),
        //         "$transaction" => array(
        //             "$id" => "04ed4113-3732-5b0c-af86-b1d2146977d0",
        //             "resource" => "$transaction",
        //             "resource_path" => "/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/transactions/04ed4113-3732-5b0c-af86"
        //         ),
        //         "user_reference" => "2VTYTH",
        //         "created_at" => "2017-02-09T07:01:18Z",
        //         "updated_at" => "2017-02-09T07:01:26Z",
        //         "resource" => "deposit",
        //         "resource_path" => "/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/deposits/f34c19f3-b730-5e3d-9f72",
        //         "$committed" => true,
        //         "payout_at" => "2017-02-12T07:01:17Z",
        //         "instant" => false,
        //         "$fee" => array( "$amount" => "0.00", "$currency" => "EUR" ),
        //         "$amount" => array( "$amount" => "114.02", "$currency" => "EUR" ),
        //         "subtotal" => array( "$amount" => "114.02", "$currency" => "EUR" ),
        //         "hold_until" => null,
        //         "hold_days" => 0,
        //         "hold_business_days" => 0,
        //         "next_step" => null
        //     }
        //
        // fiat_withdrawal
        //
        //     {
        //         "$id" => "cfcc3b4a-eeb6-5e8c-8058",
        //         "$status" => "completed",
        //         "payment_method" => array(
        //             "$id" => "8b94cfa4-f7fd-5a12-a76a",
        //             "resource" => "payment_method",
        //             "resource_path" => "/v2/payment-methods/8b94cfa4-f7fd-5a12-a76a"
        //         ),
        //         "$transaction" => array(
        //             "$id" => "fcc2550b-5104-5f83-a444",
        //             "resource" => "$transaction",
        //             "resource_path" => "/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/transactions/fcc2550b-5104-5f83-a444"
        //         ),
        //         "user_reference" => "MEUGK",
        //         "created_at" => "2018-07-26T08:55:12Z",
        //         "updated_at" => "2018-07-26T08:58:18Z",
        //         "resource" => "withdrawal",
        //         "resource_path" => "/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/withdrawals/cfcc3b4a-eeb6-5e8c-8058",
        //         "$committed" => true,
        //         "payout_at" => "2018-07-31T08:55:12Z",
        //         "instant" => false,
        //         "$fee" => array( "$amount" => "0.15", "$currency" => "EUR" ),
        //         "$amount" => array( "$amount" => "13130.69", "$currency" => "EUR" ),
        //         "subtotal" => array( "$amount" => "13130.84", "$currency" => "EUR" ),
        //         "idem" => "e549dee5-63ed-4e79-8a96",
        //         "next_step" => null
        //     }
        //
        $subtotalObject = $this->safe_value($transaction, 'subtotal', array());
        $feeObject = $this->safe_value($transaction, 'fee', array());
        $id = $this->safe_string($transaction, 'id');
        $timestamp = $this->parse8601($this->safe_value($transaction, 'created_at'));
        $updated = $this->parse8601($this->safe_value($transaction, 'updated_at'));
        $type = $this->safe_string($transaction, 'resource');
        $amount = $this->safe_number($subtotalObject, 'amount');
        $currencyId = $this->safe_string($subtotalObject, 'currency');
        $currency = $this->safe_currency_code($currencyId);
        $feeCost = $this->safe_number($feeObject, 'amount');
        $feeCurrencyId = $this->safe_string($feeObject, 'currency');
        $feeCurrency = $this->safe_currency_code($feeCurrencyId);
        $fee = array(
            'cost' => $feeCost,
            'currency' => $feeCurrency,
        );
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        if ($status === null) {
            $committed = $this->safe_value($transaction, 'committed');
            $status = $committed ? 'ok' : 'pending';
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => null,
            'tag' => null,
            'type' => $type,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function parse_trade($trade, $market = null) {
        //
        //     {
        //         "$id" => "67e0eaec-07d7-54c4-a72c-2e92826897df",
        //         "status" => "completed",
        //         "payment_method" => array(
        //             "$id" => "83562370-3e5c-51db-87da-752af5ab9559",
        //             "resource" => "payment_method",
        //             "resource_path" => "/v2/payment-methods/83562370-3e5c-51db-87da-752af5ab9559"
        //         ),
        //         "transaction" => array(
        //             "$id" => "441b9494-b3f0-5b98-b9b0-4d82c21c252a",
        //             "resource" => "transaction",
        //             "resource_path" => "/v2/accounts/2bbf394c-193b-5b2a-9155-3b4732659ede/transactions/441b9494-b3f0-5b98-b9b0-4d82c21c252a"
        //         ),
        //         "$amount" => array( "$amount" => "1.00000000", "currency" => "BTC" ),
        //         "total" => array( "$amount" => "10.25", "currency" => "USD" ),
        //         "subtotal" => array( "$amount" => "10.10", "currency" => "USD" ),
        //         "created_at" => "2015-01-31T20:49:02Z",
        //         "updated_at" => "2015-02-11T16:54:02-08:00",
        //         "resource" => "buy",
        //         "resource_path" => "/v2/accounts/2bbf394c-193b-5b2a-9155-3b4732659ede/buys/67e0eaec-07d7-54c4-a72c-2e92826897df",
        //         "committed" => true,
        //         "instant" => false,
        //         "$fee" => array( "$amount" => "0.15", "currency" => "USD" ),
        //         "payout_at" => "2015-02-18T16:54:00-08:00"
        //     }
        //
        $symbol = null;
        $totalObject = $this->safe_value($trade, 'total', array());
        $amountObject = $this->safe_value($trade, 'amount', array());
        $subtotalObject = $this->safe_value($trade, 'subtotal', array());
        $feeObject = $this->safe_value($trade, 'fee', array());
        $id = $this->safe_string($trade, 'id');
        $timestamp = $this->parse8601($this->safe_value($trade, 'created_at'));
        if ($market === null) {
            $baseId = $this->safe_string($amountObject, 'currency');
            $quoteId = $this->safe_string($totalObject, 'currency');
            if (($baseId !== null) && ($quoteId !== null)) {
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        $orderId = null;
        $side = $this->safe_string($trade, 'resource');
        $type = null;
        $cost = $this->safe_number($subtotalObject, 'amount');
        $amount = $this->safe_number($amountObject, 'amount');
        $price = null;
        if ($cost !== null) {
            if (($amount !== null) && ($amount > 0)) {
                $price = $cost / $amount;
            }
        }
        $feeCost = $this->safe_number($feeObject, 'amount');
        $feeCurrencyId = $this->safe_string($feeObject, 'currency');
        $feeCurrency = $this->safe_currency_code($feeCurrencyId);
        $fee = array(
            'cost' => $feeCost,
            'currency' => $feeCurrency,
        );
        return array(
            'info' => $trade,
            'id' => $id,
            'order' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_markets($params = array ()) {
        $response = $this->fetch_currencies_from_cache($params);
        $currencies = $this->safe_value($response, 'currencies', array());
        $exchangeRates = $this->safe_value($response, 'exchangeRates', array());
        $data = $this->safe_value($currencies, 'data', array());
        $dataById = $this->index_by($data, 'id');
        $rates = $this->safe_value($this->safe_value($exchangeRates, 'data', array()), 'rates', array());
        $baseIds = is_array($rates) ? array_keys($rates) : array();
        $result = array();
        for ($i = 0; $i < count($baseIds); $i++) {
            $baseId = $baseIds[$i];
            $base = $this->safe_currency_code($baseId);
            $type = (is_array($dataById) && array_key_exists($baseId, $dataById)) ? 'fiat' : 'crypto';
            // https://github.com/ccxt/ccxt/issues/6066
            if ($type === 'crypto') {
                for ($j = 0; $j < count($data); $j++) {
                    $quoteCurrency = $data[$j];
                    $quoteId = $this->safe_string($quoteCurrency, 'id');
                    $quote = $this->safe_currency_code($quoteId);
                    $symbol = $base . '/' . $quote;
                    $id = $baseId . '-' . $quoteId;
                    $result[] = array(
                        'id' => $id,
                        'symbol' => $symbol,
                        'base' => $base,
                        'quote' => $quote,
                        'baseId' => $baseId,
                        'quoteId' => $quoteId,
                        'active' => null,
                        'info' => $quoteCurrency,
                        'precision' => array(
                            'amount' => null,
                            'price' => null,
                        ),
                        'limits' => array(
                            'amount' => array(
                                'min' => null,
                                'max' => null,
                            ),
                            'price' => array(
                                'min' => null,
                                'max' => null,
                            ),
                            'cost' => array(
                                'min' => $this->safe_number($quoteCurrency, 'min_size'),
                                'max' => null,
                            ),
                        ),
                    );
                }
            }
        }
        return $result;
    }

    public function fetch_currencies_from_cache($params = array ()) {
        $options = $this->safe_value($this->options, 'fetchCurrencies', array());
        $timestamp = $this->safe_integer($options, 'timestamp');
        $expires = $this->safe_integer($options, 'expires', 1000);
        $now = $this->milliseconds();
        if (($timestamp === null) || (($now - $timestamp) > $expires)) {
            $currencies = $this->publicGetCurrencies ($params);
            $exchangeRates = $this->publicGetExchangeRates ($params);
            $this->options['fetchCurrencies'] = array_merge($options, array(
                'currencies' => $currencies,
                'exchangeRates' => $exchangeRates,
                'timestamp' => $now,
            ));
        }
        return $this->safe_value($this->options, 'fetchCurrencies', array());
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->fetch_currencies_from_cache($params);
        $currencies = $this->safe_value($response, 'currencies', array());
        //
        //     {
        //         "$data":array(
        //             array("$id":"AED","$name":"United Arab Emirates Dirham","min_size":"0.01000000"),
        //             array("$id":"AFN","$name":"Afghan Afghani","min_size":"0.01000000"),
        //             array("$id":"ALL","$name":"Albanian Lek","min_size":"0.01000000"),
        //             array("$id":"AMD","$name":"Armenian Dram","min_size":"0.01000000"),
        //             array("$id":"ANG","$name":"Netherlands Antillean Gulden","min_size":"0.01000000"),
        //             // ...
        //         ),
        //     }
        //
        $exchangeRates = $this->safe_value($response, 'exchangeRates', array());
        //
        //     {
        //         "$data":{
        //             "$currency":"USD",
        //             "$rates":array(
        //                 "AED":"3.67",
        //                 "AFN":"78.21",
        //                 "ALL":"110.42",
        //                 "AMD":"474.18",
        //                 "ANG":"1.75",
        //                 // ...
        //             ),
        //         }
        //     }
        //
        $data = $this->safe_value($currencies, 'data', array());
        $dataById = $this->index_by($data, 'id');
        $rates = $this->safe_value($this->safe_value($exchangeRates, 'data', array()), 'rates', array());
        $keys = is_array($rates) ? array_keys($rates) : array();
        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            $type = (is_array($dataById) && array_key_exists($key, $dataById)) ? 'fiat' : 'crypto';
            $currency = $this->safe_value($dataById, $key, array());
            $id = $this->safe_string($currency, 'id', $key);
            $name = $this->safe_string($currency, 'name');
            $code = $this->safe_currency_code($id);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency, // the original payload
                'type' => $type,
                'name' => $name,
                'active' => true,
                'fee' => null,
                'precision' => null,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_number($currency, 'min_size'),
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
                    'withdraw' => array(
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
        $timestamp = $this->seconds();
        $market = $this->market($symbol);
        $request = array_merge(array(
            'symbol' => $market['id'],
        ), $params);
        $buy = $this->publicGetPricesSymbolBuy ($request);
        $sell = $this->publicGetPricesSymbolSell ($request);
        $spot = $this->publicGetPricesSymbolSpot ($request);
        $ask = $this->safe_number($buy['data'], 'amount');
        $bid = $this->safe_number($sell['data'], 'amount');
        $last = $this->safe_number($spot['data'], 'amount');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'bid' => $bid,
            'ask' => $ask,
            'last' => $last,
            'high' => null,
            'low' => null,
            'bidVolume' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => null,
            'info' => array(
                'buy' => $buy,
                'sell' => $sell,
                'spot' => $spot,
            ),
        );
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $request = array(
            'limit' => 100,
        );
        $response = $this->privateGetAccounts (array_merge($request, $params));
        $balances = $this->safe_value($response, 'data');
        $accounts = $this->safe_value($params, 'type', $this->options['accounts']);
        $result = array( 'info' => $response );
        for ($b = 0; $b < count($balances); $b++) {
            $balance = $balances[$b];
            if ($this->in_array($balance['type'], $accounts)) {
                $currencyId = $this->safe_string($balance['balance'], 'currency');
                $code = $this->safe_currency_code($currencyId);
                $total = $this->safe_number($balance['balance'], 'amount');
                $free = $total;
                $used = null;
                if (is_array($result) && array_key_exists($code, $result)) {
                    $result[$code]['free'] = $this->sum($result[$code]['free'], $total);
                    $result[$code]['total'] = $this->sum($result[$code]['total'], $total);
                } else {
                    $account = array(
                        'free' => $free,
                        'used' => $used,
                        'total' => $total,
                    );
                    $result[$code] = $account;
                }
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $request = $this->prepare_account_request_with_currency_code($code, $limit, $params);
        $query = $this->omit($params, ['account_id', 'accountId']);
        // for pagination use parameter 'starting_after'
        // the value for the next page can be obtained from the result of the previous call in the 'pagination' field
        // eg => instance.last_json_response.pagination.next_starting_after
        $response = $this->privateGetAccountsAccountIdTransactions (array_merge($request, $query));
        return $this->parse_ledger($response['data'], $currency, $since, $limit);
    }

    public function parse_ledger_entry_status($status) {
        $types = array(
            'completed' => 'ok',
        );
        return $this->safe_string($types, $status, $status);
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            'buy' => 'trade',
            'sell' => 'trade',
            'fiat_deposit' => 'transaction',
            'fiat_withdrawal' => 'transaction',
            'exchange_deposit' => 'transaction', // fiat withdrawal (from coinbase to coinbasepro)
            'exchange_withdrawal' => 'transaction', // fiat deposit (to coinbase from coinbasepro)
            'send' => 'transaction', // crypto deposit OR withdrawal
            'pro_deposit' => 'transaction', // crypto withdrawal (from coinbase to coinbasepro)
            'pro_withdrawal' => 'transaction', // crypto deposit (to coinbase from coinbasepro)
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        // crypto deposit transaction
        //
        //     {
        //         $id => '34e4816b-4c8c-5323-a01c-35a9fa26e490',
        //         $type => 'send',
        //         $status => 'completed',
        //         $amount => array( $amount => '28.31976528', $currency => 'BCH' ),
        //         native_amount => array( $amount => '2799.65', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2019-02-28T12:35:20Z',
        //         updated_at => '2019-02-28T12:43:24Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/c01d7364-edd7-5f3a-bd1d-de53d4cbb25e/transactions/34e4816b-4c8c-5323-a01c-35a9fa26e490',
        //         instant_exchange => false,
        //         network => array(
        //             $status => 'confirmed',
        //             hash => '56222d865dae83774fccb2efbd9829cf08c75c94ce135bfe4276f3fb46d49701',
        //             transaction_url => 'https://bch.btc.com/56222d865dae83774fccb2efbd9829cf08c75c94ce135bfe4276f3fb46d49701'
        //         ),
        //         from => array( resource => 'bitcoin_cash_network', $currency => 'BCH' ),
        //         details => array( title => 'Received Bitcoin Cash', subtitle => 'From Bitcoin Cash address' )
        //     }
        //
        // crypto withdrawal transaction
        //
        //     {
        //         $id => '459aad99-2c41-5698-ac71-b6b81a05196c',
        //         $type => 'send',
        //         $status => 'completed',
        //         $amount => array( $amount => '-0.36775642', $currency => 'BTC' ),
        //         native_amount => array( $amount => '-1111.65', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2019-03-20T08:37:07Z',
        //         updated_at => '2019-03-20T08:49:33Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/c6afbd34-4bd0-501e-8616-4862c193cd84/transactions/459aad99-2c41-5698-ac71-b6b81a05196c',
        //         instant_exchange => false,
        //         network => array(
        //             $status => 'confirmed',
        //             hash => '2732bbcf35c69217c47b36dce64933d103895277fe25738ffb9284092701e05b',
        //             transaction_url => 'https://blockchain.info/tx/2732bbcf35c69217c47b36dce64933d103895277fe25738ffb9284092701e05b',
        //             transaction_fee => array( $amount => '0.00000000', $currency => 'BTC' ),
        //             transaction_amount => array( $amount => '0.36775642', $currency => 'BTC' ),
        //             confirmations => 15682
        //         ),
        //         to => array(
        //             resource => 'bitcoin_address',
        //             $address => '1AHnhqbvbYx3rnZx8uC7NbFZaTe4tafFHX',
        //             $currency => 'BTC',
        //             address_info => array( $address => '1AHnhqbvbYx3rnZx8uC7NbFZaTe4tafFHX' )
        //         ),
        //         idem => 'da0a2f14-a2af-4c5a-a37e-d4484caf582bsend',
        //         application => array(
        //             $id => '5756ab6e-836b-553b-8950-5e389451225d',
        //             resource => 'application',
        //             resource_path => '/v2/applications/5756ab6e-836b-553b-8950-5e389451225d'
        //         ),
        //         details => array( title => 'Sent Bitcoin', subtitle => 'To Bitcoin address' )
        //     }
        //
        // withdrawal transaction from coinbase to coinbasepro
        //
        //     {
        //         $id => '5b1b9fb8-5007-5393-b923-02903b973fdc',
        //         $type => 'pro_deposit',
        //         $status => 'completed',
        //         $amount => array( $amount => '-0.00001111', $currency => 'BCH' ),
        //         native_amount => array( $amount => '0.00', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2019-02-28T13:31:58Z',
        //         updated_at => '2019-02-28T13:31:58Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/c01d7364-edd7-5f3a-bd1d-de53d4cbb25e/transactions/5b1b9fb8-5007-5393-b923-02903b973fdc',
        //         instant_exchange => false,
        //         application => array(
        //             $id => '5756ab6e-836b-553b-8950-5e389451225d',
        //             resource => 'application',
        //             resource_path => '/v2/applications/5756ab6e-836b-553b-8950-5e389451225d'
        //         ),
        //         details => array( title => 'Transferred Bitcoin Cash', subtitle => 'To Coinbase Pro' )
        //     }
        //
        // withdrawal transaction from coinbase to gdax
        //
        //     {
        //         $id => 'badb7313-a9d3-5c07-abd0-00f8b44199b1',
        //         $type => 'exchange_deposit',
        //         $status => 'completed',
        //         $amount => array( $amount => '-0.43704149', $currency => 'BCH' ),
        //         native_amount => array( $amount => '-51.90', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2019-03-19T10:30:40Z',
        //         updated_at => '2019-03-19T10:30:40Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/c01d7364-edd7-5f3a-bd1d-de53d4cbb25e/transactions/badb7313-a9d3-5c07-abd0-00f8b44199b1',
        //         instant_exchange => false,
        //         details => array( title => 'Transferred Bitcoin Cash', subtitle => 'To GDAX' )
        //     }
        //
        // deposit transaction from gdax to coinbase
        //
        //     {
        //         $id => '9c4b642c-8688-58bf-8962-13cef64097de',
        //         $type => 'exchange_withdrawal',
        //         $status => 'completed',
        //         $amount => array( $amount => '0.57729420', $currency => 'BTC' ),
        //         native_amount => array( $amount => '4418.72', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2018-02-17T11:33:33Z',
        //         updated_at => '2018-02-17T11:33:33Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/c6afbd34-4bd0-501e-8616-4862c193cd84/transactions/9c4b642c-8688-58bf-8962-13cef64097de',
        //         instant_exchange => false,
        //         details => array( title => 'Transferred Bitcoin', subtitle => 'From GDAX' )
        //     }
        //
        // deposit transaction from coinbasepro to coinbase
        //
        //     {
        //         $id => '8d6dd0b9-3416-568a-889d-8f112fae9e81',
        //         $type => 'pro_withdrawal',
        //         $status => 'completed',
        //         $amount => array( $amount => '0.40555386', $currency => 'BTC' ),
        //         native_amount => array( $amount => '1140.27', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2019-03-04T19:41:58Z',
        //         updated_at => '2019-03-04T19:41:58Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/c6afbd34-4bd0-501e-8616-4862c193cd84/transactions/8d6dd0b9-3416-568a-889d-8f112fae9e81',
        //         instant_exchange => false,
        //         application => array(
        //             $id => '5756ab6e-836b-553b-8950-5e389451225d',
        //             resource => 'application',
        //             resource_path => '/v2/applications/5756ab6e-836b-553b-8950-5e389451225d'
        //         ),
        //         details => array( title => 'Transferred Bitcoin', subtitle => 'From Coinbase Pro' )
        //     }
        //
        // sell trade
        //
        //     {
        //         $id => 'a9409207-df64-585b-97ab-a50780d2149e',
        //         $type => 'sell',
        //         $status => 'completed',
        //         $amount => array( $amount => '-9.09922880', $currency => 'BTC' ),
        //         native_amount => array( $amount => '-7285.73', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2017-03-27T15:38:34Z',
        //         updated_at => '2017-03-27T15:38:34Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/c6afbd34-4bd0-501e-8616-4862c193cd84/transactions/a9409207-df64-585b-97ab-a50780d2149e',
        //         instant_exchange => false,
        //         sell => array(
        //             $id => 'e3550b4d-8ae6-5de3-95fe-1fb01ba83051',
        //             resource => 'sell',
        //             resource_path => '/v2/accounts/c6afbd34-4bd0-501e-8616-4862c193cd84/sells/e3550b4d-8ae6-5de3-95fe-1fb01ba83051'
        //         ),
        //         details => {
        //             title => 'Sold Bitcoin',
        //             subtitle => 'Using EUR Wallet',
        //             payment_method_name => 'EUR Wallet'
        //         }
        //     }
        //
        // buy trade
        //
        //     {
        //         $id => '63eeed67-9396-5912-86e9-73c4f10fe147',
        //         $type => 'buy',
        //         $status => 'completed',
        //         $amount => array( $amount => '2.39605772', $currency => 'ETH' ),
        //         native_amount => array( $amount => '98.31', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2017-03-27T09:07:56Z',
        //         updated_at => '2017-03-27T09:07:57Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/8902f85d-4a69-5d74-82fe-8e390201bda7/transactions/63eeed67-9396-5912-86e9-73c4f10fe147',
        //         instant_exchange => false,
        //         buy => array(
        //             $id => '20b25b36-76c6-5353-aa57-b06a29a39d82',
        //             resource => 'buy',
        //             resource_path => '/v2/accounts/8902f85d-4a69-5d74-82fe-8e390201bda7/buys/20b25b36-76c6-5353-aa57-b06a29a39d82'
        //         ),
        //         details => {
        //             title => 'Bought Ethereum',
        //             subtitle => 'Using EUR Wallet',
        //             payment_method_name => 'EUR Wallet'
        //         }
        //     }
        //
        // fiat deposit transaction
        //
        //     {
        //         $id => '04ed4113-3732-5b0c-af86-b1d2146977d0',
        //         $type => 'fiat_deposit',
        //         $status => 'completed',
        //         $amount => array( $amount => '114.02', $currency => 'EUR' ),
        //         native_amount => array( $amount => '97.23', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2017-02-09T07:01:21Z',
        //         updated_at => '2017-02-09T07:01:22Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/transactions/04ed4113-3732-5b0c-af86-b1d2146977d0',
        //         instant_exchange => false,
        //         fiat_deposit => array(
        //             $id => 'f34c19f3-b730-5e3d-9f72-96520448677a',
        //             resource => 'fiat_deposit',
        //             resource_path => '/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/deposits/f34c19f3-b730-5e3d-9f72-96520448677a'
        //         ),
        //         details => {
        //             title => 'Deposited funds',
        //             subtitle => 'From SEPA Transfer (GB47 BARC 20..., reference CBADVI)',
        //             payment_method_name => 'SEPA Transfer (GB47 BARC 20..., reference CBADVI)'
        //         }
        //     }
        //
        // fiat withdrawal transaction
        //
        //     {
        //         $id => '957d98e2-f80e-5e2f-a28e-02945aa93079',
        //         $type => 'fiat_withdrawal',
        //         $status => 'completed',
        //         $amount => array( $amount => '-11000.00', $currency => 'EUR' ),
        //         native_amount => array( $amount => '-9698.22', $currency => 'GBP' ),
        //         description => null,
        //         created_at => '2017-12-06T13:19:19Z',
        //         updated_at => '2017-12-06T13:19:19Z',
        //         resource => 'transaction',
        //         resource_path => '/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/transactions/957d98e2-f80e-5e2f-a28e-02945aa93079',
        //         instant_exchange => false,
        //         fiat_withdrawal => array(
        //             $id => 'f4bf1fd9-ab3b-5de7-906d-ed3e23f7a4e7',
        //             resource => 'fiat_withdrawal',
        //             resource_path => '/v2/accounts/91cd2d36-3a91-55b6-a5d4-0124cf105483/withdrawals/f4bf1fd9-ab3b-5de7-906d-ed3e23f7a4e7'
        //         ),
        //         details => {
        //             title => 'Withdrew funds',
        //             subtitle => 'To HSBC BANK PLC (GB74 MIDL...)',
        //             payment_method_name => 'HSBC BANK PLC (GB74 MIDL...)'
        //         }
        //     }
        //
        $amountInfo = $this->safe_value($item, 'amount', array());
        $amount = $this->safe_number($amountInfo, 'amount');
        $direction = null;
        if ($amount < 0) {
            $direction = 'out';
            $amount = -$amount;
        } else {
            $direction = 'in';
        }
        $currencyId = $this->safe_string($amountInfo, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        //
        // the $address and $txid do not belong to the unified ledger structure
        //
        //     $address = null;
        //     if ($item['to']) {
        //         $address = $this->safe_string($item['to'], 'address');
        //     }
        //     $txid = null;
        //
        $fee = null;
        $networkInfo = $this->safe_value($item, 'network', array());
        // $txid = network['hash']; // $txid does not belong to the unified ledger structure
        $feeInfo = $this->safe_value($networkInfo, 'transaction_fee');
        if ($feeInfo !== null) {
            $feeCurrencyId = $this->safe_string($feeInfo, 'currency');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId, $currency);
            $feeAmount = $this->safe_number($feeInfo, 'amount');
            $fee = array(
                'cost' => $feeAmount,
                'currency' => $feeCurrencyCode,
            );
        }
        $timestamp = $this->parse8601($this->safe_value($item, 'created_at'));
        $id = $this->safe_string($item, 'id');
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'type'));
        $status = $this->parse_ledger_entry_status($this->safe_string($item, 'status'));
        $path = $this->safe_string($item, 'resource_path');
        $accountId = null;
        if ($path !== null) {
            $parts = explode('/', $path);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts > 3) {
                $accountId = $parts[3];
            }
        }
        return array(
            'info' => $item,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'direction' => $direction,
            'account' => $accountId,
            'referenceId' => null,
            'referenceAccount' => null,
            'type' => $type,
            'currency' => $code,
            'amount' => $amount,
            'before' => null,
            'after' => null,
            'status' => $status,
            'fee' => $fee,
        );
    }

    public function find_account_id($code) {
        $this->load_markets();
        $this->load_accounts();
        for ($i = 0; $i < count($this->accounts); $i++) {
            $account = $this->accounts[$i];
            if ($account['code'] === $code) {
                return $account['id'];
            }
        }
        return null;
    }

    public function prepare_account_request($limit = null, $params = array ()) {
        $accountId = $this->safe_string_2($params, 'account_id', 'accountId');
        if ($accountId === null) {
            throw new ArgumentsRequired($this->id . ' method requires an account_id (or $accountId) parameter');
        }
        $request = array(
            'account_id' => $accountId,
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        return $request;
    }

    public function prepare_account_request_with_currency_code($code = null, $limit = null, $params = array ()) {
        $accountId = $this->safe_string_2($params, 'account_id', 'accountId');
        if ($accountId === null) {
            if ($code === null) {
                throw new ArgumentsRequired($this->id . ' method requires an account_id (or $accountId) parameter OR a currency $code argument');
            }
            $accountId = $this->find_account_id($code);
            if ($accountId === null) {
                throw new ExchangeError($this->id . ' could not find account id for ' . $code);
            }
        }
        $request = array(
            'account_id' => $accountId,
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        return $request;
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $fullPath = '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($method === 'GET') {
            if ($query) {
                $fullPath .= '?' . $this->urlencode($query);
            }
        }
        $url = $this->urls['api'] . $fullPath;
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $payload = '';
            if ($method !== 'GET') {
                if ($query) {
                    $body = $this->json($query);
                    $payload = $body;
                }
            }
            $auth = $nonce . $method . $fullPath . $payload;
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret));
            $headers = array(
                'CB-ACCESS-KEY' => $this->apiKey,
                'CB-ACCESS-SIGN' => $signature,
                'CB-ACCESS-TIMESTAMP' => $nonce,
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        $feedback = $this->id . ' ' . $body;
        //
        //    array("error" => "invalid_request", "error_description" => "The request is missing a required parameter, includes an unsupported parameter value, or is otherwise malformed.")
        //
        // or
        //
        //    {
        //      "$errors" => array(
        //        {
        //          "id" => "not_found",
        //          "message" => "Not found"
        //        }
        //      )
        //    }
        //
        $errorCode = $this->safe_string($response, 'error');
        if ($errorCode !== null) {
            $errorMessage = $this->safe_string($response, 'error_description');
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $errorMessage, $feedback);
            throw new ExchangeError($feedback);
        }
        $errors = $this->safe_value($response, 'errors');
        if ($errors !== null) {
            if (gettype($errors) === 'array' && count(array_filter(array_keys($errors), 'is_string')) == 0) {
                $numErrors = is_array($errors) ? count($errors) : 0;
                if ($numErrors > 0) {
                    $errorCode = $this->safe_string($errors[0], 'id');
                    $errorMessage = $this->safe_string($errors[0], 'message');
                    if ($errorCode !== null) {
                        $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $feedback);
                        $this->throw_broadly_matched_exception($this->exceptions['broad'], $errorMessage, $feedback);
                        throw new ExchangeError($feedback);
                    }
                }
            }
        }
        $data = $this->safe_value($response, 'data');
        if ($data === null) {
            throw new ExchangeError($this->id . ' failed due to a malformed $response ' . $this->json($response));
        }
    }
}
