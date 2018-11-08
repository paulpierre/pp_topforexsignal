<?php

/** ===================
 *  CarbonFX Controller
 *  ===================
 */


    switch($controllerFunction)
    {
        /** =============
         *  ADD A SIGNAL
         *  ============
         */


        case 'list':
        default:

            $accountInstance = new CarbonFX();
            $accountData = $accountInstance->get_accounts();
            unset($accountInstance);
            api_response(array(
                'code'=> RESPONSE_SUCCESS,
                'data'=> array('message'=>count($accountData) . ' accounts loaded.','accounts'=>$accountData)
            ));
    break;


    }
