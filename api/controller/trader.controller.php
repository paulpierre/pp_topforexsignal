<?php

/** =================
 *  Trader Controller
 *  =================
 */

    $traderInstance = new Trader();

    $trader_id = (isset($_POST['trader_id']))?$_POST['trader_id']:$_GET['trader_id'];

    /** ====================================
     *  NO TRADER ID SPECIFIED, LIST TRADERS
     *  ====================================
     */
    if(!isset($trader_id))
    {
        switch($controllerFunction)
        {
            /** =============================
             *  ADD FAKE FOLLOWERS TO TRADERS
             *  =============================
             */

            case 'add_followers':

                //add followers to all visible profiles with the exception of the excluded trader_id's included in the array
                $result = $traderInstance->_add_followers(array(15,38,44,43,42));

                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array('followers_added'=>$result)
                ));
                break;

            case 'get_trader_ids':
                //grab a list of trader names and matching IDs
                $traderList  = $traderInstance->get_trader_ids();

                if($traderList) {
                    api_response(array(
                        'code'=> RESPONSE_SUCCESS,
                        'data'=> array('traders'=>$traderList)
                    ));
                } else {
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>'Unable to fetch the list of traders')
                    ));
                }

                unset($traderInstance);
                break;





            /** ================
             *  SHOW ALL TRADERS
             *  ================
             */
            default:

                $trader_list = Array();
                $traders = $traderInstance->get_traders_list();

                foreach($traders as $item)
                {
                    $trader_list[] = $traderInstance->get_trader_profile($item['trader_id']);
                }

                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array(
                        'traders'=>$trader_list
                    )
                ));
                break;
        }
    }



    /** ========================
     *  API PROVIDED A TRADER ID
     *  ========================
     */

    if(isset($trader_id))
    {
        if(!is_numeric($trader_id))
        {
            $trader_name = $trader_id;
            $tid = $traderInstance->get_trader_account_id_by_trader_name($trader_name);
            //error_log(PHP_EOL. 'Looking up ' . $trader_id . ' and found: ' . $tid);
            if(!$tid || !is_numeric($tid)) {
                unset($traderInstance);
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>ERROR_INVALID_USER_ID . ': ' . $controllerObject . ((isset($trader_id))?' with ID ' . $trader_id:''))
                ));
            }
            $trader_id = $tid;

        }

        if( strlen($trader_id)>0 &&          //we were provided an ID

            !$traderInstance->is_user($trader_id) //but the user doesn't exist
           )
        {
            unset($traderInstance);
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=>ERROR_INVALID_USER_ID . ': ' . $controllerObject . ((isset($trader_id))?' with ID ' . $trader_id:''))
            ));
        }

        $traderInstance->trader_id = $trader_id;
    }



    switch($controllerFunction)
    {


        case 'commission':
            switch($controllerID)
            {
                case 'pay_pending_commission':

                    /**
                     *  Function to pay a users commission, update in the database and begin processing with Paypal
                     */

                    $trader_commission = $_REQUEST['trader_commission'];
                    $trader_note = $_REQUEST['trader_note'];
                    $trader_name = $_REQUEST['trader_name'];
                    $trader_id = $_REQUEST['trader_id'];
                    $trader_email = $_REQUEST['trader_email'];


                    if(!isset($trader_commission) && !isset($trader_id))
                    {
                        unset($traderInstance);
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_INVALID_PARAMETERS)
                        ));
                    } else {

                        $result = $traderInstance->update_trader_pending_commissions(array(
                            'trader_id'=>intval($trader_id),
                            'trader_note'=>$trader_note,
                            'trader_commission'=>floatval($trader_commission),
                            'trader_email'=>$trader_email
                        ));

                        if($result)
                        {
                            api_response(array(
                                'code'=> RESPONSE_SUCCESS,
                                'data'=> array('message'=>$trader_name . '\'s commission payment started processing.')));
                        } else {
                            api_response(array(
                                'code'=> RESPONSE_ERROR,
                                'data'=> array('message'=>'There are was an error processing that users payment')
                            ));
                        }

                    }




                    unset($traderInstance);
                    break;
                default:
                    unset($traderInstance);
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INVALID_FUNCTION)
                    ));
                    break;
            }


            break;

        /** =============
         *  TRADE HISTORY
         *  =============
         */
        case 'trades_history':
            $trader_history = $traderInstance->get_order_history();
            if(!$trader_history)
            {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>ERROR_NO_DATA_AVAILABLE . ': ' . $controllerObject . ' with function ' . $controllerFunction .', ID ' .$trader_id)
                ));
            }
             else {
                 //$data = add_trade_pips($trader_history);
                 api_response(array(
                     'code'=> RESPONSE_SUCCESS,
                     'data'=> array('trader_history'=>$trader_history)
                 ));
             }
            break;

        /** ===========
         *  OPEN TRADES
         *  ===========
         */
        case 'trades_open':
            $open_trades = $traderInstance->get_open_orders();

            if(!$open_trades)
            {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>ERROR_NO_DATA_AVAILABLE . ': ' . $controllerObject . ' with function ' . $controllerFunction .', ID ' . $trader_id)
                ));
            }
            else {

                //$data = add_trade_pips($trader_history);

                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array('open_trades'=>$open_trades)
                ));
            }
            break;

        /** =============
         *  CLOSED TRADES
         *  =============
         */
        case 'trades_closed':
            $closed_trades = $traderInstance->get_closed_orders();

            if(!$closed_trades)
            {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>ERROR_NO_DATA_AVAILABLE . ': ' . $controllerObject . ' with function ' . $controllerFunction .', ID ' . $trader_id)
                ));
            }
            else {

                //$data = add_trade_pips($trader_history);
                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array('closed_trades'=>$closed_trades)
                ));
            }
            break;

        /** ===================
         *  TRADER DAILY GROWTH
         *  ===================
         */
        case 'growth':
            $growth_daily = $traderInstance->get_daily_growth();
            foreach($growth_daily as $item)
            {
                $data[] = array('date'=>$item['date'],'value'=>$item['growth']);
            }
            //exit('<pre>' . print_r($data,true));

            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('growth_daily'=>$data)
            ));
            break;


        /** ===================
         *  TRADER DAILY GROWTH ***SOON TO BE DEPRECATED BEACUSE THIS FUCKING CHART SUCKS DICK***
         *  ===================
         */
        case 'growth_daily':
            $growth_daily = $traderInstance->get_daily_growth();
            //exit('<pre>' . print_r($growth_daily,true));
            $date = null;
            $growth = null;
            foreach($growth_daily as $item)
            {
                $date[] = $item['date'];
                $growth[] = $item['growth'];
            }
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('growth_daily'=>array(
                    'dates'=>$date,
                    'growth'=>$growth
                ))
            ));
            break;

        case 'growth_monthly':
            $growth_monthly = $traderInstance->get_monthly_growth();
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('growth_monthly'=>$growth_monthly)
            ));
            break;

        case 'avg_trade_count':
            $avg_trades_weekly = $traderInstance->get_avg_trades_per_week();
            $avg_trades_monthly = $traderInstance->get_avg_trades_per_month();
            $avg_trades_daily = $traderInstance->get_avg_trades_per_day();

            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array(
                    'avg_trades_week'=>$avg_trades_weekly,
                    'avg_trades_month'=>$avg_trades_monthly,
                    'avg_trades_day'=>$avg_trades_daily
                )
            ));

            break;

        case 'growth_avg_monthly':
            $growth_avg_monthly = $traderInstance->get_avg_monthly_growth();
            //exit('<pre>' . print_r($growth_avg_monthly,true));
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('growth_avg_monthly'=>$growth_avg_monthly)
            ));
            break;


        case 'growth_30day':
            $growth_30_day = $traderInstance->get_30_day_growth();
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('growth_30day'=>$growth_30_day)
            ));
            break;

        case 'growth_total':
            $total_growth = $traderInstance->get_total_growth();
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('total_growth'=>$total_growth)
            ));
            break;

        /** ===================
         *  TRADER'S DEPOSITS
         *  ===================
         */
        case 'deposits':
        case 'deposit':
            $account_deposit = $traderInstance->get_deposit();
            $initial_deposit = $traderInstance->get_deposit('initial');
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array(
                    'total_deposits'=>$account_deposit,
                    'intitial_deposit'=>$initial_deposit
                )
            ));
            break;

        /** =============================
         *  TRADER'S MAX AND MIN DRAWDOWN
         *  =============================
         */
        case 'drawdown':
            $max_drawdown = $traderInstance->get_drawdown();
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('max_drawdown'=>$max_drawdown)
            ));
            break;





        /** ==============
         *  TRADER PROFILE
         *  ==============
         */
        default:
        case 'profile':
            $profile = $traderInstance->get_trader_profile($trader_id);
            if(!$profile)  {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>ERROR_NO_DATA_AVAILABLE . ': ' . $controllerObject . ' with function ' . $controllerFunction .', ID ' .$trader_id)
                ));
            }

            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> $profile
            ));
            break;
/*
        default:
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=> ERROR_INVALID_FUNCTION . ': ' . $controllerFunction)
            ));
            break;
*/





    }


