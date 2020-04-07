<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\OrderNotFound;
use \ccxt\NotSupported;

class exmo extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'exmo',
            'name' => 'EXMO',
            'countries' => array( 'ES', 'RU' ), // Spain, Russia
            'rateLimit' => 350, // once every 350 ms ≈ 180 requests per minute ≈ 3 requests per second
            'version' => 'v1',
            'has' => array(
                'CORS' => false,
                'fetchClosedOrders' => 'emulated',
                'fetchDepositAddress' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => 'emulated',
                'fetchOrders' => 'emulated',
                'fetchOrderTrades' => true,
                'fetchOrderBooks' => true,
                'fetchMyTrades' => true,
                'fetchTickers' => true,
                'withdraw' => true,
                'fetchTradingFee' => true,
                'fetchTradingFees' => true,
                'fetchFundingFees' => true,
                'fetchCurrencies' => true,
                'fetchTransactions' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766491-1b0ea956-5eda-11e7-9225-40d67b481b8d.jpg',
                'api' => array(
                    'public' => 'https://api.exmo.com',
                    'private' => 'https://api.exmo.com',
                    'web' => 'https://exmo.me',
                ),
                'www' => 'https://exmo.me',
                'referral' => 'https://exmo.me/?ref=131685',
                'doc' => array(
                    'https://exmo.me/en/api_doc?ref=131685',
                    'https://github.com/exmo-dev/exmo_api_lib/tree/master/nodejs',
                ),
                'fees' => 'https://exmo.com/en/docs/fees',
            ),
            'api' => array(
                'web' => array(
                    'get' => array(
                        'ctrl/feesAndLimits',
                        'en/docs/fees',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'currency',
                        'order_book',
                        'pair_settings',
                        'ticker',
                        'trades',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'user_info',
                        'order_create',
                        'order_cancel',
                        'user_open_orders',
                        'user_trades',
                        'user_cancelled_orders',
                        'order_trades',
                        'required_amount',
                        'deposit_address',
                        'withdraw_crypt',
                        'withdraw_get_txid',
                        'excode_create',
                        'excode_load',
                        'wallet_history',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.2 / 100,
                    'taker' => 0.2 / 100,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false, // fixed funding fees for crypto, see fetchFundingFees below
                ),
            ),
            'options' => array(
                'useWebapiForFetchingFees' => false, // TODO => figure why Exmo bans us when we try to fetch() their web urls
                'feesAndLimits' => array(
                    'success' => 1,
                    'ctlr' => 'feesAndLimits',
                    'error' => '',
                    'data' => array(
                        'limits' => array(
                            array( 'pair' => 'EXM/USDT', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '100000' ),
                            array( 'pair' => 'EXM/ETH', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '5000' ),
                            array( 'pair' => 'BTC/USD', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '1', 'max_p' => '30000', 'min_a' => '1', 'max_a' => '500000' ),
                            array( 'pair' => 'BTC/KZT', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '1000', 'max_p' => '12000000', 'min_a' => '1000', 'max_a' => '100000000' ),
                            array( 'pair' => 'ETH/KZT', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '4', 'max_p' => '40000000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'USDT/KZT', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '200', 'max_p' => '4000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'ONT/EXM', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '200', 'max_a' => '15000000' ),
                            array( 'pair' => 'ONT/BTC', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.00001', 'max_a' => '10' ),
                            array( 'pair' => 'ONT/RUB', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '100', 'max_a' => '6000000' ),
                            array( 'pair' => 'ONT/UAH', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '200', 'max_a' => '250000' ),
                            array( 'pair' => 'ONG/EXM', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '100', 'max_a' => '15000000' ),
                            array( 'pair' => 'ONG/BTC', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.00001', 'max_a' => '10' ),
                            array( 'pair' => 'ONG/RUB', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '100', 'max_a' => '250000' ),
                            array( 'pair' => 'ONG/UAH', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '50', 'max_a' => '6000000' ),
                            array( 'pair' => 'CRON/BTC', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.0000001', 'max_p' => '1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'CRON/ETH', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.0000001', 'max_p' => '10', 'min_a' => '0.00001', 'max_a' => '5000' ),
                            array( 'pair' => 'CRON/USDT', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.001', 'max_a' => '500000' ),
                            array( 'pair' => 'CRON/EXM', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '100000' ),
                            array( 'pair' => 'BTC/RUB', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '1', 'max_p' => '2000000', 'min_a' => '10', 'max_a' => '50000000' ),
                            array( 'pair' => 'BTC/EUR', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '1', 'max_p' => '30000', 'min_a' => '1', 'max_a' => '500000' ),
                            array( 'pair' => 'BTC/UAH', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '1', 'max_p' => '15000000', 'min_a' => '10', 'max_a' => '15000000' ),
                            array( 'pair' => 'BTC/PLN', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '1', 'max_p' => '20000000', 'min_a' => '50', 'max_a' => '2000000' ),
                            array( 'pair' => 'BTC/TRY', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '1', 'max_p' => '800000', 'min_a' => '40', 'max_a' => '6000000' ),
                            array( 'pair' => 'BTC/USDT', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '0.01', 'max_p' => '30000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'ETH/BTC', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'ETH/USD', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'ETH/RUB', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '150', 'max_a' => '50000000' ),
                            array( 'pair' => 'XRP/BTC', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.0000001', 'max_p' => '1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'XRP/USD', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.001', 'max_a' => '500000' ),
                            array( 'pair' => 'XRP/RUB', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.000001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '50000000' ),
                            array( 'pair' => 'ADA/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'ADA/ETH', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.001', 'max_a' => '5000' ),
                            array( 'pair' => 'ADA/USD', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '500000' ),
                            array( 'pair' => 'BCH/USD', 'min_q' => '0.003', 'max_q' => '10000', 'min_p' => '0.00000001', 'max_p' => '30000', 'min_a' => '0.0001', 'max_a' => '500000' ),
                            array( 'pair' => 'BCH/RUB', 'min_q' => '0.003', 'max_q' => '10000', 'min_p' => '0.00000001', 'max_p' => '2000000', 'min_a' => '0.0001', 'max_a' => '50000000' ),
                            array( 'pair' => 'BCH/EUR', 'min_q' => '0.003', 'max_q' => '100000', 'min_p' => '0.01', 'max_p' => '300000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'BCH/UAH', 'min_q' => '0.003', 'max_q' => '100000', 'min_p' => '0.1', 'max_p' => '30000', 'min_a' => '10', 'max_a' => '15000000' ),
                            array( 'pair' => 'BCH/BTC', 'min_q' => '0.003', 'max_q' => '10000', 'min_p' => '0.00000001', 'max_p' => '5', 'min_a' => '0.0001', 'max_a' => '100' ),
                            array( 'pair' => 'BCH/ETH', 'min_q' => '0.003', 'max_q' => '10000', 'min_p' => '0.0000001', 'max_p' => '200', 'min_a' => '0.0001', 'max_a' => '5000' ),
                            array( 'pair' => 'BCH/USDT', 'min_q' => '0.003', 'max_q' => '100000', 'min_p' => '0.01', 'max_p' => '5000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'BTCZ/BTC', 'min_q' => '100', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'BTG/USD', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'BTG/BTC', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'BTG/ETH', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.0001', 'max_p' => '100', 'min_a' => '0.01', 'max_a' => '5000' ),
                            array( 'pair' => 'BTT/RUB', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.000001', 'max_p' => '1000', 'min_a' => '0.000001', 'max_a' => '100' ),
                            array( 'pair' => 'BTT/UAH', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.000001', 'max_p' => '1000', 'min_a' => '0.000001', 'max_a' => '100' ),
                            array( 'pair' => 'BTT/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '0.1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'DAI/USD', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'DAI/RUB', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '0.5', 'max_a' => '30000000' ),
                            array( 'pair' => 'DAI/BTC', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.0000001', 'max_p' => '0.1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'DAI/ETH', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.000001', 'max_p' => '10', 'min_a' => '0.0001', 'max_a' => '5000' ),
                            array( 'pair' => 'DASH/USD', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.01', 'max_p' => '10000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'DASH/RUB', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '150', 'max_a' => '50000000' ),
                            array( 'pair' => 'DASH/UAH', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.01', 'max_p' => '200000', 'min_a' => '10', 'max_a' => '15000000' ),
                            array( 'pair' => 'DASH/BTC', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.0001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'DASH/USDT', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '0.01', 'max_p' => '5000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'DCR/RUB', 'min_q' => '0.01', 'max_q' => '50000', 'min_p' => '0.00001', 'max_p' => '100000', 'min_a' => '0.5', 'max_a' => '3000000' ),
                            array( 'pair' => 'DCR/UAH', 'min_q' => '0.01', 'max_q' => '50000', 'min_p' => '0.00001', 'max_p' => '100000', 'min_a' => '0.25', 'max_a' => '1000000' ),
                            array( 'pair' => 'DCR/BTC', 'min_q' => '0.01', 'max_q' => '50000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'DOGE/USD', 'min_q' => '100', 'max_q' => '1000000000', 'min_p' => '0.0000001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '500000' ),
                            array( 'pair' => 'DOGE/BTC', 'min_q' => '100', 'max_q' => '100000000', 'min_p' => '0.0000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '100' ),
                            array( 'pair' => 'DXT/USD', 'min_q' => '100', 'max_q' => '10000000', 'min_p' => '0.00001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'DXT/BTC', 'min_q' => '100', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '100' ),
                            array( 'pair' => 'EOS/USD', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '0.01', 'max_p' => '1000', 'min_a' => '0.5', 'max_a' => '500000' ),
                            array( 'pair' => 'EOS/EUR', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.5', 'max_a' => '500000' ),
                            array( 'pair' => 'EOS/BTC', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'ETC/USD', 'min_q' => '0.2', 'max_q' => '1000', 'min_p' => '0.01', 'max_p' => '10000', 'min_a' => '0.01', 'max_a' => '500000' ),
                            array( 'pair' => 'ETC/RUB', 'min_q' => '0.2', 'max_q' => '1000', 'min_p' => '0.01', 'max_p' => '10000', 'min_a' => '0.01', 'max_a' => '50000000' ),
                            array( 'pair' => 'ETC/BTC', 'min_q' => '0.2', 'max_q' => '1000', 'min_p' => '0.0001', 'max_p' => '0.5', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'ETH/EUR', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'ETH/UAH', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.01', 'max_p' => '1000000', 'min_a' => '90', 'max_a' => '15000000' ),
                            array( 'pair' => 'ETH/PLN', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '50', 'max_a' => '2000000' ),
                            array( 'pair' => 'ETH/TRY', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.1', 'max_p' => '80000', 'min_a' => '10', 'max_a' => '6000000' ),
                            array( 'pair' => 'ETH/USDT', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'ETH/LTC', 'min_q' => '0.001', 'max_q' => '5000', 'min_p' => '0.00000001', 'max_p' => '100000', 'min_a' => '0.05', 'max_a' => '100000' ),
                            array( 'pair' => 'ETZ/BTC', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '10' ),
                            array( 'pair' => 'ETZ/ETH', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.00000001', 'max_p' => '100', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'ETZ/USDT', 'min_q' => '1', 'max_q' => '1000000', 'min_p' => '0.000001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '1000' ),
                            array( 'pair' => 'GAS/USD', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.01', 'max_p' => '50000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'GAS/BTC', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'GNT/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'GNT/ETH', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.01', 'max_a' => '5000' ),
                            array( 'pair' => 'GUSD/USD', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.1', 'max_p' => '10', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'GUSD/RUB', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.01', 'max_p' => '1000', 'min_a' => '10', 'max_a' => '50000000' ),
                            array( 'pair' => 'GUSD/BTC', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0015', 'max_a' => '100' ),
                            array( 'pair' => 'HP/BTC', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '0.1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'HB/BTC', 'min_q' => '10', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.000001', 'max_a' => '100' ),
                            array( 'pair' => 'INK/USD', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '500000' ),
                            array( 'pair' => 'INK/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'INK/ETH', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.001', 'max_a' => '5000' ),
                            array( 'pair' => 'KICK/RUB', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.000001', 'max_p' => '1000', 'min_a' => '10', 'max_a' => '50000000' ),
                            array( 'pair' => 'KICK/USDT', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '100000' ),
                            array( 'pair' => 'KICK/BTC', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '0.1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'KICK/ETH', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '5000' ),
                            array( 'pair' => 'LSK/USD', 'min_q' => '0.1', 'max_q' => '200000', 'min_p' => '0.1', 'max_p' => '1000', 'min_a' => '1', 'max_a' => '500000' ),
                            array( 'pair' => 'LSK/RUB', 'min_q' => '0.1', 'max_q' => '200000', 'min_p' => '0.001', 'max_p' => '100000', 'min_a' => '0.5', 'max_a' => '50000000' ),
                            array( 'pair' => 'LSK/BTC', 'min_q' => '1', 'max_q' => '200000', 'min_p' => '0.0000001', 'max_p' => '1', 'min_a' => '0.0015', 'max_a' => '100' ),
                            array( 'pair' => 'LTC/USD', 'min_q' => '0.05', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '10000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'LTC/RUB', 'min_q' => '0.05', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '100000', 'min_a' => '150', 'max_a' => '50000000' ),
                            array( 'pair' => 'LTC/EUR', 'min_q' => '0.05', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '10000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'LTC/UAH', 'min_q' => '0.05', 'max_q' => '1000000', 'min_p' => '0.01', 'max_p' => '300000', 'min_a' => '5', 'max_a' => '18000000' ),
                            array( 'pair' => 'LTC/BTC', 'min_q' => '0.05', 'max_q' => '1000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'MKR/BTC', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '0.0001', 'max_p' => '100', 'min_a' => '0.000001', 'max_a' => '100' ),
                            array( 'pair' => 'MKR/DAI', 'min_q' => '0.0001', 'max_q' => '1000', 'min_p' => '0.5', 'max_p' => '500000', 'min_a' => '0.005', 'max_a' => '500000' ),
                            array( 'pair' => 'MNC/USD', 'min_q' => '10', 'max_q' => '100000000', 'min_p' => '0.000001', 'max_p' => '10000', 'min_a' => '0.01', 'max_a' => '100000' ),
                            array( 'pair' => 'MNC/BTC', 'min_q' => '10', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.000001', 'max_a' => '100' ),
                            array( 'pair' => 'MNC/ETH', 'min_q' => '10', 'max_q' => '100000000', 'min_p' => '0.0000001', 'max_p' => '10', 'min_a' => '0.00001', 'max_a' => '1000' ),
                            array( 'pair' => 'MNX/USD', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '0.5', 'max_a' => '500000' ),
                            array( 'pair' => 'MNX/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'MNX/ETH', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.01', 'max_a' => '5000' ),
                            array( 'pair' => 'NEO/USD', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.01', 'max_p' => '50000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'NEO/RUB', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.001', 'max_p' => '1500000', 'min_a' => '50', 'max_a' => '50000000' ),
                            array( 'pair' => 'NEO/BTC', 'min_q' => '0.1', 'max_q' => '200000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'OMG/USD', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '0.01', 'max_p' => '1000', 'min_a' => '0.5', 'max_a' => '500000' ),
                            array( 'pair' => 'OMG/BTC', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'OMG/ETH', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.01', 'max_a' => '5000' ),
                            array( 'pair' => 'PTI/RUB', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '600000', 'min_a' => '10', 'max_a' => '600000' ),
                            array( 'pair' => 'PTI/BTC', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.000001', 'max_a' => '10' ),
                            array( 'pair' => 'PTI/EOS', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.0000001', 'max_p' => '5000', 'min_a' => '0.01', 'max_a' => '20000' ),
                            array( 'pair' => 'PTI/USDT', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.000001', 'max_p' => '10000', 'min_a' => '0.01', 'max_a' => '100000' ),
                            array( 'pair' => 'QTUM/USD', 'min_q' => '0.1', 'max_q' => '200000', 'min_p' => '0.00000001', 'max_p' => '10000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'QTUM/BTC', 'min_q' => '0.1', 'max_q' => '200000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '100' ),
                            array( 'pair' => 'QTUM/ETH', 'min_q' => '0.1', 'max_q' => '200000', 'min_p' => '0.00000001', 'max_p' => '100', 'min_a' => '0.001', 'max_a' => '5000' ),
                            array( 'pair' => 'ROOBEE/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '0.1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'SMART/USD', 'min_q' => '10', 'max_q' => '10000000', 'min_p' => '0.000001', 'max_p' => '1000', 'min_a' => '1', 'max_a' => '500000' ),
                            array( 'pair' => 'SMART/RUB', 'min_q' => '10', 'max_q' => '10000000', 'min_p' => '0.0001', 'max_p' => '100000', 'min_a' => '10', 'max_a' => '50000000' ),
                            array( 'pair' => 'SMART/EUR', 'min_q' => '10', 'max_q' => '10000000', 'min_p' => '0.000001', 'max_p' => '1000', 'min_a' => '1', 'max_a' => '500000' ),
                            array( 'pair' => 'SMART/BTC', 'min_q' => '10', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'TRX/USD', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '500000' ),
                            array( 'pair' => 'TRX/RUB', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.000001', 'max_p' => '100000', 'min_a' => '0.1', 'max_a' => '50000000' ),
                            array( 'pair' => 'TRX/UAH', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.000001', 'max_p' => '100000', 'min_a' => '0.1', 'max_a' => '50000000' ),
                            array( 'pair' => 'TRX/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'USDC/USD', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'USDC/BTC', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '100' ),
                            array( 'pair' => 'USDC/ETH', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.0000001', 'max_p' => '100', 'min_a' => '0.001', 'max_a' => '1000' ),
                            array( 'pair' => 'USDC/USDT', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '3', 'max_a' => '500000' ),
                            array( 'pair' => 'USDT/USD', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.5', 'max_p' => '10', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'USDT/RUB', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.01', 'max_p' => '1000', 'min_a' => '10', 'max_a' => '50000000' ),
                            array( 'pair' => 'USDT/EUR', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '0.1', 'max_p' => '10', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'USDT/UAH', 'min_q' => '0.01', 'max_q' => '100000', 'min_p' => '1', 'max_p' => '3000', 'min_a' => '2', 'max_a' => '15000000' ),
                            array( 'pair' => 'VLX/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '0.1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'WAVES/USD', 'min_q' => '0.5', 'max_q' => '10000', 'min_p' => '0.001', 'max_p' => '3500', 'min_a' => '0.5', 'max_a' => '500000' ),
                            array( 'pair' => 'WAVES/RUB', 'min_q' => '0.5', 'max_q' => '10000', 'min_p' => '0.01', 'max_p' => '10000', 'min_a' => '1', 'max_a' => '50000000' ),
                            array( 'pair' => 'WAVES/BTC', 'min_q' => '0.5', 'max_q' => '10000', 'min_p' => '0.000001', 'max_p' => '1', 'min_a' => '0.0001', 'max_a' => '100' ),
                            array( 'pair' => 'WAVES/ETH', 'min_q' => '0.5', 'max_q' => '10000', 'min_p' => '0.00001', 'max_p' => '30', 'min_a' => '0.0035', 'max_a' => '3500' ),
                            array( 'pair' => 'XEM/USD', 'min_q' => '10', 'max_q' => '5000000', 'min_p' => '0.00001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'XEM/EUR', 'min_q' => '10', 'max_q' => '5000000', 'min_p' => '0.00001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'XEM/UAH', 'min_q' => '1', 'max_q' => '5000000', 'min_p' => '0.0001', 'max_p' => '30000', 'min_a' => '10', 'max_a' => '15000000' ),
                            array( 'pair' => 'XEM/BTC', 'min_q' => '10', 'max_q' => '5000000', 'min_p' => '0.0000001', 'max_p' => '1', 'min_a' => '0.00015', 'max_a' => '100' ),
                            array( 'pair' => 'XLM/USD', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '500000' ),
                            array( 'pair' => 'XLM/RUB', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.00001', 'max_p' => '100000', 'min_a' => '0.1', 'max_a' => '50000000' ),
                            array( 'pair' => 'XLM/TRY', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.00001', 'max_p' => '100000', 'min_a' => '0.1', 'max_a' => '6000000' ),
                            array( 'pair' => 'XLM/BTC', 'min_q' => '1', 'max_q' => '200000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'XMR/USD', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'XMR/RUB', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.001', 'max_p' => '600000', 'min_a' => '10', 'max_a' => '16000000' ),
                            array( 'pair' => 'XMR/EUR', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'XMR/UAH', 'min_q' => '0.01', 'max_q' => '200000', 'min_p' => '0.001', 'max_p' => '300000', 'min_a' => '5', 'max_a' => '16000000' ),
                            array( 'pair' => 'XMR/BTC', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.0001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'XMR/ETH', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.00000001', 'max_p' => '100', 'min_a' => '0.001', 'max_a' => '5000' ),
                            array( 'pair' => 'XRP/EUR', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.001', 'max_a' => '500000' ),
                            array( 'pair' => 'XRP/TRY', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '6000000' ),
                            array( 'pair' => 'XRP/UAH', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '0.01', 'max_a' => '15000000' ),
                            array( 'pair' => 'XRP/USDT', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.001', 'max_p' => '1000', 'min_a' => '0.001', 'max_a' => '500000' ),
                            array( 'pair' => 'XRP/ETH', 'min_q' => '1', 'max_q' => '100000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.00001', 'max_a' => '5000' ),
                            array( 'pair' => 'XTZ/USD', 'min_q' => '0.1', 'max_q' => '100000', 'min_p' => '0.0001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '100000' ),
                            array( 'pair' => 'XTZ/RUB', 'min_q' => '0.1', 'max_q' => '100000', 'min_p' => '0.00001', 'max_p' => '100000', 'min_a' => '0.5', 'max_a' => '500000' ),
                            array( 'pair' => 'XTZ/BTC', 'min_q' => '0.1', 'max_q' => '100000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.00001', 'max_a' => '10' ),
                            array( 'pair' => 'XTZ/ETH', 'min_q' => '0.1', 'max_q' => '100000', 'min_p' => '0.0000001', 'max_p' => '10', 'min_a' => '0.0001', 'max_a' => '1000' ),
                            array( 'pair' => 'ZEC/USD', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.001', 'max_p' => '5000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'ZEC/RUB', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.001', 'max_p' => '100000', 'min_a' => '0.1', 'max_a' => '50000000' ),
                            array( 'pair' => 'ZEC/EUR', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.001', 'max_p' => '5000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'ZEC/BTC', 'min_q' => '0.01', 'max_q' => '1000', 'min_p' => '0.00001', 'max_p' => '10', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'ZRX/USD', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00001', 'max_p' => '1000', 'min_a' => '0.1', 'max_a' => '500000' ),
                            array( 'pair' => 'ZRX/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.001', 'max_a' => '100' ),
                            array( 'pair' => 'ZRX/ETH', 'min_q' => '0.01', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '10', 'min_a' => '0.01', 'max_a' => '5000' ),
                            array( 'pair' => 'ZAG/BTC', 'min_q' => '1', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '0.1', 'min_a' => '0.00001', 'max_a' => '100' ),
                            array( 'pair' => 'USD/RUB', 'min_q' => '1', 'max_q' => '500000', 'min_p' => '0.01', 'max_p' => '1000', 'min_a' => '10', 'max_a' => '50000000' ),
                            array( 'pair' => 'EXM/BTC', 'min_q' => '1', 'max_q' => '100000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.0000001', 'max_a' => '1' ),
                            array( 'pair' => 'ATMCASH/BTC', 'min_q' => '10', 'max_q' => '10000000', 'min_p' => '0.00000001', 'max_p' => '1', 'min_a' => '0.000001', 'max_a' => '10' ),
                        ),
                        'fees' => array(
                            array(
                                'group' => 'crypto',
                                'title' => 'Криптовалюта',
                                'items' => array(
                                    array( 'prov' => 'EXM', 'dep' => '0%', 'wd' => '1 EXM' ),
                                    array( 'prov' => 'BTC', 'dep' => '0%', 'wd' => '0.0005 BTC' ),
                                    array( 'prov' => 'LTC', 'dep' => '0%', 'wd' => '0.01 LTC' ),
                                    array( 'prov' => 'DOGE', 'dep' => '0%', 'wd' => '1 DOGE' ),
                                    array( 'prov' => 'DASH', 'dep' => '0%', 'wd' => '0.01 DASH' ),
                                    array( 'prov' => 'ETH', 'dep' => '0%', 'wd' => '0.01 ETH' ),
                                    array( 'prov' => 'WAVES', 'dep' => '0%', 'wd' => '0.001 WAVES' ),
                                    array( 'prov' => 'ZEC', 'dep' => '0%', 'wd' => '0.001 ZEC' ),
                                    array( 'prov' => 'USDT', 'dep' => '0%', 'wd' => '5 USDT' ),
                                    array( 'prov' => 'XMR', 'dep' => '0%', 'wd' => '0.05 XMR' ),
                                    array( 'prov' => 'XRP', 'dep' => '0%', 'wd' => '0.02 XRP' ),
                                    array( 'prov' => 'KICK', 'dep' => '0%', 'wd' => '50 KICK' ),
                                    array( 'prov' => 'ETC', 'dep' => '0%', 'wd' => '0.01 ETC' ),
                                    array( 'prov' => 'BCH', 'dep' => '0%', 'wd' => '0.001 BCH' ),
                                    array( 'prov' => 'BTG', 'dep' => '0%', 'wd' => '0.001 BTG' ),
                                    array( 'prov' => 'EOS', 'dep' => '0%', 'wd' => '0.05 EOS' ),
                                    array( 'prov' => 'BTCZ', 'dep' => '0%', 'wd' => '5 BTCZ' ),
                                    array( 'prov' => 'DXT', 'dep' => '20 DXT', 'wd' => '20 DXT' ),
                                    array( 'prov' => 'XLM', 'dep' => '0%', 'wd' => '0.01 XLM' ),
                                    array( 'prov' => 'MNX', 'dep' => '0%', 'wd' => '0.01 MNX' ),
                                    array( 'prov' => 'OMG', 'dep' => '0.1 OMG', 'wd' => '0.5 OMG' ),
                                    array( 'prov' => 'TRX', 'dep' => '0%', 'wd' => '1 TRX' ),
                                    array( 'prov' => 'ADA', 'dep' => '0%', 'wd' => '1 ADA' ),
                                    array( 'prov' => 'INK', 'dep' => '10 INK', 'wd' => '50 INK' ),
                                    array( 'prov' => 'NEO', 'dep' => '0%', 'wd' => '0%' ),
                                    array( 'prov' => 'GAS', 'dep' => '0%', 'wd' => '0%' ),
                                    array( 'prov' => 'ZRX', 'dep' => '0%', 'wd' => '1 ZRX' ),
                                    array( 'prov' => 'GNT', 'dep' => '0%', 'wd' => '1 GNT' ),
                                    array( 'prov' => 'GUSD', 'dep' => '0%', 'wd' => '0.5 GUSD' ),
                                    array( 'prov' => 'LSK', 'dep' => '0%', 'wd' => '0.1 LSK' ),
                                    array( 'prov' => 'XEM', 'dep' => '0%', 'wd' => '5 XEM' ),
                                    array( 'prov' => 'SMART', 'dep' => '0%', 'wd' => '0.5 SMART' ),
                                    array( 'prov' => 'QTUM', 'dep' => '0%', 'wd' => '0.01 QTUM' ),
                                    array( 'prov' => 'HB', 'dep' => '0%', 'wd' => '10 HB' ),
                                    array( 'prov' => 'DAI', 'dep' => '0%', 'wd' => '1 DAI' ),
                                    array( 'prov' => 'MKR', 'dep' => '0%', 'wd' => '0.005 MKR' ),
                                    array( 'prov' => 'MNC', 'dep' => '0%', 'wd' => '15 MNC' ),
                                    array( 'prov' => 'PTI', 'dep' => '0%', 'wd' => '10 PTI' ),
                                    array( 'prov' => 'ATMCASH', 'dep' => '0%', 'wd' => '5 ATMCASH' ),
                                    array( 'prov' => 'ETZ', 'dep' => '0%', 'wd' => '1 ETZ' ),
                                    array( 'prov' => 'USDC', 'dep' => '0%', 'wd' => '0.5 USDC' ),
                                    array( 'prov' => 'ROOBEE', 'dep' => '0%', 'wd' => '200 ROOBEE' ),
                                    array( 'prov' => 'DCR', 'dep' => '0%', 'wd' => '0.01 DCR' ),
                                    array( 'prov' => 'ZAG', 'dep' => '0%', 'wd' => '0%' ),
                                    array( 'prov' => 'BTT', 'dep' => '0%', 'wd' => '100 BTT' ),
                                    array( 'prov' => 'VLX', 'dep' => '0%', 'wd' => '1 VLX' ),
                                    array( 'prov' => 'CRON', 'dep' => '0%', 'wd' => '5 CRON' ),
                                    array( 'prov' => 'ONT', 'dep' => '0%', 'wd' => '1 ONT' ),
                                    array( 'prov' => 'ONG', 'dep' => '0%', 'wd' => '5 ONG' ),
                                ),
                            ),
                            array(
                                'group' => 'usd',
                                'title' => 'USD',
                                'items' => array(
                                    array( 'prov' => 'EX-CODE', 'dep' => '', 'wd' => '0.2%' ),
                                    array( 'prov' => 'Payeer', 'dep' => '3.95%', 'wd' => '-' ),
                                    array( 'prov' => 'AdvCash', 'dep' => '0%', 'wd' => '3.99%' ),
                                    array( 'prov' => 'Visa/MasterCard (Simplex)', 'dep' => '4.5% . 0.5 USD', 'wd' => '-' ),
                                    array( 'prov' => 'Visa', 'dep' => '3.45%', 'wd' => '-' ),
                                ),
                            ),
                            array(
                                'group' => 'eur',
                                'title' => 'EUR',
                                'items' => array(
                                    array( 'prov' => 'EX-CODE', 'dep' => '', 'wd' => '0.2%' ),
                                    array( 'prov' => 'Visa/MasterCard (Simplex)', 'dep' => '4.5% . 0.5 EUR', 'wd' => '-' ),
                                    array( 'prov' => 'Visa', 'dep' => '2.95%', 'wd' => '-' ),
                                    array( 'prov' => 'SEPA', 'dep' => '10 EUR', 'wd' => '50 EUR' ),
                                ),
                            ),
                            array(
                                'group' => 'rub',
                                'title' => 'RUB',
                                'items' => array(
                                    array( 'prov' => 'Payeer', 'dep' => '2.49%', 'wd' => '-' ),
                                    array( 'prov' => 'EX-CODE', 'dep' => '', 'wd' => '0.2%' ),
                                    array( 'prov' => 'Qiwi', 'dep' => '0.99%', 'wd' => '2%' ),
                                    array( 'prov' => 'Yandex Money', 'dep' => '1.95%', 'wd' => '-' ),
                                    array( 'prov' => 'Enfins', 'dep' => '0%', 'wd' => '1.49%' ),
                                    array( 'prov' => 'AdvCash', 'dep' => '0%', 'wd' => '2.49%' ),
                                    array( 'prov' => 'Visa/MasterCard', 'dep' => '1.99%', 'wd' => '4.49% . 60 RUB' ),
                                ),
                            ),
                            array(
                                'group' => 'pln',
                                'title' => 'PLN',
                                'items' => array(
                                    array( 'prov' => 'EX-CODE', 'dep' => '', 'wd' => '0.2%' ),
                                    array( 'prov' => 'Visa/MasterCard (Simplex)', 'dep' => '4.5% . 1 PLN', 'wd' => '-' ),
                                ),
                            ),
                            array(
                                'group' => 'try',
                                'title' => 'TRY',
                                'items' => array(
                                    array( 'prov' => 'EX-CODE', 'dep' => '', 'wd' => '0.2%' ),
                                    array( 'prov' => 'Visa', 'dep' => '3.05%', 'wd' => '-' ),
                                    array( 'prov' => 'Visa/MasterCard (Simplex)', 'dep' => '4.5% . 2 TRY', 'wd' => '-' ),
                                ),
                            ),
                            array(
                                'group' => 'uah',
                                'title' => 'UAH',
                                'items' => array(
                                    array( 'prov' => 'EX-CODE', 'dep' => '', 'wd' => '0.2%' ),
                                    array( 'prov' => 'Terminal', 'dep' => '2.6%', 'wd' => '-' ),
                                    array( 'prov' => 'Enfins', 'dep' => '0%', 'wd' => '1.95%' ),
                                    array( 'prov' => 'Visa/MasterCard', 'dep' => '1% . 5 UAH', 'wd' => '2.99%' ),
                                ),
                            ),
                            array(
                                'group' => 'kzt',
                                'title' => 'KZT',
                                'items' => array(
                                    array( 'prov' => 'Visa/MasterCard RoyalPay', 'dep' => '-', 'wd' => '2.99% . 450 KZT' ),
                                    array( 'prov' => 'Visa/MasterCard', 'dep' => '3.5%', 'wd' => '-' ),
                                    array( 'prov' => 'AdvCash', 'dep' => '0%', 'wd' => '1.49%' ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    '40005' => '\\ccxt\\AuthenticationError', // Authorization error, incorrect signature
                    '40009' => '\\ccxt\\InvalidNonce', //
                    '40015' => '\\ccxt\\ExchangeError', // API function do not exist
                    '40016' => '\\ccxt\\OnMaintenance', // array("result":false,"error":"Error 40016 => Maintenance work in progress")
                    '40017' => '\\ccxt\\AuthenticationError', // Wrong API Key
                    '40034' => '\\ccxt\\RateLimitExceeded', // array("result":false,"error":"Error 40034 => Access is denied, rate limit is exceeded")
                    '50052' => '\\ccxt\\InsufficientFunds',
                    '50054' => '\\ccxt\\InsufficientFunds',
                    '50304' => '\\ccxt\\OrderNotFound', // "Order was not found '123456789'" (fetching order trades for an order that does not have trades yet)
                    '50173' => '\\ccxt\\OrderNotFound', // "Order with id X was not found." (cancelling non-existent, closed and cancelled order)
                    '50319' => '\\ccxt\\InvalidOrder', // Price by order is less than permissible minimum for this pair
                    '50321' => '\\ccxt\\InvalidOrder', // Price by order is more than permissible maximum for this pair
                ),
                'broad' => array(
                    'API rate limit exceeded' => '\\ccxt\\RateLimitExceeded', // array("result":false,"error":"API rate limit exceeded for 99.33.55.224. Retry after 60 sec.","history":array(),"begin":1579392000,"end":1579478400)
                ),
            ),
        ));
    }

    public function fetch_trading_fees($params = array ()) {
        if ($this->options['useWebapiForFetchingFees']) {
            $response = $this->webGetEnDocsFees ($params);
            $parts = explode('<td class="th_fees_2" colspan="2">', $response);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts !== 2) {
                throw new NotSupported($this->id . ' fetchTradingFees format has changed');
            }
            $rest = $parts[1];
            $parts = explode('</td>', $rest);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts < 2) {
                throw new NotSupported($this->id . ' fetchTradingFees format has changed');
            }
            $fee = floatval (str_replace('%', '', $parts[0])) * 0.01;
            $taker = $fee;
            $maker = $fee;
            return array(
                // 'info' => $response,
                'maker' => $maker,
                'taker' => $taker,
            );
        } else {
            return array(
                'maker' => $this->fees['trading']['maker'],
                'taker' => $this->fees['trading']['taker'],
            );
        }
    }

    public function parse_fixed_float_value($input) {
        if (($input === null) || ($input === '-')) {
            return null;
        }
        $isPercentage = (mb_strpos($input, '%') !== false);
        $parts = explode(' ', $input);
        $value = str_replace('%', '', $parts[0]);
        $result = floatval ($value);
        if (($result > 0) && $isPercentage) {
            throw new ExchangeError($this->id . ' parseFixedFloatValue detected an unsupported non-zero percentage-based fee ' . $input);
        }
        return $result;
    }

    public function fetch_funding_fees($params = array ()) {
        $response = null;
        if ($this->options['useWebapiForFetchingFees']) {
            $response = $this->webGetCtrlFeesAndLimits ($params);
        } else {
            $response = $this->options['feesAndLimits'];
        }
        // the $code below assumes all non-zero crypto fees are fixed (for now)
        $withdraw = array();
        $deposit = array();
        $groups = $this->safe_value($response['data'], 'fees');
        $groupsByGroup = $this->index_by($groups, 'group');
        $items = $groupsByGroup['crypto']['items'];
        for ($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            $code = $this->safe_currency_code($this->safe_string($item, 'prov'));
            $withdrawalFee = $this->safe_string($item, 'wd');
            $depositFee = $this->safe_string($item, 'dep');
            if ($withdrawalFee !== null) {
                if (strlen($withdrawalFee) > 0) {
                    $withdraw[$code] = $this->parse_fixed_float_value($withdrawalFee);
                }
            }
            if ($depositFee !== null) {
                if (strlen($depositFee) > 0) {
                    $deposit[$code] = $this->parse_fixed_float_value($depositFee);
                }
            }
        }
        // sets fiat fees to null
        $fiatGroups = $this->to_array($this->omit($groupsByGroup, 'crypto'));
        for ($i = 0; $i < count($fiatGroups); $i++) {
            $code = $this->safe_currency_code($this->safe_string($fiatGroups[$i], 'title'));
            $withdraw[$code] = null;
            $deposit[$code] = null;
        }
        $result = array(
            'info' => $response,
            'withdraw' => $withdraw,
            'deposit' => $deposit,
        );
        // cache them for later use
        $this->options['fundingFees'] = $result;
        return $result;
    }

    public function fetch_currencies($params = array ()) {
        $fees = $this->fetch_funding_fees($params);
        // todo redesign the 'fee' property in currencies
        $ids = is_array($fees['withdraw']) ? array_keys($fees['withdraw']) : array();
        $limitsByMarketId = $this->index_by($fees['info']['data']['limits'], 'pair');
        $marketIds = is_array($limitsByMarketId) ? array_keys($limitsByMarketId) : array();
        $minAmounts = array();
        $minPrices = array();
        $minCosts = array();
        $maxAmounts = array();
        $maxPrices = array();
        $maxCosts = array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $marketId = $marketIds[$i];
            $limit = $limitsByMarketId[$marketId];
            list($baseId, $quoteId) = explode('/', $marketId);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $maxAmount = $this->safe_float($limit, 'max_q');
            $maxPrice = $this->safe_float($limit, 'max_p');
            $maxCost = $this->safe_float($limit, 'max_a');
            $minAmount = $this->safe_float($limit, 'min_q');
            $minPrice = $this->safe_float($limit, 'min_p');
            $minCost = $this->safe_float($limit, 'min_a');
            $minAmounts[$base] = min ($this->safe_float($minAmounts, $base, $minAmount), $minAmount);
            $maxAmounts[$base] = max ($this->safe_float($maxAmounts, $base, $maxAmount), $maxAmount);
            $minPrices[$quote] = min ($this->safe_float($minPrices, $quote, $minPrice), $minPrice);
            $minCosts[$quote] = min ($this->safe_float($minCosts, $quote, $minCost), $minCost);
            $maxPrices[$quote] = max ($this->safe_float($maxPrices, $quote, $maxPrice), $maxPrice);
            $maxCosts[$quote] = max ($this->safe_float($maxCosts, $quote, $maxCost), $maxCost);
        }
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $code = $this->safe_currency_code($id);
            $fee = $this->safe_value($fees['withdraw'], $code);
            $active = true;
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'name' => $code,
                'active' => $active,
                'fee' => $fee,
                'precision' => 8,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($minAmounts, $code),
                        'max' => $this->safe_float($maxAmounts, $code),
                    ),
                    'price' => array(
                        'min' => $this->safe_float($minPrices, $code),
                        'max' => $this->safe_float($maxPrices, $code),
                    ),
                    'cost' => array(
                        'min' => $this->safe_float($minCosts, $code),
                        'max' => $this->safe_float($maxCosts, $code),
                    ),
                ),
                'info' => $id,
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $fees = $this->fetch_trading_fees();
        $response = $this->publicGetPairSettings ($params);
        $keys = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            $id = $keys[$i];
            $market = $response[$id];
            $symbol = str_replace('_', '/', $id);
            list($baseId, $quoteId) = explode('/', $symbol);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'taker' => $fees['taker'],
                'maker' => $fees['maker'],
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($market, 'min_quantity'),
                        'max' => $this->safe_float($market, 'max_quantity'),
                    ),
                    'price' => array(
                        'min' => $this->safe_float($market, 'min_price'),
                        'max' => $this->safe_float($market, 'max_price'),
                    ),
                    'cost' => array(
                        'min' => $this->safe_float($market, 'min_amount'),
                        'max' => $this->safe_float($market, 'max_amount'),
                    ),
                ),
                'precision' => array(
                    'amount' => 8,
                    'price' => 8,
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostUserInfo ($params);
        $result = array( 'info' => $response );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $currencyId = $this->currency_id($code);
            $account = $this->account();
            if (is_array($response['balances']) && array_key_exists($currencyId, $response['balances'])) {
                $account['free'] = $this->safe_float($response['balances'], $currencyId);
            }
            if (is_array($response['reserved']) && array_key_exists($currencyId, $response['reserved'])) {
                $account['used'] = $this->safe_float($response['reserved'], $currencyId);
            }
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetOrderBook (array_merge($request, $params));
        $result = $this->safe_value($response, $market['id']);
        return $this->parse_order_book($result, null, 'bid', 'ask');
    }

    public function fetch_order_books($symbols = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $ids = null;
        if ($symbols === null) {
            $ids = implode(',', $this->ids);
            // max URL length is 2083 $symbols, including http schema, hostname, tld, etc...
            if (strlen($ids) > 2048) {
                $numIds = is_array($this->ids) ? count($this->ids) : 0;
                throw new ExchangeError($this->id . ' has ' . (string) $numIds . ' $symbols exceeding max URL length, you are required to specify a list of $symbols in the first argument to fetchOrderBooks');
            }
        } else {
            $ids = $this->market_ids($symbols);
            $ids = implode(',', $ids);
        }
        $request = array(
            'pair' => $ids,
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetOrderBook (array_merge($request, $params));
        $result = array();
        $marketIds = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $marketId = $marketIds[$i];
            $symbol = $marketId;
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $symbol = $market['symbol'];
            }
            $result[$symbol] = $this->parse_order_book($response[$marketId], null, 'bid', 'ask');
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->safe_timestamp($ticker, 'updated');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'last_trade');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy_price'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell_price'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $this->safe_float($ticker, 'avg'),
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => $this->safe_float($ticker, 'vol_curr'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker ($params);
        $result = array();
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $this->markets_by_id[$id];
            $symbol = $market['symbol'];
            $ticker = $response[$id];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $result;
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker ($params);
        $market = $this->market($symbol);
        return $this->parse_ticker($response[$market['id']], $market);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $fee = null;
        $symbol = null;
        $id = $this->safe_string($trade, 'trade_id');
        $orderId = $this->safe_string($trade, 'order_id');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'quantity');
        $cost = $this->safe_float($trade, 'amount');
        $side = $this->safe_string($trade, 'type');
        $type = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            if ($market['taker'] !== $market['maker']) {
                throw new ExchangeError($this->id . ' parseTrade can not deduce proper $fee costs, taker and maker fees now differ');
            }
            if (($side === 'buy') && ($amount !== null)) {
                $fee = array(
                    'currency' => $market['base'],
                    'cost' => $amount * $market['taker'],
                    'rate' => $market['taker'],
                );
            } else if (($side === 'sell') && ($cost !== null)) {
                $fee = array(
                    'currency' => $market['quote'],
                    'cost' => $cost * $market['taker'],
                    'rate' => $market['taker'],
                );
            }
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => $type,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        $data = $this->safe_value($response, $market['id'], array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        // a $symbol is required but it can be a single string, or a non-empty array
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument (a single $symbol or an array)');
        }
        $this->load_markets();
        $pair = null;
        $market = null;
        if (gettype($symbol) === 'array' && count(array_filter(array_keys($symbol), 'is_string')) == 0) {
            $numSymbols = is_array($symbol) ? count($symbol) : 0;
            if ($numSymbols < 1) {
                throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a non-empty $symbol array');
            }
            $marketIds = $this->market_ids($symbol);
            $pair = implode(',', $marketIds);
        } else {
            $market = $this->market($symbol);
            $pair = $market['id'];
        }
        $request = array(
            'pair' => $pair,
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privatePostUserTrades (array_merge($request, $params));
        $result = array();
        $marketIds = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $marketId = $marketIds[$i];
            $symbol = null;
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $symbol = $market['symbol'];
            } else {
                list($baseId, $quoteId) = explode('_', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
            $items = $response[$marketId];
            $trades = $this->parse_trades($items, $market, $since, $limit, array(
                'symbol' => $symbol,
            ));
            $result = $this->array_concat($result, $trades);
        }
        return $this->filter_by_since_limit($result, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $prefix = ($type === 'market') ? ($type . '_') : '';
        $market = $this->market($symbol);
        if (($type === 'market') && ($price === null)) {
            $price = 0;
        }
        $request = array(
            'pair' => $market['id'],
            'quantity' => $this->amount_to_precision($symbol, $amount),
            'type' => $prefix . $side,
            'price' => $this->price_to_precision($symbol, $price),
        );
        $response = $this->privatePostOrderCreate (array_merge($request, $params));
        $id = $this->safe_string($response, 'order_id');
        $timestamp = $this->milliseconds();
        $amount = floatval ($amount);
        $price = floatval ($price);
        $status = 'open';
        $order = array(
            'id' => $id,
            'info' => $response,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => $price * $amount,
            'amount' => $amount,
            'remaining' => $amount,
            'filled' => 0.0,
            'fee' => null,
            'trades' => null,
            'clientOrderId' => null,
            'average' => null,
        );
        $this->orders[$id] = $order;
        return $order;
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array( 'order_id' => $id );
        $response = $this->privatePostOrderCancel (array_merge($request, $params));
        if (is_array($this->orders) && array_key_exists($id, $this->orders)) {
            $this->orders[$id]['status'] = 'canceled';
        }
        return $response;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        try {
            $request = array(
                'order_id' => (string) $id,
            );
            $response = $this->privatePostOrderTrades (array_merge($request, $params));
            //
            //     {
            //         "type" => "buy",
            //         "in_currency" => "BTC",
            //         "in_amount" => "1",
            //         "out_currency" => "USD",
            //         "out_amount" => "100",
            //         "trades" => array(
            //             {
            //                 "trade_id" => 3,
            //                 "date" => 1435488248,
            //                 "type" => "buy",
            //                 "pair" => "BTC_USD",
            //                 "order_id" => 12345,
            //                 "quantity" => 1,
            //                 "price" => 100,
            //                 "amount" => 100
            //             }
            //         )
            //     }
            //
            $order = $this->parse_order($response);
            return array_merge($order, array(
                'id' => (string) $id,
            ));
        } catch (Exception $e) {
            if ($e instanceof OrderNotFound) {
                if (is_array($this->orders) && array_key_exists($id, $this->orders)) {
                    return $this->orders[$id];
                }
            }
        }
        throw new OrderNotFound($this->id . ' fetchOrder $order $id ' . (string) $id . ' not found in cache.');
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $request = array(
            'order_id' => (string) $id,
        );
        $response = $this->privatePostOrderTrades (array_merge($request, $params));
        $trades = $this->safe_value($response, 'trades');
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function update_cached_orders($openOrders, $symbol) {
        // update local cache with open orders
        for ($j = 0; $j < count($openOrders); $j++) {
            $id = $openOrders[$j]['id'];
            $this->orders[$id] = $openOrders[$j];
        }
        $openOrdersIndexedById = $this->index_by($openOrders, 'id');
        $cachedOrderIds = is_array($this->orders) ? array_keys($this->orders) : array();
        for ($k = 0; $k < count($cachedOrderIds); $k++) {
            // match each cached $order to an $order in the open orders array
            // possible reasons why a cached $order may be missing in the open orders array:
            // - $order was closed or canceled -> update cache
            // - $symbol mismatch (e.g. cached BTC/USDT, fetched ETH/USDT) -> skip
            $id = $cachedOrderIds[$k];
            $order = $this->orders[$id];
            if (!(is_array($openOrdersIndexedById) && array_key_exists($id, $openOrdersIndexedById))) {
                // cached $order is not in open orders array
                // if we fetched orders by $symbol and it doesn't match the cached $order -> won't update the cached $order
                if ($symbol !== null && $symbol !== $order['symbol']) {
                    continue;
                }
                // $order is cached but not present in the list of open orders -> mark the cached $order as closed
                if ($order['status'] === 'open') {
                    $order = array_merge($order, array(
                        'status' => 'closed', // likewise it might have been canceled externally (unnoticed by "us")
                        'cost' => null,
                        'filled' => $order['amount'],
                        'remaining' => 0.0,
                    ));
                    if ($order['cost'] === null) {
                        if ($order['filled'] !== null) {
                            $order['cost'] = $order['filled'] * $order['price'];
                        }
                    }
                    $this->orders[$id] = $order;
                }
            }
        }
        return $this->to_array($this->orders);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostUserOpenOrders ($params);
        $marketIds = is_array($response) ? array_keys($response) : array();
        $orders = array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $marketId = $marketIds[$i];
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            }
            $parsedOrders = $this->parse_orders($response[$marketId], $market);
            $orders = $this->array_concat($orders, $parsedOrders);
        }
        $this->update_cached_orders($orders, $symbol);
        return $this->filter_by_symbol_since_limit($this->to_array($this->orders), $symbol, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->fetch_orders($symbol, $since, $limit, $params);
        $orders = $this->filter_by($this->orders, 'status', 'open');
        return $this->filter_by_symbol_since_limit($orders, $symbol, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->fetch_orders($symbol, $since, $limit, $params);
        $orders = $this->filter_by($this->orders, 'status', 'closed');
        return $this->filter_by_symbol_since_limit($orders, $symbol, $since, $limit);
    }

    public function parse_order($order, $market = null) {
        //
        // fetchOrders, fetchOpenOrders, fetchClosedOrders
        //
        //     {
        //         "order_id" => "14",
        //         "created" => "1435517311",
        //         "type" => "buy",
        //         "pair" => "BTC_USD",
        //         "$price" => "100",
        //         "quantity" => "1",
        //         "$amount" => "100"
        //     }
        //
        // fetchOrder
        //
        //     {
        //         "type" => "buy",
        //         "in_currency" => "BTC",
        //         "in_amount" => "1",
        //         "out_currency" => "USD",
        //         "out_amount" => "100",
        //         "$trades" => array(
        //             {
        //                 "trade_id" => 3,
        //                 "date" => 1435488248,
        //                 "type" => "buy",
        //                 "pair" => "BTC_USD",
        //                 "order_id" => 12345,
        //                 "quantity" => 1,
        //                 "$price" => 100,
        //                 "$amount" => 100
        //             }
        //         )
        //     }
        //
        $id = $this->safe_string($order, 'order_id');
        $timestamp = $this->safe_timestamp($order, 'created');
        $symbol = null;
        $side = $this->safe_string($order, 'type');
        if ($market === null) {
            $marketId = null;
            if (is_array($order) && array_key_exists('pair', $order)) {
                $marketId = $order['pair'];
            } else if ((is_array($order) && array_key_exists('in_currency', $order)) && (is_array($order) && array_key_exists('out_currency', $order))) {
                if ($side === 'buy') {
                    $marketId = $order['in_currency'] . '_' . $order['out_currency'];
                } else {
                    $marketId = $order['out_currency'] . '_' . $order['in_currency'];
                }
            }
            if (($marketId !== null) && (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id))) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        $amount = $this->safe_float($order, 'quantity');
        if ($amount === null) {
            $amountField = ($side === 'buy') ? 'in_amount' : 'out_amount';
            $amount = $this->safe_float($order, $amountField);
        }
        $price = $this->safe_float($order, 'price');
        $cost = $this->safe_float($order, 'amount');
        $filled = 0.0;
        $trades = array();
        $transactions = $this->safe_value($order, 'trades', array());
        $feeCost = null;
        $lastTradeTimestamp = null;
        $average = null;
        $numTransactions = is_array($transactions) ? count($transactions) : 0;
        if ($numTransactions > 0) {
            $feeCost = 0;
            for ($i = 0; $i < $numTransactions; $i++) {
                $trade = $this->parse_trade($transactions[$i], $market);
                if ($id === null) {
                    $id = $trade['order'];
                }
                if ($timestamp === null) {
                    $timestamp = $trade['timestamp'];
                }
                if ($timestamp > $trade['timestamp']) {
                    $timestamp = $trade['timestamp'];
                }
                $filled = $this->sum($filled, $trade['amount']);
                $feeCost = $this->sum($feeCost, $trade['fee']['cost']);
                $trades[] = $trade;
            }
            $lastTradeTimestamp = $trades[$numTransactions - 1]['timestamp'];
        }
        $remaining = null;
        if ($amount !== null) {
            $remaining = $amount - $filled;
        }
        $status = $this->safe_string($order, 'status'); // in case we need to redefine it for canceled orders
        if ($filled >= $amount) {
            $status = 'closed';
        } else {
            $status = 'open';
        }
        if ($market === null) {
            $market = $this->get_market_from_trades($trades);
        }
        $feeCurrency = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            $feeCurrency = $market['quote'];
        }
        if ($cost === null) {
            if ($price !== null) {
                $cost = $price * $filled;
            }
        } else {
            if ($filled > 0) {
                if ($average === null) {
                    $average = $cost / $filled;
                }
                if ($price === null) {
                    $price = $cost / $filled;
                }
            }
        }
        $fee = array(
            'cost' => $feeCost,
            'currency' => $feeCurrency,
        );
        return array(
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'average' => $average,
            'trades' => $trades,
            'fee' => $fee,
            'info' => $order,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostDepositAddress ($params);
        $depositAddress = $this->safe_string($response, $code);
        $address = null;
        $tag = null;
        if ($depositAddress) {
            $addressAndTag = explode(',', $depositAddress);
            $address = $addressAndTag[0];
            $numParts = is_array($addressAndTag) ? count($addressAndTag) : 0;
            if ($numParts > 1) {
                $tag = $addressAndTag[1];
            }
        }
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function get_market_from_trades($trades) {
        $tradesBySymbol = $this->index_by($trades, 'pair');
        $symbols = is_array($tradesBySymbol) ? array_keys($tradesBySymbol) : array();
        $numSymbols = is_array($symbols) ? count($symbols) : 0;
        if ($numSymbols === 1) {
            return $this->markets[$symbols[0]];
        }
        return null;
    }

    public function calculate_fee($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $rate = $market[$takerOrMaker];
        $cost = floatval ($this->cost_to_precision($symbol, $amount * $rate));
        $key = 'quote';
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
        }
        return array(
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => floatval ($this->fee_to_precision($symbol, $cost)),
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'amount' => $amount,
            'currency' => $currency['id'],
            'address' => $address,
        );
        if ($tag !== null) {
            $request['invoice'] = $tag;
        }
        $response = $this->privatePostWithdrawCrypt (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['task_id'],
        );
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'transferred' => 'ok',
            'paid' => 'ok',
            'pending' => 'pending',
            'processing' => 'pending',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchTransactions
        //
        //          {
        //            "dt" => 1461841192,
        //            "$type" => "deposit",
        //            "curr" => "RUB",
        //            "$status" => "processing",
        //            "$provider" => "Qiwi (LA) [12345]",
        //            "$amount" => "1",
        //            "account" => "",
        //            "$txid" => "ec46f784ad976fd7f7539089d1a129fe46...",
        //          }
        //
        $timestamp = $this->safe_timestamp($transaction, 'dt');
        $amount = $this->safe_float($transaction, 'amount');
        if ($amount !== null) {
            $amount = abs($amount);
        }
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $txid = $this->safe_string($transaction, 'txid');
        $type = $this->safe_string($transaction, 'type');
        $currencyId = $this->safe_string($transaction, 'curr');
        $code = $this->safe_currency_code($currencyId, $currency);
        $address = $this->safe_string($transaction, 'account');
        if ($address !== null) {
            $parts = explode(':', $address);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts === 2) {
                $address = str_replace(' ', '', $parts[1]);
            }
        }
        $fee = null;
        // fixed funding fees only (for now)
        if (!$this->fees['funding']['percentage']) {
            $key = ($type === 'withdrawal') ? 'withdraw' : 'deposit';
            $feeCost = $this->safe_float($this->options['fundingFees'][$key], $code);
            // users don't pay for cashbacks, no fees for that
            $provider = $this->safe_string($transaction, 'provider');
            if ($provider === 'cashback') {
                $feeCost = 0;
            }
            if ($feeCost !== null) {
                // withdrawal $amount includes the $fee
                if ($type === 'withdrawal') {
                    $amount = $amount - $feeCost;
                }
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $code,
                    'rate' => null,
                );
            }
        }
        return array(
            'info' => $transaction,
            'id' => null,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'tag' => null, // refix it properly
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => $fee,
        );
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($since !== null) {
            $request['date'] = intval ($since / 1000);
        }
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $response = $this->privatePostWalletHistory (array_merge($request, $params));
        //
        //     {
        //       "result" => true,
        //       "error" => "",
        //       "begin" => "1493942400",
        //       "end" => "1494028800",
        //       "history" => [
        //          array(
        //            "dt" => 1461841192,
        //            "type" => "deposit",
        //            "curr" => "RUB",
        //            "status" => "processing",
        //            "provider" => "Qiwi (LA) [12345]",
        //            "amount" => "1",
        //            "account" => "",
        //            "txid" => "ec46f784ad976fd7f7539089d1a129fe46...",
        //          ),
        //          array(
        //            "dt" => 1463414785,
        //            "type" => "withdrawal",
        //            "curr" => "USD",
        //            "status" => "paid",
        //            "provider" => "EXCODE",
        //            "amount" => "-1",
        //            "account" => "EX-CODE_19371_USDda...",
        //            "txid" => "",
        //          ),
        //       ],
        //     }
        //
        return $this->parse_transactions($response['history'], $currency, $since, $limit);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/';
        if ($api !== 'web') {
            $url .= $this->version . '/';
        }
        $url .= $path;
        if (($api === 'public') || ($api === 'web')) {
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->urlencode(array_merge(array( 'nonce' => $nonce ), $params));
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        if (is_array($response) && array_key_exists('result', $response)) {
            //
            //     array("result":false,"error":"Error 50052 => Insufficient funds")
            //
            $success = $this->safe_value($response, 'result', false);
            if (gettype($success) === 'string') {
                if (($success === 'true') || ($success === '1')) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
            if (!$success) {
                $code = null;
                $message = $this->safe_string($response, 'error');
                $errorParts = explode(':', $message);
                $numParts = is_array($errorParts) ? count($errorParts) : 0;
                if ($numParts > 1) {
                    $errorSubParts = explode(' ', $errorParts[0]);
                    $numSubParts = is_array($errorSubParts) ? count($errorSubParts) : 0;
                    $code = ($numSubParts > 1) ? $errorSubParts[1] : $errorSubParts[0];
                }
                $feedback = $this->id . ' ' . $body;
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
                throw new ExchangeError($feedback);
            }
        }
    }
}
