<?php
global $controllerID,$controllerObject,$controllerFunction;

/** ===================
 *  MyFxBook Controller
 *  ===================
 */

switch($controllerFunction)
{
    case 'trader':
    case 'traders':
        /** =========================
         *  Functions for all traders
         *  =========================
         */

        $traderInstance = new Trader();

        switch($controllerID)
        {
            case 'sync':
                /** ====================================
                 *  Syncs all traders data with MyFxBook
                 *  ====================================
                 */

                //error_log(basename(__FILE__) . ' /traders/sync: attempting to sync files');


                $traderList = $traderInstance->get_trader_myfxbook_urls();



                if(empty($traderList) || !$traderList)
                {
                    //error_log(basename(__FILE__) . ' traderList false or empty :(');

                    unset($traderInstance);
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_NO_DATA_AVAILABLE)
                    ));

                } else {

                    //error_log(basename(__FILE__) . ' traderList: ' . print_r($traderList,true));

                    $tradersData = array();
                    $traderData = array();
                    $traderCount = 0;
                    foreach($traderList as $item)
                    {
                        //error_log(basename(__FILE__) . ' iterating through traderList. item: ' . print_r($item,true));

                        $url = $item['trader_myfxbook_url'];
                        $traderID = $item['trader_id'];
                        $traderData = extract_profile($url);
                        $traderData['trader_id'] = $traderID;


                        if($traderData)
                        {
                            //error_log(basename(__FILE__) . ' traderData has data. attempting to update DB.');
                            if($traderInstance->update_mfb_stats($traderData))
                            {
                                $traderCount++;
                            }
                        } else {
                            //error_log(basename(__FILE__) . ' traderData not set, skipping ' . print_r($traderData,true));

                            continue;
                        }
                    }

                    unset($traderInstance);

                    //error_log(basename(__FILE__) . ' traderCount: ' . $traderCount);


                    if($traderCount > 0)
                    {
                        api_response(array(
                            'code'=> RESPONSE_SUCCESS,
                            'data'=>
                            array(
                                'message'=>'Successfully retrieved MyFxBook stats for ' . $traderCount . ' trader accounts.',
                                'count'=>$traderCount,
                                'traders'=>$tradersData
                            )
                        ));
                    } else {
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>'Unable to retrieve MyFxBook stats for the requested accounts. Please contact support@topforexsignal.com')
                        ));
                    }
                }

                break;

            default:
                //if a specifics trader ID is specified and exists
                if(!empty($controllerID) && is_numeric($controllerID) && $traderInstance->is_user($controllerID))
                {
                    $traderID = $controllerID;
                    $url = $traderInstance->get_trader_myfxbook_url($traderID);

                    if(!isset($url))
                    {
                        //no URL exists for this user unfortunately
                        unset($traderInstance);
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_NO_DATA_AVAILABLE)
                        ));
                    }

                    $traderStats = extract_profile($url);
                    if($traderStats)
                    {

                        api_response(array(
                            'code'=> RESPONSE_SUCCESS,
                            'data'=>
                            array(
                                'message'=>'Successfully retrieved MyFxBook stats for trader ID: ' . $traderID . ' from URL: ' . $url,
                                'myfxbook_stats'=>$traderStats
                            )
                        ));


                    } else {
                        //this means that parsing the data from myfxbook failed
                        unset($traderInstance);
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_PARSING_DATA)
                        ));
                    }



                } elseif(empty($controllerID)) {

                    /** =====================================================
                     *  No user specified, lets show the list of trader stats
                     *  =====================================================
                     */

                    $traderList = $traderInstance->get_trader_myfxbook_urls();
                    unset($traderInstance);

                    if(empty($traderList) || !$traderList)
                    {
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_NO_DATA_AVAILABLE)
                        ));

                    } else {


                        $tradersData = array();
                        $traderData = array();


                        foreach($traderList as $item)
                        {
                            $url = $item['trader_myfxbook_url'];
                            $traderID = $item['trader_id'];
                            $traderData = extract_profile($url);
                            if(isset($traderData))
                            {
                                $tradersData[] = array(
                                    'trader_id'=>$traderID,
                                    'myfxbook_url'=>$url,
                                    'stats'=>$traderData

                                );
                            } else {
                                continue;
                            }
                        }

                        $traderCount = count($tradersData);
                        api_response(array(
                            'code'=> RESPONSE_SUCCESS,
                            'data'=>
                            array(
                                'message'=>'Successfully retrieved MyFxBook stats for ' . $traderCount . ' trader accounts.',
                                'count'=>$traderCount,
                                'traders'=>$tradersData
                            )
                        ));
                    }


                } else {

                    //trader does not exist in the system, display error
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INVALID_USER_ID)
                    ));
                }
                break;
        }



        break;

    default:
        api_response(array(
            'code'=> RESPONSE_ERROR,
            'data'=> array('message'=>ERROR_INVALID_FUNCTION)
        ));
        break;

}


