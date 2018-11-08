<?

    if(!isset($_SESSION['traderIDs']))
    {
        $res = json_decode(file_get_contents('http://' .  API_HOST.'/trader/get_trader_ids'),true);
        if($res && $res['response'] == 1)
        {
            $traderList = $res['data']['traders'];
        } else $traderList = false;
    } else {
        $traderList = $_SESSION['traderIDs'];
    }

    //error_log('traderList is here: '  .$traderList . PHP_EOL . 'raw:' . print_r($res,true));

    //exit();


    $verificationFailed = false;
    $isVerified = false;

    $userEmail = (isset($_POST['userEmail']))?strtolower($_POST['userEmail']):null;
    $userBrokerName = (isset($_POST['userBrokerName']))?strtolower($_POST['userBrokerName']):null;
    $userBrokerID = (isset($_POST['userBrokerID']))?strtolower($_POST['userBrokerID']):null;
    $userFullName = (isset($_POST['userFullName']))?$_POST['userFullName']:null;
    $userTraderID = (isset($_POST['userTraderID']))?strtolower($_POST['userTraderID']):null;
    $userTraderName = (isset($_POST['userTraderName']))?$_POST['userTraderName']:null;

    if($userEmail && $userBrokerID)
    {

        $isVerified = true;

        //lets email this information
        $emailBody = '<b>User Name:</b>' . $userFullName. '<br>' . '<b>User Email:</b> ' . $userEmail . '<br>' . '<b>Broker:</b> ' . $userBrokerName . ' - ID: ' . $userBrokerID . '<br><b>Signal:</b> ' . $userTraderName;
        $emailSubject = 'Verification Submission - ' . $userEmail. ' - '. $userFullName;
        $emailTo = 'support@topforexsignal.com';

        $emailHeader = "From: $emailTo\n" . "MIME-Version: 1.0\n" . "Content-type: text/html; charset=utf-8\n";


        //if(send_email($emailTo,$emailSubject,$emailBody))
        mail($emailTo, $emailSubject, $emailBody, $emailHeader,"-f$emailTo");


        //enable this later!!
        /*
        $result = file_get_contents('http://' .  API_HOST.'/broker/verify/?user_email='  . urlencode($userEmail) . '&user_broker_account=' . urlencode($userBrokerID) . '&user_broker_name=' . $userBrokerName . '&user_trader_id=' . $userTraderID . '&user_trader_name=' . $userTraderName) ;
        $res = json_decode($result,true);

        if(intval($res['response']) == 1)
        {
            $isVerified = true;
            $verificationFailed = false;
        } elseif(intval($res['response'])==0) {
            $isVerified = false;
            $verificationFailed = true;
        }*/
    }
?>
<section id="home" class="content">
    <div class="main-title">
        <div class="container">
            <h2>Verify Broker Account</h2>
        </div>
    </div>

    <div class="margin30"></div>

    <div class="container">
        <div class="welcome">
            <div class="trader-container" style="padding-bottom:30px;">
                <div id="msform">
                    <form id="verify-form" method="post" accept-charset="iso-8859-1" action="/verify" >
                        <div id="form-broker-account">


                            <? if($verificationFailed) { ?>

                            <p>
                                <h2 style="color:#ff0000;" class="aligncenter">Account not found!</h2>
                                Sorry, your account number is not under our IB system yet.
                                Sometimes it might take up to 24 hours for us to receive your account number.
                                <br/><br/>
                                Please check with your broker or email us at support@topforexsignal.com
                            <div class="margin10"></div>

                            </p>
                            <? } ?>
                            <? if($isVerified) { ?>

                            <p>
                            <h2>Thanks! </h2>
                            Your account has been submitted to be verified. We will set it up for you and you should hear from us within 24 hours.
                            Send us an email at <a href="mailto:support@topforexsignal.com">support@topforexsignal.com</a> if you have any questions.
                            </p>
                                    <!--

                                <p>
                                     <h2 class="aligncenter">
                                        Congratulations, you're verified!<div class="selected-icon fa fa-check" style="float:none;position:relative;top:0;"></div>
                                    </h2>
                                    You can now connect to our signal providers. Click the continue button below to complete the process.
                                    <div class="margin40"></div>
                                    <a  href="https://www.4xservices.com/subscribe.php?sid=1883da5d-fec6-4913-b6ff-619d90eb6b2f&currency=6" class="button large alignright blue" >Continue</a>
                                    <a class="button large  alignright btnFinish" href="/guide" target="_new">Setup Guide</a>
                                </p>
                                -->

                            <? } else { ?>
                            <br>
                            <h2 style="font-weight:bold;" class="aligncenter">Receive 50% off by verifying!</h2><br/>
                            <p class="aligncenter margin50">You should have your live trading account with one of our supported brokers before you can verify. If not please <a class="green" style="text-decoration:underline;font-weight:bold;" href="/signup/#1">signup</a> first.
                            <h2 class="aligncenter">Please fill out the following</h2>
                            <br/><span class="aligncenter">Your MT4 accounts need to be funded to be verified</span>
                            <select id="verify-select-broker" name="userBrokerName" class="aligncenter">
                                <option value="">Select your broker</option>
                                <option value="hfx">HotForex</option>
                                <option value="axi">AxiTrader</option>
                                <option value="ic">IC Markets</option>
                            </select>
                                <input id="verify-email-address" class="aligncenter" type="text" name="userEmail" placeholder="Email address" />
                                <input id="verify-full-name" class="aligncenter" type="text" name="userFullName" placeholder="Your full name" />

                            <div class="margin10"></div>
                                <input id="verify-broker-id" class="aligncenter" type="text" name="userBrokerID" placeholder="MT4 Account #" />
                            <select id="verify-select-trader" name="userTraderName" class="aligncenter">
                                <option value="">Select Signal Provider</option>
                                <?
                                //$trader_list = $html['data']['traders'];


                                foreach($traderList as $t_item)
                                {
                                    ?>
                                    <option value="<? print $t_item['trader_full_name']; ?>" <? if(intval($t_item['trader_id']) == intval($item['trader_id'])) print ' selected="selected"'; ?>><? print $t_item['trader_full_name']; ?></option>
                                    <?
                                }
                                ?>

                            </select>

                                <div class="margin10"></div>
                                <a href="#" id="verify-submit" class="button large alignright" >Verify account</a>
                            <? } ?>


                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
