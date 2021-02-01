<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\NotSupported;

class bitfinex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitfinex',
            'name' => 'Bitfinex',
            'countries' => array( 'VG' ),
            'version' => 'v1',
            'rateLimit' => 1500,
            'certified' => true,
            'pro' => true,
            // new metainfo interface
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'CORS' => false,
                'createDepositAddress' => true,
                'createOrder' => true,
                'deposit' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => false,
                'fetchFundingFees' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTradingFee' => true,
                'fetchTradingFees' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => false,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '3h' => '3h',
                '6h' => '6h',
                '12h' => '12h',
                '1d' => '1D',
                '1w' => '7D',
                '2w' => '14D',
                '1M' => '1M',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766244-e328a50c-5ed2-11e7-947b-041416579bb3.jpg',
                'api' => array(
                    'v2' => 'https://api-pub.bitfinex.com', // https://github.com/ccxt/ccxt/issues/5109
                    'public' => 'https://api.bitfinex.com',
                    'private' => 'https://api.bitfinex.com',
                ),
                'www' => 'https://www.bitfinex.com',
                'referral' => 'https://www.bitfinex.com/?refcode=P61eYxFL',
                'doc' => array(
                    'https://docs.bitfinex.com/v1/docs',
                    'https://github.com/bitfinexcom/bitfinex-api-node',
                ),
            ),
            'api' => array(
                // v2 symbol ids require a 't' prefix
                // just the public part of it (use bitfinex2 for everything else)
                'v2' => array(
                    'get' => array(
                        'platform/status',
                        'tickers',
                        'ticker/{symbol}',
                        'trades/{symbol}/hist',
                        'book/{symbol}/{precision}',
                        'book/{symbol}/P0',
                        'book/{symbol}/P1',
                        'book/{symbol}/P2',
                        'book/{symbol}/P3',
                        'book/{symbol}/R0',
                        'stats1/{key}:{size}:{symbol}:{side}/{section}',
                        'stats1/{key}:{size}:{symbol}/{section}',
                        'stats1/{key}:{size}:{symbol}:long/last',
                        'stats1/{key}:{size}:{symbol}:long/hist',
                        'stats1/{key}:{size}:{symbol}:short/last',
                        'stats1/{key}:{size}:{symbol}:short/hist',
                        'candles/trade:{timeframe}:{symbol}/{section}',
                        'candles/trade:{timeframe}:{symbol}/last',
                        'candles/trade:{timeframe}:{symbol}/hist',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'book/{symbol}',
                        // 'candles/{symbol}',
                        'lendbook/{currency}',
                        'lends/{currency}',
                        'pubticker/{symbol}',
                        'stats/{symbol}',
                        'symbols',
                        'symbols_details',
                        'tickers',
                        'trades/{symbol}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'account_fees',
                        'account_infos',
                        'balances',
                        'basket_manage',
                        'credits',
                        'deposit/new',
                        'funding/close',
                        'history',
                        'history/movements',
                        'key_info',
                        'margin_infos',
                        'mytrades',
                        'mytrades_funding',
                        'offer/cancel',
                        'offer/new',
                        'offer/status',
                        'offers',
                        'offers/hist',
                        'order/cancel',
                        'order/cancel/all',
                        'order/cancel/multi',
                        'order/cancel/replace',
                        'order/new',
                        'order/new/multi',
                        'order/status',
                        'orders',
                        'orders/hist',
                        'position/claim',
                        'position/close',
                        'positions',
                        'summary',
                        'taken_funds',
                        'total_taken_funds',
                        'transfer',
                        'unused_taken_funds',
                        'withdraw',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'maker' => 0.1 / 100,
                    'taker' => 0.2 / 100,
                    'tiers' => array(
                        'taker' => [
                            [0, 0.2 / 100],
                            [500000, 0.2 / 100],
                            [1000000, 0.2 / 100],
                            [2500000, 0.2 / 100],
                            [5000000, 0.2 / 100],
                            [7500000, 0.2 / 100],
                            [10000000, 0.18 / 100],
                            [15000000, 0.16 / 100],
                            [20000000, 0.14 / 100],
                            [25000000, 0.12 / 100],
                            [30000000, 0.1 / 100],
                        ],
                        'maker' => [
                            [0, 0.1 / 100],
                            [500000, 0.08 / 100],
                            [1000000, 0.06 / 100],
                            [2500000, 0.04 / 100],
                            [5000000, 0.02 / 100],
                            [7500000, 0],
                            [10000000, 0],
                            [15000000, 0],
                            [20000000, 0],
                            [25000000, 0],
                            [30000000, 0],
                        ],
                    ),
                ),
                'funding' => array(
                    'tierBased' => false, // true for tier-based/progressive
                    'percentage' => false, // fixed commission
                    // Actually deposit fees are free for larger deposits (> $1000 USD equivalent)
                    // these values below are deprecated, we should not hardcode fees and limits anymore
                    // to be reimplemented with bitfinex funding fees from their API or web endpoints
                    'deposit' => array(
                        'BTC' => 0.0004,
                        'IOTA' => 0.5,
                        'ETH' => 0.0027,
                        'BCH' => 0.0001,
                        'LTC' => 0.001,
                        'EOS' => 0.24279,
                        'XMR' => 0.04,
                        'SAN' => 0.99269,
                        'DASH' => 0.01,
                        'ETC' => 0.01,
                        'XRP' => 0.02,
                        'YYW' => 16.915,
                        'NEO' => 0,
                        'ZEC' => 0.001,
                        'BTG' => 0,
                        'OMG' => 0.14026,
                        'DATA' => 20.773,
                        'QASH' => 1.9858,
                        'ETP' => 0.01,
                        'QTUM' => 0.01,
                        'EDO' => 0.95001,
                        'AVT' => 1.3045,
                        'USDT' => 0,
                        'TRX' => 28.184,
                        'ZRX' => 1.9947,
                        'RCN' => 10.793,
                        'TNB' => 31.915,
                        'SNT' => 14.976,
                        'RLC' => 1.414,
                        'GNT' => 5.8952,
                        'SPK' => 10.893,
                        'REP' => 0.041168,
                        'BAT' => 6.1546,
                        'ELF' => 1.8753,
                        'FUN' => 32.336,
                        'SNG' => 18.622,
                        'AID' => 8.08,
                        'MNA' => 16.617,
                        'NEC' => 1.6504,
                        'XTZ' => 0.2,
                    ),
                    'withdraw' => array(
                        'BTC' => 0.0004,
                        'IOTA' => 0.5,
                        'ETH' => 0.0027,
                        'BCH' => 0.0001,
                        'LTC' => 0.001,
                        'EOS' => 0.24279,
                        'XMR' => 0.04,
                        'SAN' => 0.99269,
                        'DASH' => 0.01,
                        'ETC' => 0.01,
                        'XRP' => 0.02,
                        'YYW' => 16.915,
                        'NEO' => 0,
                        'ZEC' => 0.001,
                        'BTG' => 0,
                        'OMG' => 0.14026,
                        'DATA' => 20.773,
                        'QASH' => 1.9858,
                        'ETP' => 0.01,
                        'QTUM' => 0.01,
                        'EDO' => 0.95001,
                        'AVT' => 1.3045,
                        'USDT' => 20,
                        'TRX' => 28.184,
                        'ZRX' => 1.9947,
                        'RCN' => 10.793,
                        'TNB' => 31.915,
                        'SNT' => 14.976,
                        'RLC' => 1.414,
                        'GNT' => 5.8952,
                        'SPK' => 10.893,
                        'REP' => 0.041168,
                        'BAT' => 6.1546,
                        'ELF' => 1.8753,
                        'FUN' => 32.336,
                        'SNG' => 18.622,
                        'AID' => 8.08,
                        'MNA' => 16.617,
                        'NEC' => 1.6504,
                        'XTZ' => 0.2,
                    ),
                ),
            ),
            // todo rewrite for https://api-pub.bitfinex.com//v2/conf/pub:map:tx:method
            'commonCurrencies' => array(
                'ABS' => 'ABYSS',
                'AIO' => 'AION',
                'ALG' => 'ALGO', // https://github.com/ccxt/ccxt/issues/6034
                'AMP' => 'AMPL',
                'ATM' => 'ATMI',
                'ATO' => 'ATOM', // https://github.com/ccxt/ccxt/issues/5118
                'BAB' => 'BCH',
                'CTX' => 'CTXC',
                'DAD' => 'DADI',
                'DAT' => 'DATA',
                'DSH' => 'DASH',
                'DRK' => 'DRK',
                // https://github.com/ccxt/ccxt/issues/7399
                // https://coinmarketcap.com/currencies/pnetwork/
                // https://en.cryptonomist.ch/blog/eidoo/the-edo-to-pnt-upgrade-what-you-need-to-know-updated/
                'EDO' => 'PNT',
                'GSD' => 'GUSD',
                'HOT' => 'Hydro Protocol',
                'IOS' => 'IOST',
                'IOT' => 'IOTA',
                'IQX' => 'IQ',
                'MIT' => 'MITH',
                'MNA' => 'MANA',
                'NCA' => 'NCASH',
                'ORS' => 'ORS Group', // conflict with Origin Sport #3230
                'POY' => 'POLY',
                'QSH' => 'QASH',
                'QTM' => 'QTUM',
                'RBT' => 'RBTC',
                'SEE' => 'SEER',
                'SNG' => 'SNGLS',
                'SPK' => 'SPANK',
                'STJ' => 'STORJ',
                'TRI' => 'TRIO',
                'TSD' => 'TUSD',
                'YYW' => 'YOYOW',
                'UDC' => 'USDC',
                'UST' => 'USDT',
                'UTN' => 'UTNP',
                'VSY' => 'VSYS',
                'WAX' => 'WAXP',
                'XCH' => 'XCHF',
                'ZBT' => 'ZB',
            ),
            'exceptions' => array(
                'exact' => array(
                    'temporarily_unavailable' => '\\ccxt\\ExchangeNotAvailable', // Sorry, the service is temporarily unavailable. See https://www.bitfinex.com/ for more info.
                    'Order could not be cancelled.' => '\\ccxt\\OrderNotFound', // non-existent order
                    'No such order found.' => '\\ccxt\\OrderNotFound', // ?
                    'Order price must be positive.' => '\\ccxt\\InvalidOrder', // on price <= 0
                    'Could not find a key matching the given X-BFX-APIKEY.' => '\\ccxt\\AuthenticationError',
                    'Key price should be a decimal number, e.g. "123.456"' => '\\ccxt\\InvalidOrder', // on isNaN (price)
                    'Key amount should be a decimal number, e.g. "123.456"' => '\\ccxt\\InvalidOrder', // on isNaN (amount)
                    'ERR_RATE_LIMIT' => '\\ccxt\\RateLimitExceeded',
                    'Ratelimit' => '\\ccxt\\RateLimitExceeded',
                    'Nonce is too small.' => '\\ccxt\\InvalidNonce',
                    'No summary found.' => '\\ccxt\\ExchangeError', // fetchTradingFees (summary) endpoint can give this vague error message
                    'Cannot evaluate your available balance, please try again' => '\\ccxt\\ExchangeNotAvailable',
                    'Unknown symbol' => '\\ccxt\\BadSymbol',
                ),
                'broad' => array(
                    'Invalid X-BFX-SIGNATURE' => '\\ccxt\\AuthenticationError',
                    'This API key does not have permission' => '\\ccxt\\PermissionDenied', // authenticated but not authorized
                    'not enough exchange balance for ' => '\\ccxt\\InsufficientFunds', // when buying cost is greater than the available quote currency
                    'minimum size for ' => '\\ccxt\\InvalidOrder', // when amount below limits.amount.min
                    'Invalid order' => '\\ccxt\\InvalidOrder', // ?
                    'The available balance is only' => '\\ccxt\\InsufficientFunds', // array("status":"error","message":"Cannot withdraw 1.0027 ETH from your exchange wallet. The available balance is only 0.0 ETH. If you have limit orders, open positions, unused or active margin funding, this will decrease your available balance. To increase it, you can cancel limit orders or reduce/close your positions.","withdrawal_id":0,"fees":"0.0027")
                ),
            ),
            'precisionMode' => SIGNIFICANT_DIGITS,
            'options' => array(
                'currencyNames' => array(
                    'AGI' => 'agi',
                    'AID' => 'aid',
                    'AIO' => 'aio',
                    'ANT' => 'ant',
                    'AVT' => 'aventus', // #1811
                    'BAT' => 'bat',
                    // https://github.com/ccxt/ccxt/issues/5833
                    'BCH' => 'bab', // undocumented
                    // 'BCH' => 'bcash', // undocumented
                    'BCI' => 'bci',
                    'BFT' => 'bft',
                    'BSV' => 'bsv',
                    'BTC' => 'bitcoin',
                    'BTG' => 'bgold',
                    'CFI' => 'cfi',
                    'COMP' => 'comp',
                    'DAI' => 'dai',
                    'DADI' => 'dad',
                    'DASH' => 'dash',
                    'DATA' => 'datacoin',
                    'DTH' => 'dth',
                    'EDO' => 'eidoo', // #1811
                    'ELF' => 'elf',
                    'EOS' => 'eos',
                    'ETC' => 'ethereumc',
                    'ETH' => 'ethereum',
                    'ETP' => 'metaverse',
                    'FUN' => 'fun',
                    'GNT' => 'golem',
                    'IOST' => 'ios',
                    'IOTA' => 'iota',
                    // https://github.com/ccxt/ccxt/issues/5833
                    'LEO' => 'let', // ETH chain
                    // 'LEO' => 'les', // EOS chain
                    'LINK' => 'link',
                    'LRC' => 'lrc',
                    'LTC' => 'litecoin',
                    'LYM' => 'lym',
                    'MANA' => 'mna',
                    'MIT' => 'mit',
                    'MKR' => 'mkr',
                    'MTN' => 'mtn',
                    'NEO' => 'neo',
                    'ODE' => 'ode',
                    'OMG' => 'omisego',
                    'OMNI' => 'mastercoin',
                    'QASH' => 'qash',
                    'QTUM' => 'qtum', // #1811
                    'RCN' => 'rcn',
                    'RDN' => 'rdn',
                    'REP' => 'rep',
                    'REQ' => 'req',
                    'RLC' => 'rlc',
                    'SAN' => 'santiment',
                    'SNGLS' => 'sng',
                    'SNT' => 'status',
                    'SPANK' => 'spk',
                    'STORJ' => 'stj',
                    'TNB' => 'tnb',
                    'TRX' => 'trx',
                    'TUSD' => 'tsd',
                    'USD' => 'wire',
                    'USDC' => 'udc', // https://github.com/ccxt/ccxt/issues/5833
                    'UTK' => 'utk',
                    'USDT' => 'tetheruso', // Tether on Omni
                    // 'USDT' => 'tetheruse', // Tether on ERC20
                    // 'USDT' => 'tetherusl', // Tether on Liquid
                    // 'USDT' => 'tetherusx', // Tether on Tron
                    // 'USDT' => 'tetheruss', // Tether on EOS
                    'VEE' => 'vee',
                    'WAX' => 'wax',
                    'XLM' => 'xlm',
                    'XMR' => 'monero',
                    'XRP' => 'ripple',
                    'XVG' => 'xvg',
                    'YOYOW' => 'yoyow',
                    'ZEC' => 'zcash',
                    'ZRX' => 'zrx',
                    'XTZ' => 'xtz',
                ),
                'orderTypes' => array(
                    'limit' => 'exchange limit',
                    'market' => 'exchange market',
                ),
            ),
        ));
    }

    public function fetch_funding_fees($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostAccountFees ($params);
        $fees = $response['withdraw'];
        $withdraw = array();
        $ids = is_array($fees) ? array_keys($fees) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $code = $this->safe_currency_code($id);
            $withdraw[$code] = $this->safe_float($fees, $id);
        }
        return array(
            'info' => $response,
            'withdraw' => $withdraw,
            'deposit' => $withdraw,  // only for deposits of less than $1000
        );
    }

    public function fetch_trading_fees($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostSummary ($params);
        //
        //     {
        //         time => '2019-02-20T15:50:19.152000Z',
        //         trade_vol_30d => array(
        //             {
        //                 curr => 'Total (USD)',
        //                 vol => 0,
        //                 vol_maker => 0,
        //                 vol_BFX => 0,
        //                 vol_BFX_maker => 0,
        //                 vol_ETHFX => 0,
        //                 vol_ETHFX_maker => 0
        //             }
        //         ),
        //         fees_funding_30d => array(),
        //         fees_funding_total_30d => 0,
        //         fees_trading_30d => array(),
        //         fees_trading_total_30d => 0,
        //         maker_fee => 0.001,
        //         taker_fee => 0.002
        //     }
        //
        return array(
            'info' => $response,
            'maker' => $this->safe_float($response, 'maker_fee'),
            'taker' => $this->safe_float($response, 'taker_fee'),
        );
    }

    public function fetch_markets($params = array ()) {
        $ids = $this->publicGetSymbols ();
        $details = $this->publicGetSymbolsDetails ();
        $result = array();
        for ($i = 0; $i < count($details); $i++) {
            $market = $details[$i];
            $id = $this->safe_string($market, 'pair');
            if (!$this->in_array($id, $ids)) {
                continue;
            }
            $id = strtoupper($id);
            $baseId = null;
            $quoteId = null;
            if (mb_strpos($id, ':') !== false) {
                $parts = explode(':', $id);
                $baseId = $parts[0];
                $quoteId = $parts[1];
            } else {
                $baseId = mb_substr($id, 0, 3 - 0);
                $quoteId = mb_substr($id, 3, 6 - 3);
            }
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'price' => $this->safe_integer($market, 'price_precision'),
                // https://docs.bitfinex.com/docs/introduction#amount-$precision
                // The amount field allows up to 8 decimals.
                // Anything exceeding this will be rounded to the 8th decimal.
                'amount' => 8,
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($market, 'minimum_order_size'),
                    'max' => $this->safe_float($market, 'maximum_order_size'),
                ),
                'price' => array(
                    'min' => pow(10, -$precision['price']),
                    'max' => pow(10, $precision['price']),
                ),
            );
            $limits['cost'] = array(
                'min' => $limits['amount']['min'] * $limits['price']['min'],
                'max' => null,
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function amount_to_precision($symbol, $amount) {
        // https://docs.bitfinex.com/docs/introduction#$amount-precision
        // The $amount field allows up to 8 decimals.
        // Anything exceeding this will be rounded to the 8th decimal.
        return $this->decimal_to_precision($amount, TRUNCATE, $this->markets[$symbol]['precision']['amount'], DECIMAL_PLACES);
    }

    public function price_to_precision($symbol, $price) {
        $price = $this->decimal_to_precision($price, ROUND, $this->markets[$symbol]['precision']['price'], $this->precisionMode);
        // https://docs.bitfinex.com/docs/introduction#$price-precision
        // The precision level of all trading prices is based on significant figures.
        // All pairs on Bitfinex use up to 5 significant digits and up to 8 decimals (e.g. 1.2345, 123.45, 1234.5, 0.00012345).
        // Prices submit with a precision larger than 5 will be cut by the API.
        return $this->decimal_to_precision($price, TRUNCATE, 8, DECIMAL_PLACES);
    }

    public function calculate_fee($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $rate = $market[$takerOrMaker];
        $cost = $amount * $rate;
        $key = 'quote';
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
        }
        $code = $market[$key];
        $currency = $this->safe_value($this->currencies, $code);
        if ($currency !== null) {
            $precision = $this->safe_integer($currency, 'precision');
            if ($precision !== null) {
                $cost = floatval($this->currency_to_precision($code, $cost));
            }
        }
        return array(
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => $cost,
        );
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $balanceType = $this->safe_string($params, 'type', 'exchange');
        $query = $this->omit($params, 'type');
        $response = $this->privatePostBalances ($query);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            if ($balance['type'] === $balanceType) {
                $currencyId = $this->safe_string($balance, 'currency');
                $code = $this->safe_currency_code($currencyId);
                // bitfinex had BCH previously, now it's BAB, but the old
                // BCH symbol is kept for backward-compatibility
                // we need a workaround here so that the old BCH $balance
                // would not override the new BAB $balance (BAB is unified to BCH)
                // https://github.com/ccxt/ccxt/issues/4989
                if (!(is_array($result) && array_key_exists($code, $result))) {
                    $account = $this->account();
                    $account['free'] = $this->safe_float($balance, 'available');
                    $account['total'] = $this->safe_float($balance, 'amount');
                    $result[$code] = $account;
                }
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['limit_bids'] = $limit;
            $request['limit_asks'] = $limit;
        }
        $response = $this->publicGetBookSymbol (array_merge($request, $params));
        return $this->parse_order_book($response, null, 'bids', 'asks', 'price', 'amount');
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickers ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $this->parse_ticker($response[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $ticker = $this->publicGetPubtickerSymbol (array_merge($request, $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->safe_float($ticker, 'timestamp');
        if ($timestamp !== null) {
            $timestamp *= 1000;
        }
        $timestamp = intval($timestamp);
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        } else if (is_array($ticker) && array_key_exists('pair', $ticker)) {
            $marketId = $this->safe_string($ticker, 'pair');
            if ($marketId !== null) {
                if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                    $market = $this->markets_by_id[$marketId];
                    $symbol = $market['symbol'];
                } else {
                    $baseId = mb_substr($marketId, 0, 3 - 0);
                    $quoteId = mb_substr($marketId, 3, 6 - 3);
                    $base = $this->safe_currency_code($baseId);
                    $quote = $this->safe_currency_code($quoteId);
                    $symbol = $base . '/' . $quote;
                }
            }
        }
        $last = $this->safe_float($ticker, 'last_price');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
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
            'average' => $this->safe_float($ticker, 'mid'),
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market) {
        $id = $this->safe_string($trade, 'tid');
        $timestamp = $this->safe_float($trade, 'timestamp');
        if ($timestamp !== null) {
            $timestamp = intval($timestamp) * 1000;
        }
        $type = null;
        $side = $this->safe_string_lower($trade, 'type');
        $orderId = $this->safe_string($trade, 'order_id');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $fee = null;
        if (is_array($trade) && array_key_exists('fee_amount', $trade)) {
            $feeCost = -$this->safe_float($trade, 'fee_amount');
            $feeCurrencyId = $this->safe_string($trade, 'fee_currency');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $market['symbol'],
            'type' => $type,
            'order' => $orderId,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = 50, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'limit_trades' => $limit,
        );
        if ($since !== null) {
            $request['timestamp'] = intval($since / 1000);
        }
        $response = $this->publicGetTradesSymbol (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a `$symbol` argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit_trades'] = $limit;
        }
        if ($since !== null) {
            $request['timestamp'] = intval($since / 1000);
        }
        $response = $this->privatePostMytrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
            'side' => $side,
            'amount' => $this->amount_to_precision($symbol, $amount),
            'type' => $this->safe_string($this->options['orderTypes'], $type, $type),
            'ocoorder' => false,
            'buy_price_oco' => 0,
            'sell_price_oco' => 0,
        );
        if ($type === 'market') {
            $request['price'] = (string) $this->nonce();
        } else {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostOrderNew (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function edit_order($id, $symbol, $type, $side, $amount = null, $price = null, $params = array ()) {
        $this->load_markets();
        $order = array(
            'order_id' => intval($id),
        );
        if ($price !== null) {
            $order['price'] = $this->price_to_precision($symbol, $price);
        }
        if ($amount !== null) {
            $order['amount'] = $this->number_to_string($amount);
        }
        if ($symbol !== null) {
            $order['symbol'] = $this->market_id($symbol);
        }
        if ($side !== null) {
            $order['side'] = $side;
        }
        if ($type !== null) {
            $order['type'] = $this->safe_string($this->options['orderTypes'], $type, $type);
        }
        $response = $this->privatePostOrderCancelReplace (array_merge($order, $params));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => intval($id),
        );
        return $this->privatePostOrderCancel (array_merge($request, $params));
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        return $this->privatePostOrderCancelAll ($params);
    }

    public function parse_order($order, $market = null) {
        $side = $this->safe_string($order, 'side');
        $open = $this->safe_value($order, 'is_live');
        $canceled = $this->safe_value($order, 'is_cancelled');
        $status = null;
        if ($open) {
            $status = 'open';
        } else if ($canceled) {
            $status = 'canceled';
        } else {
            $status = 'closed';
        }
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string_upper($order, 'symbol');
            if ($marketId !== null) {
                if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                    $market = $this->markets_by_id[$marketId];
                }
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $orderType = $order['type'];
        $exchange = mb_strpos($orderType, 'exchange ') !== false;
        if ($exchange) {
            $parts = explode(' ', $order['type']);
            $orderType = $parts[1];
        }
        $timestamp = $this->safe_float($order, 'timestamp');
        if ($timestamp !== null) {
            $timestamp = intval($timestamp) * 1000;
        }
        $id = $this->safe_string($order, 'id');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $orderType,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $this->safe_float($order, 'price'),
            'stopPrice' => null,
            'average' => $this->safe_float($order, 'avg_execution_price'),
            'amount' => $this->safe_float($order, 'original_amount'),
            'remaining' => $this->safe_float($order, 'remaining_amount'),
            'filled' => $this->safe_float($order, 'executed_amount'),
            'status' => $status,
            'fee' => null,
            'cost' => null,
            'trades' => null,
        );
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($symbol !== null) {
            if (!(is_array($this->markets) && array_key_exists($symbol, $this->markets))) {
                throw new ExchangeError($this->id . ' has no $symbol ' . $symbol);
            }
        }
        $response = $this->privatePostOrders ($params);
        $orders = $this->parse_orders($response, null, $since, $limit);
        if ($symbol !== null) {
            $orders = $this->filter_by($orders, 'symbol', $symbol);
        }
        return $orders;
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privatePostOrdersHist (array_merge($request, $params));
        $orders = $this->parse_orders($response, null, $since, $limit);
        if ($symbol !== null) {
            $orders = $this->filter_by($orders, 'symbol', $symbol);
        }
        $orders = $this->filter_by_array($orders, 'status', array( 'closed', 'canceled' ), false);
        return $orders;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => intval($id),
        );
        $response = $this->privatePostOrderStatus (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1457539800000,
        //         0.02594,
        //         0.02594,
        //         0.02594,
        //         0.02594,
        //         0.1
        //     )
        //
        return array(
            $this->safe_integer($ohlcv, 0),
            $this->safe_float($ohlcv, 1),
            $this->safe_float($ohlcv, 3),
            $this->safe_float($ohlcv, 4),
            $this->safe_float($ohlcv, 2),
            $this->safe_float($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($limit === null) {
            $limit = 100;
        }
        $market = $this->market($symbol);
        $v2id = 't' . $market['id'];
        $request = array(
            'symbol' => $v2id,
            'timeframe' => $this->timeframes[$timeframe],
            'sort' => 1,
            'limit' => $limit,
        );
        if ($since !== null) {
            $request['start'] = $since;
        }
        $response = $this->v2GetCandlesTradeTimeframeSymbolHist (array_merge($request, $params));
        //
        //     [
        //         [1457539800000,0.02594,0.02594,0.02594,0.02594,0.1],
        //         [1457547300000,0.02577,0.02577,0.02577,0.02577,0.01],
        //         [1457550240000,0.0255,0.0253,0.0255,0.0252,3.2640000000000002],
        //     ]
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function get_currency_name($code) {
        // todo rewrite for https://api-pub.bitfinex.com//v2/conf/pub:map:tx:method
        if (is_array($this->options['currencyNames']) && array_key_exists($code, $this->options['currencyNames'])) {
            return $this->options['currencyNames'][$code];
        }
        throw new NotSupported($this->id . ' ' . $code . ' not supported for withdrawal');
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $request = array(
            'renew' => 1,
        );
        $response = $this->fetch_deposit_address($code, array_merge($request, $params));
        return $response;
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        // todo rewrite for https://api-pub.bitfinex.com//v2/conf/pub:map:tx:method
        $name = $this->get_currency_name($code);
        $request = array(
            'method' => $name,
            'wallet_name' => 'exchange',
            'renew' => 0, // a value of 1 will generate a new $address
        );
        $response = $this->privatePostDepositNew (array_merge($request, $params));
        $address = $this->safe_value($response, 'address');
        $tag = null;
        if (is_array($response) && array_key_exists('address_pool', $response)) {
            $tag = $address;
            $address = $response['address_pool'];
        }
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currencyId = $this->safe_string($params, 'currency');
        $query = $this->omit($params, 'currency');
        $currency = null;
        if ($currencyId === null) {
            if ($code === null) {
                throw new ArgumentsRequired($this->id . ' fetchTransactions() requires a $currency `$code` argument or a `$currency` parameter');
            } else {
                $currency = $this->currency($code);
                $currencyId = $currency['id'];
            }
        }
        $query['currency'] = $currencyId;
        if ($since !== null) {
            $query['since'] = intval($since / 1000);
        }
        $response = $this->privatePostHistoryMovements (array_merge($query, $params));
        //
        //     array(
        //         {
        //             "id":581183,
        //             "txid" => 123456,
        //             "$currency":"BTC",
        //             "method":"BITCOIN",
        //             "type":"WITHDRAWAL",
        //             "amount":".01",
        //             "description":"3QXYWgRGX2BPYBpUDBssGbeWEa5zq6snBZ, offchain transfer ",
        //             "address":"3QXYWgRGX2BPYBpUDBssGbeWEa5zq6snBZ",
        //             "status":"COMPLETED",
        //             "timestamp":"1443833327.0",
        //             "timestamp_created" => "1443833327.1",
        //             "fee" => 0.1,
        //         }
        //     )
        //
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // crypto
        //
        //     {
        //         "id" => 12042490,
        //         "fee" => "-0.02",
        //         "txid" => "EA5B5A66000B66855865EFF2494D7C8D1921FCBE996482157EBD749F2C85E13D",
        //         "$type" => "DEPOSIT",
        //         "amount" => "2099.849999",
        //         "method" => "RIPPLE",
        //         "$status" => "COMPLETED",
        //         "address" => "2505189261",
        //         "$currency" => "XRP",
        //         "$timestamp" => "1551730524.0",
        //         "description" => "EA5B5A66000B66855865EFF2494D7C8D1921FCBE996482157EBD749F2C85E13D",
        //         "timestamp_created" => "1551730523.0"
        //     }
        //
        // fiat
        //
        //     {
        //         "id" => 12725095,
        //         "fee" => "-60.0",
        //         "txid" => null,
        //         "$type" => "WITHDRAWAL",
        //         "amount" => "9943.0",
        //         "method" => "WIRE",
        //         "$status" => "SENDING",
        //         "address" => null,
        //         "$currency" => "EUR",
        //         "$timestamp" => "1561802484.0",
        //         "description" => "Name => bob, AccountAddress => some address, Account => someaccountno, Bank => bank address, SWIFT => foo, Country => UK, Details of Payment => withdrawal name, Intermediary Bank Name => , Intermediary Bank Address => , Intermediary Bank City => , Intermediary Bank Country => , Intermediary Bank Account => , Intermediary Bank SWIFT => , Fee => -60.0",
        //         "timestamp_created" => "1561716066.0"
        //     }
        //
        $timestamp = $this->safe_float($transaction, 'timestamp_created');
        if ($timestamp !== null) {
            $timestamp = intval($timestamp * 1000);
        }
        $updated = $this->safe_float($transaction, 'timestamp');
        if ($updated !== null) {
            $updated = intval($updated * 1000);
        }
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $type = $this->safe_string_lower($transaction, 'type'); // DEPOSIT or WITHDRAWAL
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $feeCost = $this->safe_float($transaction, 'fee');
        if ($feeCost !== null) {
            $feeCost = abs($feeCost);
        }
        return array(
            'info' => $transaction,
            'id' => $this->safe_string($transaction, 'id'),
            'txid' => $this->safe_string($transaction, 'txid'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $this->safe_string($transaction, 'address'), // todo => this is actually the tag for XRP transfers (the address is missing)
            'tag' => null, // refix it properly for the tag from description
            'type' => $type,
            'amount' => $this->safe_float($transaction, 'amount'),
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => array(
                'currency' => $code,
                'cost' => $feeCost,
                'rate' => null,
            ),
        );
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'SENDING' => 'pending',
            'CANCELED' => 'canceled',
            'ZEROCONFIRMED' => 'failed', // ZEROCONFIRMED happens e.g. in a double spend attempt (I had one in my movements!)
            'COMPLETED' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        // todo rewrite for https://api-pub.bitfinex.com//v2/conf/pub:map:tx:method
        $name = $this->get_currency_name($code);
        $request = array(
            'withdraw_type' => $name,
            'walletselected' => 'exchange',
            'amount' => $this->number_to_string($amount),
            'address' => $address,
        );
        if ($tag !== null) {
            $request['payment_id'] = $tag;
        }
        $responses = $this->privatePostWithdraw (array_merge($request, $params));
        $response = $responses[0];
        $id = $this->safe_string($response, 'withdrawal_id');
        $message = $this->safe_string($response, 'message');
        $errorMessage = $this->find_broadly_matched_key($this->exceptions['broad'], $message);
        if ($id === 0) {
            if ($errorMessage !== null) {
                $ExceptionClass = $this->exceptions['broad'][$errorMessage];
                throw new $ExceptionClass($this->id . ' ' . $message);
            }
            throw new ExchangeError($this->id . ' withdraw returned an $id of zero => ' . $this->json($response));
        }
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function fetch_positions($symbols = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostPositions ($params);
        //
        //     array(
        //         {
        //             "id":943715,
        //             "symbol":"btcusd",
        //             "status":"ACTIVE",
        //             "base":"246.94",
        //             "amount":"1.0",
        //             "timestamp":"1444141857.0",
        //             "swap":"0.0",
        //             "pl":"-2.22042"
        //         }
        //     )
        //
        // todo unify parsePosition/parsePositions
        return $response;
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/' . $this->implode_params($path, $params);
        if ($api === 'v2') {
            $request = '/' . $api . $request;
        } else {
            $request = '/' . $this->version . $request;
        }
        $query = $this->omit($params, $this->extract_params($path));
        $url = $this->urls['api'][$api] . $request;
        if (($api === 'public') || (mb_strpos($path, '/hist') !== false)) {
            if ($query) {
                $suffix = '?' . $this->urlencode($query);
                $url .= $suffix;
                $request .= $suffix;
            }
        }
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $query = array_merge(array(
                'nonce' => (string) $nonce,
                'request' => $request,
            ), $query);
            $body = $this->json($query);
            $payload = base64_encode($body);
            $secret = $this->encode($this->secret);
            $signature = $this->hmac($payload, $secret, 'sha384');
            $headers = array(
                'X-BFX-APIKEY' => $this->apiKey,
                'X-BFX-PAYLOAD' => $this->decode($payload),
                'X-BFX-SIGNATURE' => $signature,
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        if ($code >= 400) {
            if ($body[0] === '{') {
                $feedback = $this->id . ' ' . $body;
                $message = $this->safe_string_2($response, 'message', 'error');
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
                throw new ExchangeError($feedback); // unknown $message
            }
        }
    }
}
