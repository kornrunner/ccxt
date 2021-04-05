<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class therock extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'therock',
            'name' => 'TheRockTrading',
            'countries' => array( 'MT' ),
            'rateLimit' => 1000,
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchDeposits' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTransactions' => 'emulated',
                'fetchWithdrawals' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766869-75057fa2-5ee9-11e7-9a6f-13e641fa4707.jpg',
                'api' => 'https://api.therocktrading.com',
                'www' => 'https://therocktrading.com',
                'doc' => array(
                    'https://api.therocktrading.com/doc/v1/index.html',
                    'https://api.therocktrading.com/doc/',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'funds',
                        'funds/{id}/orderbook',
                        'funds/{id}/ticker',
                        'funds/{id}/trades',
                        'funds/tickers',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'balances',
                        'balances/{id}',
                        'discounts',
                        'discounts/{id}',
                        'funds',
                        'funds/{id}',
                        'funds/{id}/trades',
                        'funds/{fund_id}/orders',
                        'funds/{fund_id}/orders/{id}',
                        'funds/{fund_id}/position_balances',
                        'funds/{fund_id}/positions',
                        'funds/{fund_id}/positions/{id}',
                        'transactions',
                        'transactions/{id}',
                        'withdraw_limits/{id}',
                        'withdraw_limits',
                    ),
                    'post' => array(
                        'atms/withdraw',
                        'funds/{fund_id}/orders',
                    ),
                    'delete' => array(
                        'funds/{fund_id}/orders/{id}',
                        'funds/{fund_id}/orders/remove_all',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.2 / 100,
                    'taker' => 0.2 / 100,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(
                        'BTC' => 0.0005,
                        'BCH' => 0.0005,
                        'PPC' => 0.02,
                        'ETH' => 0.001,
                        'ZEC' => 0.001,
                        'LTC' => 0.002,
                        'EUR' => 2.5,  // worst-case scenario => https://therocktrading.com/en/pages/fees
                    ),
                    'deposit' => array(
                        'BTC' => 0,
                        'BCH' => 0,
                        'PPC' => 0,
                        'ETH' => 0,
                        'ZEC' => 0,
                        'LTC' => 0,
                        'EUR' => 0,
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'Request already running' => '\\ccxt\\BadRequest',
                    'cannot specify multiple address types' => '\\ccxt\\BadRequest',
                    'Currency is not included in the list' => '\\ccxt\\BadRequest',
                    'Record not found' => '\\ccxt\\OrderNotFound',
                ),
                'broad' => array(
                    'before must be greater than after param' => '\\ccxt\\BadRequest',
                    'must be shorter than 60 days' => '\\ccxt\\BadRequest',
                    'must be a multiple of (period param) in minutes' => '\\ccxt\\BadRequest',
                    'Address allocation limit reached for currency' => '\\ccxt\\InvalidAddress',
                    'is not a valid value for param currency' => '\\ccxt\\BadRequest',
                    ' is invalid' => '\\ccxt\\InvalidAddress',
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetFunds ($params);
        //
        //     { funds => array( array(                      $id =>   "BTCEUR",
        //                              description =>   "Trade Bitcoin with Euro",
        //                                     type =>   "currency",
        //                            base_currency =>   "EUR",
        //                           trade_currency =>   "BTC",
        //                                  $buy_fee =>    0.2,
        //                                 $sell_fee =>    0.2,
        //                      minimum_price_offer =>    0.01,
        //                   minimum_quantity_offer =>    0.0005,
        //                   base_currency_decimals =>    2,
        //                  trade_currency_decimals =>    4,
        //                                leverages => array()                           ),
        //                {                      $id =>   "LTCEUR",
        //                              description =>   "Trade Litecoin with Euro",
        //                                     type =>   "currency",
        //                            base_currency =>   "EUR",
        //                           trade_currency =>   "LTC",
        //                                  $buy_fee =>    0.2,
        //                                 $sell_fee =>    0.2,
        //                      minimum_price_offer =>    0.01,
        //                   minimum_quantity_offer =>    0.01,
        //                   base_currency_decimals =>    2,
        //                  trade_currency_decimals =>    2,
        //                                leverages => array()                            } ) }
        //
        $markets = $this->safe_value($response, 'funds');
        $result = array();
        if ($markets === null) {
            throw new ExchangeError($this->id . ' fetchMarkets got an unexpected response');
        } else {
            for ($i = 0; $i < count($markets); $i++) {
                $market = $markets[$i];
                $id = $this->safe_string($market, 'id');
                $baseId = $this->safe_string($market, 'trade_currency');
                $quoteId = $this->safe_string($market, 'base_currency');
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
                $buy_fee = $this->safe_number($market, 'buy_fee');
                $sell_fee = $this->safe_number($market, 'sell_fee');
                $taker = max ($buy_fee, $sell_fee);
                $taker = $taker / 100;
                $maker = $taker;
                $result[] = array(
                    'id' => $id,
                    'symbol' => $symbol,
                    'base' => $base,
                    'quote' => $quote,
                    'baseId' => $baseId,
                    'quoteId' => $quoteId,
                    'info' => $market,
                    'active' => true,
                    'maker' => $maker,
                    'taker' => $taker,
                    'precision' => array(
                        'amount' => $this->safe_integer($market, 'trade_currency_decimals'),
                        'price' => $this->safe_integer($market, 'base_currency_decimals'),
                    ),
                    'limits' => array(
                        'amount' => array(
                            'min' => $this->safe_number($market, 'minimum_quantity_offer'),
                            'max' => null,
                        ),
                        'price' => array(
                            'min' => $this->safe_number($market, 'minimum_price_offer'),
                            'max' => null,
                        ),
                        'cost' => array(
                            'min' => null,
                            'max' => null,
                        ),
                    ),
                );
            }
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetBalances ($params);
        $balances = $this->safe_value($response, 'balances', array());
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'trading_balance');
            $account['total'] = $this->safe_number($balance, 'balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $this->market_id($symbol),
        );
        $orderbook = $this->publicGetFundsIdOrderbook (array_merge($request, $params));
        $timestamp = $this->parse8601($this->safe_string($orderbook, 'date'));
        return $this->parse_order_book($orderbook, $timestamp, 'bids', 'asks', 'price', 'amount');
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->parse8601($ticker['date']);
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
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
            'open' => $this->safe_number($ticker, 'open'),
            'close' => $last,
            'last' => $last,
            'previousClose' => $this->safe_number($ticker, 'close'), // previous day close, if any
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'volume_traded'),
            'quoteVolume' => $this->safe_number($ticker, 'volume'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetFundsTickers ($params);
        $tickers = $this->index_by($response['tickers'], 'fund_id');
        $ids = is_array($tickers) ? array_keys($tickers) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $this->safe_market($id);
            $symbol = $market['symbol'];
            $ticker = $tickers[$id];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $ticker = $this->publicGetFundsIdTicker (array_merge(array(
            'id' => $market['id'],
        ), $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades, fetchOrder trades
        //
        //     {      $id =>  4493548,
        //       fund_id => "ETHBTC",
        //        $amount =>  0.203,
        //         $price =>  0.02783576,
        //          $side => "buy",
        //          dark =>  false,
        //          date => "2018-11-30T08:19:18.236Z" }
        //
        // fetchMyTrades
        //
        //     {           $id =>    237338,
        //            fund_id =>   "BTCEUR",
        //             $amount =>    0.348,
        //              $price =>    348,
        //               $side =>   "sell",
        //               dark =>    false,
        //           order_id =>    14920648,
        //               date =>   "2015-06-03T00:49:49.000Z",
        //       $transactions => array( array(       $id =>  2770768,
        //                             date => "2015-06-03T00:49:49.000Z",
        //                             type => "sold_currency_to_fund",
        //                            $price =>  121.1,
        //                         currency => "EUR"                       ),
        //                       array(       $id =>  2770769,
        //                             date => "2015-06-03T00:49:49.000Z",
        //                             type => "released_currency_to_fund",
        //                            $price =>  0.348,
        //                         currency => "BTC"                        ),
        //                       {       $id =>  2770772,
        //                             date => "2015-06-03T00:49:49.000Z",
        //                             type => "paid_commission",
        //                            $price =>  0.06,
        //                         currency => "EUR",
        //                         trade_id =>  440492                     }   ) }
        //
        $marketId = $this->safe_string($trade, 'fund_id');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->parse8601($this->safe_string($trade, 'date'));
        $id = $this->safe_string($trade, 'id');
        $orderId = $this->safe_string($trade, 'order_id');
        $side = $this->safe_string($trade, 'side');
        $priceString = $this->safe_string($trade, 'price');
        $amountString = $this->safe_string($trade, 'amount');
        $price = $this->parse_number($priceString);
        $amount = $this->parse_number($amountString);
        $cost = $this->parse_number(Precise::string_mul($priceString, $amountString));
        $fee = null;
        $feeCost = null;
        $transactions = $this->safe_value($trade, 'transactions', array());
        $transactionsByType = $this->group_by($transactions, 'type');
        $feeTransactions = $this->safe_value($transactionsByType, 'paid_commission', array());
        for ($i = 0; $i < count($feeTransactions); $i++) {
            if ($feeCost === null) {
                $feeCost = 0;
            }
            $feeCost = $this->sum($feeCost, $this->safe_number($feeTransactions[$i], 'price'));
        }
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $market['quote'],
            );
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'order' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function parse_ledger_entry_direction($direction) {
        $directions = array(
            'affiliate_earnings' => 'in',
            'atm_payment' => 'in',
            'bought_currency_from_fund' => 'out',
            'bought_shares' => 'out',
            'paid_commission' => 'out',
            'paypal_payment' => 'in',
            'pos_payment' => 'in',
            'released_currency_to_fund' => 'out',
            'rollover_commission' => 'out',
            'sold_currency_to_fund' => 'in',
            'sold_shares' => 'in',
            'transfer_received' => 'in',
            'transfer_sent' => 'out',
            'withdraw' => 'out',
            // commented types will be shown as-is
            // 'acquired_currency_from_fund' => '',
            // 'acquired_insurance' => '',
            // 'dividend_distributed_to_holders' => '',
            // 'dividend_from_shares' => '',
            // 'exposed_position' => '',
            // 'insurances_reimbursement' => '',
            // 'lent_currency' => '',
            // 'linden_lab_assessment' => '',
            // 'position_transfer_received' => '',
            // 'return_lent_currency' => '',
            // 'returned_lent_currency' => '',
            // 'the_rock_assessment' => '',
        );
        return $this->safe_string($directions, $direction, $direction);
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            'affiliate_earnings' => 'referral',
            'atm_payment' => 'transaction',
            'bought_currency_from_fund' => 'trade',
            'bought_shares' => 'trade',
            'paid_commission' => 'fee',
            'paypal_payment' => 'transaction',
            'pos_payment' => 'transaction',
            'released_currency_to_fund' => 'trade',
            'rollover_commission' => 'fee',
            'sold_currency_to_fund' => 'trade',
            'sold_shares' => 'trade',
            'transfer_received' => 'transfer',
            'transfer_sent' => 'transfer',
            'withdraw' => 'transaction',
            // commented $types will be shown as-is
            // 'acquired_currency_from_fund' => '',
            // 'acquired_insurance' => '',
            // 'dividend_distributed_to_holders' => '',
            // 'dividend_from_shares' => '',
            // 'exposed_position' => '',
            // 'insurances_reimbursement' => '',
            // 'lent_currency' => '',
            // 'linden_lab_assessment' => '',
            // 'position_transfer_received' => '',
            // 'return_lent_currency' => '',
            // 'returned_lent_currency' => '',
            // 'the_rock_assessment' => '',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        // withdrawal
        //
        //     {
        //         "$id" => 21311223,
        //         "date" => "2015-06-30T13:55:11.000Z",
        //         "$type" => "withdraw",
        //         "price" => 103.00,
        //         "$currency" => "EUR",
        //         "fund_id" => null,
        //         "order_id" => null,
        //         "trade_id" => null,
        //         "transfer_detail" => {
        //             "method" => "wire_transfer",
        //             "$id" => "F112DD3",
        //             "recipient" => "IT123456789012",
        //             "confirmations" => 0
        //         }
        //     }
        //
        // deposit
        //
        //     {
        //         "$id" => 21311222,
        //         "date" => "2015-06-30T13:55:11.000Z",
        //         "$type" => "atm_payment",
        //         "price" => 2.01291,
        //         "$currency" => "BTC",
        //         "fund_id" => "null",
        //         "order_id" => null,
        //         "trade_id" => null,
        //         "transfer_detail" => {
        //             "method" => "bitcoin",
        //             "$id" => "0e3e2357e806b6cdb1f70b54c3a3a17b6714ee1f0e68bebb44a74b1efd512098",
        //             "recipient" => "mzb3NgX9Dr6jgGAu31L6jsPGB2zkaFxxyf",
        //             "confirmations" => 3
        //         }
        //     }
        //
        // trade fee
        //
        //     {
        //         "$id" => 21311221,
        //         "date" => "2015-06-30T13:55:11.000Z",
        //         "$type" => "paid_commission",
        //         "price" => 0.0001,
        //         "fund_id" => "BTCEUR",
        //         "order_id" => 12832371,
        //         "trade_id" => 12923212,
        //         "$currency" => "BTC",
        //         "transfer_detail" => null
        //     }
        //
        $id = $this->safe_string($item, 'id');
        $referenceId = null;
        $type = $this->safe_string($item, 'type');
        $direction = $this->parse_ledger_entry_direction($type);
        $type = $this->parse_ledger_entry_type($type);
        if ($type === 'trade' || $type === 'fee') {
            $referenceId = $this->safe_string($item, 'trade_id');
        }
        $currencyId = $this->safe_string($item, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $amount = $this->safe_number($item, 'price');
        $timestamp = $this->parse8601($this->safe_string($item, 'date'));
        $status = 'ok';
        return array(
            'info' => $item,
            'id' => $id,
            'direction' => $direction,
            'account' => null,
            'referenceId' => $referenceId,
            'referenceAccount' => null,
            'type' => $type,
            'currency' => $code,
            'amount' => $amount,
            'before' => null,
            'after' => null,
            'status' => $status,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => null,
        );
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'page' => 1,
            // 'fund_id' => 'ETHBTC', // filter by fund symbol
            // 'currency' => 'BTC', // filter by $currency
            // 'after' => '2015-02-06T08:47:26Z', // filter after a certain timestamp
            // 'before' => '2015-02-06T08:47:26Z',
            // 'type' => 'withdraw',
            // 'order_id' => '12832371', // filter by a specific order ID
            // 'trade_id' => '12923212', // filter by a specific trade ID
            // 'transfer_method' => 'bitcoin', // wire_transfer, ripple, greenaddress, bitcoin, litecoin, namecoin, peercoin, dogecoin
            // 'transfer_recipient' => '1MAHLhJoz9W2ydbRf972WSgJYJ3Ui7aotm', // filter by a specific recipient (e.g. Bitcoin address, IBAN)
            // 'transfer_id' => '8261949194985b01985006724dca5d6059989e096fa95608271d00dd902327fa', // filter by a specific transfer ID (e.g. Bitcoin TX hash)
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($since !== null) {
            $request['after'] = $this->iso8601($since);
        }
        $response = $this->privateGetTransactions (array_merge($request, $params));
        //
        //     {
        //         "$transactions" => array(
        //             {
        //                 "id" => 21311223,
        //                 "date" => "2015-06-30T13:55:11.000Z",
        //                 "type" => "withdraw",
        //                 "price" => 103.00,
        //                 "$currency" => "EUR",
        //                 "fund_id" => null,
        //                 "order_id" => null,
        //                 "trade_id" => null,
        //                 "transfer_detail" => array(
        //                     "method" => "wire_transfer",
        //                     "id" => "F112DD3",
        //                     "recipient" => "IT123456789012",
        //                     "confirmations" => 0
        //                 }
        //             ),
        //             {
        //                 "id" => 21311222,
        //                 "date" => "2015-06-30T13:55:11.000Z",
        //                 "type" => "atm_payment",
        //                 "price" => 2.01291,
        //                 "$currency" => "BTC",
        //                 "fund_id" => "null",
        //                 "order_id" => null,
        //                 "trade_id" => null,
        //                 "transfer_detail" => array(
        //                     "method" => "bitcoin",
        //                     "id" => "0e3e2357e806b6cdb1f70b54c3a3a17b6714ee1f0e68bebb44a74b1efd512098",
        //                     "recipient" => "mzb3NgX9Dr6jgGAu31L6jsPGB2zkaFxxyf",
        //                     "confirmations" => 3
        //                 }
        //             ),
        //             {
        //                 "id" => 21311221,
        //                 "date" => "2015-06-30T13:55:11.000Z",
        //                 "type" => "paid_commission",
        //                 "price" => 0.0001,
        //                 "fund_id" => "BTCEUR",
        //                 "order_id" => 12832371,
        //                 "trade_id" => 12923212,
        //                 "$currency" => "BTC",
        //                 "transfer_detail" => null
        //             }
        //         ),
        //         "meta" => {
        //             "total_count" => 1221,
        //             "first" => array( "page" => 1, "href" => "https://api.therocktrading.com/v1/transactions?page=1" ),
        //             "previous" => null,
        //             "current" => array( "page" => 1, "href" => "https://api.therocktrading.com/v1/transactions?page=1" ),
        //             "next" => array( "page" => 2, "href" => "https://api.therocktrading.com/v1/transactions?page=2" ),
        //             "last" => array( "page" => 1221, "href" => "https://api.therocktrading.com/v1/transactions?page=1221" )
        //         }
        //     }
        //
        $transactions = $this->safe_value($response, 'transactions', array());
        return $this->parse_ledger($transactions, $currency, $since, $limit);
    }

    public function parse_transaction_type($type) {
        $types = array(
            'withdraw' => 'withdrawal',
            'atm_payment' => 'deposit',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchWithdrawals
        //
        //     // fiat
        //
        //     {
        //         "$id" => 21311223,
        //         "date" => "2015-06-30T13:55:11.000Z",
        //         "$type" => "withdraw",
        //         "price" => 103.00,
        //         "$currency" => "EUR",
        //         "fund_id" => null,
        //         "order_id" => null,
        //         "trade_id" => null,
        //         "transfer_detail" => {
        //             "$method" => "wire_transfer",
        //             "$id" => "F112DD3",
        //             "recipient" => "IT123456789012",
        //             "confirmations" => 0
        //         }
        //     }
        //
        //     {
        //         "$id" => 12564223,
        //         "date" => "2017-08-07T08:13:50.023Z",
        //         "note" => "GB7IDL401573388",
        //         "$type" => "withdraw",
        //         "price" => 4345.93,
        //         "fund_id" => null,
        //         "$currency" => "EUR",
        //         "order_id" => null,
        //         "trade_id" => null,
        //         "transfer_detail" => {
        //             "$id" => "EXECUTEDBUTUNCHECKED",
        //             "$method" => "wire_transfer",
        //             "recipient" => "GB7IDL401573388",
        //             "confirmations" => 0
        //         }
        //     }
        //
        //     // crypto
        //
        //     {
        //         $id => 20914695,
        //         date => '2018-02-24T07:13:23.002Z',
        //         $type => 'withdraw',
        //         price => 2.70883607,
        //         $currency => 'BCH',
        //         fund_id => null,
        //         order_id => null,
        //         trade_id => null,
        //         note => '1MAHLhJoz9W2ydbRf972WSgJYJ3Ui7aotm',
        //         transfer_detail => {
        //             $method => 'bitcoin_cash',
        //             $id => '8261949194985b01985006724dca5d6059989e096fa95608271d00dd902327fa',
        //             recipient => '1MAHLhJoz9W2ydbRf972WSgJYJ3Ui7aotm',
        //             confirmations => 0
        //         }
        //     }
        //
        //
        // fetchDeposits
        //
        //     // fiat
        //
        //     {
        //         $id => 16176632,
        //         date => '2017-11-20T21:00:13.355Z',
        //         $type => 'atm_payment',
        //         price => 5000,
        //         $currency => 'EUR',
        //         fund_id => null,
        //         order_id => null,
        //         trade_id => null,
        //         note => 'Mistral deposit',
        //         transfer_detail => {
        //             $method => 'wire_transfer',
        //             $id => '972JQ49337DX769T',
        //             recipient => null,
        //             confirmations => 0
        //         }
        //     }
        //
        //     // crypto
        //
        //     {
        //         "$id" => 21311222,
        //         "date" => "2015-06-30T13:55:11.000Z",
        //         "$type" => "atm_payment",
        //         "price" => 2.01291,
        //         "$currency" => "BTC",
        //         "fund_id" => "null",
        //         "order_id" => null,
        //         "trade_id" => null,
        //         "transfer_detail" => {
        //             "$method" => "bitcoin",
        //             "$id" => "0e3e2357e806b6cdb1f70b54c3a3a17b6714ee1f0e68bebb44a74b1efd512098",
        //             "recipient" => "mzb3NgX9Dr6jgGAu31L6jsPGB2zkaFxxyf",
        //             "confirmations" => 3
        //         }
        //     }
        //
        $id = $this->safe_string($transaction, 'id');
        $type = $this->parse_transaction_type($this->safe_string($transaction, 'type'));
        $detail = $this->safe_value($transaction, 'transfer_detail', array());
        $method = $this->safe_string($detail, 'method');
        $txid = null;
        $address = null;
        if ($method !== null) {
            if ($method !== 'wire_transfer') {
                $txid = $this->safe_string($detail, 'id');
                $address = $this->safe_string($detail, 'recipient');
            }
        }
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $amount = $this->safe_number($transaction, 'price');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'date'));
        $status = 'ok';
        // todo parse tags
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'addressFrom' => null,
            'addressTo' => $address,
            'address' => $address,
            'tagFrom' => null,
            'tagTo' => null,
            'tag' => null,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => null,
        );
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'type' => 'withdraw',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'type' => 'atm_payment',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'page' => 1,
            // 'fund_id' => 'ETHBTC', // filter by fund symbol
            // 'currency' => 'BTC', // filter by $currency
            // 'after' => '2015-02-06T08:47:26Z', // filter after a certain timestamp
            // 'before' => '2015-02-06T08:47:26Z',
            // 'type' => 'withdraw',
            // 'order_id' => '12832371', // filter by a specific order ID
            // 'trade_id' => '12923212', // filter by a specific trade ID
            // 'transfer_method' => 'bitcoin', // wire_transfer, ripple, greenaddress, bitcoin, litecoin, namecoin, peercoin, dogecoin
            // 'transfer_recipient' => '1MAHLhJoz9W2ydbRf972WSgJYJ3Ui7aotm', // filter by a specific recipient (e.g. Bitcoin address, IBAN)
            // 'transfer_id' => '8261949194985b01985006724dca5d6059989e096fa95608271d00dd902327fa', // filter by a specific transfer ID (e.g. Bitcoin TX hash)
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($since !== null) {
            $request['after'] = $this->iso8601($since);
        }
        $params = array_merge($request, $params);
        $response = $this->privateGetTransactions ($params);
        //
        //     {
        //         "$transactions" => array(
        //             {
        //                 "id" => 21311223,
        //                 "date" => "2015-06-30T13:55:11.000Z",
        //                 "type" => "withdraw",
        //                 "price" => 103.00,
        //                 "$currency" => "EUR",
        //                 "fund_id" => null,
        //                 "order_id" => null,
        //                 "trade_id" => null,
        //                 "transfer_detail" => array(
        //                     "method" => "wire_transfer",
        //                     "id" => "F112DD3",
        //                     "recipient" => "IT123456789012",
        //                     "confirmations" => 0
        //                 }
        //             ),
        //             {
        //                 "id" => 21311222,
        //                 "date" => "2015-06-30T13:55:11.000Z",
        //                 "type" => "atm_payment",
        //                 "price" => 2.01291,
        //                 "$currency" => "BTC",
        //                 "fund_id" => "null",
        //                 "order_id" => null,
        //                 "trade_id" => null,
        //                 "transfer_detail" => array(
        //                     "method" => "bitcoin",
        //                     "id" => "0e3e2357e806b6cdb1f70b54c3a3a17b6714ee1f0e68bebb44a74b1efd512098",
        //                     "recipient" => "mzb3NgX9Dr6jgGAu31L6jsPGB2zkaFxxyf",
        //                     "confirmations" => 3
        //                 }
        //             ),
        //             {
        //                 "id" => 21311221,
        //                 "date" => "2015-06-30T13:55:11.000Z",
        //                 "type" => "paid_commission",
        //                 "price" => 0.0001,
        //                 "fund_id" => "BTCEUR",
        //                 "order_id" => 12832371,
        //                 "trade_id" => 12923212,
        //                 "$currency" => "BTC",
        //                 "transfer_detail" => null
        //             }
        //         ),
        //         "meta" => {
        //             "total_count" => 1221,
        //             "first" => array( "page" => 1, "href" => "https://api.therocktrading.com/v1/transactions?page=1" ),
        //             "previous" => null,
        //             "current" => array( "page" => 1, "href" => "https://api.therocktrading.com/v1/transactions?page=1" ),
        //             "next" => array( "page" => 2, "href" => "https://api.therocktrading.com/v1/transactions?page=2" ),
        //             "last" => array( "page" => 1221, "href" => "https://api.therocktrading.com/v1/transactions?page=1221" )
        //         }
        //     }
        //
        $transactions = $this->safe_value($response, 'transactions', array());
        $transactionTypes = array( 'withdraw', 'atm_payment' );
        $depositsAndWithdrawals = $this->filter_by_array($transactions, 'type', $transactionTypes, false);
        return $this->parse_transactions($depositsAndWithdrawals, $currency, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'active' => 'open',
            'executed' => 'closed',
            'deleted' => 'canceled',
            // don't know what this $status means
            // 'conditional' => '?',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "$id" => 4325578,
        //         "fund_id":"BTCEUR",
        //         "$side":"buy",
        //         "$type":"limit",
        //         "$status":"executed",
        //         "$price":0.0102,
        //         "$amount" => 50.0,
        //         "amount_unfilled" => 0.0,
        //         "conditional_type" => null,
        //         "conditional_price" => null,
        //         "date":"2015-06-03T00:49:48.000Z",
        //         "close_on" => nil,
        //         "leverage" => 1.0,
        //         "position_id" => null,
        //         "$trades" => array(
        //             {
        //                 "$id":237338,
        //                 "fund_id":"BTCEUR",
        //                 "$amount":50,
        //                 "$price":0.0102,
        //                 "$side":"buy",
        //                 "dark":false,
        //                 "date":"2015-06-03T00:49:49.000Z"
        //             }
        //         )
        //     }
        //
        $id = $this->safe_string($order, 'id');
        $marketId = $this->safe_string($order, 'fund_id');
        $symbol = $this->safe_symbol($marketId, $market);
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $timestamp = $this->parse8601($this->safe_string($order, 'date'));
        $type = $this->safe_string($order, 'type');
        $side = $this->safe_string($order, 'side');
        $amount = $this->safe_number($order, 'amount');
        $remaining = $this->safe_number($order, 'amount_unfilled');
        $filled = null;
        if ($amount !== null) {
            if ($remaining !== null) {
                $filled = $amount - $remaining;
            }
        }
        $price = $this->safe_number($order, 'price');
        $trades = $this->safe_value($order, 'trades');
        $cost = null;
        $average = null;
        $lastTradeTimestamp = null;
        if ($trades !== null) {
            $numTrades = is_array($trades) ? count($trades) : 0;
            if ($numTrades > 0) {
                $trades = $this->parse_trades($trades, $market, null, null, array(
                    'orderId' => $id,
                ));
                // todo => determine the $cost and the $average $price from $trades
                $cost = 0;
                $filled = 0;
                for ($i = 0; $i < $numTrades; $i++) {
                    $trade = $trades[$i];
                    $cost = $this->sum($cost, $trade['cost']);
                    $filled = $this->sum($filled, $trade['amount']);
                }
                if ($filled > 0) {
                    $average = $cost / $filled;
                }
                $lastTradeTimestamp = $trades[$numTrades - 1]['timestamp'];
            } else {
                $cost = 0;
            }
        }
        $stopPrice = $this->safe_number($order, 'conditional_price');
        return array(
            'id' => $id,
            'clientOrderId' => null,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'average' => $average,
            'remaining' => $remaining,
            'fee' => null,
            'trades' => $trades,
        );
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'active',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'executed',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'fund_id' => $market['id'],
            // 'after' => '2015-02-06T08:47:26Z',
            // 'before' => '2015-02-06T08:47:26Z'
            // 'status' => 'active', // 'executed', 'conditional'
            // 'side' => 'buy', // 'sell'
            // 'position_id' => 123, // filter $orders by margin position id
        );
        if ($since !== null) {
            $request['after'] = $this->iso8601($since);
        }
        $response = $this->privateGetFundsFundIdOrders (array_merge($request, $params));
        //
        //     {
        //         $orders => array(
        //             {
        //                 id => 299333648,
        //                 fund_id => 'BTCEUR',
        //                 side => 'sell',
        //                 type => 'limit',
        //                 status => 'executed',
        //                 price => 5821,
        //                 amount => 0.1,
        //                 amount_unfilled => 0,
        //                 conditional_type => null,
        //                 conditional_price => null,
        //                 date => '2018-06-18T17:38:16.129Z',
        //                 close_on => null,
        //                 dark => false,
        //                 leverage => 1,
        //                 position_id => 0
        //             }
        //         )
        //     }
        //
        $orders = $this->safe_value($response, 'orders', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $id,
            'fund_id' => $market['id'],
        );
        $response = $this->privatePostFundsFundIdOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type === 'market') {
            $price = 0;
        }
        $request = array(
            'fund_id' => $this->market_id($symbol),
            'side' => $side,
            'amount' => $amount,
            'price' => $price,
        );
        $response = $this->privatePostFundsFundIdOrders (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
            'fund_id' => $this->market_id($symbol),
        );
        $response = $this->privateDeleteFundsFundIdOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
        );
        if ($limit !== null) {
            $request['per_page'] = $limit; // default 25 max 200
        }
        if ($since !== null) {
            $request['after'] = $this->iso8601($since);
        }
        $response = $this->privateGetFundsIdTrades (array_merge($request, $params));
        //
        //     { trades => array( {           id =>    237338,
        //                        fund_id =>   "BTCEUR",
        //                         amount =>    0.348,
        //                          price =>    348,
        //                           side =>   "sell",
        //                           dark =>    false,
        //                       order_id =>    14920648,
        //                           date =>   "2015-06-03T00:49:49.000Z",
        //                   transactions => array( array(       id =>  2770768,
        //                                         date => "2015-06-03T00:49:49.000Z",
        //                                         type => "sold_currency_to_fund",
        //                                        price =>  121.1,
        //                                     currency => "EUR"                       ),
        //                                   array(       id =>  2770769,
        //                                         date => "2015-06-03T00:49:49.000Z",
        //                                         type => "released_currency_to_fund",
        //                                        price =>  0.348,
        //                                     currency => "BTC"                        ),
        //                                   {       id =>  2770772,
        //                                         date => "2015-06-03T00:49:49.000Z",
        //                                         type => "paid_commission",
        //                                        price =>  0.06,
        //                                     currency => "EUR",
        //                                     trade_id =>  440492                     }   ) } ),
        //         meta => { total_count =>    31,
        //                       first => array( href => "https://api.therocktrading.com/v1/funds/BTCXRP/trades?page=1" ),
        //                    previous =>    null,
        //                     current => array( href => "https://api.therocktrading.com/v1/funds/BTCXRP/trades?page=1" ),
        //                        next => array( href => "https://api.therocktrading.com/v1/funds/BTCXRP/trades?page=2" ),
        //                        last => array( href => "https://api.therocktrading.com/v1/funds/BTCXRP/trades?page=2" )  } }
        //
        return $this->parse_trades($response['trades'], $market, $since, $limit);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
        );
        if ($limit !== null) {
            $request['per_page'] = $limit; // default 25 max 200
        }
        if ($since !== null) {
            $request['after'] = $this->iso8601($since);
        }
        $response = $this->publicGetFundsIdTrades (array_merge($request, $params));
        //
        //     { trades => array( array(      id =>  4493548,
        //                   fund_id => "ETHBTC",
        //                    amount =>  0.203,
        //                     price =>  0.02783576,
        //                      side => "buy",
        //                      dark =>  false,
        //                      date => "2018-11-30T08:19:18.236Z" ),
        //                 {      id =>  4492926,
        //                   fund_id => "ETHBTC",
        //                    amount =>  0.04,
        //                     price =>  0.02767034,
        //                      side => "buy",
        //                      dark =>  false,
        //                      date => "2018-11-30T07:03:03.897Z" }  ),
        //         meta => { total_count =>    null,
        //                       first => array( page =>  1,
        //                                href => "https://api.therocktrading.com/v1/funds/ETHBTC/trades?page=1" ),
        //                    previous =>    null,
        //                     current => array( page =>  1,
        //                                href => "https://api.therocktrading.com/v1/funds/ETHBTC/trades?page=1" ),
        //                        next => array( page =>  2,
        //                                href => "https://api.therocktrading.com/v1/funds/ETHBTC/trades?page=2" ),
        //                        last =>    null                                                                   } }
        //
        return $this->parse_trades($response['trades'], $market, $since, $limit);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        $headers = ($headers === null) ? array() : $headers;
        if ($api === 'private') {
            $this->check_required_credentials();
            if ($query) {
                if ($method === 'POST') {
                    $body = $this->json($query);
                    $headers['Content-Type'] = 'application/json';
                } else {
                    $queryString = $this->rawencode($query);
                    if (strlen($queryString)) {
                        $url .= '?' . $queryString;
                    }
                }
            }
            $nonce = (string) $this->nonce();
            $auth = $nonce . $url;
            $headers['X-TRT-KEY'] = $this->apiKey;
            $headers['X-TRT-NONCE'] = $nonce;
            $headers['X-TRT-SIGN'] = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha512');
        } else if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->rawencode($query);
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        //
        //     {
        //         "$errors":
        //         array(
        //             array( "$message" => ":currency is not a valid value for param currency","code" => "11","meta" => array( "key":"currency","value":":currency") ),
        //             array( "$message" => "Address allocation limit reached for currency :currency.","code" => "13" ),
        //             array( "$message" => "Request already running", "code" => "50"),
        //             array( "$message" => "cannot specify multiple address types", "code" => "12" ),
        //             array( "$message" => ":address_type is invalid", "code" => "12" )
        //         )
        //     }
        //
        $errors = $this->safe_value($response, 'errors', array());
        $numErrors = is_array($errors) ? count($errors) : 0;
        if ($numErrors > 0) {
            $feedback = $this->id . ' ' . $body;
            // here we throw the first $error we can identify
            for ($i = 0; $i < $numErrors; $i++) {
                $error = $errors[$i];
                $message = $this->safe_string($error, 'message');
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            }
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}
