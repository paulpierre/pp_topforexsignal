<?php
date_default_timezone_set('Europe/Amsterdam');
session_start();
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

//credentials for email
define('SUPPORT_EMAIL','##########');  // Gmail username
define('SUPPORT_EMAIL_PASSWORD','##########');    // GMAIL password
define('SUPPORT_EMAIL_NAME','Top Forex Signal Support');



    //Apache rewrite handler
    $q = explode('/',$_SERVER['REQUEST_URI']);
    $controllerObject = strtolower((isset($q[1]))?$q[1]:'');
    $controllerFunction = strtolower((isset($q[2]))?$q[2]:'');
    $controllerID = strtolower((isset($q[3]))?$q[3]:'');
    $controllerData = strtolower((isset($q[4]))?$q[4]:'');


    define('MODE',(isset($_SERVER['MODE']))?$_SERVER['MODE']:'prod');
    switch(MODE)
    {
        case 'prod':
            define('API_HOST','api.topforexsignal.com');
            define('WWW_HOST','www.topforexsignal.com');

            define('DASHBOARD_HOST','dashboard.topforexsignal.com');
            break;
        default:
        case 'local':
            define('API_HOST','api.fxparse');
        define('WWW_HOST','fxparse');

        define('DASHBOARD_HOST','dashboard.fxparse');
            break;
    }


    define('WWW_PATH',getcwd() . '/');
    define('LIB_PATH',WWW_PATH . 'lib/');
    //include(LIB_PATH . 'class.phpmailer.php');

    //Include the header file

    if(isset($controllerObject) && $controllerObject == 'free')
    {
        include(WWW_PATH . '/lp/landing.php');
        exit();
    }


    if(isset($controllerObject) && strlen($controllerObject > 5) && file_exists(WWW_PATH . $controllerObject . '/index.php'))
    {
        include(WWW_PATH . $controllerObject . '/index.php');
        exit();
    }


    //If the requesting URL object name is a php file, include it
    if(file_exists(WWW_PATH . $controllerObject . '.php'))
        render($controllerObject);
        //If not, lets just load the default page
    else render('home');

    function render($page)
    {
        global $controllerObject;
        require(WWW_PATH . 'header.php');
        include_once(WWW_PATH . strtolower($page) . '.php');
        require(WWW_PATH . 'footer.php');
        exit();
    }

?>