function fetch($url) {

    //initialize curl
    $ch =  curl_init();

    //set up the parameters
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT,         30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,  30);
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, 1 );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2');

    $result = curl_exec($ch);

    curl_close( $ch );
    if(strlen($result) == 0) return false; else return $result;

}

function extract_profile($url)
{

    //error_log(basename(__FILE__) . ' extract_profile - URL: ' . $url);


    //lets grab the raw data
    $rawData = fetch($url);

    //error_log(basename(__FILE__) . ' extract_profile - retrieved raw data. data length: ' . strlen($rawData));


    if(strlen($rawData) < 1)
    {
        //error_log(basename(__FILE__) . ' extract_profile - no data received. returning false');
        return false;
    }

    ini_set('max_execution_time', 60);

    //lets serialize it with phpQuery
    $doc = phpQuery::newDocument($rawData);

    //lets find the #stats ul node
    $_t = $doc->find('#stats')->children('li');




    //lets parse each line and assign them to our vars
    $gain =             $_t->eq(0)->children('span')->eq(1)->text();
    $gainAbsolute =     $_t->eq(1)->children('span')->eq(1)->text();
    $gainDaily =        $_t->eq(2)->children('span')->eq(1)->text();
    $gainMonthly =      $_t->eq(3)->children('span')->eq(1)->text();
    $drawdown =         $_t->eq(4)->children('span')->eq(1)->text();
    $balance =          $_t->eq(5)->children('span')->eq(1)->text();
    $equity =           explode('%',trim(str_replace(array('$','(',')'),'',$_t->eq(6)->children('span')->eq(1)->text())));
    $equityHighest =    explode('$',trim(str_replace(array('(',')'),'',$_t->eq(7)->children('span')->eq(1)->text())));
    $profit =           $_t->eq(8)->children('span')->eq(1)->text();
    $interest =         $_t->eq(9)->children('span')->eq(1)->text();
    $deposits =         $_t->eq(10)->children('span')->eq(1)->text();
    $withdrawals =      $_t->eq(11)->children('span')->eq(1)->text();



    //lets store the data and clean it up
    $traderStats = array(
        'gain'=>rtrim($gain,'%'),
        'gain_absolute'=>rtrim($gainAbsolute,'%'),
        'gain_daily'=>rtrim($gainDaily,'%'),
        'gain_monthly'=>rtrim($gainMonthly,'%'),
        'drawdown'=>rtrim($drawdown,'%'),
        'balance'=>str_replace('$','',$balance),
        'equity'=>array('percentage'=>$equity[0],'amount'=>$equity[1]),
        'equity_highest'=>array('date'=>$equityHighest[0],'amount'=>$equityHighest[1]),
        'profit'=>str_replace('$','',$profit),
        'interest'=>str_replace('$','',$interest),
        'deposits'=>str_replace('$','',$deposits),
        'withdrawals'=>str_replace('$','',$withdrawals)
    );
    //error_log(basename(__FILE__).' DATA SET: ' . print_r($traderStats,true));
    unset($doc);

    //error_log(basename(__FILE__) . ' extract_profile - traderStats parsed. Raw data: ' . print_r($traderStats,true));


    return $traderStats;
}