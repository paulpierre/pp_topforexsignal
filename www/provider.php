<?
$userEmail = (isset($_POST['userEmail']))?strtolower($_POST['userEmail']):null;
$userDetails= (isset($_POST['userDetails']))?strtolower($_POST['userDetails']):null;
$userMyFxBookURL = (isset($_POST['userMyFxBookURL']))?strtolower($_POST['userMyFxBookURL']):null;


if($userEmail && $userMyFxBookURL)
{

    $isVerified = true;

    //lets email this information
    $emailBody = '<b>User Email:</b>' . $userEmail. '<br>' . '<b>MyFxBook:</b> ' . $userMyFxBookURL . '<br>' . '<b>Details:</b> ' . $userDetails ;
    $emailSubject = 'Signal Provider Application - ' . $userEmail;
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
<!-- home section Starts here -->
<section id="provider" class="content">
    <div class="main-title">
            <h2>Be a Forex Signal Provider</h2>
    </div>
    <div class="margin30"></div>


    <div class="container">
        <div class="welcome">
            <div class="margin10"></div>

            <div class="provider-container alignleft">

                <img src="/images/traders03.png"  class="alignleft" width="234" height="160"/>
                <h2 style="color:#97ac5e;margin-top:25px;">Join the winners circle</h2>

                <p>
                    We are very selective with our signal providers and signal providers are only being paid for winning trades.
                    However we have tons of happy clients.
                    However if you have a winning track record, join Top Forex Signals and offer your service to our huge subscriber base.
                    We pay out as much as <strong>0.3 - 0.5 pips</strong> per winning trade.

                </p>

                <div class="margin30"></div>
                <h2 style="color:#97ac5e;">What is the requirement to be a signal provider?</h2>

                <p>
                    <ul>
                        <li>A connected real and verified MetaTrader 4 account with Myfxbook with a minimum of 1 month history.</li>
                        <li>Drawdown of no more than 40%.</li>
                        <li>Return of at least 10% and higher than drawdown.</li>
                        <li>Average pip win per trade of at least 5.</li>
                        <li>Average trade time over 5 minutes.</li>
                        <li>An account balance of at least $1000.</li>
                        <li>At least 100 trades.</li>
                    </ul>
                </p>

                <br/>
                <br/>
                <h2 style="color:#97ac5e;">How much money can I make?</h2>
                <p>
                    AutoTrade providers earn as much as <strong>0.3 - 0.5 pips pips per winning trade</strong>.
                    Let's check the following scenario:
                    <ul>
                        <li>You have 150 subscribers.</li>
                        <li>You make on average 100 trades per month.</li>
                        <li><strong>65%</strong> of your trades are profitable.</li>
                        <li>Total volume of all subscribers is <strong>400 mini lots</strong> (40 standard lot).</li>
                        <li>Your income is: 400*0.65*100*0.5=<strong>$14,000</strong></li>
                    </ul>
                </p>



                <div class="margin50"></div>

                <div class="aligncenter">
                    <div class="newsletter-container column one-half aligncenter">
                       <?
                        if($isVerified)
                        {
                          ?>
                            <h2>Thanks for applying!</h2>

                                We will get back to you in 48hrs or sooner.


                            <?
                        } else {
                        ?>
                        <h2>Apply to Become a Signal Provider</h2>

                        <div class="provider-form">
                            <form name="" class="" action="/provider" method="post">
                                <table>
                                    <tr>
                                        <td>Your email address:</td>
                                        <td>
                                            <input type="text" required placeholder="Email Address" name="userEmail">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Your Myfxbook URL:
                                        </td>
                                        <td>
                                            <input type="text" required placeholder="Myfxbook URL" name="userMyFxBookURL">

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Details:
                                        </td>
                                        <td>
                                            <textarea name="userDetails" placeholder="Details about your request">

                                            </textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="submit" value="submit" class="button" style="background-color:#2184be" name="btnsubmit">

                                        </td>
                                    </tr>

                                </table>
                            </form>
                            <div id="ajax_subscribe_msg"></div>
                        </div>
                            <? } ?>
                    </div>
                </div>

            </div>


        </div>
    </div>
</section>
