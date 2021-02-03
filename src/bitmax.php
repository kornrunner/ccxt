<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;

class bitmax extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitmax',
            'name' => 'BitMax',
            'countries' => array( 'CN' ), // China
            'rateLimit' => 500,
            // new metainfo interface
            'has' => array(
                'CORS' => false,
                'fetchMarkets' => true,
                'fetchCurrencies' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchOHLCV' => true,
                'fetchTrades' => true,
                'fetchAccounts' => true,
                'fetchBalance' => true,
                'createOrder' => true,
                'cancelOrder' => true,
                'cancelAllOrders' => true,
                'fetchDepositAddress' => true,
                'fetchTransactions' => true,
                'fetchDeposits' => true,
                'fetchWithdrawals' => true,
                'fetchOrder' => true,
                'fetchOrders' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
            ),
            'timeframes' => array(
                '1m' => '1',
                '5m' => '5',
                '15m' => '15',
                '30m' => '30',
                '1h' => '60',
                '2h' => '120',
                '4h' => '240',
                '6h' => '360',
                '12h' => '720',
                '1d' => '1d',
                '1w' => '1w',
                '1M' => '1m',
            ),
            'version' => 'v1',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/66820319-19710880-ef49-11e9-8fbe-16be62a11992.jpg',
                'api' => 'https://bitmax.io',
                'test' => 'https://bitmax-test.io',
                'www' => 'https://bitmax.io',
                'doc' => array(
                    'https://bitmax-exchange.github.io/bitmax-pro-api/#bitmax-pro-api-documentation',
                ),
                'fees' => 'https://bitmax.io/#/feeRate/tradeRate',
                'referral' => 'https://bitmax.io/#/register?inviteCode=EL6BXBQM',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'assets',
                        'products',
                        'ticker',
                        'barhist/info',
                        'barhist',
                        'depth',
                        'trades',
                        'cash/assets', // not documented
                        'cash/products', // not documented
                        'margin/assets', // not documented
                        'margin/products', // not documented
                        'futures/collateral',
                        'futures/contracts',
                        'futures/ref-px',
                        'futures/market-data',
                        'futures/funding-rates',
                    ),
                ),
                'accountCategory' => array(
                    'get' => array(
                        'balance',
                        'order/open',
                        'order/status',
                        'order/hist/current',
                        'risk',
                    ),
                    'post' => array(
                        'order',
                        'order/batch',
                    ),
                    'delete' => array(
                        'order',
                        'order/all',
                        'order/batch',
                    ),
                ),
                'accountGroup' => array(
                    'get' => array(
                        'cash/balance',
                        'margin/balance',
                        'margin/risk',
                        'transfer',
                        'futures/collateral-balance',
                        'futures/position',
                        'futures/risk',
                        'futures/funding-payments',
                        'order/hist',
                    ),
                    'post' => array(
                        'futures/transfer/deposit',
                        'futures/transfer/withdraw',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'info',
                        'wallet/transactions',
                        'wallet/deposit/address', // not documented
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.002,
                    'maker' => 0.002,
                ),
            ),
            'precisionMode' => TICK_SIZE,
            'options' => array(
                'account-category' => 'cash', // 'cash'/'margin'/'futures'
                'account-group' => null,
                'fetchClosedOrders' => array(
                    'method' => 'accountGroupGetOrderHist', // 'accountGroupGetAccountCategoryOrderHistCurrent'
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    // not documented
                    '1900' => '\\ccxt\\BadRequest', // array("code":1900,"message":"Invalid Http Request Input")
                    '2100' => '\\ccxt\\AuthenticationError', // array("code":2100,"message":"ApiKeyFailure")
                    '5002' => '\\ccxt\\BadSymbol', // array("code":5002,"message":"Invalid Symbol")
                    '6001' => '\\ccxt\\BadSymbol', // array("code":6001,"message":"Trading is disabled on symbol.")
                    '6010' => '\\ccxt\\InsufficientFunds', // array('code' => 6010, 'message' => 'Not enough balance.')
                    '60060' => '\\ccxt\\InvalidOrder', // array( 'code' => 60060, 'message' => 'The order is already filled or canceled.' )
                    '600503' => '\\ccxt\\InvalidOrder', // array("code":600503,"message":"Notional is too small.")
                    // documented
                    '100001' => '\\ccxt\\BadRequest', // INVALID_HTTP_INPUT Http request is invalid
                    '100002' => '\\ccxt\\BadRequest', // DATA_NOT_AVAILABLE Some required data is missing
                    '100003' => '\\ccxt\\BadRequest', // KEY_CONFLICT The same key exists already
                    '100004' => '\\ccxt\\BadRequest', // INVALID_REQUEST_DATA The HTTP request contains invalid field or argument
                    '100005' => '\\ccxt\\BadRequest', // INVALID_WS_REQUEST_DATA Websocket request contains invalid field or argument
                    '100006' => '\\ccxt\\BadRequest', // INVALID_ARGUMENT The arugment is invalid
                    '100007' => '\\ccxt\\BadRequest', // ENCRYPTION_ERROR Something wrong with data encryption
                    '100008' => '\\ccxt\\BadSymbol', // SYMBOL_ERROR Symbol does not exist or not valid for the request
                    '100009' => '\\ccxt\\AuthenticationError', // AUTHORIZATION_NEEDED Authorization is require for the API access or request
                    '100010' => '\\ccxt\\BadRequest', // INVALID_OPERATION The action is invalid or not allowed for the account
                    '100011' => '\\ccxt\\BadRequest', // INVALID_TIMESTAMP Not a valid timestamp
                    '100012' => '\\ccxt\\BadRequest', // INVALID_STR_FORMAT String format does not
                    '100013' => '\\ccxt\\BadRequest', // INVALID_NUM_FORMAT Invalid number input
                    '100101' => '\\ccxt\\ExchangeError', // UNKNOWN_ERROR Some unknown error
                    '150001' => '\\ccxt\\BadRequest', // INVALID_JSON_FORMAT Require a valid json object
                    '200001' => '\\ccxt\\AuthenticationError', // AUTHENTICATION_FAILED Authorization failed
                    '200002' => '\\ccxt\\ExchangeError', // TOO_MANY_ATTEMPTS Tried and failed too many times
                    '200003' => '\\ccxt\\ExchangeError', // ACCOUNT_NOT_FOUND Account not exist
                    '200004' => '\\ccxt\\ExchangeError', // ACCOUNT_NOT_SETUP Account not setup properly
                    '200005' => '\\ccxt\\ExchangeError', // ACCOUNT_ALREADY_EXIST Account already exist
                    '200006' => '\\ccxt\\ExchangeError', // ACCOUNT_ERROR Some error related with error
                    '200007' => '\\ccxt\\ExchangeError', // CODE_NOT_FOUND
                    '200008' => '\\ccxt\\ExchangeError', // CODE_EXPIRED Code expired
                    '200009' => '\\ccxt\\ExchangeError', // CODE_MISMATCH Code does not match
                    '200010' => '\\ccxt\\AuthenticationError', // PASSWORD_ERROR Wrong assword
                    '200011' => '\\ccxt\\ExchangeError', // CODE_GEN_FAILED Do not generate required code promptly
                    '200012' => '\\ccxt\\ExchangeError', // FAKE_COKE_VERIFY
                    '200013' => '\\ccxt\\ExchangeError', // SECURITY_ALERT Provide security alert message
                    '200014' => '\\ccxt\\PermissionDenied', // RESTRICTED_ACCOUNT Account is restricted for certain activity, such as trading, or withdraw.
                    '200015' => '\\ccxt\\PermissionDenied', // PERMISSION_DENIED No enough permission for the operation
                    '300001' => '\\ccxt\\InvalidOrder', // INVALID_PRICE Order price is invalid
                    '300002' => '\\ccxt\\InvalidOrder', // INVALID_QTY Order size is invalid
                    '300003' => '\\ccxt\\InvalidOrder', // INVALID_SIDE Order side is invalid
                    '300004' => '\\ccxt\\InvalidOrder', // INVALID_NOTIONAL Notional is too small or too large
                    '300005' => '\\ccxt\\InvalidOrder', // INVALID_TYPE Order typs is invalid
                    '300006' => '\\ccxt\\InvalidOrder', // INVALID_ORDER_ID Order id is invalid
                    '300007' => '\\ccxt\\InvalidOrder', // INVALID_TIME_IN_FORCE Time In Force in order request is invalid
                    '300008' => '\\ccxt\\InvalidOrder', // INVALID_ORDER_PARAMETER Some order parameter is invalid
                    '300009' => '\\ccxt\\InvalidOrder', // TRADING_VIOLATION Trading violation on account or asset
                    '300011' => '\\ccxt\\InsufficientFunds', // INVALID_BALANCE No enough account or asset balance for the trading
                    '300012' => '\\ccxt\\BadSymbol', // INVALID_PRODUCT Not a valid product supported by exchange
                    '300013' => '\\ccxt\\InvalidOrder', // INVALID_BATCH_ORDER Some or all orders are invalid in batch order request
                    '300020' => '\\ccxt\\InvalidOrder', // TRADING_RESTRICTED There is some trading restriction on account or asset
                    '300021' => '\\ccxt\\InvalidOrder', // TRADING_DISABLED Trading is disabled on account or asset
                    '300031' => '\\ccxt\\InvalidOrder', // NO_MARKET_PRICE No market price for market type order trading
                    '310001' => '\\ccxt\\InsufficientFunds', // INVALID_MARGIN_BALANCE No enough margin balance
                    '310002' => '\\ccxt\\InvalidOrder', // INVALID_MARGIN_ACCOUNT Not a valid account for margin trading
                    '310003' => '\\ccxt\\InvalidOrder', // MARGIN_TOO_RISKY Leverage is too high
                    '310004' => '\\ccxt\\BadSymbol', // INVALID_MARGIN_ASSET This asset does not support margin trading
                    '310005' => '\\ccxt\\InvalidOrder', // INVALID_REFERENCE_PRICE There is no valid reference price
                    '510001' => '\\ccxt\\ExchangeError', // SERVER_ERROR Something wrong with server.
                    '900001' => '\\ccxt\\ExchangeError', // HUMAN_CHALLENGE Human change do not pass
                ),
                'broad' => array(),
            ),
            'commonCurrencies' => array(
                'BTCBEAR' => 'BEAR',
                'BTCBULL' => 'BULL',
            ),
        ));
    }

    public function get_account($params = array ()) {
        // get current or provided bitmax sub-$account
        $account = $this->safe_value($params, 'account', $this->options['account']);
        return strtolower($account).capitalize ();
    }

    public function fetch_currencies($params = array ()) {
        $assets = $this->publicGetAssets ($params);
        //
        //     {
        //         "$code":0,
        //         "data":array(
        //             array(
        //                 "assetCode" : "LTCBULL",
        //                 "assetName" : "3X Long LTC Token",
        //                 "precisionScale" : 9,
        //                 "nativeScale" : 4,
        //                 "withdrawalFee" : "0.2",
        //                 "minWithdrawalAmt" : "1.0",
        //                 "$status" : "Normal"
        //             ),
        //         )
        //     }
        //
        $margin = $this->publicGetMarginAssets ($params);
        //
        //     {
        //         "$code":0,
        //         "data":array(
        //             {
        //                 "assetCode":"BTT",
        //                 "borrowAssetCode":"BTT-B",
        //                 "interestAssetCode":"BTT-I",
        //                 "nativeScale":0,
        //                 "numConfirmations":1,
        //                 "withdrawFee":"100.0",
        //                 "minWithdrawalAmt":"1000.0",
        //                 "statusCode":"Normal",
        //                 "statusMessage":"",
        //                 "interestRate":"0.001"
        //             }
        //         )
        //     }
        //
        $cash = $this->publicGetCashAssets ($params);
        //
        //     {
        //         "$code":0,
        //         "data":array(
        //             {
        //                 "assetCode":"LTCBULL",
        //                 "nativeScale":4,
        //                 "numConfirmations":20,
        //                 "withdrawFee":"0.2",
        //                 "minWithdrawalAmt":"1.0",
        //                 "statusCode":"Normal",
        //                 "statusMessage":""
        //             }
        //         )
        //     }
        //
        $assetsData = $this->safe_value($assets, 'data', array());
        $marginData = $this->safe_value($margin, 'data', array());
        $cashData = $this->safe_value($cash, 'data', array());
        $assetsById = $this->index_by($assetsData, 'assetCode');
        $marginById = $this->index_by($marginData, 'assetCode');
        $cashById = $this->index_by($cashData, 'assetCode');
        $dataById = $this->deep_extend($assetsById, $marginById, $cashById);
        $ids = is_array($dataById) ? array_keys($dataById) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $currency = $dataById[$id];
            $code = $this->safe_currency_code($id);
            $precision = $this->safe_integer_2($currency, 'precisionScale', 'nativeScale');
            // why would the exchange API have different names for the same field
            $fee = $this->safe_float_2($currency, 'withdrawFee', 'withdrawalFee');
            $status = $this->safe_string_2($currency, 'status', 'statusCode');
            $active = ($status === 'Normal');
            $margin = (is_array($currency) && array_key_exists('borrowAssetCode', $currency));
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'type' => null,
                'margin' => $margin,
                'name' => $this->safe_string($currency, 'assetName'),
                'active' => $active,
                'fee' => $fee,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => $this->safe_float($currency, 'minWithdrawalAmt'),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $products = $this->publicGetProducts ($params);
        //
        //     {
        //         "code":0,
        //         "data":array(
        //             array(
        //                 "$symbol":"LBA/BTC",
        //                 "baseAsset":"LBA",
        //                 "quoteAsset":"BTC",
        //                 "$status":"Normal",
        //                 "minNotional":"0.000625",
        //                 "maxNotional":"6.25",
        //                 "marginTradable":false,
        //                 "commissionType":"Quote",
        //                 "commissionReserveRate":"0.001",
        //                 "tickSize":"0.000000001",
        //                 "lotSize":"1"
        //             ),
        //         )
        //     }
        //
        $cash = $this->publicGetCashProducts ($params);
        //
        //     {
        //         "code":0,
        //         "data":array(
        //             {
        //                 "$symbol":"QTUM/BTC",
        //                 "domain":"BTC",
        //                 "tradingStartTime":1569506400000,
        //                 "collapseDecimals":"0.0001,0.000001,0.00000001",
        //                 "minQty":"0.000000001",
        //                 "maxQty":"1000000000",
        //                 "minNotional":"0.000625",
        //                 "maxNotional":"12.5",
        //                 "statusCode":"Normal",
        //                 "statusMessage":"",
        //                 "tickSize":"0.00000001",
        //                 "useTick":false,
        //                 "lotSize":"0.1",
        //                 "useLot":false,
        //                 "commissionType":"Quote",
        //                 "commissionReserveRate":"0.001",
        //                 "qtyScale":1,
        //                 "priceScale":8,
        //                 "notionalScale":4
        //             }
        //         )
        //     }
        //
        $futures = $this->publicGetFuturesContracts ($params);
        //
        //     {
        //         "code":0,
        //         "data":array(
        //             {
        //                 "$symbol":"BTC-PERP",
        //                 "tradingStartTime":1579701600000,
        //                 "collapseDecimals":"1,0.1,0.01",
        //                 "minQty":"0.000000001",
        //                 "maxQty":"1000000000",
        //                 "minNotional":"5",
        //                 "maxNotional":"1000000",
        //                 "statusCode":"Normal",
        //                 "statusMessage":"",
        //                 "tickSize":"0.25",
        //                 "lotSize":"0.0001",
        //                 "priceScale":2,
        //                 "qtyScale":4,
        //                 "notionalScale":2
        //             }
        //         )
        //     }
        //
        $productsData = $this->safe_value($products, 'data', array());
        $productsById = $this->index_by($productsData, 'symbol');
        $cashData = $this->safe_value($cash, 'data', array());
        $futuresData = $this->safe_value($futures, 'data', array());
        $cashAndFuturesData = $this->array_concat($cashData, $futuresData);
        $cashAndFuturesById = $this->index_by($cashAndFuturesData, 'symbol');
        $dataById = $this->deep_extend($productsById, $cashAndFuturesById);
        $ids = is_array($dataById) ? array_keys($dataById) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $dataById[$id];
            $baseId = $this->safe_string($market, 'baseAsset');
            $quoteId = $this->safe_string($market, 'quoteAsset');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $precision = array(
                'amount' => $this->safe_float($market, 'lotSize'),
                'price' => $this->safe_float($market, 'tickSize'),
            );
            $status = $this->safe_string($market, 'status');
            $active = ($status === 'Normal');
            $type = (is_array($market) && array_key_exists('useLot', $market)) ? 'spot' : 'future';
            $spot = ($type === 'spot');
            $future = ($type === 'future');
            $symbol = $id;
            if (!$future) {
                $symbol = $base . '/' . $quote;
            }
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'type' => $type,
                'spot' => $spot,
                'future' => $future,
                'active' => $active,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($market, 'minQty'),
                        'max' => $this->safe_float($market, 'maxQty'),
                    ),
                    'price' => array(
                        'min' => $this->safe_float($market, 'tickSize'),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => $this->safe_float($market, 'minNotional'),
                        'max' => $this->safe_float($market, 'maxNotional'),
                    ),
                ),
            );
        }
        return $result;
    }

    public function calculate_fee($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        // TODO => fee calculation here is incorrect, we need to support tiered fee calculation.
        $market = $this->markets[$symbol];
        $key = 'quote';
        $rate = $market[$takerOrMaker];
        $cost = $amount * $rate;
        $precision = $market['precision']['price'];
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
            $precision = $market['precision']['amount'];
        }
        $cost = $this->decimal_to_precision($cost, ROUND, $precision, $this->precisionMode);
        return array(
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => floatval($cost),
        );
    }

    public function fetch_accounts($params = array ()) {
        $accountGroup = $this->safe_string($this->options, 'account-group');
        $response = null;
        if ($accountGroup === null) {
            $response = $this->privateGetInfo ($params);
            //
            //     {
            //         "code":0,
            //         "$data":{
            //             "email":"igor.kroitor@gmail.com",
            //             "$accountGroup":8,
            //             "viewPermission":true,
            //             "tradePermission":true,
            //             "transferPermission":true,
            //             "cashAccount":["cshrHKLZCjlZ2ejqkmvIHHtPmLYqdnda"],
            //             "marginAccount":["martXoh1v1N3EMQC5FDtSj5VHso8aI2Z"],
            //             "futuresAccount":["futc9r7UmFJAyBY2rE3beA2JFxav2XFF"],
            //             "userUID":"U6491137460"
            //         }
            //     }
            //
            $data = $this->safe_value($response, 'data', array());
            $accountGroup = $this->safe_string($data, 'accountGroup');
            $this->options['account-group'] = $accountGroup;
        }
        return array(
            array(
                'id' => $accountGroup,
                'type' => null,
                'currency' => null,
                'info' => $response,
            ),
        );
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $defaultAccountCategory = $this->safe_string($this->options, 'account-category', 'cash');
        $options = $this->safe_value($this->options, 'fetchBalance', array());
        $accountCategory = $this->safe_string($options, 'account-category', $defaultAccountCategory);
        $accountCategory = $this->safe_string($params, 'account-category', $accountCategory);
        $params = $this->omit($params, 'account-category');
        $account = $this->safe_value($this->accounts, 0, array());
        $accountGroup = $this->safe_string($account, 'id');
        $request = array(
            'account-group' => $accountGroup,
        );
        $method = 'accountCategoryGetBalance';
        if ($accountCategory === 'futures') {
            $method = 'accountGroupGetFuturesCollateralBalance';
        } else {
            $request['account-category'] = $accountCategory;
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // cash
        //
        //     {
        //         'code' => 0,
        //         'data' => array(
        //             array(
        //                 'asset' => 'BCHSV',
        //                 'totalBalance' => '64.298000048',
        //                 'availableBalance' => '64.298000048',
        //             ),
        //         )
        //     }
        //
        // margin
        //
        //     {
        //         'code' => 0,
        //         'data' => array(
        //             array(
        //                 'asset' => 'BCHSV',
        //                 'totalBalance' => '64.298000048',
        //                 'availableBalance' => '64.298000048',
        //                 'borrowed' => '0',
        //                 'interest' => '0',
        //             ),
        //         )
        //     }
        //
        // futures
        //
        //     {
        //         "$code":0,
        //         "data":array(
        //             array("asset":"BTC","totalBalance":"0","availableBalance":"0","maxTransferrable":"0","priceInUSDT":"9456.59"),
        //             array("asset":"ETH","totalBalance":"0","availableBalance":"0","maxTransferrable":"0","priceInUSDT":"235.95"),
        //             array("asset":"USDT","totalBalance":"0","availableBalance":"0","maxTransferrable":"0","priceInUSDT":"1"),
        //             array("asset":"USDC","totalBalance":"0","availableBalance":"0","maxTransferrable":"0","priceInUSDT":"1.00035"),
        //             array("asset":"PAX","totalBalance":"0","availableBalance":"0","maxTransferrable":"0","priceInUSDT":"1.00045"),
        //             array("asset":"USDTR","totalBalance":"0","availableBalance":"0","maxTransferrable":"0","priceInUSDT":"1")
        //         )
        //     }
        //
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'data', array());
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $code = $this->safe_currency_code($this->safe_string($balance, 'asset'));
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'availableBalance');
            $account['total'] = $this->safe_float($balance, 'totalBalance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetDepth (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "$data":{
        //             "m":"depth-snapshot",
        //             "$symbol":"BTC-PERP",
        //             "$data":{
        //                 "ts":1590223998202,
        //                 "seqnum":115444921,
        //                 "asks":[
        //                     ["9207.5","18.2383"],
        //                     ["9207.75","18.8235"],
        //                     ["9208","10.7873"],
        //                 ],
        //                 "bids":[
        //                     ["9207.25","0.4009"],
        //                     ["9207","0.003"],
        //                     ["9206.5","0.003"],
        //                 ]
        //             }
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $orderbook = $this->safe_value($data, 'data', array());
        $timestamp = $this->safe_integer($orderbook, 'ts');
        $result = $this->parse_order_book($orderbook, $timestamp);
        $result['nonce'] = $this->safe_integer($orderbook, 'seqnum');
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //     {
        //         "$symbol":"QTUM/BTC",
        //         "$open":"0.00016537",
        //         "$close":"0.00019077",
        //         "high":"0.000192",
        //         "low":"0.00016537",
        //         "volume":"846.6",
        //         "$ask":["0.00018698","26.2"],
        //         "$bid":["0.00018408","503.7"],
        //         "$type":"spot"
        //     }
        //
        $timestamp = null;
        $marketId = $this->safe_string($ticker, 'symbol');
        $symbol = null;
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
        } else if ($marketId !== null) {
            $type = $this->safe_string($ticker, 'type');
            if ($type === 'spot') {
                list($baseId, $quoteId) = explode('/', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $close = $this->safe_float($ticker, 'close');
        $bid = $this->safe_value($ticker, 'bid', array());
        $ask = $this->safe_value($ticker, 'ask', array());
        $open = $this->safe_float($ticker, 'open');
        $change = null;
        $percentage = null;
        $average = null;
        if (($open !== null) && ($close !== null)) {
            $change = $close - $open;
            if ($open > 0) {
                $percentage = $change / $open * 100;
            }
            $average = $this->sum($open, $close) / 2;
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($bid, 0),
            'bidVolume' => $this->safe_float($bid, 1),
            'ask' => $this->safe_float($ask, 0),
            'askVolume' => $this->safe_float($ask, 1),
            'vwap' => null,
            'open' => $open,
            'close' => $close,
            'last' => $close,
            'previousClose' => null, // previous day $close
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_tickers($rawTickers, $symbols = null) {
        $tickers = array();
        for ($i = 0; $i < count($rawTickers); $i++) {
            $tickers[] = $this->parse_ticker($rawTickers[$i]);
        }
        return $this->filter_by_array($tickers, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "$data":{
        //             "$symbol":"BTC-PERP", // or "BTC/USDT"
        //             "open":"9073",
        //             "close":"9185.75",
        //             "high":"9185.75",
        //             "low":"9185.75",
        //             "volume":"576.8334",
        //             "ask":["9185.75","15.5863"],
        //             "bid":["9185.5","0.003"],
        //             "type":"derivatives", // or "spot"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ticker($data, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($symbols !== null) {
            $marketIds = $this->market_ids($symbols);
            $request['symbol'] = implode(',', $marketIds);
        }
        $response = $this->publicGetTicker (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "$data":[
        //             {
        //                 "symbol":"QTUM/BTC",
        //                 "open":"0.00016537",
        //                 "close":"0.00019077",
        //                 "high":"0.000192",
        //                 "low":"0.00016537",
        //                 "volume":"846.6",
        //                 "ask":["0.00018698","26.2"],
        //                 "bid":["0.00018408","503.7"],
        //                 "type":"spot"
        //             }
        //         ]
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_tickers($data, $symbols);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "m":"bar",
        //         "s":"BTC/USDT",
        //         "$data":{
        //             "i":"1",
        //             "ts":1590228000000,
        //             "o":"9139.59",
        //             "c":"9131.94",
        //             "h":"9139.99",
        //             "l":"9121.71",
        //             "v":"25.20648"
        //         }
        //     }
        //
        $data = $this->safe_value($ohlcv, 'data', array());
        return array(
            $this->safe_integer($data, 'ts'),
            $this->safe_float($data, 'o'),
            $this->safe_float($data, 'h'),
            $this->safe_float($data, 'l'),
            $this->safe_float($data, 'c'),
            $this->safe_float($data, 'v'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'interval' => $this->timeframes[$timeframe],
        );
        // if $since and $limit are not specified
        // the exchange will return just 1 last candle by default
        $duration = $this->parse_timeframe($timeframe);
        $options = $this->safe_value($this->options, 'fetchOHLCV', array());
        $defaultLimit = $this->safe_integer($options, 'limit', 500);
        if ($since !== null) {
            $request['from'] = $since;
            if ($limit === null) {
                $limit = $defaultLimit;
            } else {
                $limit = min ($limit, $defaultLimit);
            }
            $request['to'] = $this->sum($since, $limit * $duration * 1000, 1);
        } else if ($limit !== null) {
            $request['n'] = $limit; // max 500
        }
        $response = $this->publicGetBarhist (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "$data":array(
        //             {
        //                 "m":"bar",
        //                 "s":"BTC/USDT",
        //                 "$data":{
        //                     "i":"1",
        //                     "ts":1590228000000,
        //                     "o":"9139.59",
        //                     "c":"9131.94",
        //                     "h":"9139.99",
        //                     "l":"9121.71",
        //                     "v":"25.20648"
        //                 }
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        //
        // public fetchTrades
        //
        //     {
        //         "p":"9128.5", // $price
        //         "q":"0.0030", // quantity
        //         "ts":1590229002385, // $timestamp
        //         "bm":false, // if true, the buyer is the $market maker, we only use this field to "define the $side" of a public $trade
        //         "seqnum":180143985289898554
        //     }
        //
        $timestamp = $this->safe_integer($trade, 'ts');
        $price = $this->safe_float_2($trade, 'price', 'p');
        $amount = $this->safe_float($trade, 'q');
        $cost = null;
        if (($price !== null) && ($amount !== null)) {
            $cost = $price * $amount;
        }
        $buyerIsMaker = $this->safe_value($trade, 'bm', false);
        $makerOrTaker = $buyerIsMaker ? 'maker' : 'taker';
        $side = $buyerIsMaker ? 'buy' : 'sell';
        $symbol = null;
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => null,
            'order' => null,
            'type' => null,
            'takerOrMaker' => $makerOrTaker,
            'side' => $side,
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
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['n'] = $limit; // max 100
        }
        $response = $this->publicGetTrades (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "data":{
        //             "m":"$trades",
        //             "$symbol":"BTC-PERP",
        //             "data":array(
        //                 array("p":"9128.5","q":"0.0030","ts":1590229002385,"bm":false,"seqnum":180143985289898554),
        //                 array("p":"9129","q":"0.0030","ts":1590229002642,"bm":false,"seqnum":180143985289898587),
        //                 array("p":"9129.5","q":"0.0030","ts":1590229021306,"bm":false,"seqnum":180143985289899043)
        //             )
        //         }
        //     }
        //
        $records = $this->safe_value($response, 'data', array());
        $trades = $this->safe_value($records, 'data', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'PendingNew' => 'open',
            'New' => 'open',
            'PartiallyFilled' => 'open',
            'Filled' => 'closed',
            'Canceled' => 'canceled',
            'Rejected' => 'rejected',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "$id" => "16e607e2b83a8bXHbAwwoqDo55c166fa",
        //         "orderId" => "16e85b4d9b9a8bXHbAwwoqDoc3d66830",
        //         "orderType" => "Market",
        //         "$symbol" => "BTC/USDT",
        //         "$timestamp" => 1573576916201
        //     }
        //
        // fetchOrder, fetchOpenOrders, fetchClosedOrders
        //
        //     {
        //         "$symbol" =>       "BTC/USDT",
        //         "$price" =>        "8131.22",
        //         "orderQty" =>     "0.00082",
        //         "orderType" =>    "Market",
        //         "avgPx" =>        "7392.02",
        //         "cumFee" =>       "0.005152238",
        //         "cumFilledQty" => "0.00082",
        //         "errorCode" =>    "",
        //         "feeAsset" =>     "USDT",
        //         "lastExecTime" => 1575953151764,
        //         "orderId" =>      "a16eee20b6750866943712zWEDdAjt3",
        //         "seqNum" =>       2623469,
        //         "$side" =>         "Buy",
        //         "$status" =>       "Filled",
        //         "$stopPrice" =>    "",
        //         "execInst" =>     "NULL_VAL"
        //     }
        //
        //     array(
        //         "ac" => "FUTURES",
        //         "accountId" => "testabcdefg",
        //         "avgPx" => "0",
        //         "cumFee" => "0",
        //         "cumQty" => "0",
        //         "errorCode" => "NULL_VAL",
        //         "execInst" => "NULL_VAL",
        //         "feeAsset" => "USDT",
        //         "lastExecTime" => 1584072844085,
        //         "orderId" => "r170d21956dd5450276356bbtcpKa74",
        //         "orderQty" => "1.1499",
        //         "orderType" => "Limit",
        //         "$price" => "4000",
        //         "sendingTime" => 1584072841033,
        //         "seqNum" => 24105338,
        //         "$side" => "Buy",
        //         "$status" => "Canceled",
        //         "$stopPrice" => "",
        //         "$symbol" => "BTC-PERP"
        //     ),
        //
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '/');
        $timestamp = $this->safe_integer_2($order, 'timestamp', 'sendingTime');
        $lastTradeTimestamp = $this->safe_integer($order, 'lastExecTime');
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'orderQty');
        $average = $this->safe_float($order, 'avgPx');
        $filled = $this->safe_float_2($order, 'cumFilledQty', 'cumQty');
        $remaining = null;
        if ($filled !== null) {
            if ($filled === 0) {
                $timestamp = $lastTradeTimestamp;
                $lastTradeTimestamp = null;
            }
            if ($amount !== null) {
                $remaining = max (0, $amount - $filled);
            }
        }
        $cost = null;
        if (($average !== null) && ($filled !== null)) {
            $cost = $average * $filled;
        }
        $id = $this->safe_string($order, 'orderId');
        $clientOrderId = $this->safe_string($order, 'id');
        if ($clientOrderId !== null) {
            if (strlen($clientOrderId) < 1) {
                $clientOrderId = null;
            }
        }
        $type = $this->safe_string_lower($order, 'orderType');
        $side = $this->safe_string_lower($order, 'side');
        $feeCost = $this->safe_float($order, 'cumFee');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($order, 'feeAsset');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        $stopPrice = $this->safe_float($order, 'stopPrice');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'amount' => $amount,
            'cost' => $cost,
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => null,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = $this->market($symbol);
        $defaultAccountCategory = $this->safe_string($this->options, 'account-category', 'cash');
        $options = $this->safe_value($this->options, 'createOrder', array());
        $accountCategory = $this->safe_string($options, 'account-category', $defaultAccountCategory);
        $accountCategory = $this->safe_string($params, 'account-category', $accountCategory);
        $params = $this->omit($params, 'account-category');
        $account = $this->safe_value($this->accounts, 0, array());
        $accountGroup = $this->safe_value($account, 'id');
        $clientOrderId = $this->safe_string_2($params, 'clientOrderId', 'id');
        $request = array(
            'account-group' => $accountGroup,
            'account-category' => $accountCategory,
            'symbol' => $market['id'],
            'time' => $this->milliseconds(),
            'orderQty' => $this->amount_to_precision($symbol, $amount),
            'orderType' => $type, // "limit", "$market", "stop_market", "stop_limit"
            'side' => $side, // "buy" or "sell"
            // 'orderPrice' => $this->price_to_precision($symbol, $price),
            // 'stopPrice' => $this->price_to_precision($symbol, $stopPrice), // required for stop orders
            // 'postOnly' => 'false', // 'false', 'true'
            // 'timeInForce' => 'GTC', // GTC, IOC, FOK
            // 'respInst' => 'ACK', // ACK, 'ACCEPT, DONE
        );
        if ($clientOrderId !== null) {
            $request['id'] = $clientOrderId;
            $params = $this->omit($params, array( 'clientOrderId', 'id' ));
        }
        if (($type === 'limit') || ($type === 'stop_limit')) {
            $request['orderPrice'] = $this->price_to_precision($symbol, $price);
        }
        if (($type === 'stop_limit') || ($type === 'stop_market')) {
            $stopPrice = $this->safe_float($params, 'stopPrice');
            if ($stopPrice === null) {
                throw new InvalidOrder($this->id . ' createOrder() requires a $stopPrice parameter for ' . $type . ' orders');
            } else {
                $request['stopPrice'] = $this->price_to_precision($symbol, $stopPrice);
                $params = $this->omit($params, 'stopPrice');
            }
        }
        $response = $this->accountCategoryPostOrder (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "$data" => {
        //             "ac" => "MARGIN",
        //             "accountId" => "cshQtyfq8XLAA9kcf19h8bXHbAwwoqDo",
        //             "action" => "place-order",
        //             "$info" => array(
        //                 "id" => "16e607e2b83a8bXHbAwwoqDo55c166fa",
        //                 "orderId" => "16e85b4d9b9a8bXHbAwwoqDoc3d66830",
        //                 "orderType" => "Market",
        //                 "$symbol" => "BTC/USDT",
        //                 "timestamp" => 1573576916201
        //             ),
        //             "status" => "Ack"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $info = $this->safe_value($data, 'info', array());
        return $this->parse_order($info, $market);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $defaultAccountCategory = $this->safe_string($this->options, 'account-category', 'cash');
        $options = $this->safe_value($this->options, 'fetchOrder', array());
        $accountCategory = $this->safe_string($options, 'account-category', $defaultAccountCategory);
        $accountCategory = $this->safe_string($params, 'account-category', $accountCategory);
        $params = $this->omit($params, 'account-category');
        $account = $this->safe_value($this->accounts, 0, array());
        $accountGroup = $this->safe_value($account, 'id');
        $request = array(
            'account-group' => $accountGroup,
            'account-category' => $accountCategory,
            'orderId' => $id,
        );
        $response = $this->accountCategoryGetOrderStatus (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "$accountCategory" => "CASH",
        //         "accountId" => "cshQtyfq8XLAA9kcf19h8bXHbAwwoqDo",
        //         "$data" => array(
        //             {
        //                 "$symbol" =>       "BTC/USDT",
        //                 "price" =>        "8131.22",
        //                 "orderQty" =>     "0.00082",
        //                 "orderType" =>    "Market",
        //                 "avgPx" =>        "7392.02",
        //                 "cumFee" =>       "0.005152238",
        //                 "cumFilledQty" => "0.00082",
        //                 "errorCode" =>    "",
        //                 "feeAsset" =>     "USDT",
        //                 "lastExecTime" => 1575953151764,
        //                 "orderId" =>      "a16eee20b6750866943712zWEDdAjt3",
        //                 "seqNum" =>       2623469,
        //                 "side" =>         "Buy",
        //                 "status" =>       "Filled",
        //                 "stopPrice" =>    "",
        //                 "execInst" =>     "NULL_VAL"
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order($data);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $defaultAccountCategory = $this->safe_string($this->options, 'account-category', 'cash');
        $options = $this->safe_value($this->options, 'fetchOpenOrders', array());
        $accountCategory = $this->safe_string($options, 'account-category', $defaultAccountCategory);
        $accountCategory = $this->safe_string($params, 'account-category', $accountCategory);
        $params = $this->omit($params, 'account-category');
        $account = $this->safe_value($this->accounts, 0, array());
        $accountGroup = $this->safe_value($account, 'id');
        $request = array(
            'account-group' => $accountGroup,
            'account-category' => $accountCategory,
        );
        $response = $this->accountCategoryGetOrderOpen (array_merge($request, $params));
        //
        //     {
        //         "ac" => "CASH",
        //         "accountId" => "cshQtyfq8XLAA9kcf19h8bXHbAwwoqDo",
        //         "code" => 0,
        //         "$data" => array(
        //             array(
        //                 "avgPx" => "0",         // Average filled price of the $order
        //                 "cumFee" => "0",       // cumulative fee paid for this $order
        //                 "cumFilledQty" => "0", // cumulative filled quantity
        //                 "errorCode" => "",     // error code; could be empty
        //                 "feeAsset" => "USDT",  // fee asset
        //                 "lastExecTime" => 1576019723550, //  The last execution time of the $order
        //                 "orderId" => "s16ef21882ea0866943712034f36d83", // server provided orderId
        //                 "orderQty" => "0.0083",  // $order quantity
        //                 "orderType" => "Limit",  // $order type
        //                 "price" => "7105",       // $order price
        //                 "seqNum" => 8193258,     // sequence number
        //                 "side" => "Buy",         // $order side
        //                 "status" => "New",       // $order status on matching engine
        //                 "stopPrice" => "",       // only available for stop $market and stop $limit $orders; otherwise empty
        //                 "$symbol" => "BTC/USDT",
        //                 "execInst" => "NULL_VAL" // execution instruction
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        if ($accountCategory === 'futures') {
            return $this->parse_orders($data, $market, $since, $limit);
        }
        // a workaround for https://github.com/ccxt/ccxt/issues/7187
        $orders = array();
        for ($i = 0; $i < count($data); $i++) {
            $order = $this->parse_order($data[$i], $market);
            $orders[] = $order;
        }
        return $this->filter_by_symbol_since_limit($orders, $symbol, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $defaultAccountCategory = $this->safe_string($this->options, 'account-category');
        $options = $this->safe_value($this->options, 'fetchClosedOrders', array());
        $accountCategory = $this->safe_string($options, 'account-category', $defaultAccountCategory);
        $accountCategory = $this->safe_string($params, 'account-category', $accountCategory);
        $params = $this->omit($params, 'account-category');
        $account = $this->safe_value($this->accounts, 0, array());
        $accountGroup = $this->safe_value($account, 'id');
        $request = array(
            'account-group' => $accountGroup,
            // 'category' => $accountCategory,
            // 'symbol' => $market['id'],
            // 'orderType' => 'market', // optional, string
            // 'side' => 'buy', // or 'sell', optional, case insensitive.
            // 'status' => 'Filled', // "Filled", "Canceled", or "Rejected"
            // 'startTime' => exchange.milliseconds (),
            // 'endTime' => exchange.milliseconds (),
            // 'page' => 1,
            // 'pageSize' => 100,
        );
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        $method = $this->safe_value($options, 'method', 'accountGroupGetOrderHist');
        if ($method === 'accountGroupGetOrderHist') {
            if ($accountCategory !== null) {
                $request['category'] = $accountCategory;
            }
        } else {
            $request['account-category'] = $accountCategory;
        }
        if ($since !== null) {
            $request['startTime'] = $since;
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // accountCategoryGetOrderHistCurrent
        //
        //     {
        //         "code":0,
        //         "accountId":"cshrHKLZCjlZ2ejqkmvIHHtPmLYqdnda",
        //         "ac":"CASH",
        //         "$data":array(
        //             {
        //                 "seqNum":15561826728,
        //                 "orderId":"a17294d305c0U6491137460bethu7kw9",
        //                 "$symbol":"ETH/USDT",
        //                 "orderType":"Limit",
        //                 "lastExecTime":1591635618200,
        //                 "price":"200",
        //                 "orderQty":"0.1",
        //                 "side":"Buy",
        //                 "status":"Canceled",
        //                 "avgPx":"0",
        //                 "cumFilledQty":"0",
        //                 "stopPrice":"",
        //                 "errorCode":"",
        //                 "cumFee":"0",
        //                 "feeAsset":"USDT",
        //                 "execInst":"NULL_VAL"
        //             }
        //         )
        //     }
        //
        // accountGroupGetOrderHist
        //
        //     {
        //         "code" => 0,
        //         "$data" => {
        //             "$data" => array(
        //                 array(
        //                     "ac" => "FUTURES",
        //                     "accountId" => "testabcdefg",
        //                     "avgPx" => "0",
        //                     "cumFee" => "0",
        //                     "cumQty" => "0",
        //                     "errorCode" => "NULL_VAL",
        //                     "execInst" => "NULL_VAL",
        //                     "feeAsset" => "USDT",
        //                     "lastExecTime" => 1584072844085,
        //                     "orderId" => "r170d21956dd5450276356bbtcpKa74",
        //                     "orderQty" => "1.1499",
        //                     "orderType" => "Limit",
        //                     "price" => "4000",
        //                     "sendingTime" => 1584072841033,
        //                     "seqNum" => 24105338,
        //                     "side" => "Buy",
        //                     "status" => "Canceled",
        //                     "stopPrice" => "",
        //                     "$symbol" => "BTC-PERP"
        //                 ),
        //             ),
        //             "hasNext" => False,
        //             "$limit" => 500,
        //             "page" => 1,
        //             "pageSize" => 20
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data');
        $isArray = gettype($data) === 'array' && count(array_filter(array_keys($data), 'is_string')) == 0;
        if (!$isArray) {
            $data = $this->safe_value($data, 'data', array());
        }
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $this->load_accounts();
        $market = $this->market($symbol);
        $defaultAccountCategory = $this->safe_string($this->options, 'account-category', 'cash');
        $options = $this->safe_value($this->options, 'cancelOrder', array());
        $accountCategory = $this->safe_string($options, 'account-category', $defaultAccountCategory);
        $accountCategory = $this->safe_string($params, 'account-category', $accountCategory);
        $params = $this->omit($params, 'account-category');
        $account = $this->safe_value($this->accounts, 0, array());
        $accountGroup = $this->safe_value($account, 'id');
        $clientOrderId = $this->safe_string_2($params, 'clientOrderId', 'id');
        $request = array(
            'account-group' => $accountGroup,
            'account-category' => $accountCategory,
            'symbol' => $market['id'],
            'time' => $this->milliseconds(),
            'id' => 'foobar',
        );
        if ($clientOrderId === null) {
            $request['orderId'] = $id;
        } else {
            $request['id'] = $clientOrderId;
            $params = $this->omit($params, array( 'clientOrderId', 'id' ));
        }
        $response = $this->accountCategoryDeleteOrder (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "$data" => {
        //             "accountId" => "cshQtyfq8XLAA9kcf19h8bXHbAwwoqDo",
        //             "ac" => "CASH",
        //             "action" => "cancel-order",
        //             "status" => "Ack",
        //             "$info" => {
        //                 "$id" =>        "wv8QGquoeamhssvQBeHOHGQCGlcBjj23",
        //                 "orderId" =>   "16e6198afb4s8bXHbAwwoqDo2ebc19dc",
        //                 "orderType" => "", // could be empty
        //                 "$symbol" =>    "ETH/USDT",
        //                 "timestamp" =>  1573594877822
        //             }
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $info = $this->safe_value($data, 'info', array());
        return $this->parse_order($info, $market);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $defaultAccountCategory = $this->safe_string($this->options, 'account-category', 'cash');
        $options = $this->safe_value($this->options, 'cancelAllOrders', array());
        $accountCategory = $this->safe_string($options, 'account-category', $defaultAccountCategory);
        $accountCategory = $this->safe_string($params, 'account-category', $accountCategory);
        $params = $this->omit($params, 'account-category');
        $account = $this->safe_value($this->accounts, 0, array());
        $accountGroup = $this->safe_value($account, 'id');
        $request = array(
            'account-group' => $accountGroup,
            'account-category' => $accountCategory,
            'time' => $this->milliseconds(),
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        $response = $this->accountCategoryDeleteOrderAll (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "data" => {
        //             "ac" => "CASH",
        //             "accountId" => "cshQtyfq8XLAA9kcf19h8bXHbAwwoqDo",
        //             "action" => "cancel-all",
        //             "info" => array(
        //                 "id" =>  "2bmYvi7lyTrneMzpcJcf2D7Pe9V1P9wy",
        //                 "orderId" => "",
        //                 "orderType" => "NULL_VAL",
        //                 "$symbol" => "",
        //                 "timestamp" => 1574118495462
        //             ),
        //             "status" => "Ack"
        //         }
        //     }
        //
        return $response;
    }

    public function parse_deposit_address($depositAddress, $currency = null) {
        //
        //     {
        //         $address => "0xe7c70b4e73b6b450ee46c3b5c0f5fb127ca55722",
        //         destTag => "",
        //         tagType => "",
        //         $tagId => "",
        //         chainName => "ERC20",
        //         numConfirmations => 20,
        //         withdrawalFee => 1,
        //         nativeScale => 4,
        //         tips => array()
        //     }
        //
        $address = $this->safe_string($depositAddress, 'address');
        $tagId = $this->safe_string($depositAddress, 'tagId');
        $tag = $this->safe_string($depositAddress, $tagId);
        $this->check_address($address);
        $code = ($currency === null) ? null : $currency['code'];
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $depositAddress,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $chainName = $this->safe_string($params, 'chainName');
        $params = $this->omit($params, 'chainName');
        $request = array(
            'asset' => $currency['id'],
        );
        $response = $this->privateGetWalletDepositAddress (array_merge($request, $params));
        //
        //     {
        //         "$code":0,
        //         "$data":{
        //             "asset":"USDT",
        //             "assetName":"Tether",
        //             "$address":array(
        //                 array(
        //                     "$address":"1N22odLHXnLPCjC8kwBJPTayarr9RtPod6",
        //                     "destTag":"",
        //                     "tagType":"",
        //                     "tagId":"",
        //                     "$chainName":"Omni",
        //                     "numConfirmations":3,
        //                     "withdrawalFee":4.7,
        //                     "nativeScale":4,
        //                     "tips":array()
        //                 ),
        //                 {
        //                     "$address":"0xe7c70b4e73b6b450ee46c3b5c0f5fb127ca55722",
        //                     "destTag":"",
        //                     "tagType":"",
        //                     "tagId":"",
        //                     "$chainName":"ERC20",
        //                     "numConfirmations":20,
        //                     "withdrawalFee":1.0,
        //                     "nativeScale":4,
        //                     "tips":array()
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $addresses = $this->safe_value($data, 'address', array());
        $numAddresses = is_array($addresses) ? count($addresses) : 0;
        $address = null;
        if ($numAddresses > 1) {
            $addressesByChainName = $this->index_by($addresses, 'chainName');
            if ($chainName === null) {
                $chainNames = is_array($addressesByChainName) ? array_keys($addressesByChainName) : array();
                $chains = implode(', ', $chainNames);
                throw new ArgumentsRequired($this->id . ' fetchDepositAddress returned more than one $address, a $chainName parameter is required, one of ' . $chains);
            }
            $address = $this->safe_value($addressesByChainName, $chainName, array());
        } else {
            // first $address
            $address = $this->safe_value($addresses, 0, array());
        }
        $result = $this->parse_deposit_address($address, $currency);
        return array_merge($result, array(
            'info' => $response,
        ));
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'txType' => 'deposit',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'txType' => 'withdrawal',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'asset' => $currency['id'],
            // 'page' => 1,
            // 'pageSize' => 20,
            // 'startTs' => $this->milliseconds(),
            // 'endTs' => $this->milliseconds(),
            // 'txType' => undefned, // deposit, withdrawal
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['asset'] = $currency['id'];
        }
        if ($since !== null) {
            $request['startTs'] = $since;
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $response = $this->privateGetWalletTransactions (array_merge($request, $params));
        //
        //     {
        //         $code => 0,
        //         $data => {
        //             $data => array(
        //                 {
        //                     requestId => "wuzd1Ojsqtz4bCA3UXwtUnnJDmU8PiyB",
        //                     time => 1591606166000,
        //                     asset => "USDT",
        //                     transactionType => "deposit",
        //                     amount => "25",
        //                     commission => "0",
        //                     networkTransactionId => "0xbc4eabdce92f14dbcc01d799a5f8ca1f02f4a3a804b6350ea202be4d3c738fce",
        //                     status => "pending",
        //                     numConfirmed => 8,
        //                     numConfirmations => 20,
        //                     destAddress => array( address => "0xe7c70b4e73b6b450ee46c3b5c0f5fb127ca55722" )
        //                 }
        //             ),
        //             page => 1,
        //             pageSize => 20,
        //             hasNext => false
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $transactions = $this->safe_value($data, 'data', array());
        return $this->parse_transactions($transactions, $currency, $since, $limit);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'reviewing' => 'pending',
            'pending' => 'pending',
            'confirmed' => 'ok',
            'rejected' => 'rejected',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //     {
        //         requestId => "wuzd1Ojsqtz4bCA3UXwtUnnJDmU8PiyB",
        //         time => 1591606166000,
        //         asset => "USDT",
        //         transactionType => "deposit",
        //         $amount => "25",
        //         commission => "0",
        //         networkTransactionId => "0xbc4eabdce92f14dbcc01d799a5f8ca1f02f4a3a804b6350ea202be4d3c738fce",
        //         $status => "pending",
        //         numConfirmed => 8,
        //         numConfirmations => 20,
        //         $destAddress => {
        //             $address => "0xe7c70b4e73b6b450ee46c3b5c0f5fb127ca55722",
        //             destTag => "..." // for currencies that have it
        //         }
        //     }
        //
        $id = $this->safe_string($transaction, 'requestId');
        $amount = $this->safe_float($transaction, 'amount');
        $destAddress = $this->safe_value($transaction, 'destAddress', array());
        $address = $this->safe_string($destAddress, 'address');
        $tag = $this->safe_string($destAddress, 'destTag');
        $txid = $this->safe_string($transaction, 'networkTransactionId');
        $type = $this->safe_string($transaction, 'transactionType');
        $timestamp = $this->safe_integer($transaction, 'time');
        $currencyId = $this->safe_string($transaction, 'asset');
        $code = $this->safe_currency_code($currencyId, $currency);
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $feeCost = $this->safe_float($transaction, 'commission');
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'addressTo' => $address,
            'addressFrom' => null,
            'tag' => $tag,
            'tagTo' => $tag,
            'tagFrom' => null,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => array(
                'currency' => $code,
                'cost' => $feeCost,
            ),
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = '';
        $query = $params;
        $accountCategory = ($api === 'accountCategory');
        if ($accountCategory || ($api === 'accountGroup')) {
            $url .= $this->implode_params('/{account-group}', $params);
            $query = $this->omit($params, 'account-group');
        }
        $request = $this->implode_params($path, $query);
        $url .= '/api/pro/' . $this->version;
        if ($accountCategory) {
            $url .= $this->implode_params('/{account-category}', $query);
            $query = $this->omit($query, 'account-category');
        }
        $url .= '/' . $request;
        $query = $this->omit($query, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $timestamp = (string) $this->milliseconds();
            $payload = $timestamp . '+' . $request;
            $hmac = $this->hmac($this->encode($payload), $this->encode($this->secret), 'sha256', 'base64');
            $headers = array(
                'x-auth-key' => $this->apiKey,
                'x-auth-timestamp' => $timestamp,
                'x-auth-signature' => $hmac,
            );
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
            } else {
                $headers['Content-Type'] = 'application/json';
                $body = $this->json($query);
            }
        }
        $url = $this->urls['api'] . $url;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        //
        //     array('code' => 6010, 'message' => 'Not enough balance.')
        //     array('code' => 60060, 'message' => 'The order is already filled or canceled.')
        //     array("$code":2100,"$message":"ApiKeyFailure")
        //     array("$code":300001,"$message":"Price is too low from market price.","$reason":"INVALID_PRICE","accountId":"cshrHKLZCjlZ2ejqkmvIHHtPmLYqdnda","ac":"CASH","action":"place-order","status":"Err","info":array("symbol":"BTC/USDT"))
        //
        $code = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'message');
        $error = ($code !== null) && ($code !== '0');
        if ($error || ($message !== null)) {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}
