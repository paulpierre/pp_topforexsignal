<?php
global $controllerID,$controllerObject,$controllerFunction;

/** ====================
 *  Reporting controller
 *  ====================
 */




switch($controllerFunction)
{


    case 'email':

        switch($controllerID)
        {
            case 'traders':
                $email_date = date('m-d-Y');

                $traderInstance = new Trader();
                $traders = $traderInstance->get_traders_list();
                foreach($traders as $item)
                {
                    $traderInstance->trader_id = $item['trader_id'];
                    $trader_list[] = $traderInstance->get_trader_weekly_performance($item['trader_id']);
                }
                unset($traderInstance);


                $trader_email .= <<<EOT
<style>
* {
    font-family:Helvetica;
    font-size:14px;
    font-weight:normal;
}
th,h2 {
    font-weight:bold;
}
table,td {
 border:1px #c0c0c0 solid;
 }
 td.red {
 font-color:red;
}
td.green {
    font-color:green;
}
</style>
EOT;

                $trader_email .= '<h2>Trader Weekly Performance for '.$email_date.':</h2><br/><br/>';

                $trader_email .='<table><thead><th>Name</th><th>Weekly Growth</th><th>Pips</th><th>Total Growth</th></thead>';


                foreach($trader_list as $item)
                {
                    $trader_email .= '<tr><td><b>' . $item['trader_name'] . '</b></td>';
                    $trader_email .= '<td><b>' . $item['trader_growth_week'] . '%</b></td>';
                    $trader_email .= '<td><b>' . $item['trader_pips_week'] . '</b></td>';
                    $trader_email .= '<td><b>' . $item['trader_growth_total'] . '%</b></td></tr>';
                }

                $trader_email .= '</table><br/><br/><b>-TFS Mail Bot</b>';

                $email = 'sinsua@gmail.com';


                if($email && filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $emailSubject = 'Trader Performance for '. $email_date;
                    $emailHeader = "From: support@topforexsignal.com\n"  . "cc:guitarsmith@gmail.com,aaronczt@gmail.com\nMIME-Version: 1.0\n" . "Content-type: text/html; charset=utf-8\n";
                    if(mail($email, $emailSubject, $trader_email, $emailHeader,"-f$email")) {

                        api_response(array(
                            'code'=> RESPONSE_SUCCESS,
                            'data'=> array(
                                'message'=>'Successfully sent weekly summary for ' . count($trader_list) . ' traders.',
                                'traderData'=>$trader_list
                            )
                        ));

                    } else {
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>'Internal error sending email')));
                    }
                } else
                {
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>'Must specify a valid email address to send report to.')));
                }



                break;
        }



    case 'dummy':
        /** ==============
         *  /report/dummy
         *  ==============
         *  Injects dummy data into the database 30 days forwards
         */

        if(!isset($controllerID) && !is_numeric($controllerID))
        {
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=>'Must specify total # of records to generate')
            ));
        }

        $traderInstance = new Trader();

        $tradersPerDay = 0;
        $startDate = 0;
        $endDate = 0;

        $numTrades = $controllerID;

        $masterID = 5381;
        $subscriptionID = 10552;
        $symbolID = 'EURUSD';


        for($i=0;$i<$numTrades;$i++)
        {
            $tradeTicket = rand(28000000,28999999);
            $mastertradeTicket = rand(13820000,13829999);
            $loginID = rand(100000,999999);
            $orderType = (rand(0,1)==0)?'BUY':'SELL';
            $d = mt_rand(1372055681,time());
            $openDate=  date("Y-m-d H:i:s",$d);
            $closeDate = date("Y-m-d H:i:s",$d+259200);
            $openPrice =  rand(120000,139999)/1000;
            $closePrice = rand(120000,139999)/1000;
            $profit =  $closePrice - $openPrice;
            $lotSize = rand(0.01,20.00);
            $pips = $profit * 10;
            $accountID = rand(10000,19999);
            $tradeData =  array(
                'trade_id'=> $loginID,
                'trader_account_id'=>$accountID,
                'trade_close_price'=>$closePrice,
                'trade_close_time'=>$closeDate,
                'trader_login'=>$loginID,
                'trade_master_trade_id'=>$mastertradeTicket,
                'trade_open_price'=>$openPrice,
                'trade_open_time'=>$openDate,
                'trade_order_type'=>$orderType,
                'trade_pips'=>$pips,
                'trade_profit'=>number_format($profit,2),
                'trade_size'=>$lotSize,
                'trade_stop_loss'=>0.00,
                'trader_subscription_id'=>$subscriptionID,
                'trade_symbol_id'=>$symbolID,
                'trade_take_profit'=>0.00000,
                'trade_ticket'=>$tradeTicket,
                'trade_fx_account_id'=>$masterID
            );

            $result = $traderInstance->update_master_trade($tradeData);

            //print print_r($tradeData,true) . '<br>';
        }


        api_response(array(
            'code'=> RESPONSE_SUCCESS,
            'data'=> array('message'=>$i .' total records generated successfully.')
        ));


    break;


    /** ==============
     *  /report/broker
     *  ==============
     *  Extracts broker commission transaction data from broker API
     *  and inserts it into the database.
     *  1. Get last date time from database
     *  2. Request transactional data from last date time to current date
     */


    case 'broker':

        /** ====================================================
         *  /report/broker/broker_id
         *  ------------------------
         *  This function scrapes broker commissions on a client
         *  per-trade basis and stores it in to the 'commissions
         *  table into the TFS database. This function gets called
         *  by a cronjob that runs every 15 minutes.
         * =====================================================
         */


    $broker_id = $controllerID;

        /**
         *  Lets determine which broker we're requesting so we employ
         *  the appropriate scraping code
         */

        switch($broker_id)
        {

            /** =========
             *  HOTFOREX
             *  =========
             */
            case BROKER_HFX:

                $brokerInstance = new Broker;

               //grab the data of the last trade we have in the database as our "from" date
                $date1 = $brokerInstance->get_last_record_date();

                //if there is nothing available in the database, let go the start of the year
                if(!$date1) $date1 = '2015-01-01 00:00:00';

                //the "to" date will be exactly the current time we're trying to query
                $date2 = date('Y-m-d H:i:s');

                //lets grab the credentials
                $brokerLogin =  HFX_LOGIN;
                $brokerPassword = HFX_PASSWORD;

                //to make sure the API is not being abused, lets verify the unique hash the requester
                //must include when making this API call
                $brokerHash = md5(HFX_LOGIN . HFX_PASSWORD);
                $_SESSION['brokerHash'] = $brokerHash;
                if(!isset($broker_key) && isset($_SESSION['brokerHash'])) $broker_key = $_SESSION['brokerHash'];

                //if they do not provide or if it is invalid, lets deny them access to the API method end-point
                if(!$broker_key || $broker_key !== $brokerHash)
                {
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INVALID_API_KEY)
                    ));
                }

                /** ==============================================
                 *   Lets grab the XSRF token from the cookie file
                 *  ==============================================
                 */

                //lets set the path to where we want the cookies stored
                $cookie_file = TMP_PATH.$brokerHash . '.txt';

                //lets actually open the cookie file and grab the xsrf security token
                $contents = file_get_contents($cookie_file);
                $xsrf_token = trim(get_string_between($contents, "_xsrf", "\n"));

                //now that we have the xsrf token, lets log into hotforex.. we will login anyway even if we
                //are currently logged in to be safe
                $brokerPost = 'username=' . $brokerLogin .'&password=' . $brokerPassword .'&login=Login&_xsrf=' . $xsrf_token;
                $url = "https://my.hotforex.com/login";

                $_ch = curl_init();
                curl_setopt ($_ch, CURLOPT_URL, $url);
                curl_setopt ($_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt ($_ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
                curl_setopt ($_ch, CURLOPT_TIMEOUT, 10);
                curl_setopt( $_ch, CURLOPT_POST, 1 );
                curl_setopt( $_ch, CURLOPT_POSTFIELDS, $brokerPost );
                curl_setopt ($_ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt ($_ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($_ch, CURLOPT_COOKIEJAR,$cookie_file);
                curl_setopt ($_ch, CURLOPT_COOKIEFILE, $cookie_file);
                curl_setopt ($_ch, CURLOPT_REFERER, $url);
                $result = curl_exec ($_ch);
                curl_close($_ch);


                //okay, we are now logged in, lets call the export transactions function inside Hotforex
                $url = 'https://my.hotforex.com/exportAffTransactions';

                //lets pass the parameters we determined above, the date range and xsrf token we grabbed earlier
                //the result will be a CSV transaction record filtered by the date range specified
                $postData = '_xsrf=' . $xsrf_token . '&date_1=' .$date1 . '&date_2=' . $date2;

                $_ch = curl_init();

                curl_setopt( $_ch, CURLOPT_AUTOREFERER, 1 );
                curl_setopt($_ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt ($_ch, CURLOPT_URL, $url);
                curl_setopt ($_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt ($_ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
                curl_setopt ($_ch, CURLOPT_TIMEOUT, 10);
                curl_setopt ($_ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt ($_ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt( $_ch, CURLOPT_POST, 1 );
                curl_setopt( $_ch, CURLOPT_POSTFIELDS, $postData );
                curl_setopt ($_ch, CURLOPT_COOKIEJAR,$cookie_file);
                curl_setopt ($_ch, CURLOPT_COOKIEFILE, $cookie_file);
                curl_setopt ($_ch, CURLOPT_REFERER, $url);
                $result = curl_exec ($_ch);
                curl_close($_ch);

                //if we do not have the predicted format we are looking for then it probably means
                //that we aren't grabbing the data we want, lets throw an error, email me just so I know
                //that this piece of shit broke on me :)
                if(strpos($result,'TICKET,LOGIN,SYMBOL,AMOUNT,TYPE,PROCESSDATE,STATUS') < 0 || strlen($result) < 1)
                {
                    error_alert('HFX API ERROR: /report/broker','API ERROR: There was an error scraping historical commission data from HotForex at API end point /report/broker/');
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>'There was an error processing the trade data from broker id ' . $broker_id)
                    ));
                }


                //we verified that this is the CSV data we want. This is currently a big fucking text blob
                //lets break it up into lines and explode it into an array
                $trades = explode(',Approved',$result);

                //if we have less 1 line or less, this means we have no new data to update.. lets quit then
                if(count($trades) < 2 )
                {
                    api_response(array(
                        'code'=> RESPONSE_SUCCESS,
                        'data'=> array(
                            'message'=>'No new commissions to add to the commissions database.'
                        )
                    ));
                }


                //okay, so we do have data.. right now each row is still useless in CSV format, lets break down each
                //row into their respective columns so we can extract the data
                for($i=1;$i<count($trades)-1;$i++)
                {
                    $row = explode(',',trim($trades[$i]));
                    $tradeData[] = array(
                        'trade_ticket'=>$row[0],
                        'user_id'=>$row[1],
                        'trade_symbol'=>$row[2],
                        'trade_profit'=>$row[3],
                        'trade_date'=>$row[5],
                        'broker_id'=>$broker_id,
                        'trade_tcreate'=>$date2
                    );
                }



                //missions accomplished, we have the CSV data processed, now lets update the database with this
                //spanking new data!
                if($brokerInstance->update_commission($tradeData))
                {
                    api_response(array(
                        'code'=> RESPONSE_SUCCESS,
                        'data'=> array(
                            'message'=>'Successfully added ' . count($tradeData) . ' records to the commissions table for date d1: ' . $date1 . ' d2:' . $date2
                        )
                    ));
                } else {

                    //if we have an issue update the database, we should email me .. this is a pretty serious error. lame.
                    error_alert('HFX API ERROR: /report/broker','API ERROR: There was an error updating commission data into the database at end point /report/broker/');
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>'There was an error updating commission in TFS')
                    ));
                }

                //and we're done!!

                break;

                default:
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INVALID_FUNCTION)
                    ));
                break;
        }



        break;


    /** ==============
     *  /report/sync
     *  ==============
     *  Syncs the 4x Solutions reporting API with our database
     */

    case 'sync':



        //$url = "https://api.4xsolutions.com/tradereplicator/api/mastertrades?filter.ishistory=true";
        $url = "http://api.4xsolutions.com/tradereplicator/api/clienttrades?filter.ishistory=true";

        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , false );
        curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , false );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:Application/json',
            'Accept: application/json',
            'API-KEY: ' . FXSOLUTIONS_API_KEY,
        ));

        $result = curl_exec($ch);
        $response = json_decode($result, true);
        //$tradeData = $response['Result']['Items']['MasterTradeView'];
        $trades = $response['Result']['Items']['ClientTradeView'];
       //error_log($result. PHP_EOL.print_r($trades,true));

        curl_close($ch);


        if(empty($trades)) {
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=>'Unable to sync with trade copy services')
            ));

            error_alert("API ERROR:" . RESPONSE_ERROR,"4X Solutions API connection failed to sync with TopForexSignal. Fix it!");

        }

        $traderInstance = new Trader();

        foreach($trades as $tradeData)
        {

            $date = DateTime::createFromFormat( 'd/m/Y H:i:s', $tradeData['CloseTime']);
            $closeTime = $date->format('Y-m-d H:i:s');
            $date = DateTime::createFromFormat( 'd/m/Y H:i:s', $tradeData['OpenTime']);
            $openTime = $date->format('Y-m-d H:i:s');
            $result = $traderInstance->update_master_trade(
                array(
                    'trade_id'=>$tradeData['ClientTradeID'],
                    'trader_account_id'=>$tradeData['AccountID'],
                    'trade_close_price'=>$tradeData['ClosePrice'],
                    'trade_close_time'=>$closeTime,
                    'trader_name'=>$tradeData['MasterName'],
                    'trader_login'=>$tradeData['Login'],
                    'trade_master_trade_id'=>$tradeData['MasterTradeID'],
                    'trade_open_price'=>$tradeData['OpenPrice'],
                    'trade_open_time'=>$openTime,
                    'trade_order_type'=>$tradeData['OrderTypeName'],
                    'trade_pips'=>$tradeData['Pips'],
                    'trade_profit'=>$tradeData['Profit'],
                    'trade_size'=>$tradeData['Size'],
                    'trade_stop_loss'=>$tradeData['StopLoss'],
                    'trader_subscription_id'=>$tradeData['SubscriptionID'],
                    'trade_symbol_id'=>$tradeData['SymbolID'],
                    'trade_take_profit'=>$tradeData['TakeProfit'],
                    'trade_ticket'=>$tradeData['Ticket'],
                    'trade_fx_account_id'=>$tradeData['UserId']
                )
            );
           // error_log(basename(__FILE__) . ' sync result: ' . print_r($result,true));


        }


        api_response(array(
            'code'=> RESPONSE_SUCCESS,
            'data'=> array('message'=>'Sync from trade copy service successful','date'=>time(),'trades'=>count($trades))
        ));

        unset($traderInstance);
    break;




    /** ==================
     *  /report/commission
     *  ==================

     */
    case 'commission':
        $traderInstance = new Trader();


        switch($controllerID)
        {
            case 'admin':
                    $traderData = $traderInstance->get_all_trader_pending_commissions();

                    api_response(array(
                        'code'=> RESPONSE_SUCCESS,
                        'data'=> array(
                            'message'=>'Trader commission data',
                            'traderData'=>$traderData
                        )
                    ));

                    unset($traderInstance);

                break;
            default:
                //make sure the ID is a real user
                if((!is_numeric($controllerID) || !$traderInstance->is_user($controllerID)) && $traderInstance->get_trader_master_copier_id($controllerID))
                {
                    unset($traderInstance);
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INVALID_USER_ID)
                    ));
                }
                $traderName = $traderInstance->get_trader_master_copier_id($controllerID);


                $trader_commissions_total = $traderInstance->get_trader_total_commissions("'". $traderName . "'");
                $trader_commissions_pending = $traderInstance->get_trader_pending_commissions($controllerID);
                $trader_commissions_approved = $traderInstance->get_trader_approved_commissions($controllerID);
                $trader_commissions_paid = $traderInstance->get_trader_paid_commissions($controllerID);
                $trader_commissions_history = $traderInstance->get_trader_commission_history($controllerID);


                $traderData = array(

                    'commissions_total' => $trader_commissions_total[0]['commission'],
                    'commissions_pending'=>$trader_commissions_pending,
                    'commissions_approved'=> $trader_commissions_approved[0]['payout'],
                    'commissions_paid'=>$trader_commissions_paid[0]['payout'],
                    'commissions_history'=>$trader_commissions_history
                );

                unset($traderInstance);

                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array(
                        'message'=>'Trader commission data successful for user ID ' . $account_id,
                        'traderData'=>$traderData
                    )
                ));


                break;
            break;
        }






    /** ==============
     *  /report/dashboard
     *  ==============

     */
    case 'dashboard':
        $traderInstance = new Trader();

        if(!is_numeric($controllerID) && $traderInstance->is_user($controllerID) && $traderInstance->get_trader_master_copier_id($controllerID))
        {
            unset($traderInstance);
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=>ERROR_INVALID_USER_ID)
            ));
        }
        $account_id = "'" . $traderInstance->get_trader_master_copier_id($controllerID) . "'";


        $get_range = (isset($_GET['range']))?$_GET['range']:'last7d';;

        switch($get_range)
        {
            case 'last7d':
                $range = ' AND cast(trade_close_time as DATE) between date_sub(now(),INTERVAL 7 DAY) and now() ';
                break;
            case 'last14d':
                $range = ' AND cast(trade_close_time as DATE) between date_sub(now(),INTERVAL 2 WEEK) and now()';
                break;
            case 'last30d':
                $range = ' AND cast(trade_close_time as DATE) between date_sub(now(),INTERVAL 1 MONTH) and now() ';
                break;

            case 'last6m':
                $range = ' AND cast(trade_close_time as DATE) between date_sub(now(),INTERVAL 6 MONTH) and now() ';
                break;

            case 'last12m':
                $range = ' AND cast(trade_close_time as DATE) between date_sub(now(),INTERVAL 1 YEAR) and now() ';
            break;

            case 'currentMonth':
                $range = ' AND MONTH(cast(trade_close_time as DATE)) = MONTH(CURDATE()) ';

                break;

            case 'lastMonth':
                $range = ' AND MONTH(cast(trade_close_time as DATE)) = MONTH(CURDATE()) - 1';

                break;

            case 'thisYear':
                $range = ' AND YEAR(cast(trade_close_time as DATE)) = YEAR(CURDATE()) ';

                break;




        }


        $total_followers = $traderInstance->get_trader_total_followers($account_id);//,(isset($range))?$range:'');
        $total_trade_volume = $traderInstance->get_trader_trade_volume($account_id,(isset($range))?$range:'');
        $total_profitable_trade_volume = $traderInstance->get_trader_profitable_trade_volume($account_id,(isset($range))?$range:'');
        $total_commissions = $traderInstance->get_trader_total_commissions($account_id,(isset($range))?$range:'');
        $daily_trade_volume = $traderInstance->get_trader_daily_trade_volume($account_id,(isset($range))?$range:'');
        $daily_profitable_trades = $traderInstance->get_trader_daily_profitable_trades($account_id,(isset($range))?$range:'');
        $daily_unprofitable_trades = $traderInstance->get_trader_daily_unprofitable_trades($account_id,(isset($range))?$range:'');
        //$daily_total_trades = $traderInstance->get_trader_daily_total_trades($account_id,(isset($range))?$range:'');
        $daily_commissions = $traderInstance->get_trader_daily_commissions($account_id,(isset($range))?$range:'');
        //$total_trades = $traderInstance->get_trader_total_trades($account_id,(isset($range))?$range:'');
        $total_profitable_trades = $traderInstance->get_trader_total_profitable_trades($controllerID,(isset($range))?$range:'');
        $total_unprofitable_trades = $traderInstance->get_trader_total_unprofitable_trades($controllerID,(isset($range))?$range:'');

        $traderData = array(
            'range'=>$get_range,
            'total_followers'=>count($total_followers),
            'total_trade_volume'=>floatval($total_trade_volume[0]['trade_size']),
            'total_profitable_volume'=>floatval($total_profitable_trade_volume[0]['trade_size']),
            'total_trade_count'=>$total_profitable_trades[0]['trade_count'] + $total_unprofitable_trades[0]['trade_count'],//count($daily_profitable_trades) + count($daily_unprofitable_trades) ,
            'total_profitable_trades'=>$total_profitable_trades[0]['trade_count'] ,
            'total_commissions'=>floatval($total_commissions[0]['commission']),
            'daily_trade_volume'=>process_chart('Trade Volume','trade_size',$daily_trade_volume),
            'daily_profitable_trades'=>process_chart('Profitable Trades','trades',$daily_profitable_trades),
            'daily_unprofitable_trades'=>process_chart('Unprofitable Trades','trades',$daily_unprofitable_trades),
            'daily_commissions'=>process_chart('Commissions','commission',$daily_commissions)
        );



        unset($traderInstance);
        //exit('{"order":{"label":"  Site Revenue","data":[[1,1],[2,2],[3,0],[4,0],[5,0],[6,0],[7,0],[8,0],[9,0],[10,0],[11,0],[12,0],[13,0],[14,0],[15,0],[16,0],[17,0],[18,0],[19,0],[20,0],[21,0],[22,0],[23,0],[24,0],[25,0],[26,0],[27,0],[28,0],[29,0],[30,0]]},"xaxis":[[1,"1"],[2,"2"],[3,"3"],[4,"4"],[5,"5"],[6,"6"],[7,"7"],[8,"8"],[9,"9"],[10,"10"],[11,"11"],[12,"12"],[13,"13"],[14,"14"],[15,"15"],[16,"16"],[17,"17"],[18,"18"],[19,"19"],[20,"20"],[21,"21"],[22,"22"],[23,"23"],[24,"24"],[25,"25"],[26,"26"],[27,"27"],[28,"28"],[29,"29"],[30,"30"]]}') ;

        //exit('<pre>'.print_r($traderData,true));

        api_response(array(
            'code'=> RESPONSE_SUCCESS,
            'data'=> array(
                'message'=>'Trader performance data successful for user ID ' . $account_id,
                'traderData'=>$traderData
                )
        ));


    break;
}

function process_chart($label,$target,$data)
{
    $x = Array();
    //return $data;
    foreach($data as $item)
    {
        //$date = date('U',strtotime($item['date']));
        $chart_date = strtotime($item['date'] . ' UTC') * 1000;

        //lets format floats according to type
        switch(strtolower($target))
        {
            case 'trades':
                $x[] = array($chart_date,intval($item[$target]));

                break;
            default:
                $x[] = array($chart_date,number_format(floatval($item[$target]),2));

                break;
        }

    }

    //return $x;
    $result = array(
        'order'=>array(
            'label'=>$label,
            'data'=>$x
        )
    );

    return $result;
}


function error_alert($subject,$message)
{
    //if(!MODE == 'prod') return;
    $emailHeader = "From: support@topforexsignal.com\n" . "cc:guitarsmith@gmail.com,sinsua@gmail.com\nMIME-Version: 1.0\n" . "Content-type: text/html; charset=utf-8\n";
    mail("support@topforexsignal.com", $subject, $message, $emailHeader,"-fsupport@topforexsignal.com");
}

function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}
