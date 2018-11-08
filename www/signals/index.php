<?    define('MODE',(isset($_SERVER['MODE']))?$_SERVER['MODE']:'prod'); ?>
<!doctype html>
<!--[if IE 7 ]>    <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en-gb" class="no-js" xmlns="http://www.w3.org/1999/html"> <!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!--[if lt IE 9]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->

    <title> Top Forex Signal - Copy Professional Traders for Free!</title>

    <meta name="description" content="Top Forex Signal is the Forex community's FREE premiere money making Forex signal provider. Get started in less than 5 minutes.">
    <meta name="author" content="Top Forex Signal">
    <meta name="keywords" content="high quality forex signal, good forex signal, profitable forex signal, forex signals, make money with forex signals, forex signals" />
    <!-- **Favicon** -->
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />

    <!-- **CSS - stylesheets** -->
    <link id="default-css" href="css/style.css" rel="stylesheet" media="all" />
    <link href="css/responsive.css" rel="stylesheet" media="all" />

    <!-- **Google - Fonts** -->
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css' />
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300italic,400italic,600' rel='stylesheet' type='text/css' />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>

    <!-- SLIDER STYLES ENDS -->

    <!-- **jQuery** -->
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,900' rel='stylesheet' type='text/css'>
</head>
<body>


