<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class deribit extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'deribit',
            'name' => 'Deribit',
            'countries' => array( 'NL' ), // Netherlands
            'version' => 'v2',
            'userAgent' => null,
            'rateLimit' => 500,
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'CORS' => true,
                'createDepositAddress' => true,
                'createOrder' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => false,
                'fetchOrderTrades' => true,
                'fetchStatus' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchTransactions' => false,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1',
                '3m' => '3',
                '5m' => '5',
                '10m' => '10',
                '15m' => '15',
                '30m' => '30',
                '1h' => '60',
                '2h' => '120',
                '3h' => '180',
                '6h' => '360',
                '12h' => '720',
                '1d' => '1D',
            ),
            'urls' => array(
                'test' => 'https://test.deribit.com',
                'logo' => 'https://user-images.githubusercontent.com/1294454/41933112-9e2dd65a-798b-11e8-8440-5bab2959fcb8.jpg',
                'api' => 'https://www.deribit.com',
                'www' => 'https://www.deribit.com',
                'doc' => array(
                    'https://docs.deribit.com/v2',
                    'https://github.com/deribit',
                ),
                'fees' => 'https://www.deribit.com/pages/information/fees',
                'referral' => 'https://www.deribit.com/reg-1189.4038',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        // Authentication
                        'auth',
                        'exchange_token',
                        'fork_token',
                        // Session management
                        'set_heartbeat',
                        'disable_heartbeat',
                        // Supporting
                        'get_time',
                        'hello',
                        'test',
                        // Subscription management
                        'subscribe',
                        'unsubscribe',
                        // Account management
                        'get_announcements',
                        // Market data
                        'get_book_summary_by_currency',
                        'get_book_summary_by_instrument',
                        'get_contract_size',
                        'get_currencies',
                        'get_funding_chart_data',
                        'get_funding_rate_history',
                        'get_funding_rate_value',
                        'get_historical_volatility',
                        'get_index',
                        'get_instruments',
                        'get_last_settlements_by_currency',
                        'get_last_settlements_by_instrument',
                        'get_last_trades_by_currency',
                        'get_last_trades_by_currency_and_time',
                        'get_last_trades_by_instrument',
                        'get_last_trades_by_instrument_and_time',
                        'get_order_book',
                        'get_trade_volumes',
                        'get_tradingview_chart_data',
                        'ticker',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        // Authentication
                        'logout',
                        // Session management
                        'enable_cancel_on_disconnect',
                        'disable_cancel_on_disconnect',
                        'get_cancel_on_disconnect',
                        // Subscription management
                        'subscribe',
                        'unsubscribe',
                        // Account management
                        'change_api_key_name',
                        'change_scope_in_api_key',
                        'change_subaccount_name',
                        'create_api_key',
                        'create_subaccount',
                        'disable_api_key',
                        'disable_tfa_for_subaccount',
                        'enable_api_key',
                        'get_account_summary',
                        'get_email_language',
                        'get_new_announcements',
                        'get_position',
                        'get_positions',
                        'get_subaccounts',
                        'list_api_keys',
                        'remove_api_key',
                        'reset_api_key',
                        'set_announcement_as_read',
                        'set_api_key_as_default',
                        'set_email_for_subaccount',
                        'set_email_language',
                        'set_password_for_subaccount',
                        'toggle_notifications_from_subaccount',
                        'toggle_subaccount_login',
                        // Block Trade
                        'execute_block_trade',
                        'get_block_trade',
                        'get_last_block_trades_by_currency',
                        'invalidate_block_trade_signature',
                        'verify_block_trade',
                        // Trading
                        'buy',
                        'sell',
                        'edit',
                        'cancel',
                        'cancel_all',
                        'cancel_all_by_currency',
                        'cancel_all_by_instrument',
                        'cancel_by_label',
                        'close_position',
                        'get_margins',
                        'get_open_orders_by_currency',
                        'get_open_orders_by_instrument',
                        'get_order_history_by_currency',
                        'get_order_history_by_instrument',
                        'get_order_margin_by_ids',
                        'get_order_state',
                        'get_stop_order_history',
                        'get_user_trades_by_currency',
                        'get_user_trades_by_currency_and_time',
                        'get_user_trades_by_instrument',
                        'get_user_trades_by_instrument_and_time',
                        'get_user_trades_by_order',
                        'get_settlement_history_by_instrument',
                        'get_settlement_history_by_currency',
                        // Wallet
                        'cancel_transfer_by_id',
                        'cancel_withdrawal',
                        'create_deposit_address',
                        'get_current_deposit_address',
                        'get_deposits',
                        'get_transfers',
                        'get_withdrawals',
                        'submit_transfer_to_subaccount',
                        'submit_transfer_to_user',
                        'withdraw',
                    ),
                ),
            ),
            'exceptions' => array(
                // 0 or absent Success, No error.
                '9999' => '\\ccxt\\PermissionDenied', // 'api_not_enabled' User didn't enable API for the Account.
                '10000' => '\\ccxt\\AuthenticationError', // 'authorization_required' Authorization issue, invalid or absent signature etc.
                '10001' => '\\ccxt\\ExchangeError', // 'error' Some general failure, no public information available.
                '10002' => '\\ccxt\\InvalidOrder', // 'qty_too_low' Order quantity is too low.
                '10003' => '\\ccxt\\InvalidOrder', // 'order_overlap' Rejection, order overlap is found and self-trading is not enabled.
                '10004' => '\\ccxt\\OrderNotFound', // 'order_not_found' Attempt to operate with order that can't be found by specified id.
                '10005' => '\\ccxt\\InvalidOrder', // 'price_too_low <Limit>' Price is too low, <Limit> defines current limit for the operation.
                '10006' => '\\ccxt\\InvalidOrder', // 'price_too_low4idx <Limit>' Price is too low for current index, <Limit> defines current bottom limit for the operation.
                '10007' => '\\ccxt\\InvalidOrder', // 'price_too_high <Limit>' Price is too high, <Limit> defines current up limit for the operation.
                '10008' => '\\ccxt\\InvalidOrder', // 'price_too_high4idx <Limit>' Price is too high for current index, <Limit> defines current up limit for the operation.
                '10009' => '\\ccxt\\InsufficientFunds', // 'not_enough_funds' Account has not enough funds for the operation.
                '10010' => '\\ccxt\\OrderNotFound', // 'already_closed' Attempt of doing something with closed order.
                '10011' => '\\ccxt\\InvalidOrder', // 'price_not_allowed' This price is not allowed for some reason.
                '10012' => '\\ccxt\\InvalidOrder', // 'book_closed' Operation for instrument which order book had been closed.
                '10013' => '\\ccxt\\PermissionDenied', // 'pme_max_total_open_orders <Limit>' Total limit of open orders has been exceeded, it is applicable for PME users.
                '10014' => '\\ccxt\\PermissionDenied', // 'pme_max_future_open_orders <Limit>' Limit of count of futures' open orders has been exceeded, it is applicable for PME users.
                '10015' => '\\ccxt\\PermissionDenied', // 'pme_max_option_open_orders <Limit>' Limit of count of options' open orders has been exceeded, it is applicable for PME users.
                '10016' => '\\ccxt\\PermissionDenied', // 'pme_max_future_open_orders_size <Limit>' Limit of size for futures has been exceeded, it is applicable for PME users.
                '10017' => '\\ccxt\\PermissionDenied', // 'pme_max_option_open_orders_size <Limit>' Limit of size for options has been exceeded, it is applicable for PME users.
                '10018' => '\\ccxt\\PermissionDenied', // 'non_pme_max_future_position_size <Limit>' Limit of size for futures has been exceeded, it is applicable for non-PME users.
                '10019' => '\\ccxt\\PermissionDenied', // 'locked_by_admin' Trading is temporary locked by admin.
                '10020' => '\\ccxt\\ExchangeError', // 'invalid_or_unsupported_instrument' Instrument name is not valid.
                '10021' => '\\ccxt\\InvalidOrder', // 'invalid_amount' Amount is not valid.
                '10022' => '\\ccxt\\InvalidOrder', // 'invalid_quantity' quantity was not recognized as a valid number (for API v1).
                '10023' => '\\ccxt\\InvalidOrder', // 'invalid_price' price was not recognized as a valid number.
                '10024' => '\\ccxt\\InvalidOrder', // 'invalid_max_show' max_show parameter was not recognized as a valid number.
                '10025' => '\\ccxt\\InvalidOrder', // 'invalid_order_id' Order id is missing or its format was not recognized as valid.
                '10026' => '\\ccxt\\InvalidOrder', // 'price_precision_exceeded' Extra precision of the price is not supported.
                '10027' => '\\ccxt\\InvalidOrder', // 'non_integer_contract_amount' Futures contract amount was not recognized as integer.
                '10028' => '\\ccxt\\DDoSProtection', // 'too_many_requests' Allowed request rate has been exceeded.
                '10029' => '\\ccxt\\OrderNotFound', // 'not_owner_of_order' Attempt to operate with not own order.
                '10030' => '\\ccxt\\ExchangeError', // 'must_be_websocket_request' REST request where Websocket is expected.
                '10031' => '\\ccxt\\ExchangeError', // 'invalid_args_for_instrument' Some of arguments are not recognized as valid.
                '10032' => '\\ccxt\\InvalidOrder', // 'whole_cost_too_low' Total cost is too low.
                '10033' => '\\ccxt\\NotSupported', // 'not_implemented' Method is not implemented yet.
                '10034' => '\\ccxt\\InvalidOrder', // 'stop_price_too_high' Stop price is too high.
                '10035' => '\\ccxt\\InvalidOrder', // 'stop_price_too_low' Stop price is too low.
                '10036' => '\\ccxt\\InvalidOrder', // 'invalid_max_show_amount' Max Show Amount is not valid.
                '10040' => '\\ccxt\\ExchangeNotAvailable', // 'retry' Request can't be processed right now and should be retried.
                '10041' => '\\ccxt\\OnMaintenance', // 'settlement_in_progress' Settlement is in progress. Every day at settlement time for several seconds, the system calculates user profits and updates balances. That time trading is paused for several seconds till the calculation is completed.
                '10043' => '\\ccxt\\InvalidOrder', // 'price_wrong_tick' Price has to be rounded to a certain tick size.
                '10044' => '\\ccxt\\InvalidOrder', // 'stop_price_wrong_tick' Stop Price has to be rounded to a certain tick size.
                '10045' => '\\ccxt\\InvalidOrder', // 'can_not_cancel_liquidation_order' Liquidation order can't be canceled.
                '10046' => '\\ccxt\\InvalidOrder', // 'can_not_edit_liquidation_order' Liquidation order can't be edited.
                '10047' => '\\ccxt\\DDoSProtection', // 'matching_engine_queue_full' Reached limit of pending Matching Engine requests for user.
                '10048' => '\\ccxt\\ExchangeError', // 'not_on_this_server' The requested operation is not available on this server.
                '11008' => '\\ccxt\\InvalidOrder', // 'already_filled' This request is not allowed in regards to the filled order.
                '11029' => '\\ccxt\\BadRequest', // 'invalid_arguments' Some invalid input has been detected.
                '11030' => '\\ccxt\\ExchangeError', // 'other_reject <Reason>' Some rejects which are not considered as very often, more info may be specified in <Reason>.
                '11031' => '\\ccxt\\ExchangeError', // 'other_error <Error>' Some errors which are not considered as very often, more info may be specified in <Error>.
                '11035' => '\\ccxt\\DDoSProtection', // 'no_more_stops <Limit>' Allowed amount of stop orders has been exceeded.
                '11036' => '\\ccxt\\InvalidOrder', // 'invalid_stoppx_for_index_or_last' Invalid StopPx (too high or too low) as to current index or market.
                '11037' => '\\ccxt\\BadRequest', // 'outdated_instrument_for_IV_order' Instrument already not available for trading.
                '11038' => '\\ccxt\\InvalidOrder', // 'no_adv_for_futures' Advanced orders are not available for futures.
                '11039' => '\\ccxt\\InvalidOrder', // 'no_adv_postonly' Advanced post-only orders are not supported yet.
                '11041' => '\\ccxt\\InvalidOrder', // 'not_adv_order' Advanced order properties can't be set if the order is not advanced.
                '11042' => '\\ccxt\\PermissionDenied', // 'permission_denied' Permission for the operation has been denied.
                '11043' => '\\ccxt\\BadRequest', // 'bad_argument' Bad argument has been passed.
                '11044' => '\\ccxt\\InvalidOrder', // 'not_open_order' Attempt to do open order operations with the not open order.
                '11045' => '\\ccxt\\BadRequest', // 'invalid_event' Event name has not been recognized.
                '11046' => '\\ccxt\\BadRequest', // 'outdated_instrument' At several minutes to instrument expiration, corresponding advanced implied volatility orders are not allowed.
                '11047' => '\\ccxt\\BadRequest', // 'unsupported_arg_combination' The specified combination of arguments is not supported.
                '11048' => '\\ccxt\\ExchangeError', // 'wrong_max_show_for_option' Wrong Max Show for options.
                '11049' => '\\ccxt\\BadRequest', // 'bad_arguments' Several bad arguments have been passed.
                '11050' => '\\ccxt\\BadRequest', // 'bad_request' Request has not been parsed properly.
                '11051' => '\\ccxt\\OnMaintenance', // 'system_maintenance' System is under maintenance.
                '11052' => '\\ccxt\\ExchangeError', // 'subscribe_error_unsubscribed' Subscription error. However, subscription may fail without this error, please check list of subscribed channels returned, as some channels can be not subscribed due to wrong input or lack of permissions.
                '11053' => '\\ccxt\\ExchangeError', // 'transfer_not_found' Specified transfer is not found.
                '11090' => '\\ccxt\\InvalidAddress', // 'invalid_addr' Invalid address.
                '11091' => '\\ccxt\\InvalidAddress', // 'invalid_transfer_address' Invalid addres for the transfer.
                '11092' => '\\ccxt\\InvalidAddress', // 'address_already_exist' The address already exists.
                '11093' => '\\ccxt\\DDoSProtection', // 'max_addr_count_exceeded' Limit of allowed addresses has been reached.
                '11094' => '\\ccxt\\ExchangeError', // 'internal_server_error' Some unhandled error on server. Please report to admin. The details of the request will help to locate the problem.
                '11095' => '\\ccxt\\ExchangeError', // 'disabled_deposit_address_creation' Deposit address creation has been disabled by admin.
                '11096' => '\\ccxt\\ExchangeError', // 'address_belongs_to_user' Withdrawal instead of transfer.
                '12000' => '\\ccxt\\AuthenticationError', // 'bad_tfa' Wrong TFA code
                '12001' => '\\ccxt\\DDoSProtection', // 'too_many_subaccounts' Limit of subbacounts is reached.
                '12002' => '\\ccxt\\ExchangeError', // 'wrong_subaccount_name' The input is not allowed as name of subaccount.
                '12998' => '\\ccxt\\AuthenticationError', // 'tfa_over_limit' The number of failed TFA attempts is limited.
                '12003' => '\\ccxt\\AuthenticationError', // 'login_over_limit' The number of failed login attempts is limited.
                '12004' => '\\ccxt\\AuthenticationError', // 'registration_over_limit' The number of registration requests is limited.
                '12005' => '\\ccxt\\AuthenticationError', // 'country_is_banned' The country is banned (possibly via IP check).
                '12100' => '\\ccxt\\ExchangeError', // 'transfer_not_allowed' Transfer is not allowed. Possible wrong direction or other mistake.
                '12999' => '\\ccxt\\AuthenticationError', // 'tfa_used' TFA code is correct but it is already used. Please, use next code.
                '13000' => '\\ccxt\\AuthenticationError', // 'invalid_login' Login name is invalid (not allowed or it contains wrong characters).
                '13001' => '\\ccxt\\AuthenticationError', // 'account_not_activated' Account must be activated.
                '13002' => '\\ccxt\\PermissionDenied', // 'account_blocked' Account is blocked by admin.
                '13003' => '\\ccxt\\AuthenticationError', // 'tfa_required' This action requires TFA authentication.
                '13004' => '\\ccxt\\AuthenticationError', // 'invalid_credentials' Invalid credentials has been used.
                '13005' => '\\ccxt\\AuthenticationError', // 'pwd_match_error' Password confirmation error.
                '13006' => '\\ccxt\\AuthenticationError', // 'security_error' Invalid Security Code.
                '13007' => '\\ccxt\\AuthenticationError', // 'user_not_found' User's security code has been changed or wrong.
                '13008' => '\\ccxt\\ExchangeError', // 'request_failed' Request failed because of invalid input or internal failure.
                '13009' => '\\ccxt\\AuthenticationError', // 'unauthorized' Wrong or expired authorization token or bad signature. For example, please check scope of the token, 'connection' scope can't be reused for other connections.
                '13010' => '\\ccxt\\BadRequest', // 'value_required' Invalid input, missing value.
                '13011' => '\\ccxt\\BadRequest', // 'value_too_short' Input is too short.
                '13012' => '\\ccxt\\PermissionDenied', // 'unavailable_in_subaccount' Subaccount restrictions.
                '13013' => '\\ccxt\\BadRequest', // 'invalid_phone_number' Unsupported or invalid phone number.
                '13014' => '\\ccxt\\BadRequest', // 'cannot_send_sms' SMS sending failed -- phone number is wrong.
                '13015' => '\\ccxt\\BadRequest', // 'invalid_sms_code' Invalid SMS code.
                '13016' => '\\ccxt\\BadRequest', // 'invalid_input' Invalid input.
                '13017' => '\\ccxt\\ExchangeError', // 'subscription_failed' Subscription hailed, invalid subscription parameters.
                '13018' => '\\ccxt\\ExchangeError', // 'invalid_content_type' Invalid content type of the request.
                '13019' => '\\ccxt\\ExchangeError', // 'orderbook_closed' Closed, expired order book.
                '13020' => '\\ccxt\\ExchangeError', // 'not_found' Instrument is not found, invalid instrument name.
                '13021' => '\\ccxt\\PermissionDenied', // 'forbidden' Not enough permissions to execute the request, forbidden.
                '13025' => '\\ccxt\\ExchangeError', // 'method_switched_off_by_admin' API method temporarily switched off by administrator.
                '-32602' => '\\ccxt\\BadRequest', // 'Invalid params' see JSON-RPC spec.
                '-32601' => '\\ccxt\\BadRequest', // 'Method not found' see JSON-RPC spec.
                '-32700' => '\\ccxt\\BadRequest', // 'Parse error' see JSON-RPC spec.
                '-32000' => '\\ccxt\\BadRequest', // 'Missing params' see JSON-RPC spec.
            ),
            'precisionMode' => TICK_SIZE,
            'options' => array(
                'code' => 'BTC',
                'fetchBalance' => array(
                    'code' => 'BTC',
                ),
            ),
        ));
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetGetTime ($params);
        //
        //     {
        //         jsonrpc => '2.0',
        //         result => 1583922446019,
        //         usIn => 1583922446019955,
        //         usOut => 1583922446019956,
        //         usDiff => 1,
        //         testnet => false
        //     }
        //
        return $this->safe_integer($response, 'result');
    }

    public function code_from_options($methodName) {
        $defaultCode = $this->safe_value($this->options, 'code', 'BTC');
        $options = $this->safe_value($this->options, $methodName, array());
        return $this->safe_value($options, 'code', $defaultCode);
    }

    public function fetch_status($params = array ()) {
        $request = array(
            // 'expected_result' => false, // true will trigger an error for testing purposes
        );
        $this->publicGetTest (array_merge($request, $params));
        //
        //     {
        //         jsonrpc => '2.0',
        //         result => array( version => '1.2.26' ),
        //         usIn => 1583922623964485,
        //         usOut => 1583922623964487,
        //         usDiff => 2,
        //         testnet => false
        //     }
        //
        $this->status = array_merge($this->status, array(
            'status' => 'ok',
            'updated' => $this->milliseconds(),
        ));
        return $this->status;
    }

    public function fetch_markets($params = array ()) {
        $currenciesResponse = $this->publicGetGetCurrencies ($params);
        //
        //     {
        //         jsonrpc => '2.0',
        //         $result => array(
        //             {
        //                 withdrawal_priorities => array(
        //                     array( value => 0.15, name => 'very_low' ),
        //                     array( value => 1.5, name => 'very_high' ),
        //                 ),
        //                 withdrawal_fee => 0.0005,
        //                 min_withdrawal_fee => 0.0005,
        //                 min_confirmations => 1,
        //                 fee_precision => 4,
        //                 currency_long => 'Bitcoin',
        //                 currency => 'BTC',
        //                 coin_type => 'BITCOIN'
        //             }
        //         ),
        //         usIn => 1583761588590479,
        //         usOut => 1583761588590544,
        //         usDiff => 65,
        //         testnet => false
        //     }
        //
        $currenciesResult = $this->safe_value($currenciesResponse, 'result', array());
        $result = array();
        for ($i = 0; $i < count($currenciesResult); $i++) {
            $currencyId = $this->safe_string($currenciesResult[$i], 'currency');
            $request = array(
                'currency' => $currencyId,
            );
            $instrumentsResponse = $this->publicGetGetInstruments (array_merge($request, $params));
            //
            //     {
            //         jsonrpc => '2.0',
            //         $result => array(
            //             array(
            //                 tick_size => 0.0005,
            //                 taker_commission => 0.0004,
            //                 strike => 300,
            //                 settlement_period => 'week',
            //                 quote_currency => 'USD',
            //                 option_type => 'call',
            //                 min_trade_amount => 1,
            //                 maker_commission => 0.0004,
            //                 kind => 'option',
            //                 is_active => true,
            //                 instrument_name => 'ETH-13MAR20-300-C',
            //                 expiration_timestamp => 1584086400000,
            //                 creation_timestamp => 1582790403000,
            //                 contract_size => 1,
            //                 base_currency => 'ETH'
            //             ),
            //         ),
            //         usIn => 1583761889500586,
            //         usOut => 1583761889505066,
            //         usDiff => 4480,
            //         testnet => false
            //     }
            //
            $instrumentsResult = $this->safe_value($instrumentsResponse, 'result', array());
            for ($k = 0; $k < count($instrumentsResult); $k++) {
                $market = $instrumentsResult[$k];
                $id = $this->safe_string($market, 'instrument_name');
                $baseId = $this->safe_string($market, 'base_currency');
                $quoteId = $this->safe_string($market, 'quote_currency');
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $type = $this->safe_string($market, 'kind');
                $future = ($type === 'future');
                $option = ($type === 'option');
                $active = $this->safe_value($market, 'is_active');
                $minTradeAmount = $this->safe_float($market, 'min_trade_amount');
                $tickSize = $this->safe_float($market, 'tick_size');
                $precision = array(
                    'amount' => $minTradeAmount,
                    'price' => $tickSize,
                );
                $result[] = array(
                    'id' => $id,
                    'symbol' => $id,
                    'base' => $base,
                    'quote' => $quote,
                    'active' => $active,
                    'precision' => $precision,
                    'taker' => $this->safe_float($market, 'taker_commission'),
                    'maker' => $this->safe_float($market, 'maker_commission'),
                    'limits' => array(
                        'amount' => array(
                            'min' => $minTradeAmount,
                            'max' => null,
                        ),
                        'price' => array(
                            'min' => $tickSize,
                            'max' => null,
                        ),
                        'cost' => array(
                            'min' => null,
                            'max' => null,
                        ),
                    ),
                    'type' => $type,
                    'spot' => false,
                    'future' => $future,
                    'option' => $option,
                    'info' => $market,
                );
            }
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $code = $this->code_from_options('fetchBalance');
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateGetGetAccountSummary (array_merge($request, $params));
        //
        //     {
        //         jsonrpc => '2.0',
        //         $result => array(
        //             total_pl => 0,
        //             session_upl => 0,
        //             session_rpl => 0,
        //             session_funding => 0,
        //             portfolio_margining_enabled => false,
        //             options_vega => 0,
        //             options_theta => 0,
        //             options_session_upl => 0,
        //             options_session_rpl => 0,
        //             options_pl => 0,
        //             options_gamma => 0,
        //             options_delta => 0,
        //             margin_balance => 0.00062359,
        //             maintenance_margin => 0,
        //             limits => array(
        //                 non_matching_engine_burst => 300,
        //                 non_matching_engine => 200,
        //                 matching_engine_burst => 20,
        //                 matching_engine => 2
        //             ),
        //             initial_margin => 0,
        //             futures_session_upl => 0,
        //             futures_session_rpl => 0,
        //             futures_pl => 0,
        //             equity => 0.00062359,
        //             deposit_address => '13tUtNsJSZa1F5GeCmwBywVrymHpZispzw',
        //             delta_total => 0,
        //             $currency => 'BTC',
        //             $balance => 0.00062359,
        //             available_withdrawal_funds => 0.00062359,
        //             available_funds => 0.00062359
        //         ),
        //         usIn => 1583775838115975,
        //         usOut => 1583775838116520,
        //         usDiff => 545,
        //         testnet => false
        //     }
        //
        $result = array(
            'info' => $response,
        );
        $balance = $this->safe_value($response, 'result', array());
        $currencyId = $this->safe_string($balance, 'currency');
        $currencyCode = $this->safe_currency_code($currencyId);
        $account = $this->account();
        $account['free'] = $this->safe_float($balance, 'availableFunds');
        $account['used'] = $this->safe_float($balance, 'maintenanceMargin');
        $account['total'] = $this->safe_float($balance, 'equity');
        $result[$currencyCode] = $account;
        return $this->parse_balance($result);
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateGetCreateDepositAddress (array_merge($request, $params));
        //
        //     {
        //         'jsonrpc' => '2.0',
        //         'id' => 7538,
        //         'result' => {
        //             'address' => '2N8udZGBc1hLRCFsU9kGwMPpmYUwMFTuCwB',
        //             'creation_timestamp' => 1550575165170,
        //             'currency' => 'BTC',
        //             'type' => 'deposit'
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $address = $this->safe_string($result, 'address');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => null,
            'info' => $response,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateGetGetCurrentDepositAddress (array_merge($request, $params));
        //
        //     {
        //         jsonrpc => '2.0',
        //         $result => array(
        //             type => 'deposit',
        //             status => 'ready',
        //             requires_confirmation => true,
        //             $currency => 'BTC',
        //             creation_timestamp => 1514694684651,
        //             $address => '13tUtNsJSZa1F5GeCmwBywVrymHpZispzw'
        //         ),
        //         usIn => 1583785137274288,
        //         usOut => 1583785137274454,
        //         usDiff => 166,
        //         testnet => false
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $address = $this->safe_string($result, 'address');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => null,
            'info' => $response,
        );
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // fetchTicker /public/ticker
        //
        //     {
        //         $timestamp => 1583778859480,
        //         $stats => array( volume => 60627.57263769, low => 7631.5, high => 8311.5 ),
        //         state => 'open',
        //         settlement_price => 7903.21,
        //         open_interest => 111543850,
        //         min_price => 7634,
        //         max_price => 7866.51,
        //         mark_price => 7750.02,
        //         last_price => 7750.5,
        //         instrument_name => 'BTC-PERPETUAL',
        //         index_price => 7748.01,
        //         funding_8h => 0.0000026,
        //         current_funding => 0,
        //         best_bid_price => 7750,
        //         best_bid_amount => 19470,
        //         best_ask_price => 7750.5,
        //         best_ask_amount => 343280
        //     }
        //
        // fetchTicker /public/get_book_summary_by_instrument
        // fetchTickers /public/get_book_summary_by_currency
        //
        //     array(
        //         volume => 124.1,
        //         underlying_price => 7856.445926872601,
        //         underlying_index => 'SYN.BTC-10MAR20',
        //         quote_currency => 'USD',
        //         open_interest => 121.8,
        //         mid_price => 0.01975,
        //         mark_price => 0.01984559,
        //         low => 0.0095,
        //         $last => 0.0205,
        //         interest_rate => 0,
        //         instrument_name => 'BTC-10MAR20-7750-C',
        //         high => 0.0295,
        //         estimated_delivery_price => 7856.29,
        //         creation_timestamp => 1583783678366,
        //         bid_price => 0.0185,
        //         base_currency => 'BTC',
        //         ask_price => 0.021
        //     ),
        //
        $timestamp = $this->safe_integer_2($ticker, 'timestamp', 'creation_timestamp');
        $marketId = $this->safe_string($ticker, 'instrument_name');
        $symbol = $this->safe_symbol($marketId, $market);
        $last = $this->safe_float_2($ticker, 'last_price', 'last');
        $stats = $this->safe_value($ticker, 'stats', $ticker);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float_2($stats, 'high', 'max_price'),
            'low' => $this->safe_float_2($stats, 'low', 'min_price'),
            'bid' => $this->safe_float_2($ticker, 'best_bid_price', 'bid_price'),
            'bidVolume' => $this->safe_float($ticker, 'best_bid_amount'),
            'ask' => $this->safe_float_2($ticker, 'best_ask_price', 'ask_price'),
            'askVolume' => $this->safe_float($ticker, 'best_ask_amount'),
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => $this->safe_float($stats, 'volume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument_name' => $market['id'],
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        //
        //     {
        //         jsonrpc => '2.0',
        //         $result => array(
        //             timestamp => 1583778859480,
        //             stats => array( volume => 60627.57263769, low => 7631.5, high => 8311.5 ),
        //             state => 'open',
        //             settlement_price => 7903.21,
        //             open_interest => 111543850,
        //             min_price => 7634,
        //             max_price => 7866.51,
        //             mark_price => 7750.02,
        //             last_price => 7750.5,
        //             instrument_name => 'BTC-PERPETUAL',
        //             index_price => 7748.01,
        //             funding_8h => 0.0000026,
        //             current_funding => 0,
        //             best_bid_price => 7750,
        //             best_bid_amount => 19470,
        //             best_ask_price => 7750.5,
        //             best_ask_amount => 343280
        //         ),
        //         usIn => 1583778859483941,
        //         usOut => 1583778859484075,
        //         usDiff => 134,
        //         testnet => false
        //     }
        //
        $result = $this->safe_value($response, 'result');
        return $this->parse_ticker($result, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $code = $this->code_from_options('fetchTickers');
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->publicGetGetBookSummaryByCurrency (array_merge($request, $params));
        //
        //     {
        //         jsonrpc => '2.0',
        //         $result => array(
        //             array(
        //                 volume => 124.1,
        //                 underlying_price => 7856.445926872601,
        //                 underlying_index => 'SYN.BTC-10MAR20',
        //                 quote_currency => 'USD',
        //                 open_interest => 121.8,
        //                 mid_price => 0.01975,
        //                 mark_price => 0.01984559,
        //                 low => 0.0095,
        //                 last => 0.0205,
        //                 interest_rate => 0,
        //                 instrument_name => 'BTC-10MAR20-7750-C',
        //                 high => 0.0295,
        //                 estimated_delivery_price => 7856.29,
        //                 creation_timestamp => 1583783678366,
        //                 bid_price => 0.0185,
        //                 base_currency => 'BTC',
        //                 ask_price => 0.021
        //             ),
        //         ),
        //         usIn => 1583783678361966,
        //         usOut => 1583783678372069,
        //         usDiff => 10103,
        //         testnet => false
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $tickers = array();
        for ($i = 0; $i < count($result); $i++) {
            $ticker = $this->parse_ticker($result[$i]);
            $symbol = $ticker['symbol'];
            $tickers[$symbol] = $ticker;
        }
        return $this->filter_by_array($tickers, 'symbol', $symbols);
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument_name' => $market['id'],
            'resolution' => $this->timeframes[$timeframe],
        );
        $duration = $this->parse_timeframe($timeframe);
        $now = $this->milliseconds();
        if ($since === null) {
            if ($limit === null) {
                throw new ArgumentsRequired($this->id . ' fetchOHLCV() requires a $since argument or a $limit argument');
            } else {
                $request['start_timestamp'] = $now - ($limit - 1) * $duration * 1000;
                $request['end_timestamp'] = $now;
            }
        } else {
            $request['start_timestamp'] = $since;
            if ($limit === null) {
                $request['end_timestamp'] = $now;
            } else {
                $request['end_timestamp'] = $this->sum($since, $limit * $duration * 1000);
            }
        }
        $response = $this->publicGetGetTradingviewChartData (array_merge($request, $params));
        //
        //     {
        //         jsonrpc => '2.0',
        //         $result => array(
        //             volume => array( 3.6680847969999992, 22.682721123, 3.011587939, 0 ),
        //             ticks => array( 1583916960000, 1583917020000, 1583917080000, 1583917140000 ),
        //             status => 'ok',
        //             open => array( 7834, 7839, 7833.5, 7833 ),
        //             low => array( 7834, 7833.5, 7832.5, 7833 ),
        //             high => array( 7839.5, 7839, 7833.5, 7833 ),
        //             cost => array( 28740, 177740, 23590, 0 ),
        //             close => array( 7839.5, 7833.5, 7833, 7833 )
        //         ),
        //         usIn => 1583917166709801,
        //         usOut => 1583917166710175,
        //         usDiff => 374,
        //         testnet => false
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $ohlcvs = $this->convert_trading_view_to_ohlcv($result, 'ticks', 'open', 'high', 'low', 'close', 'volume', true);
        return $this->parse_ohlcvs($ohlcvs, $market, $timeframe, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         'trade_seq' => 39201926,
        //         'trade_id':' 64135724',
        //         'timestamp' => 1583174775400,
        //         'tick_direction' => 1,
        //         'price' => 8865.0,
        //         'instrument_name' => 'BTC-PERPETUAL',
        //         'index_price' => 8863.31,
        //         'direction' => 'buy',
        //         'amount' => 10.0
        //     }
        //
        // fetchMyTrades, fetchOrderTrades (private)
        //
        //     {
        //         "trade_seq" => 3,
        //         "trade_id" => "ETH-34066",
        //         "$timestamp" => 1550219814585,
        //         "tick_direction" => 1,
        //         "state" => "open",
        //         "self_trade" => false,
        //         "reduce_only" => false,
        //         "$price" => 0.04,
        //         "post_only" => false,
        //         "order_type" => "limit",
        //         "order_id" => "ETH-334607",
        //         "matching_id" => null,
        //         "$liquidity" => "M",
        //         "iv" => 56.83,
        //         "instrument_name" => "ETH-22FEB19-120-C",
        //         "index_price" => 121.37,
        //         "fee_currency" => "ETH",
        //         "$fee" => 0.0011,
        //         "direction" => "buy",
        //         "$amount" => 11
        //     }
        //
        $id = $this->safe_string($trade, 'trade_id');
        $marketId = $this->safe_string($trade, 'instrument_name');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->safe_integer($trade, 'timestamp');
        $side = $this->safe_string($trade, 'direction');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $amount * $price;
            }
        }
        $liquidity = $this->safe_string($trade, 'liquidity');
        $takerOrMaker = null;
        if ($liquidity !== null) {
            // M = maker, T = taker, MT = both
            $takerOrMaker = ($liquidity === 'M') ? 'maker' : 'taker';
        }
        $feeCost = $this->safe_float($trade, 'fee');
        $fee = null;
        if ($feeCost !== null) {
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
            'symbol' => $symbol,
            'order' => $this->safe_string($trade, 'order_id'),
            'type' => $this->safe_string($trade, 'order_type'),
            'side' => $side,
            'takerOrMaker' => $takerOrMaker,
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
            'instrument_name' => $market['id'],
            'include_old' => true,
        );
        $method = ($since === null) ? 'publicGetGetLastTradesByInstrument' : 'publicGetGetLastTradesByInstrumentAndTime';
        if ($since !== null) {
            $request['start_timestamp'] = $since;
        }
        if ($limit !== null) {
            $request['count'] = $limit; // default 10
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         'jsonrpc' => '2.0',
        //         'result' => array(
        //             'trades' => array(
        //                 array(
        //                     'trade_seq' => 39201926,
        //                     'trade_id':' 64135724',
        //                     'timestamp' => 1583174775400,
        //                     'tick_direction' => 1,
        //                     'price' => 8865.0,
        //                     'instrument_name' => 'BTC-PERPETUAL',
        //                     'index_price' => 8863.31,
        //                     'direction' => 'buy',
        //                     'amount' => 10.0
        //                 ),
        //             ),
        //             'has_more' => true,
        //         ),
        //         'usIn' => 1583779594843931,
        //         'usOut' => 1583779594844446,
        //         'usDiff' => 515,
        //         'testnet' => false
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $trades = $this->safe_value($result, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument_name' => $market['id'],
        );
        if ($limit !== null) {
            $request['depth'] = $limit;
        }
        $response = $this->publicGetGetOrderBook (array_merge($request, $params));
        //
        //     {
        //         jsonrpc => '2.0',
        //         $result => array(
        //             $timestamp => 1583781354740,
        //             stats => array( volume => 61249.66735634, low => 7631.5, high => 8311.5 ),
        //             state => 'open',
        //             settlement_price => 7903.21,
        //             open_interest => 111536690,
        //             min_price => 7695.13,
        //             max_price => 7929.49,
        //             mark_price => 7813.06,
        //             last_price => 7814.5,
        //             instrument_name => 'BTC-PERPETUAL',
        //             index_price => 7810.12,
        //             funding_8h => 0.0000031,
        //             current_funding => 0,
        //             change_id => 17538025952,
        //             bids => [
        //                 [7814, 351820],
        //                 [7813.5, 207490],
        //                 [7813, 32160],
        //             ],
        //             best_bid_price => 7814,
        //             best_bid_amount => 351820,
        //             best_ask_price => 7814.5,
        //             best_ask_amount => 11880,
        //             asks => [
        //                 [7814.5, 11880],
        //                 [7815, 18100],
        //                 [7815.5, 2640],
        //             ],
        //         ),
        //         usIn => 1583781354745804,
        //         usOut => 1583781354745932,
        //         usDiff => 128,
        //         testnet => false
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $timestamp = $this->safe_integer($result, 'timestamp');
        $nonce = $this->safe_integer($result, 'change_id');
        $orderbook = $this->parse_order_book($result, $timestamp);
        $orderbook['nonce'] = $nonce;
        return $orderbook;
    }

    public function parse_order_status($status) {
        $statuses = array(
            'open' => 'open',
            'cancelled' => 'canceled',
            'filled' => 'closed',
            'rejected' => 'rejected',
            'untriggered' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_time_in_force($timeInForce) {
        $timeInForces = array(
            'good_til_cancelled' => 'GTC',
            'fill_or_kill' => 'FOK',
            'immediate_or_cancel' => 'IOC',
        );
        return $this->safe_string($timeInForces, $timeInForce, $timeInForce);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "time_in_force" => "good_til_cancelled",
        //         "reduce_only" => false,
        //         "profit_loss" => 0,
        //         "$price" => "market_price",
        //         "post_only" => false,
        //         "order_type" => "$market",
        //         "order_state" => "$filled",
        //         "order_id" => "ETH-349249",
        //         "max_show" => 40,
        //         "last_update_timestamp" => 1550657341322,
        //         "label" => "market0000234",
        //         "is_liquidation" => false,
        //         "instrument_name" => "ETH-PERPETUAL",
        //         "filled_amount" => 40,
        //         "direction" => "buy",
        //         "creation_timestamp" => 1550657341322,
        //         "commission" => 0.000139,
        //         "average_price" => 143.81,
        //         "api" => true,
        //         "$amount" => 40,
        //         "$trades" => array(), // injected by createOrder
        //     }
        //
        $timestamp = $this->safe_integer($order, 'creation_timestamp');
        $lastUpdate = $this->safe_integer($order, 'last_update_timestamp');
        $id = $this->safe_string($order, 'order_id');
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'average_price');
        $amount = $this->safe_float($order, 'amount');
        $filled = $this->safe_float($order, 'filled_amount');
        $lastTradeTimestamp = null;
        if ($filled !== null) {
            if ($filled > 0) {
                $lastTradeTimestamp = $lastUpdate;
            }
        }
        $remaining = null;
        $cost = null;
        if ($filled !== null) {
            if ($amount !== null) {
                $remaining = $amount - $filled;
            }
            if ($price !== null) {
                $cost = $price * $filled;
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'order_state'));
        $marketId = $this->safe_string($order, 'instrument_name');
        $market = $this->safe_market($marketId, $market);
        $side = $this->safe_string_lower($order, 'direction');
        $feeCost = $this->safe_float($order, 'commission');
        $fee = null;
        if ($feeCost !== null) {
            $feeCost = abs($feeCost);
            $fee = array(
                'cost' => $feeCost,
                'currency' => $market['base'],
            );
        }
        $type = $this->safe_string($order, 'order_type');
        // injected in createOrder
        $trades = $this->safe_value($order, 'trades');
        if ($trades !== null) {
            $trades = $this->parse_trades($trades, $market);
        }
        $timeInForce = $this->parse_time_in_force($this->safe_string($order, 'time_in_force'));
        $stopPrice = $this->safe_value($order, 'stop_price');
        $postOnly = $this->safe_value($order, 'post_only');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $market['symbol'],
            'type' => $type,
            'timeInForce' => $timeInForce,
            'postOnly' => $postOnly,
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
            'trades' => $trades,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => $id,
        );
        $response = $this->privateGetGetOrderState (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "$id" => 4316,
        //         "$result" => {
        //             "time_in_force" => "good_til_cancelled",
        //             "reduce_only" => false,
        //             "profit_loss" => 0.051134,
        //             "price" => 118.94,
        //             "post_only" => false,
        //             "order_type" => "limit",
        //             "order_state" => "filled",
        //             "order_id" => "ETH-331562",
        //             "max_show" => 37,
        //             "last_update_timestamp" => 1550219810944,
        //             "label" => "",
        //             "is_liquidation" => false,
        //             "instrument_name" => "ETH-PERPETUAL",
        //             "filled_amount" => 37,
        //             "direction" => "sell",
        //             "creation_timestamp" => 1550219749176,
        //             "commission" => 0.000031,
        //             "average_price" => 118.94,
        //             "api" => false,
        //             "amount" => 37
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result');
        return $this->parse_order($result);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument_name' => $market['id'],
            // for perpetual and futures the $amount is in USD
            // for options it is in corresponding cryptocurrency contracts, e.g., BTC or ETH
            'amount' => $this->amount_to_precision($symbol, $amount),
            'type' => $type, // limit, stop_limit, $market, stop_market, default is limit
            // 'label' => 'string', // user-defined label for the $order (maximum 64 characters)
            // 'price' => $this->price_to_precision($symbol, 123.45), // only for limit and stop_limit orders
            // 'time_in_force' : 'good_til_cancelled', // fill_or_kill, immediate_or_cancel
            // 'max_show' => 123.45, // max $amount within an $order to be shown to other customers, 0 for invisible $order
            // 'post_only' => false, // if the new $price would cause the $order to be filled immediately (as taker), the $price will be changed to be just below the spread.
            // 'reject_post_only' => false, // if true the $order is put to $order book unmodified or $request is rejected
            // 'reduce_only' => false, // if true, the $order is intended to only reduce a current position
            // 'stop_price' => false, // stop $price, required for stop_limit orders
            // 'trigger' => 'index_price', // mark_price, last_price, required for stop_limit orders
            // 'advanced' => 'usd', // 'implv', advanced option $order $type, options only
        );
        $priceIsRequired = false;
        $stopPriceIsRequired = false;
        if ($type === 'limit') {
            $priceIsRequired = true;
        } else if ($type === 'stop_limit') {
            $priceIsRequired = true;
            $stopPriceIsRequired = true;
        }
        if ($priceIsRequired) {
            if ($price !== null) {
                $request['price'] = $this->price_to_precision($symbol, $price);
            } else {
                throw new ArgumentsRequired($this->id . ' createOrder() requires a $price argument for a ' . $type . ' order');
            }
        }
        if ($stopPriceIsRequired) {
            $stopPrice = $this->safe_float_2($params, 'stop_price', 'stopPrice');
            if ($stopPrice === null) {
                throw new ArgumentsRequired($this->id . ' createOrder() requires a stop_price or $stopPrice param for a ' . $type . ' order');
            } else {
                $request['stop_price'] = $this->price_to_precision($symbol, $stopPrice);
            }
            $params = $this->omit($params, array( 'stop_price', 'stopPrice' ));
        }
        $method = 'privateGet' . $this->capitalize($side);
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "id" => 5275,
        //         "$result" => {
        //             "$trades" => array(
        //                 {
        //                     "trade_seq" => 14151,
        //                     "trade_id" => "ETH-37435",
        //                     "timestamp" => 1550657341322,
        //                     "tick_direction" => 2,
        //                     "state" => "closed",
        //                     "self_trade" => false,
        //                     "$price" => 143.81,
        //                     "order_type" => "$market",
        //                     "order_id" => "ETH-349249",
        //                     "matching_id" => null,
        //                     "liquidity" => "T",
        //                     "label" => "market0000234",
        //                     "instrument_name" => "ETH-PERPETUAL",
        //                     "index_price" => 143.73,
        //                     "fee_currency" => "ETH",
        //                     "fee" => 0.000139,
        //                     "direction" => "buy",
        //                     "$amount" => 40
        //                 }
        //             ),
        //             "$order" => {
        //                 "time_in_force" => "good_til_cancelled",
        //                 "reduce_only" => false,
        //                 "profit_loss" => 0,
        //                 "$price" => "market_price",
        //                 "post_only" => false,
        //                 "order_type" => "$market",
        //                 "order_state" => "filled",
        //                 "order_id" => "ETH-349249",
        //                 "max_show" => 40,
        //                 "last_update_timestamp" => 1550657341322,
        //                 "label" => "market0000234",
        //                 "is_liquidation" => false,
        //                 "instrument_name" => "ETH-PERPETUAL",
        //                 "filled_amount" => 40,
        //                 "direction" => "buy",
        //                 "creation_timestamp" => 1550657341322,
        //                 "commission" => 0.000139,
        //                 "average_price" => 143.81,
        //                 "api" => true,
        //                 "$amount" => 40
        //             }
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $order = $this->safe_value($result, 'order');
        $trades = $this->safe_value($result, 'trades', array());
        $order['trades'] = $trades;
        return $this->parse_order($order, $market);
    }

    public function edit_order($id, $symbol, $type, $side, $amount = null, $price = null, $params = array ()) {
        if ($amount === null) {
            throw new ArgumentsRequired($this->id . ' editOrder() requires an $amount argument');
        }
        if ($price === null) {
            throw new ArgumentsRequired($this->id . ' editOrder() requires a $price argument');
        }
        $this->load_markets();
        $request = array(
            'order_id' => $id,
            // for perpetual and futures the $amount is in USD
            // for options it is in corresponding cryptocurrency contracts, e.g., BTC or ETH
            'amount' => $this->amount_to_precision($symbol, $amount),
            'price' => $this->price_to_precision($symbol, $price), // required
            // 'post_only' => false, // if the new $price would cause the $order to be filled immediately (as taker), the $price will be changed to be just below the spread.
            // 'reject_post_only' => false, // if true the $order is put to $order book unmodified or $request is rejected
            // 'reduce_only' => false, // if true, the $order is intended to only reduce a current position
            // 'stop_price' => false, // stop $price, required for stop_limit orders
            // 'advanced' => 'usd', // 'implv', advanced option $order $type, options only
        );
        $response = $this->privateGetEdit (array_merge($request, $params));
        $result = $this->safe_value($response, 'result', array());
        $order = $this->safe_value($result, 'order');
        $trades = $this->safe_value($result, 'trades', array());
        $order['trades'] = $trades;
        return $this->parse_order($order);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => $id,
        );
        $response = $this->privateGetCancel (array_merge($request, $params));
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_order($result);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $method = null;
        if ($symbol === null) {
            $method = 'privateGetCancelAll';
        } else {
            $method = 'privateGetCancelAllByInstrument';
            $market = $this->market($symbol);
            $request['instrument_name'] = $market['id'];
        }
        $response = $this->$method (array_merge($request, $params));
        return $response;
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        $method = null;
        if ($symbol === null) {
            $code = $this->code_from_options('fetchOpenOrders');
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
            $method = 'privateGetGetOpenOrdersByCurrency';
        } else {
            $market = $this->market($symbol);
            $request['instrument_name'] = $market['id'];
            $method = 'privateGetGetOpenOrdersByInstrument';
        }
        $response = $this->$method (array_merge($request, $params));
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_orders($result, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        $method = null;
        if ($symbol === null) {
            $code = $this->code_from_options('fetchClosedOrders');
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
            $method = 'privateGetGetOrderHistoryByCurrency';
        } else {
            $market = $this->market($symbol);
            $request['instrument_name'] = $market['id'];
            $method = 'privateGetGetOrderHistoryByInstrument';
        }
        $response = $this->$method (array_merge($request, $params));
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_orders($result, $market, $since, $limit);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => $id,
        );
        $response = $this->privateGetGetUserTradesByOrder (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "$id" => 9367,
        //         "$result" => {
        //             "$trades" => array(
        //                 array(
        //                     "trade_seq" => 3,
        //                     "trade_id" => "ETH-34066",
        //                     "timestamp" => 1550219814585,
        //                     "tick_direction" => 1,
        //                     "state" => "open",
        //                     "self_trade" => false,
        //                     "reduce_only" => false,
        //                     "price" => 0.04,
        //                     "post_only" => false,
        //                     "order_type" => "$limit",
        //                     "order_id" => "ETH-334607",
        //                     "matching_id" => null,
        //                     "liquidity" => "M",
        //                     "iv" => 56.83,
        //                     "instrument_name" => "ETH-22FEB19-120-C",
        //                     "index_price" => 121.37,
        //                     "fee_currency" => "ETH",
        //                     "fee" => 0.0011,
        //                     "direction" => "buy",
        //                     "amount" => 11
        //                 ),
        //             ),
        //             "has_more" => true
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $trades = $this->safe_value($result, 'trades', array());
        return $this->parse_trades($trades, null, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'include_old' => true,
        );
        $market = null;
        $method = null;
        if ($symbol === null) {
            $code = $this->code_from_options('fetchMyTrades');
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
            if ($since === null) {
                $method = 'privateGetGetUserTradesByCurrency';
            } else {
                $method = 'privateGetGetUserTradesByCurrencyAndTime';
                $request['start_timestamp'] = $since;
            }
        } else {
            $market = $this->market($symbol);
            $request['instrument_name'] = $market['id'];
            if ($since === null) {
                $method = 'privateGetGetUserTradesByInstrument';
            } else {
                $method = 'privateGetGetUserTradesByInstrumentAndTime';
                $request['start_timestamp'] = $since;
            }
        }
        if ($limit !== null) {
            $request['count'] = $limit; // default 10
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "id" => 9367,
        //         "$result" => {
        //             "$trades" => array(
        //                 array(
        //                     "trade_seq" => 3,
        //                     "trade_id" => "ETH-34066",
        //                     "timestamp" => 1550219814585,
        //                     "tick_direction" => 1,
        //                     "state" => "open",
        //                     "self_trade" => false,
        //                     "reduce_only" => false,
        //                     "price" => 0.04,
        //                     "post_only" => false,
        //                     "order_type" => "$limit",
        //                     "order_id" => "ETH-334607",
        //                     "matching_id" => null,
        //                     "liquidity" => "M",
        //                     "iv" => 56.83,
        //                     "instrument_name" => "ETH-22FEB19-120-C",
        //                     "index_price" => 121.37,
        //                     "fee_currency" => "ETH",
        //                     "fee" => 0.0011,
        //                     "direction" => "buy",
        //                     "amount" => 11
        //                 ),
        //             ),
        //             "has_more" => true
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $trades = $this->safe_value($result, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        if ($code === null) {
            throw new ArgumentsRequired($this->id . ' fetchDeposits() requires a $currency $code argument');
        }
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $response = $this->privateGetGetDeposits (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "id" => 5611,
        //         "$result" => {
        //             "count" => 1,
        //             "$data" => array(
        //                 {
        //                     "address" => "2N35qDKDY22zmJq9eSyiAerMD4enJ1xx6ax",
        //                     "amount" => 5,
        //                     "$currency" => "BTC",
        //                     "received_timestamp" => 1549295017670,
        //                     "state" => "completed",
        //                     "transaction_id" => "230669110fdaf0a0dbcdc079b6b8b43d5af29cc73683835b9bc6b3406c065fda",
        //                     "updated_timestamp" => 1549295130159
        //                 }
        //             )
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $data = $this->safe_value($result, 'data', array());
        return $this->parse_transactions($data, $currency, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        if ($code === null) {
            throw new ArgumentsRequired($this->id . ' fetchWithdrawals() requires a $currency $code argument');
        }
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $response = $this->privateGetGetWithdrawals (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "id" => 2745,
        //         "$result" => {
        //             "count" => 1,
        //             "$data" => array(
        //                 {
        //                     "address" => "2NBqqD5GRJ8wHy1PYyCXTe9ke5226FhavBz",
        //                     "amount" => 0.5,
        //                     "confirmed_timestamp" => null,
        //                     "created_timestamp" => 1550571443070,
        //                     "$currency" => "BTC",
        //                     "fee" => 0.0001,
        //                     "id" => 1,
        //                     "priority" => 0.15,
        //                     "state" => "unconfirmed",
        //                     "transaction_id" => null,
        //                     "updated_timestamp" => 1550571443070
        //                 }
        //             )
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $data = $this->safe_value($result, 'data', array());
        return $this->parse_transactions($data, $currency, $since, $limit, $params);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'completed' => 'ok',
            'unconfirmed' => 'pending',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchWithdrawals
        //
        //     {
        //         "$address" => "2NBqqD5GRJ8wHy1PYyCXTe9ke5226FhavBz",
        //         "amount" => 0.5,
        //         "confirmed_timestamp" => null,
        //         "created_timestamp" => 1550571443070,
        //         "$currency" => "BTC",
        //         "$fee" => 0.0001,
        //         "id" => 1,
        //         "priority" => 0.15,
        //         "state" => "unconfirmed",
        //         "transaction_id" => null,
        //         "updated_timestamp" => 1550571443070
        //     }
        //
        // fetchDeposits
        //
        //     {
        //         "$address" => "2N35qDKDY22zmJq9eSyiAerMD4enJ1xx6ax",
        //         "amount" => 5,
        //         "$currency" => "BTC",
        //         "received_timestamp" => 1549295017670,
        //         "state" => "completed",
        //         "transaction_id" => "230669110fdaf0a0dbcdc079b6b8b43d5af29cc73683835b9bc6b3406c065fda",
        //         "updated_timestamp" => 1549295130159
        //     }
        //
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $timestamp = $this->safe_integer_2($transaction, 'created_timestamp', 'received_timestamp');
        $updated = $this->safe_integer($transaction, 'updated_timestamp');
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'state'));
        $address = $this->safe_string($transaction, 'address');
        $feeCost = $this->safe_float($transaction, 'fee');
        $type = 'deposit';
        $fee = null;
        if ($feeCost !== null) {
            $type = 'withdrawal';
            $fee = array(
                'cost' => $feeCost,
                'currency' => $code,
            );
        }
        return array(
            'info' => $transaction,
            'id' => $this->safe_string($transaction, 'id'),
            'txid' => $this->safe_string($transaction, 'transaction_id'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'addressTo' => $address,
            'addressFrom' => null,
            'tag' => null,
            'tagTo' => null,
            'tagFrom' => null,
            'type' => $type,
            'amount' => $this->safe_float($transaction, 'amount'),
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function fetch_position($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument_name' => $market['id'],
        );
        $response = $this->privateGetGetPosition (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "id" => 404,
        //         "$result" => {
        //             "average_price" => 0,
        //             "delta" => 0,
        //             "direction" => "buy",
        //             "estimated_liquidation_price" => 0,
        //             "floating_profit_loss" => 0,
        //             "index_price" => 3555.86,
        //             "initial_margin" => 0,
        //             "instrument_name" => "BTC-PERPETUAL",
        //             "leverage" => 100,
        //             "kind" => "future",
        //             "maintenance_margin" => 0,
        //             "mark_price" => 3556.62,
        //             "open_orders_margin" => 0.000165889,
        //             "realized_profit_loss" => 0,
        //             "settlement_price" => 3555.44,
        //             "size" => 0,
        //             "size_currency" => 0,
        //             "total_profit_loss" => 0
        //         }
        //     }
        //
        // todo unify parsePosition/parsePositions
        $result = $this->safe_value($response, 'result');
        return $result;
    }

    public function fetch_positions($symbols = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $code = $this->code_from_options('fetchPositions');
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateGetGetPositions (array_merge($request, $params));
        //
        //     {
        //         "jsonrpc" => "2.0",
        //         "id" => 2236,
        //         "$result" => array(
        //             {
        //                 "average_price" => 7440.18,
        //                 "delta" => 0.006687487,
        //                 "direction" => "buy",
        //                 "estimated_liquidation_price" => 1.74,
        //                 "floating_profit_loss" => 0,
        //                 "index_price" => 7466.79,
        //                 "initial_margin" => 0.000197283,
        //                 "instrument_name" => "BTC-PERPETUAL",
        //                 "kind" => "future",
        //                 "leverage" => 34,
        //                 "maintenance_margin" => 0.000143783,
        //                 "mark_price" => 7476.65,
        //                 "open_orders_margin" => 0.000197288,
        //                 "realized_funding" => -1e-8,
        //                 "realized_profit_loss" => -9e-9,
        //                 "settlement_price" => 7476.65,
        //                 "size" => 50,
        //                 "size_currency" => 0.006687487,
        //                 "total_profit_loss" => 0.000032781
        //             }
        //         )
        //     }
        //
        // todo unify parsePosition/parsePositions
        $result = $this->safe_value($response, 'result', array());
        return $result;
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
            'address' => $address, // must be in the $address book
            'amount' => $amount,
            // 'priority' => 'high', // low, mid, high, very_high, extreme_high, insane
            // 'tfa' => '123456', // if enabled
        );
        if ($this->twofa !== null) {
            $request['tfa'] = $this->oath();
        }
        $response = $this->privateGetWithdraw (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'id'),
        );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/' . 'api/' . $this->version . '/' . $api . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $request .= '?' . $this->urlencode($params);
            }
        }
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $timestamp = (string) $this->milliseconds();
            $requestBody = '';
            if ($params) {
                $request .= '?' . $this->urlencode($params);
            }
            $requestData = $method . "\n" . $request . "\n" . $requestBody . "\n"; // eslint-disable-line quotes
            $auth = $timestamp . "\n" . $nonce . "\n" . $requestData; // eslint-disable-line quotes
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha256');
            $headers = array(
                'Authorization' => 'deri-hmac-sha256 id=' . $this->apiKey . ',ts=' . $timestamp . ',sig=' . $signature . ',' . 'nonce=' . $nonce,
            );
        }
        $url = $this->urls['api'] . $request;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (!$response) {
            return; // fallback to default $error handler
        }
        //
        //     {
        //         jsonrpc => '2.0',
        //         $error => array(
        //             message => 'Invalid params',
        //             data => array( $reason => 'invalid currency', param => 'currency' ),
        //             code => -32602
        //         ),
        //         testnet => false,
        //         usIn => 1583763842150374,
        //         usOut => 1583763842150410,
        //         usDiff => 36
        //     }
        //
        $error = $this->safe_value($response, 'error');
        if ($error !== null) {
            $errorCode = $this->safe_string($error, 'code');
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions, $errorCode, $feedback);
            throw new ExchangeError($feedback); // unknown message
        }
    }
}
