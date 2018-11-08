<?php

//set the timezone to hotforex
date_default_timezone_set('Europe/Amsterdam');

//lets start dat session
session_start();

//so ajax won't bitch at us
header('Access-Control-Allow-Origin: *');


/** =========
 *  INCLUDES
 *  =========
 */

//set root path
define('API_PATH',getcwd() . '/');

//operational paths
define('LIB_PATH',API_PATH . 'lib/');
define('MODEL_PATH',API_PATH . 'model/');
define('CONTROLLER_PATH',API_PATH . 'controller/');
define('LOG_PATH',API_PATH . '../log/');
define('TMP_PATH',API_PATH . 'tmp/');

//Broker credentials
define('HFX_LOGIN','##########');
define('HFX_PASSWORD','##########');
define('BROKER_HFX',1);

define('XM_LOGIN','##########');
define('XM_PASSWORD','##########');

define('IC_LOGIN','##########');
define('IC_PASSWORD','##########');
define('BROKER_IC',2);


define('AXI_LOGIN','##########');
define('AXI_PASSWORD','##########');
define('BROKER_AXI',3);


define('FX_LOGIN','##########');
define('FX_PASSWORD','##########');

define('AWEBER_LOGIN','##########');
define('AWEBER_PASSWORD','##########');

//Aweber credentials
define('AWEBER_API_APP_ID','##########');
define('AWEBER_API_KEY','##########');
define('AWEBER_API_SECRET','##########');



//4XSolutions API Key
define('FXSOLUTIONS_API_KEY','##########'); 


define('SUPPORT_EMAIL','##########');  // Gmail Credentials
define('SUPPORT_EMAIL_PASSWORD','##########');
define('SUPPORT_EMAIL_NAME','Top Forex Signal Support');

//libraries
include(LIB_PATH . 'database.class.php');
include(LIB_PATH . 'phpQuery.class.php');
include(LIB_PATH . 'excelreader.class.php');
include(LIB_PATH . 'utility.php');
//models
include(MODEL_PATH . 'order.model.php');
include(MODEL_PATH . 'trader.model.php');
include(MODEL_PATH . 'user.model.php');
include(MODEL_PATH . 'signal.model.php');
include(MODEL_PATH . 'carbonfx.model.php');
include(MODEL_PATH . 'broker.model.php');




//for debugging purposes
define('DEBUG',false);

//for environment purposes
define('MODE',(isset($_SERVER['MODE']))?$_SERVER['MODE']:'prod');

if(MODE=='prod') error_reporting(0);


/** =================
 *  MYSQL CREDENTIALS
 *  =================
 */

switch(MODE)
{
    case 'local':
        define('WWW_HOST','fxparse');
        define('API_HOST','api.fxparse');
        define('DATABASE_HOST','127.0.0.1');
        define('DATABASE_PORT',3306);
        define('DATABASE_NAME','fxparse');
        define('DATABASE_USERNAME','##########');
        define('DATABASE_PASSWORD','##########');
        break;
    default:
    case 'prod':
        define('WWW_HOST','www.topforexsignal.com');
        define('API_HOST','api.topforexsignal.com');
        define('DATABASE_HOST','##########');
        define('DATABASE_PORT',3306);
        define('DATABASE_NAME','topforex_fxparse');
        define('DATABASE_USERNAME','##########');
        define('DATABASE_PASSWORD','##########');
        break;
}


/** ==============
 *  ERROR MESSAGES
 *  ==============
 */

define('ERROR_INVALID_PARAMETERS','Invalid parameters passed');
define('ERROR_INVALID_OBJECT','Invalid object');
define('ERROR_INVALID_USER_ID','Invalid ID for object');
define('ERROR_INVALID_FUNCTION','Invalid object function');
define('ERROR_NO_DATA_AVAILABLE','No data available for object');
define('ERROR_INVALID_BROKER','Invalid broker');
define('ERROR_INVALID_API_KEY','Invalid API key');
define('ERROR_BROKER_LOGIN','Error logging into broker account to retrieve user accounts');
define('ERROR_INVALID_USER_BROKER_ACCOUNT','Verification failed, broker account ID and email not found in system');
define('ERROR_INTERNAL_ERROR','An internal error occurred attempting to perform that operation');
define('ERROR_PARSING_DATA','An internal error occurred attempting to parse the data from the source');

/** =========
 *  CONSTANTS
 *  =========
 */

//Transaction status
define('TX_STATUS_CLOSED',1);
define('TX_STATUS_OPEN',0);
define('TX_STATUS_UNKNOWN',5);

//Transaction type
define('TX_TYPE_BUY',0);
define('TX_TYPE_SELL',1);
define('TX_TYPE_BUY_LIMIT',2);
define('TX_TYPE_SELL_LIMIT',3);
define('TX_TYPE_BUY_STOP',4);
define('TX_TYPE_SELL_STOP',5);

//Response codes
define('RESPONSE_SUCCESS',1);
define('RESPONSE_ERROR',0);

//trader account types - column: trader_account_type
define('TRADER_ACCOUNT_LIVE',0); //the account is a real live master account
define('TRADER_ACCOUNT_DEMO',1); //the account is a demo account

//trader system type - column: trader_system_type
define('TRADER_MANUAL',0); //the account is being managed by a real human being
define('TRADER_EA',1); //the account is being managed by an Expert Advisor aka robot
define('TRADER_HYBRID',2); //the account is a hybrid of both


/** ===========
 *  URL MAPPING
 *  ===========
 */

//Apache rewrite handler
$q = explode('/',$_SERVER['REQUEST_URI']);
$controllerObject = strtolower((isset($q[1]))?$q[1]:'');
$controllerFunction = strtolower((isset($q[2]))?$q[2]:'');
$controllerID = strtolower((isset($q[3]))?$q[3]:'');
$controllerData = strtolower((isset($q[4]))?$q[4]:'');


/** ==================
 *  CONTROLLER ROUTING
 *  ==================
 */
//Load the object's appropriate controller
$_controller = CONTROLLER_PATH . $controllerObject . '.controller.php';
if(file_exists($_controller))
{
    include($_controller);
} else
{
    api_response(array(
        'code'=> RESPONSE_ERROR,
        'data'=> array('message'=>ERROR_INVALID_OBJECT)
    ));
}



/** ============
 *  API RESPONSE
 *  ============
 */
function api_response($res)
{
    $response_code = $res['code'];
    $response_data = $res['data'];

    header('Content-Type: application/json');
    if(DEBUG)
    {
        exit('<pre>' . print_r(
            array(
                'response'=>$response_code,
                'data'=>$response_data

            ),true));
    }
    exit(json_encode(
        array(
            'response'=>$response_code,
            'data'=>$response_data
        )
    ));
}