<?php
global $controllerID,$controllerObject,$controllerFunction;

/** =================
 *  Broker Controller
 *  =================
 */

$userInstance = new User();
$userList = $userInstance->get_user_broker_account_list();


    $broker_key = (isset($_POST['broker_key']))?$_POST['broker_key']:$_GET['broker_key'];

    switch($controllerFunction)
    {


        /** ==============
         *  /broker/verify
         *  ==============
         *  verify a user with a broker
         */

        case 'verify':

            $userEmail = (isset($_POST['user_email']))?strtolower($_POST['user_email']):strtolower($_GET['user_email']);
            $userBrokerID = (isset($_POST['user_broker_account']))?strtolower($_POST['user_broker_account']):strtolower($_GET['user_broker_account']);
            if(!isset($userEmail)) $userEmail = false;
            if(!isset($userBrokerID)) $userBrokerID = false;

            if($userEmail && $userBrokerID)
            {
                $userInstance = new User();
                if($userInstance->is_verified($userEmail,$userBrokerID))
                {
                    api_response(array(
                        'code'=> RESPONSE_SUCCESS,
                        'data'=>
                        array(
                                'message'=>'User with ' . $userEmail . ' successfully verified in system with account ID: ' . $userBrokerID,
                                'user_email'=>$userEmail,
                                'user_broker_account'=>$userBrokerID
                        )
                    ));

                } else {
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_INVALID_USER_BROKER_ACCOUNT)
                    ));

                }
            } else {
                api_response(array(
                    'code'=> RESPONSE_ERROR,
                    'data'=> array('message'=>'You must specify a user email address and user broker account identifier')
                ));
            }



        /** ============
         *  /broker/sync
         *  ============
         *  Syncs user accounts from broker to our DB via logging into broker
         *  and scraping a particular page containing broker account IDs. This
         *  requires a broker_key for security purposes. Only TFS should be
         *  accessing this URL.
         */
        case 'sync':



            $brokerLogin = null;
            $brokerPassword = null;
            $brokerHash = null;
            $brokerPost = null;
            $brokerURL = null;


            switch($controllerID)
            {


                case 'ic':
                    $brokerLogin =  IC_LOGIN;
                    $brokerPassword = IC_PASSWORD;
                    $brokerHash = md5(IC_LOGIN . IC_PASSWORD);
                    $_SESSION['brokerHash'] = $brokerHash;
                    if(!isset($broker_key) && isset($_SESSION['brokerHash'])) $broker_key = $_SESSION['brokerHash'];
                    $brokerPost = 'login=' . $brokerLogin . '&password='. $brokerPassword . '&ReturnUrl=';//https://secure.icmarkets.com/Statistic/ClientsBill';
                    $brokerURL = 'https://secure.icmarkets.com/Account/LogOn';
                    $targetURL = 'https://secure.icmarkets.com/Statistic/ClientsBill'; //server #1

                    if(!$broker_key || $broker_key !== $brokerHash)
                    {
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_INVALID_API_KEY)
                        ));
                    }


                    /** ========================
                     *  LETS LOG INTO THE SYSTEM
                     *  ========================
                     */
                    $rawData = fetch($brokerURL,array(
                        'post'=>$brokerPost,
                        'cookiefile'=>TMP_PATH.$brokerHash . '.txt',
                        'dest'=>'https://secure.icmarkets.com/Statistic/ClientsBill',
                    ));


                    if(empty($rawData))
                    {
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_BROKER_LOGIN,'response'=>$rawData)
                        ));
                    }
                    else {
                            $newAccounts = extract_account('ic',$rawData);
                    }
                    break;


                /** ========
                 *  HotForex
                 *  ========
                 *  HotForex contains multiple servers / pages to scrape
                 *  url: http://api.fxparse/broker/sync/hfx/?broker_key=044cb35128d614f30698bc77269bb2a5
                 */
                case 'hfx':
                    $brokerLogin =  HFX_LOGIN;
                    $brokerPassword = HFX_PASSWORD;
                    $brokerHash = md5(HFX_LOGIN . HFX_PASSWORD);
                    $_SESSION['brokerHash'] = $brokerHash;
                    if(!isset($broker_key) && isset($_SESSION['brokerHash'])) $broker_key = $_SESSION['brokerHash'];

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
                    $cookie_file = TMP_PATH.$brokerHash . '.txt';

                    $url="https://my.hotforex.com/login";
                    $_ch = curl_init();
                    curl_setopt ($_ch, CURLOPT_URL, $url);
                    curl_setopt ($_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt ($_ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
                    curl_setopt ($_ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt ($_ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt ($_ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt ($_ch, CURLOPT_COOKIEJAR,$cookie_file);
                    curl_setopt ($_ch, CURLOPT_COOKIEFILE, $cookie_file);  // <-- add this line
                    curl_setopt ($_ch, CURLOPT_REFERER, $url);

                    $result = curl_exec ($_ch);

                    curl_close($_ch);



                    $contents = file_get_contents($cookie_file);


                    $xsrf_token = trim(get_string_between($contents, "_xsrf", "my.hotforex.com"));
                    if(!$xsrf_token) $xsrf_token = trim(get_string_between($contents, "_xsrf", "\n"));





                    $brokerPost = 'username=' . $brokerLogin .'&password=' . $brokerPassword .'&login=Login&_xsrf=' . $xsrf_token;
                    $brokerURL = 'https://my.hotforex.com/login';

                    $targetURL = array(
                        0=>array('https://my.hotforex.com/en/partners/clients.html','refid=131722&regulator=HF&server=1&submit=Search&_xsrf=' . $xsrf_token),
                        1=>array('https://my.hotforex.com/en/partners/clients.html','refid=131722&regulator=HF&server=2&submit=Search&_xsrf=' . $xsrf_token),
                        2=>array('https://my.hotforex.com/en/partners/clients.html','refid=131722&regulator=HFCY&server=1&submit=Search&_xsrf=' . $xsrf_token),
                        3=>array('https://my.hotforex.com/en/partners/clients.html','refid=131722&regulator=HFCY&server=2&submit=Search&_xsrf=' . $xsrf_token)
                    );


                    /** ========================
                     *  LETS LOG INTO THE SYSTEM
                     *  ========================
                     */
                    $rawData = fetch($brokerURL,array(
                        'cookiefile'=>$cookie_file,
                        'post'=>$brokerPost,
                        //'hiddenVars'=>true,
                        'refer'=>'https://my.hotforex.com',
                        'headers'=>array(
                            'DNT'=>'1',
                            'Content-Type'=>'application/x-www-form-urlencoded',
                            'Cache-Control'=>'max-age=0'
                        )
                    ));



                    if(empty($rawData))
                    {
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_BROKER_LOGIN,'response'=>$rawData)
                        ));
                    }
                    else {

                        /** ============================================================
                         *  WE ARE LOGGED IN, NOW LETS SCRAPE THE TARGET PAGES SPECIFIED
                         *  ============================================================
                         */
                        $_tmp = Array();
                        $newAccounts = Array();
                        foreach($targetURL as $item)
                        {
                            $brokerURL = $item[0];
                            $brokerPost = $item[1];
                            //error_log('accessing: ' . $brokerURL . '?' . $brokerPost );

                            $rawData = fetch($brokerURL,array(
                                'cookiefile'=>$cookie_file,//TMP_PATH.$brokerHash . '.txt',
                                'post'=>$brokerPost,
                            ));

                           $_account = extract_account('hfx',$rawData);

                            if(count($_account)>0)
                            {
                                $_tmp = $newAccounts;
                                $newAccounts = array_merge($_tmp, $_account);

                            }

                        }
                        //exit('<pre>' . print_r($newAccounts,true));
                    }
                break;

                case '4x':
                    $brokerLogin =  FX_LOGIN;
                    $brokerPassword = FX_PASSWORD;
                    $brokerHash = md5(FX_LOGIN . FX_PASSWORD);
                    $brokerPost = 'DefaultScriptManager_HiddenField=&__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUKLTg0ODI5MTM1N2QYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFElJvb3QkY3RsMDgkYnRuTG9nbwUbUm9vdCRNYWluJGN0bDAwJGNoa1JlbWVtYmVyeDJZJMSSz89TMhrbQhJbAf3%2FBnzV3oD8U2Kp5cIaZFg%3D&__SCROLLPOSITIONX=0&__SCROLLPOSITIONY=0&__EVENTVALIDATION=%2FwEdAAaqVB4p8iXCxwPu0fzlphS%2F7cdMFDWhzz8yePHhEIC8CXla0UtUYJ7V5%2BY9mItxFV%2BpPXTokvDKWL%2BaREWUDPj%2FIi9KT2dAegUQoo%2F1s0LoH%2Fi%2BZql1eWNj%2F4vHAAdmhHmqCUValcy6%2Bcg9D9yk7rvvIiEnzUWdLLHqMahHSOBSLg%3D%3D&Root%24Main%24ctl00%24txtEmail='. urlencode($brokerLogin).'&Root%24Main%24ctl00%24txtPassword=' . $brokerPassword .'&Root%24Main%24ctl00%24chkRemember=on&Root%24Main%24ctl00%24btnLogin=Login&GRID_SELECTED_ROW_INDEX=';
                    $brokerURL = 'http://replicator.4xservices.com/Login.aspx';
                    $brokerDestination = 'http://replicator.4xservices.com/Dashboard/Accounts.aspx';
                    $rawData = fetch($brokerURL,array(
                        'cookiefile'=>TMP_PATH.$brokerHash . '.txt',
                        'post'=>$brokerPost,
                        'dest'=>$brokerDestination
                    ));


                    if(empty($rawData))
                    {
                        api_response(array(
                            'code'=> RESPONSE_ERROR,
                            'data'=> array('message'=>ERROR_BROKER_LOGIN,'response'=>$rawData)
                        ));
                    } else {
                        $newAccounts = extract_account('4x',$rawData);
                    }

                    if($newAccounts)
                    {
                        $traderInstance = new Trader();

                        foreach($newAccounts as $item)
                        {
                            $traderInstance->update_trader_mt4_server(
                                array('trader_server'  => $item['server_name'],
                                'trader_account' => $item['account_id']
                            ));
                        }

                        api_response(array(
                            'code'=> RESPONSE_SUCCESS,
                            'data'=> array(
                                'message'=>'Successfully updated '. count($newAccounts) . ' new  MT4 accounts',
                                'accounts'=>$newAccounts
                            )
                        ));
                    } else {
                        api_response(array(
                            'code'=> RESPONSE_SUCCESS,
                            'data'=> array(
                                'message'=>'No new accounts to add'
                            )
                        ));
                    }

                break;

                default:
                    api_response(array(
                        'code'=> RESPONSE_ERROR,
                        'data'=> array('message'=>ERROR_BROKER_LOGIN,'response'=>$rawData)
                    ));
                break;
            }



            /** ========================================================
             *  Determine if there was any data returned from extraction
             *  ========================================================
             */

            if(count($newAccounts) > 0)
            {

                notify_accounts($newAccounts);

                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array(
                        'message'=>'Successfully added '. count($newAccounts) . ' new accounts',
                        'accounts'=>$newAccounts
                    )
                ));
            } else {
                api_response(array(
                    'code'=> RESPONSE_SUCCESS,
                    'data'=> array(
                        'message'=>'No new accounts to add'
                    )
                ));
            }
        break;


        default:
            api_response(array(
                'code'=> RESPONSE_ERROR,
                'data'=> array('message'=>ERROR_INVALID_BROKER)
            ));
            break;

    }



    function notify_accounts($accountList)
    {
        include_once(LIB_PATH . 'phpmailer.class.php');

        foreach($accountList as $item)
        {
            $email = isset($item['email'])?$item['email']:false;
            $name = isset($item['name'])?$item['name']:'Top Forex Signal User';
            if($email && filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $emailBody = <<<EOT
Welcome <b>$name</b>,
<br/><br/>
Your trading account under our partner broker is now live.
<br/><br/>
You can start connecting your account to our signal provider by going to:
<br/>
http://topforexsignal.com/verify
<br/><br/>

We are happy to have you on board!<br/>
<br/>
-<i>Top Forex Signal Team</i>
EOT;
                $emailSubject = 'You are connected to Top Forex Signal!';
                $emailHeader = "From: support@topforexsignal.com\n" . "bcc:support@topforexsignal.com\n" . "MIME-Version: 1.0\n" . "Content-type: text/html; charset=utf-8\n";
                if(mail($email, $emailSubject, $emailBody, $emailHeader,"-f$email")) {
                    return true;
                } else {
                    error_log('Error sending broker connection email to ' . $email);
                    return false;
                }
            }
        }
    }


    function get_string_between($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return false;
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }



    /** ==========================
     *  fetch(url,options <array>)
     *  ==========================
     *  Logs into a secure page and retrieves the content
     *  auth - user password pair for http authentication, like .htaccess
     *  post - POST data to pass through
     *  refer - referrer URL to pass through to the host
     *  cookiefile - name of the cookie to store
     *  useragent - optional useragent you may want to set
     *  timeout - timeout to url, by default its 5 seconds
     *  dest - page to retrieve once we login
     */

    function fetch( $url, $z=null ) {
        $ch =  curl_init();
        $hiddenVal = '';



        if(isset($z['hiddenVars']) && $z['hiddenVars'])
        {
            /**
             *  This is if the input field of the form has any hidden fields, lets include this
             */

            $rawData = file_get_contents($url);
            $doc = phpQuery::newDocument($rawData);
            $t = $doc->find('form input');
            $length = $t->length();
            for($i=0;$i< $length-1;$i++)
            {
                $type = $t->eq($i)->attr('type');
                $name =  $t->eq($i)->attr('name');
                $value = $t->eq($i)->attr('value');

                if($type =='hidden')
                {
                    $postVal[] = array('name'=>$name,'value'=>urlencode($value));
                }

            }
            if(!empty($postVal))
            {
                foreach($postVal as $item)
                {
                    $hiddenVal .= '&'. $item['name'] . '=' .$item['value'] ;
                }
            }

            //error_log('hiddenVars found:' . $hiddenVal,true);

        }

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,         10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,  30);

        curl_setopt( $ch, CURLOPT_URL, $url );

        if(isset($z['auth'])) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD,$z['auth']);
            //error_log('authenticating with: ' . $z['auth']);
        }

        if(isset($z['binary'])) curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

        if(isset($z['header'])) curl_setopt($ch,CURLOPT_HTTPHEADER,$z['header']);

        curl_setopt( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_HEADER, 0);

        if( isset($z['post']) )
        {
            $postData = $z['post'];
            if(isset($hiddenVal)) {
                $postData .=$hiddenVal;
                //error_log('post: ' . $postData);
            }
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );


        }
        if( isset($z['refer']) ) curl_setopt( $ch, CURLOPT_REFERER, $z['refer'] );

        curl_setopt( $ch, CURLOPT_USERAGENT,isset($z['useragent']) ? $z['useragent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2');
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, ( isset($z['timeout']) ? $z['timeout'] : 5 ) );

        if(isset($z['cookiefile']))
        {
            curl_setopt( $ch, CURLOPT_COOKIEJAR,  $z['cookiefile'] );
            curl_setopt( $ch, CURLOPT_COOKIEFILE, $z['cookiefile'] );
        }

        if(isset($z['cookie']))
        {
            curl_setopt($ch, CURLOPT_COOKIE, $z['cookie']);
        }

        //error_log('fetch(): accessing: ' . $url);
        $result = curl_exec( $ch );

        if(isset($z['dest']))
        {
            if(isset($z['dest_post']))
            {
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $z['dest_post']);
            }
            //error_log('fetch(): accessing secondary URL: ' . $z['dest']);

            curl_setopt($ch, CURLOPT_URL, $z['dest']);
            $result = curl_exec($ch);
        }

        curl_close( $ch );
        return $result;
    }

    /** ====================================
     *  extract_account(brokerName, rawData)
     *  ====================================
     *  Using phpQuery, extracts pertinent user account information from
     *  broker website by processing raw page text, return array of data
     *  brokerName = the name of the broker e.g. hfx = hotforex
     *  rawData = raw HTML data to parse, unqiue for each broker
     */
    function extract_account($brokerName,$rawData)
    {
        switch($brokerName)
        {

            case '4x':
                /** =============================
                 *  4X API SCRAPE SERVER LOCATION
                 *  =============================
                 *  This is a special one-off case where we scrape the 4X dashboard for the traders
                 *  MT4 server. This is not asked of the user during the registration process, but we
                 *  can guarantee we have it once they sign up with 4x. It employs the following logic:
                 *  1. Grab all trader_accounts from traders table with empty trader_server
                 *  2. Grab all accounts from 4x dashboard
                 *  3. Query the intersect for server data and stash to array
                 *  4. Iterate through array and store server in DB
                 */

                //$rawData = file_get_contents('tmp/4xtest.html');

                $doc = phpQuery::newDocument($rawData);


                //STEP #1 - Grab all trader_accounts from traders table with empty trader_server
                $traderInstance = new Trader();
                $traderAccounts = $traderInstance->get_trader_account_by_server_name('');

                //print('<pre> doesnt have server:' . print_r($traderAccounts));
                //exit(print_r($traderAccounts,true));


                //STEP #2 - Grab all accounts from 4x dashboard
                $t = $doc->find('#Main_ctl00_gridList tr');
                for($i=1; $i < count($t);$i++)
                {
                    $accountID      =  $t->eq($i)->find('td.col-account-hash')->text();
                    $accountName    =  $t->eq($i)->find('td.col-account-name')->text();
                    $accountFXID    =  $t->eq($i)->find('td.col-edit input')->attr('onclick');
                    $accountIDs[] = array(
                        'account_id'    =>intval($accountID),
                        'account_name'  => trim($accountName),
                        'account_fx_id' => rtrim(ltrim($accountFXID,'OpenModal("/Dashboard/Accounts/Enter.aspx?id='),'");return false;')
                    );
                }
                //exit('<pre>' . print_r($accountIDs,true));


                //STEP #3 - Query the intersect for server data and stash to array
                foreach($traderAccounts  as $item)
                {
                   // if(in_array(intval($item),$accountIDs)) $intersect[] = intval($item);
                    foreach($accountIDs as $account)
                    {
                        if($account['account_id'] == $item) $intersect[] = $account;

                    }
                    //print '<pre>checking for item: ' . $item . ' in  '. print_r($accountIDs,true);

                }



                //exit('<pre>Intersect:<br/>'.print_r($intersect,true));
                //print 'found intersects:'. print_r($intersect,true);
                if(empty($intersect)) return false;

                //STEP #4 - Iterate through array and store server in DB
                foreach($intersect as $item)
                {
                    //print '<pre>item:' .  print_r($item,true);
                    $targetURL = 'http://replicator.4xservices.com/Dashboard/Accounts/Enter.aspx?id=' . $item['account_fx_id'];
                    $rawData = fetch($targetURL,array(
                        'cookiefile'=>TMP_PATH.md5(FX_LOGIN . FX_PASSWORD) . '.txt'


                    ));
                    //print 'fetching url:' . $targetURL . ' with data:  <br>' .$rawData;


                    if($doc) unset($doc);
                    $doc = phpQuery::newDocument($rawData);
                    $t = $doc->find('#Main_ctl00_lstBrokerServer');
                    $serverName =  $t->find('option[selected="selected"]')->text();

                    $result[] = array(
                        'server_name'=>$serverName,
                        'account_fx_id'=>$item['account_fx_id'],
                        'account_id' => $item['account_id']
                    );


                }
                if(empty($result)) return false;
                //exit('<pre>' . print_r($rawData,true));
                return $result;
                break;


            case 'hfx':
                $doc = phpQuery::newDocument($rawData);

                $_t = $doc->find('#datatable tbody tr');
                $_count = count($_t);
                $accounts = array();
                for($i=1;$i < $_count;$i++)
                {
                    $userID = $_t->eq($i)->children('td')->eq(1)->text();
                    $userEmail = $_t->eq($i)->children('td')->eq(2)->children('span')->text();
                    $userCountry = $_t->eq($i)->children('td')->eq(3)->children('b')->text();

                    $_t->eq($i)->children('td')->eq(2)->children('span')->remove();
                    $_t->eq($i)->children('td')->eq(3)->children('b')->remove();

                    $userFullName = $_t->eq($i)->children('td')->eq(2)->text();
                    $userDate = $_t->eq($i)->children('td')->eq(3)->text();
                    $accounts[]= array(
                        'id'=>$userID,
                        'email'=> $userEmail,
                        'name'=>$userFullName,
                        'date'=>$userDate,
                        'country'=>$userCountry,
                        'broker'=>$brokerName,
                        'status'=>1
                    );

                    $accountCheck[] = $userID;
                }
                unset($doc);

                $userInstance = new User();
                $userList = $userInstance->get_user_broker_account_list();

                $newAccounts = Array();

                //lets run a comparison
                foreach($accounts as $item)
                {
                    if(is_array($userList) && !in_array($item['id'],$userList))
                    {
                        $result = $userInstance->update_user_account($item);
                        $newAccounts[] = $item;
                    }
                }
                unset($userInstance);

                return $newAccounts; //lets return an array of account information
            break;

            case 'xm':
                $doc = phpQuery::newDocument($rawData);
                $_t = $doc->find('table.xsys_tbl tr.xsys_tblrow');
                $_count = count($_t);
                //exit('<pre> data:' . $_t->html() . ' </pre>');
                //exit(print_r($_t,true));
                //$accounts = array();
                for($i=1;$i == $_count;$i++)
                {
                    $userID = $_t->eq($i-1)->children('td')->eq(3)->text();
                    $userDate = $_t->eq($i-1)->children('td')->eq(7)->text();
                    $_userCountry = explode(':',$_t->eq($i-1)->children('td')->eq(6)->text());
                    $userCountry = $_userCountry[1];

                    $accounts[]= array(
                        'id'=>$userID,
                        'date'=>date_format(date_create_from_format('d/m/Y',$userDate),'m/d/Y'),
                        'country'=>$userCountry,
                        'broker'=>$brokerName,
                        'status'=>1
                    );

                    $accountCheck[] = $userID;
                }

                unset($doc);


                $userInstance = new User();
                $userList = $userInstance->get_user_broker_account_list();


                $newAccounts = Array();

                //lets run a comparison
                foreach($accounts as $item)
                {
                    if(is_array($userList) && !in_array($item['id'],$userList))
                    {
                        $result = $userInstance->update_user_account($item);
                        $newAccounts[] = $item;
                    }
                }
                unset($userInstance);
                return $newAccounts; //lets return an array of account information
            break;


            case 'ic':

                $doc = phpQuery::newDocument($rawData);
                $_t = $doc->find('#tbodyContent tr');
                $_count = count($_t);
                //error_log('processing account data with a count of: ' . $_count);
                $accounts = array();
                for($i=0;$i < $_count;$i++)
                {
                    $userDate = $_t->eq($i)->children('td')->eq(11)->children('div')->eq(0)->text();
                    $userID = $_t->eq($i)->children('td')->eq(5)->children('div')->eq(0)->text();
                    $userFullName = $_t->eq($i)->children('td')->eq(7)->children('div')->eq(0)->text();
                    //error_log('date: ' . $userDate . ' id:'. $userID.  ' name: ' . $userFullName);
                    $accounts[]= array(
                        'id'=>trim(preg_replace('/\s\s+/', ' ', $userID)),
                        'name'=> trim(preg_replace('/\s\s+/', ' ', $userFullName)),
                        'date'=>preg_replace('/\s\s+/', ' ', $userDate),
                        'broker'=>$brokerName,
                        'status'=>1
                    );



                    $accountCheck[] = $userID;
                }
                unset($doc);

                //exit(print_r($accounts,true));



                $userInstance = new User();
                $userList = $userInstance->get_user_broker_account_list();

                $newAccounts = Array();

                //lets run a comparison
                foreach($accounts as $item)
                {
                    if(is_array($userList) && !in_array($item['id'],$userList))
                    {
                        $result = $userInstance->update_user_account($item);
                        $newAccounts[] = $item;
                    }
                }
                unset($userInstance);

                return $newAccounts; //lets return an array of account information                break;

            case 'axi':
                exit($rawData);
                break;

            default:
                return array();
            break;
        }
    }


