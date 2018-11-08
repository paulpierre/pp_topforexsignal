<?php
global $controllerID,$controllerObject,$controllerFunction;

/** =================
 *  Aweber Controller
 *  =================
 */

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

        break;

        case 'rss':
            print <<<END
<rss xmlns:blogChannel="http://backend.userland.com/blogChannelModule" xmlns:media="http://search.yahoo.com/mrss/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
<channel>
<title>Top Forex Signal</title>
<link>http://api.topforexsignal.com/aweber/rss</link>
<description>Top Forex Signal</description>
<language>en</language>
<copyright>Copyright 2015 Top Forex Signal</copyright>
<lastBuildDate>Thu, 05 Feb 2015 04:30:13 -0500</lastBuildDate>
<atom:link href="http://api.topforexsignal.com/aweber/rss" rel="self"/>
<image>
<title>Top Forex Signal</title>
<url>
http://topforexsignal.com/images/logo.png
</url>
<link>http://api.topforexsignal.com/aweber/rss</link>
</image>
    <item>
    <title>Signal Alert! Sell EUR/USD Signal</title>
    <link>
    http://www.topforexsignal.com</link>
    <description>
    <![CDATA[
    Hi {!firstname_fix}

    It's time to take action now!

    sell EURUSD @ 1.3010

    SL @ 1.3045

    TP1 @ 1.2983

    TP2 @ 1.2946



    For more signals you can subscribe to our service by visiting

    Top Forex Signal



    For those of you who are new at forex trading



    here's a quick definition of the terms used in

    the signal:

    Long = Buy



    Short = Sell

    SL = Stop Loss

    TP = Take Profit

    Thanks from Fried Fish Forex

    ]]>
    </description>
    <guid isPermaLink="false">
    http://api.topforexsignal.com/aweber/rss/1</guid>
    <pubDate>Thu, 05 Feb 2015 04:30:12 -0500</pubDate>
    </item>

    <item>
    <title>Second Signal Alert! Sell EUR/USD Signal</title>
    <link>
    http://www.topforexsignal.com</link>
    <description>
    <![CDATA[
    Hi {!firstname_fix}

    It's time to take action now!

    sell EURUSD @ 1.3010

    SL @ 1.3045

    TP1 @ 1.2983

    TP2 @ 1.2946



    For more signals you can subscribe to our service by visiting

    Top Forex Signal



    For those of you who are new at forex trading



    here's a quick definition of the terms used in

    the signal:

    Long = Buy



    Short = Sell

    SL = Stop Loss

    TP = Take Profit

    Thanks from Fried Fish Forex

    ]]>
    </description>
    <guid isPermaLink="false">
    http://api.topforexsignal.com/aweber/rss/2</guid>
    <pubDate>Fri, 06 Feb 2015 05:40:00 -0500</pubDate>
    </item>

 <item>
    <title>Third Signal Alert! Sell EUR/USD Signal</title>
    <link>
    http://www.topforexsignal.com</link>
    <description>
    <![CDATA[
    Hi {!firstname_fix}

    Another test

    ]]>
    </description>
    <guid isPermaLink="false">
    http://api.topforexsignal.com/aweber/rss/3</guid>
    <pubDate>Fri, 06 Feb 2015 01:19:12 -0500</pubDate>
    </item>
     <item>
    <title>Third Signal Alert! Sell EUR/USD Signal</title>
    <link>
    http://www.topforexsignal.com</link>
    <description>
    <![CDATA[
    Hi {!firstname_fix}

    Another test

    ]]>
    </description>
    <guid isPermaLink="false">
    http://api.topforexsignal.com/aweber/rss/4</guid>
    <pubDate>Fri, 06 Feb 2015 05:19:12 -0500</pubDate>
    </item>
</channel>
</rss>

END;

            break;

    }


?>