<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\InvalidOrder;

class bleutrade extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bleutrade',
            'name' => 'Bleutrade',
            'countries' => ['BR'], // Brazil
            'rateLimit' => 1000,
            'certified' => false,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createLimitOrder' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'editOrder' => false,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => false,
                'fetchOrderTrades' => false,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => false,
                'fetchWithdrawals' => true,
                'withdraw' => false,
            ),
            'timeframes' => array(
                '1h' => '1h',
                '4h' => '4h',
                '8h' => '8h',
                '1d' => '1d',
                '1w' => '1w',
            ),
            'hostname' => 'bleutrade.com',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/30303000-b602dbe6-976d-11e7-956d-36c5049c01e7.jpg',
                'api' => array(
                    'v3Private' => 'https://{hostname}/api/v3/private',
                    'v3Public' => 'https://{hostname}/api/v3/public',
                ),
                'www' => 'https://bleutrade.com',
                'doc' => array(
                    'https://app.swaggerhub.com/apis-docs/bleu/white-label/3.0.0',
                ),
                'fees' => 'https://bleutrade.com/fees/',
            ),
            'api' => array(
                'v3Public' => array(
                    'get' => array(
                        'getassets',
                        'getmarkets',
                        'getticker',
                        'getmarketsummary',
                        'getmarketsummaries',
                        'getorderbook',
                        'getmarkethistory',
                        'getcandles',
                    ),
                ),
                'v3Private' => array(
                    'get' => array(
                        'statement',
                    ),
                    'post' => array(
                        'getbalance',
                        'getbalances',
                        'buylimit',
                        'selllimit',
                        'buylimitami',
                        'selllimitami',
                        'buystoplimit',
                        'sellstoplimit',
                        'ordercancel',
                        'getopenorders',
                        'getcloseorders',
                        'getdeposithistory',
                        'getdepositaddress',
                        'getmytransactions',
                        'withdraw',
                        'directtransfer',
                        'getwithdrawhistory',
                        'getlimits',
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'EPC' => 'Epacoin',
            ),
            'exceptions' => array(
                'exact' => array(
                    'ERR_INSUFICIENT_BALANCE' => '\\ccxt\\InsufficientFunds',
                    'ERR_LOW_VOLUME' => '\\ccxt\\BadRequest',
                    'Invalid form' => '\\ccxt\\BadRequest',
                ),
                'broad' => array(
                    'Order is not open' => '\\ccxt\\InvalidOrder',
                    'Invalid Account / Api KEY / Api Secret' => '\\ccxt\\AuthenticationError', // also happens when an invalid nonce is used
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.25 / 100,
                    'maker' => 0.25 / 100,
                ),
            ),
            'options' => array(
                'parseOrderStatus' => true,
            ),
        ));
        // undocumented api calls
        // https://bleutrade.com/api/v3/public/tradingview/symbols?symbol=ETH_BTC
        // https://bleutrade.com/api/v3/public/tradingview/config
        // https://bleutrade.com/api/v3/public/tradingview/time
        // https://bleutrade.com/api/v3/private/getcloseorders?market=ETH_BTC
        // https://bleutrade.com/config contains the fees
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->v3PublicGetGetassets ($params);
        $items = $response['result'];
        $result = array();
        for ($i = 0; $i < count($items); $i++) {
            //   { Asset => 'USDT',
            //     AssetLong => 'Tether',
            //     MinConfirmation => 4,
            //     WithdrawTxFee => 1,
            //     WithdrawTxFeePercent => 0,
            //     SystemProtocol => 'ETHERC20',
            //     IsActive => true,
            //     InfoMessage => '',
            //     MaintenanceMode => false,
            //     MaintenanceMessage => '',
            //     FormatPrefix => '',
            //     FormatSufix => '',
            //     DecimalSeparator => '.',
            //     ThousandSeparator => ',',
            //     DecimalPlaces => 8,
            //     Currency => 'USDT',
            //     CurrencyLong => 'Tether',
            //     CoinType => 'ETHERC20' }
            $item = $items[$i];
            $id = $this->safe_string($item, 'Asset');
            $code = $this->safe_currency_code($id);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'name' => $this->safe_string($item, 'AssetLong'),
                'active' => $this->safe_value($item, 'IsActive') && !$this->safe_value($item, 'MaintenanceMode'),
                'fee' => $this->safe_number($item, 'WithdrawTxFee'),
                'precision' => $this->safe_number($item, 'DecimalPlaces'),
                'info' => $item,
                'limits' => $this->limits,
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        // https://github.com/ccxt/ccxt/issues/5668
        $response = $this->v3PublicGetGetmarkets ($params);
        $result = array();
        $markets = $this->safe_value($response, 'result');
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            //   { MarketName => 'LTC_USDT',
            //     MarketAsset => 'LTC',
            //     BaseAsset => 'USDT',
            //     MarketAssetLong => 'Litecoin',
            //     BaseAssetLong => 'Tether',
            //     IsActive => true,
            //     MinTradeSize => 0.0001,
            //     InfoMessage => '',
            //     MarketCurrency => 'LTC',
            //     BaseCurrency => 'USDT',
            //     MarketCurrencyLong => 'Litecoin',
            //     BaseCurrencyLong => 'Tether' }
            $id = $this->safe_string($market, 'MarketName');
            $baseId = $this->safe_string($market, 'MarketAsset');
            $quoteId = $this->safe_string($market, 'BaseAsset');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => 8,
                'price' => 8,
            );
            $active = $this->safe_value($market, 'IsActive', false);
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'info' => $market,
                'precision' => $precision,
                'maker' => $this->fees['trading']['maker'],
                'taker' => $this->fees['trading']['taker'],
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_number($market, 'MinTradeSize'),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'market' => $this->market_id($symbol),
            'type' => 'ALL',
        );
        if ($limit !== null) {
            $request['depth'] = $limit; // 50
        }
        $response = $this->v3PublicGetGetorderbook (array_merge($request, $params));
        $orderbook = $this->safe_value($response, 'result');
        if (!$orderbook) {
            throw new ExchangeError($this->id . ' no $orderbook data in ' . $this->json($response));
        }
        return $this->parse_order_book($orderbook, null, 'buy', 'sell', 'Rate', 'Quantity');
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->v3PublicGetGetmarketsummary (array_merge($request, $params));
        $ticker = $this->safe_value($response, 'result', array());
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->v3PublicGetGetmarketsummaries ($params);
        $result = $this->safe_value($response, 'result');
        $tickers = array();
        for ($i = 0; $i < count($result); $i++) {
            $ticker = $this->parse_ticker($result[$i]);
            $tickers[] = $ticker;
        }
        return $this->filter_by_array($tickers, 'symbol', $symbols);
    }

    public function parse_ticker($ticker, $market = null) {
        //   { TimeStamp => '2020-01-14 14:32:28',
        //     MarketName => 'LTC_USDT',
        //     MarketAsset => 'LTC',
        //     BaseAsset => 'USDT',
        //     MarketAssetName => 'Litecoin',
        //     BaseAssetName => 'Tether',
        //     PrevDay => 49.2867503,
        //     High => 56.78622664,
        //     Low => 49.27384025,
        //     Last => 53.94,
        //     Average => 51.37509368,
        //     Volume => 1.51282404,
        //     BaseVolume => 77.72147677,
        //     Bid => 53.62070218,
        //     Ask => 53.94,
        //     IsActive => 'true',
        //     InfoMessage => '',
        //     MarketCurrency => 'Litecoin',
        //     BaseCurrency => 'Tether' }
        $timestamp = $this->parse8601($this->safe_string($ticker, 'TimeStamp'));
        $marketId = $this->safe_string($ticker, 'MarketName');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $previous = $this->safe_number($ticker, 'PrevDay');
        $last = $this->safe_number($ticker, 'Last');
        $change = null;
        $percentage = null;
        if ($last !== null) {
            if ($previous !== null) {
                $change = $last - $previous;
                if ($previous > 0) {
                    $percentage = ($change / $previous) * 100;
                }
            }
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'High'),
            'low' => $this->safe_number($ticker, 'Low'),
            'bid' => $this->safe_number($ticker, 'Bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'Ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $previous,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'Volume'),
            'quoteVolume' => $this->safe_number($ticker, 'BaseVolume'),
            'info' => $ticker,
        );
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        return [
            $this->parse8601($ohlcv['TimeStamp'] . '+00:00'),
            $this->safe_number($ohlcv, 'Open'),
            $this->safe_number($ohlcv, 'High'),
            $this->safe_number($ohlcv, 'Low'),
            $this->safe_number($ohlcv, 'Close'),
            $this->safe_number($ohlcv, 'Volume'),
        ];
    }

    public function fetch_ohlcv($symbol, $timeframe = '15m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'period' => $this->timeframes[$timeframe],
            'market' => $market['id'],
            'count' => $limit,
        );
        $response = $this->v3PublicGetGetcandles (array_merge($request, $params));
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_ohlcvs($result, $market, $timeframe, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type !== 'limit') {
            // todo => STOP-LIMIT and AMI order types are supported
            throw new InvalidOrder($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $request = array(
            'rate' => $this->price_to_precision($symbol, $price),
            'quantity' => $this->amount_to_precision($symbol, $amount),
            'tradeType' => ($side === 'buy') ? '1' : '0',
            'market' => $this->market_id($symbol),
        );
        $response = null;
        if ($side === 'buy') {
            $response = $this->v3PrivatePostBuylimit (array_merge($request, $params));
        } else {
            $response = $this->v3PrivatePostSelllimit (array_merge($request, $params));
        }
        //   array( success =>  true,
        //     message => "",
        //     result => "161105236" ),
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'result'),
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'orderid' => $id,
        );
        $response = $this->v3PrivatePostOrdercancel (array_merge($request, $params));
        // array( success => true, message => '', result => '' )
        return $response;
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['market'] = $market['id'];
        }
        $response = $this->v3PrivatePostGetopenorders (array_merge($request, $params));
        $items = $this->safe_value($response, 'result', array());
        return $this->parse_orders($items, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->v3PrivatePostGetbalances ($params);
        $result = array( 'info' => $response );
        $items = $response['result'];
        for ($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            $currencyId = $this->safe_string($item, 'Asset');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($item, 'Available');
            $account['total'] = $this->safe_number($item, 'Balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['market'] = $market['id'];
        }
        $response = $this->v3PrivatePostGetcloseorders (array_merge($request, $params));
        $orders = $this->safe_value($response, 'result', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_transactions_with_method($method, $code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->$method ($params);
        $transactions = $this->safe_value($response, 'result', array());
        return $this->parse_transactions($transactions, $code, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_with_method('v3PrivatePostGetdeposithistory', $code, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_with_method('v3PrivatePostGetwithdrawhistory', $code, $since, $limit, $params);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'asset' => $currency['id'],
        );
        $response = $this->v3PrivatePostGetdepositaddress (array_merge($request, $params));
        //   { success => true,
        //     message => '',
        //     result:
        //     { Asset => 'ETH',
        //         AssetName => 'Ethereum',
        //         DepositAddress => '0x748c5c8jhksjdfhd507d3aa9',
        //         Currency => 'ETH',
        //         CurrencyName => 'Ethereum' } }
        $item = $response['result'];
        $address = $this->safe_string($item, 'DepositAddress');
        return array(
            'currency' => $code,
            'address' => $this->check_address($address),
            // 'tag' => tag,
            'info' => $item,
        );
    }

    public function parse_ledger_entry_type($type) {
        // deposits don't seem to appear in here
        $types = array(
            'TRADE' => 'trade',
            'WITHDRAW' => 'transaction',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        // trade (both sides)
        //
        //     {
        //         ID => 109660527,
        //         TimeStamp => '2018-11-14 15:12:57.140776',
        //         Asset => 'ETH',
        //         AssetName => 'Ethereum',
        //         Amount => 0.01,
        //         Type => 'TRADE',
        //         Description => 'Trade +, order $id 133111123',
        //         Comments => '',
        //         CoinSymbol => 'ETH',
        //         CoinName => 'Ethereum'
        //     }
        //
        //     {
        //         ID => 109660526,
        //         TimeStamp => '2018-11-14 15:12:57.140776',
        //         Asset => 'BTC',
        //         AssetName => 'Bitcoin',
        //         Amount => -0.00031776,
        //         Type => 'TRADE',
        //         Description => 'Trade -, order $id 133111123, $fee -0.00000079',
        //         Comments => '',
        //         CoinSymbol => 'BTC',
        //         CoinName => 'Bitcoin'
        //     }
        //
        // withdrawal
        //
        //     {
        //         ID => 104672316,
        //         TimeStamp => '2018-05-03 08:18:19.031831',
        //         Asset => 'DOGE',
        //         AssetName => 'Dogecoin',
        //         Amount => -61893.87864686,
        //         Type => 'WITHDRAW',
        //         Description => 'Withdraw => 61883.87864686 to address DD8tgehNNyYB2iqVazi2W1paaztgcWXtF6; $fee 10.00000000',
        //         Comments => '',
        //         CoinSymbol => 'DOGE',
        //         CoinName => 'Dogecoin'
        //     }
        //
        $code = $this->safe_currency_code($this->safe_string($item, 'CoinSymbol'), $currency);
        $description = $this->safe_string($item, 'Description');
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'Type'));
        $referenceId = null;
        $fee = null;
        $delimiter = ($type === 'trade') ? ', ' : '; ';
        $parts = explode($delimiter, $description);
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            if (mb_strpos($part, 'fee') === 0) {
                $part = str_replace('fee ', '', $part);
                $feeCost = floatval($part);
                if ($feeCost < 0) {
                    $feeCost = -$feeCost;
                }
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $code,
                );
            } else if (mb_strpos($part, 'order id') === 0) {
                $referenceId = str_replace('order $id ', '', $part);
            }
            //
            // does not belong to Ledger, related to parseTransaction
            //
            //     if (mb_strpos($part, 'Withdraw') === 0) {
            //         $details = explode(' to address ', $part);
            //         if (strlen($details) > 1) {
            //             address = $details[1];
            //     }
            //
        }
        $timestamp = $this->parse8601($this->safe_string($item, 'TimeStamp'));
        $amount = $this->safe_number($item, 'Amount');
        $direction = null;
        if ($amount !== null) {
            $direction = 'in';
            if ($amount < 0) {
                $direction = 'out';
                $amount = -$amount;
            }
        }
        $id = $this->safe_string($item, 'ID');
        return array(
            'id' => $id,
            'info' => $item,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'direction' => $direction,
            'account' => null,
            'referenceId' => $referenceId,
            'referenceAccount' => null,
            'type' => $type,
            'currency' => $code,
            'amount' => $amount,
            'before' => null,
            'after' => null,
            'status' => 'ok',
            'fee' => $fee,
        );
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // only seems to return 100 $items and there is no documented way to change page size or offset
        $request = array(
        );
        $response = $this->v3PrivatePostGetmytransactions (array_merge($request, $params));
        $items = $response['result'];
        return $this->parse_ledger($items, $code, $since, $limit);
    }

    public function parse_order($order, $market = null) {
        //
        // fetchClosedOrders
        //
        //   { OrderID => 89742658,
        //     Exchange => 'DOGE_BTC',
        //     Type => 'BUY',
        //     Quantity => 10000,
        //     QuantityRemaining => 0,
        //     QuantityBaseTraded => 0,
        //     Price => 6.6e-7,
        //     Status => 'OK',
        //     Created => '2018-02-16 08:55:36',
        //     Comments => '' }
        //
        //  fetchOpenOrders
        //
        //   { OrderID => 161105302,
        //     Exchange => 'ETH_BTC',
        //     Type => 'SELL',
        //     Quantity => 0.4,
        //     QuantityRemaining => 0.4,
        //     QuantityBaseTraded => 0,
        //     Price => 0.04,
        //     Status => 'OPEN',
        //     Created => '2020-01-22 09:21:27',
        //     Comments => array( String => '', Valid => true )
        $side = strtolower($this->safe_string($order, 'Type'));
        $status = $this->parse_order_status($this->safe_string($order, 'Status'));
        $marketId = $this->safe_string($order, 'Exchange');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $timestamp = null;
        if (is_array($order) && array_key_exists('Created', $order)) {
            $timestamp = $this->parse8601($order['Created'] . '+00:00');
        }
        $price = $this->safe_number($order, 'Price');
        $amount = $this->safe_number($order, 'Quantity');
        $remaining = $this->safe_number($order, 'QuantityRemaining');
        $average = $this->safe_number($order, 'PricePerUnit');
        $id = $this->safe_string($order, 'OrderID');
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'average' => $average,
            'amount' => $amount,
            'filled' => null,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'trades' => null,
        ));
    }

    public function parse_order_status($status) {
        $statuses = array(
            'OK' => 'closed',
            'OPEN' => 'open',
            'CANCELED' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //  deposit:
        //
        //   { ID => 118698752,
        //     Timestamp => '2020-01-21 11:16:09',
        //     Asset => 'ETH',
        //     Amount => 1,
        //     TransactionID => '',
        //     Status => 'CONFIRMED',
        //     Label => '0x748c5c8228d0c596f4d07f338blah',
        //     Symbol => 'ETH' }
        //
        // withdrawal:
        //
        //   { ID => 689281,
        //     Timestamp => '2019-07-05 13:14:43',
        //     Asset => 'BTC',
        //     Amount => -0.108959,
        //     TransactionID => 'da48d6901fslfjsdjflsdjfls852b87e362cad1',
        //     Status => 'CONFIRMED',
        //     Label => '0.1089590;35wztHPMgrebFvvblah;0.00100000',
        //     Symbol => 'BTC' }
        //
        $id = $this->safe_string($transaction, 'ID');
        $amount = $this->safe_number($transaction, 'Amount');
        $type = 'deposit';
        if ($amount < 0) {
            $amount = abs($amount);
            $type = 'withdrawal';
        }
        $currencyId = $this->safe_string($transaction, 'Asset');
        $code = $this->safe_currency_code($currencyId, $currency);
        $label = $this->safe_string($transaction, 'Label');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'Timestamp'));
        $txid = $this->safe_string($transaction, 'TransactionID');
        $address = null;
        $feeCost = null;
        $labelParts = explode(';', $label);
        if (strlen($labelParts) === 3) {
            $amount = floatval($labelParts[0]);
            $address = $labelParts[1];
            $feeCost = floatval($labelParts[2]);
        } else {
            $address = $label;
        }
        $fee = null;
        if ($feeCost !== null) {
            $fee = array(
                'currency' => $code,
                'cost' => $feeCost,
            );
        }
        $status = 'ok';
        if ($txid === 'CANCELED') {
            $txid = null;
            $status = 'canceled';
        }
        return array(
            'info' => $transaction,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'tag' => null,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'fee' => $fee,
        );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->implode_params($this->urls['api'][$api], array(
            'hostname' => $this->hostname,
        )) . '/';
        if ($api === 'v3Private') {
            $this->check_required_credentials();
            $request = array(
                'apikey' => $this->apiKey,
                'nonce' => $this->nonce(),
            );
            $url .= $path . '?' . $this->urlencode(array_merge($request, $params));
            $signature = $this->hmac($this->encode($url), $this->encode($this->secret), 'sha512');
            $headers = array( 'apisign' => $signature );
        } else {
            $url .= $path . '?' . $this->urlencode($params);
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        //    examples...
        //    array("$success":false,"message":"Erro => Order is not open.","result":"") <-- 'error' is spelt wrong
        //    array("$success":false,"message":"Error => Very low volume.","result":"ERR_LOW_VOLUME")
        //    array("$success":false,"message":"Error => Insuficient Balance","result":"ERR_INSUFICIENT_BALANCE")
        //    array("$success":false,"message":"Invalid form","result":null)
        //
        $success = $this->safe_value($response, 'success');
        if ($success === null) {
            throw new ExchangeError($this->id . ' => malformed $response => ' . $this->json($response));
        }
        if (!$success) {
            $feedback = $this->id . ' ' . $body;
            $errorCode = $this->safe_string($response, 'result');
            if ($errorCode !== null) {
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $errorCode, $feedback);
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $feedback);
            }
            $errorMessage = $this->safe_string($response, 'message');
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $errorMessage, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorMessage, $feedback);
            throw new ExchangeError($feedback);
        }
    }
}
