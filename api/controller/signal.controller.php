<?php

/** =================
 *  Signal Controller
 *  =================
 */

$signalInstance = new Signal();

$signal_id = (isset($_POST['signal_id']))?$_POST['signal_id']:$_GET['signal_id'];

/** ====================================
 *  NO SIGNAL ID SPECIFIED, LIST SIGNALS
 *  ====================================
 */
if(!isset($trader_id))
{
    switch($controllerFunction)
    {
        /** =============
         *  ADD A SIGNAL
         *  ============
         */

        case 'add':
            $signal_status = (isset($_POST['signal_status']))?$_POST['signal_status']:$_GET['signal_status'];
            $signal_pair = (isset($_POST['signal_pair']))?$_POST['signal_pair']:$_GET['signal_pair'];
            $signal_tp = (isset($_POST['signal_tp']))?$_POST['signal_tp']:$_GET['signal_tp'];
            $signal_sl = (isset($_POST['signal_sl']))?$_POST['signal_sl']:$_GET['signal_sl'];
            $signal_price = (isset($_POST['signal_price']))?$_POST['signal_price']:$_GET['signal_price'];
            $signal_result = (isset($_POST['signal_result']))?$_POST['signal_result']:$_GET['signal_result'];
            $signal_action = (isset($_POST['signal_action']))?$_POST['signal_action']:$_GET['signal_action'];
            $signal_trader = (isset($_POST['trader_id']))?$_POST['trader_id']:$_GET['trader_id'];
            $signal_winloss = (isset($_POST['signal_winloss']))?$_POST['signal_winloss']:$_GET['signal_winloss'];



            if(
                    !isset($signal_status)
                ||  !isset($signal_pair)
                ||  !isset($signal_tp)
                ||  !isset($signal_sl)
                ||  !isset($signal_price)
            ){
                unset($signalInstance);
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'Please check to ensure you\'ve set a proper status, pair, TP, SL, and price.')
                ));

            }

            $o = array(
                'signal_status'=>$signal_status,
                'signal_pair'=> $signal_pair,
                'signal_tp'=> $signal_tp,
                'signal_sl'=>$signal_sl,
                'signal_price'=>$signal_price,
                'signal_result'=>(isset($signal_result))?$signal_result:0,
                'signal_action'=>$signal_action,
                'trader_id'=>(isset($signal_trader))?$signal_trader:5,
                'signal_winloss'=>($signal_winloss)
            );

            if($signalInstance->add_signal($o))
            {
                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array('message'=>'Successfully added a new signal.')
                ));
            } else {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'There was an internal error added a new signal.')
                ));
            }
        break;



        case 'update':
            $signal_status = (isset($_POST['signal_status']))?$_POST['signal_status']:$_GET['signal_status'];
            $signal_pair = (isset($_POST['signal_pair']))?$_POST['signal_pair']:$_GET['signal_pair'];
            $signal_tp = (isset($_POST['signal_tp']))?$_POST['signal_tp']:$_GET['signal_tp'];
            $signal_sl = (isset($_POST['signal_sl']))?$_POST['signal_sl']:$_GET['signal_sl'];
            $signal_price = (isset($_POST['signal_price']))?$_POST['signal_price']:$_GET['signal_price'];
            $signal_result = (isset($_POST['signal_result']))?$_POST['signal_result']:$_GET['signal_result'];
            $signal_id = (isset($_POST['signal_id']))?$_POST['signal_id']:$_GET['signal_id'];
            $signal_action = (isset($_POST['signal_action']))?$_POST['signal_action']:$_GET['signal_action'];
            $signal_trader = (isset($_POST['trader_id']))?$_POST['trader_id']:$_GET['trader_id'];
            $signal_winloss = (isset($_POST['signal_winloss']))?$_POST['signal_winloss']:$_GET['signal_winloss'];
            $signal_date = (isset($_POST['signal_date']))?$_POST['signal_date']:$_GET['signal_date'];




            if(!isset($signal_id))
            {
                unset($signalInstance);
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'There was an error updating a signal. Please provide a valid signal ID.'
                    )
                ));
            }
            if(
                    !isset($signal_status)
                ||  !isset($signal_pair)
                ||  !isset($signal_tp)
                ||  !isset($signal_sl)
                ||  !isset($signal_price)
            ){
                unset($signalInstance);
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'Please check to ensure you\'ve set a proper status, pair, TP, SL, and price for ID: ' .  $signal_id . '.'

                    )
                ));

            }

            $o = array(
                'signal_id'=> $signal_id,
                'signal_status'=>$signal_status,
                'signal_pair'=> $signal_pair,
                'signal_tp'=> $signal_tp,
                'signal_sl'=>$signal_sl,
                'signal_price'=>$signal_price,
                'signal_result'=>(isset($signal_result))?$signal_result:0,
                'signal_action'=>$signal_action,
                'trader_id'=>$signal_trader,
                'signal_winloss'=>$signal_winloss,
                'signal_date'=>$signal_date

            );

            if($signalInstance->update_signal($o))
            {
                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array('message'=>'Successfully update signal.')
                ));
            } else {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'There was an internal error updating that signal.')
                ));
            }
            break;


        /** ================
         *  SHOW ALL SIGNALS
         *  ================
         */
        default:
            $signal_list = $signalInstance->get_signals();
            $traderInstance = new Trader();
            $trader_list = $traderInstance->get_trader_ids();
            unset($traderInstance);




            if($signal_list)
            {

              // $signals = $signal_list;
                foreach($signal_list as $item)
                {

                    //do any conversions we need to do
                    $signals[] = array(
                        'signal_id'=>$item['signal_id'],
                        'signal_status'=>$item['signal_status'],
                        'signal_pair'=>$item['signal_pair'],
                        'signal_tp'=>substr($item['signal_tp'],0,6),
                        'signal_sl'=>substr($item['signal_sl'],0,6),
                        'signal_price'=>substr($item['signal_price'],0,6),
                        'signal_winloss'=>$item['signal_winloss'],
                        'signal_result'=>$item['signal_result'],
                        'signal_tmodified'=>$item['signal_tmodified'],
                        'signal_tcreate'=>$item['signal_tcreate'],
                        'signal_date'=>$item['signal_date'],
                        'signal_action'=>$item['signal_action'],
                        'trader_id'=>$item['trader_id']
                    );
                }



                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array(
                        'signals'=>$signals,
                        'message'=>'Successfully loaded '. count($signal_list). ' signals.',
                        'traders'=>$trader_list
                        //'html'=>$html
                    )
                ));
            } else {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'There are no signals available in the system.')
                ));
            }


            break;
    }
}