<div class="wrapper" >
    <div class="inner-wrapper">
        <!-- Header div Starts here -->
        <header id="header">
            <div class="container">
                <div id="logo">
                    <a href="/"> <img src="img/logo.png" alt="" title=""> </a>
                </div>
                <div id="menu-container">

                </div>
            </div>
        </header>


         <!-- Header div Ends here -->
        <div id="main">

            <div class="container" style="padding-top:50px;">


                <?

                $data = json_decode(file_get_contents('http://'.((MODE=='local')?'api.fxparse':'api.topforexsignal.com') . '/signal'),true);

                $signals = $data['data']['signals'];
                $traders = $data['data']['traders'];
                $trader_key = array();
                foreach($traders as $item)
                {
                    $trader_key[$item['trader_id']] = $item['trader_full_name'];
                }

                //error_log(print_r($trader_key,true));

                //error_log(print_r($data,true));
                $item = $signals[0];
                switch($item['signal_action'])
                {

                    case 1: $action = 'Buy';break;
                    case 2: $action = 'Sell';break;
                    case 3: $action = 'Buy Limit';break;
                    case 4: $action = 'Sell Limit';break;
                    case 5: $action = 'Buy Stop';break;
                    case 6: $actiion = 'Sell Stop'; break;
                }

                ?>
                <h1>Today's FREE Signal</h1>
                <h2>by <? print $trader_key[$signals[0]['trader_id']];?></h2>
                <h2 class="signal-title"><? print $action . ' ' . $item['signal_pair'] . ' @ ' . $item['signal_price'];?></h2>
                <div class="trader-container"  style="padding-bottom:30px;">
                    <span class="trader-stats one-half column" >
                       <div style="text-align:left;">Signal Provider: <a href="http://www.topforexsignal.com/trader/<? print $item['trader_id'];?>"><strong><? print $trader_key[$item['trader_id']];?></strong></a></div>

                       <div style="text-align:left;">Date <strong><? print date('d-m-Y',strtotime($item['signal_date']));?></strong></div>
                       <div style="text-align:left;">Status <strong class="<? print ($item['signal_status']==0)?'red':'green' ?>"><? print ($item['signal_status']==0)?'Expired':'Active';?></strong></div>
                       <div style="text-align:left;">Action <strong ><? print $action?></strong></div>
                       <div style="text-align:left;">Currency Pair: <strong style="color:#0066cc;"><? print $item['signal_pair'];?></strong></div>
                       <div style="text-align:left;">Market Price: <strong><? print $item['signal_price'];?></strong></div>
                       <div style="text-align:left;">Take Profit: <strong><? print $item['signal_tp'];?></strong></div>
                       <div style="text-align:left;">Stop Loss: <strong><? print $item['signal_sl'];?></strong></div>
                        <div style="text-align:left;">Win/Loss: <strong><?

                            switch($item['signal_winloss'])
                            {
                                case '0': print '<b style="color:#c0c0c0;">Pending</b>';break;
                                case '1': print '<b style="color:green;">Win</b>'; break;
                                case '2': print '<b style="color:red;">Loss</b>'; break;
                            }
                            ?></strong></div>
                        <div style="text-align:left;">Result:  <? if(intval($item['signal_result'])!==0) { ?><strong class="<? ($item['signal_result'] <0)?'red':'green'?>"><? print $item['signal_result'];?> pips</strong><? } else { ?> <strong style="color:#dc8800;">Did Not Trigger</strong> <?} ?></div>
                    </span>
                    <span>
                        <img src="img/newsignal.png" style="border-radius:10px;"/>
                    </span>


                </div>
            </div>

            <div class="container">
                <h2>Signal History:</h2>

            </div>
            <div class="container " style="overflow-y:scroll;max-height:250px !important;">
                <div class="signal-table">

                <?
                if(count($signals) > 0)
                {
                    ?>
                    <div class="signal-archive header">
                    <span>Signal #</span>
                    <span>Date</span>
                    <span>Status</span>
                    <span>Action</span>
                    <span>Pair</span>
                    <span>Market Price</span>
                    <span>Take Profit</span>
                    <span>Stop Loss</span>
                    <span>Result</span>
                    <span>Trader</span>
                    <span>Win/Loss</span>


                    </div>
                    <?
                    foreach($signals as $item)

                    {
                        ?>
                        <div class="signal-archive" >
                            <span><? print $item['signal_id'];?></span>

                            <span style="width:70px;"><? print date('m-d-y',strtotime($item['signal_date']));?></span>
                            <span> <? print($item['signal_status']==0)?'Expired':'Active'; ?></span>
                            <?
                            switch($item['signal_action'])
                            {
                                case 1: $action = 'Buy';break;
                                case 2: $action = 'Sell';break;
                                case 3: $action = 'Buy Limit';break;
                                case 4: $action = 'Sell Limit';break;
                                case 5: $action = 'Buy Stop';break;
                                case 6: $actiion = 'Sell Stop'; break;
                            }
                            ?>
                            <span> <? print $action; ?></span>
                                <span> <? print $item['signal_pair'];?></span>
                                    <span> <? print $item['signal_price'];?></span>
                                        <span><? print $item['signal_tp'];?></span>
                            <span> <? print $item['signal_sl'];?></span>
                            <? if(intval($item['signal_result'])!==0) { ?>
                                <span>
                                    <strong class="<? print ($item['signal_result'] <0)?'red':'green'?>">
                                        <? print $item['signal_result'];?>
                                    </strong>
                                    pips
                                </span>
                            <? } else { ?>  <span style="color:#dc8800;line-height:18px;">Did Not Trigger</span><?} ?>
                            <span><? print $trader_key[$item['trader_id']];?></span>
                            <span><?

                                switch($item['signal_winloss'])
                                {
                                    case '0': print '<b style="color:#c0c0c0;">Pending</b>';break;
                                    case '1': print '<b style="color:green;">Win</b>'; break;
                                    case '2': print '<b style="color:red;">Loss</b>'; break;
                                }
                                ?></span>


                        </div>
                        <?
                    }
                } else {
                    print 'No signals available';
                }
                ?>
                </div>
            </div>

            <div class="container signal-upsell">
                <span class="aligncenter">
                    <h2>Tired of not getting signals in time?</h2>
                    <img src="img/traders01.png"/>
                    <h1>Try Forex Auto Copying</h1>
                </span>
                <p>
                        If you are really serious about forex trading you need to subscribe to our free auto trade copying service by visiting <a href="http://www.topforexsignal.com/signup/#1" target="_new">here</a>.
                </p>
                <p>
                        Our signal providers trade a lot more often there and almost 24 hours a day 5 days a week! That's a lot of trading!
                </p>
                <p>
                The trade copying service is
                <ul>
                    <li><h2>100% automated trading</h2></li>
                    <li><h2>100% free (limited time)</h2></li>
                </ul>
                </p>


                <p>For those of you who are new at forex tradinghere's a quick definition of the terms used in the signal:
                <ul>
                    <li>Long = Buy</li>
                    <li>Short = Sell</li>
                    <li>SL = Stop Loss</li>
                    <li>TP = Take Profit</li>
                </ul>


                Remember, that you can be trading a lot more by using our auto trade copying service by visiting <a href="http://www.topforexsignal.com/signup/#1" target="_new">here</a>.

               <div class="aligncenter">
                <button onclick="window.location='http://www.topforexsignal.com/signup/#1';" target="_new" class="button" style="margin-top:20px;padding:20px;font-size:18px;border-radius:10px;">Start Auto Copying Signals</button>
               </div>
                </p>

            </div>


        </div>

    </div>
    <footer>
        <div class="copyright" style="padding-top:0;">
            <div class="container ">
                <p class="aligncenter" style="width:100%;">&copy; 2014 Top Forex Signal | All Rights Reserved </p>
                <ul class="social-media">
                    <li style="padding:10px;text-align:center;"><a href="http://www.facebook.com/topforexsignal" target="_new" class="fa fa-facebook"></a></li>
                    <li style="padding:10px;text-align:center;"><a href="http://www.twitter.com/topforexsignal" target="_new"  class="fa fa-twitter"></a></li>
                </ul>
            </div>
        </div>
    </footer>
</div><!-- Wrapper End -->

</body>
</html>