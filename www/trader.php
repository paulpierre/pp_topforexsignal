<?php
    $q = explode('/',$_SERVER['REQUEST_URI']);
    $trader_id = $q[2];

    //this page requires a trader ID
    if(!isset($trader_id)) exit();//render('home');
    //lets get that trader's information
    $result = file_get_contents('http://' . API_HOST . '/trader/?trader_id=' . $trader_id);
    $response =json_decode($result,true);
    if($response['response'] == 0 || !isset($response['data']))exit(); //an error has occurred
    $trader = $response['data'];

?>


<section id="home" class="content" xmlns="http://www.w3.org/1999/html">
    <div class="main-title" id="trader-name-container">
        <div class="container">
            <h2 class="trader-name"><span class="fa fa-user"></span> <? print $trader['full_name'];?></h2>
            <p class="trader-info-tagline"><? print $trader['tagline'];?></p>
        </div>
    </div>
    <div class="margin30 header-padding"></div>

    <div class="container">
        <div class="welcome">
            <div class="margin10"></div>
            <div class="container trader-info" >
                <div class="container" style="background-color:#fff;">

                <?

                if($trader['id'] == 5)
                {
                ?>
                    <div style="line-height:30px;padding:20px; color:#333;background-color:#d2f5af;border: 3px solid #7da655;border-radius:10px;">
                        <div class="aligncenter">
                            <strong style="padding:5px;">STATUS UPDATE</strong><br/><span class="fa fa-clock-o"></span> 2015-03-05T13:23:57+00:00
                        </div>
                        words cannot express my guilty feeling. i know the news have affected the trades and made a lot lose money. i am truly sorry. the only thing i can do now is to continue to try to win for everyone. thank you.
                    </div>
                <?
                }
                ?>

                    <div class="margin20"></div>

                    <h2 class="border-title">Profile<span></span></h2>


                    <div class="container trader-info-about">
                        <img src="http://<? print DASHBOARD_HOST .  $trader['img'];?>" class="trader-info-img alignleft"/>
                        <p class="trader-info-bio">
                          <? print $trader['bio'];?>
                        </p>
                        <h1 class="aligncenter">
                            <a  href="http://<? print WWW_HOST ?>/signup/#1" class="button large aligncenter" style="font-size:20px;width:195px;">Copy This Signal</a>
                        </h1>

                    </div>

                    <div class="margin20"></div>

                    <h2 class="border-title">Performance<span></span></h2>

                    <table id="trader-table"  class="one-third">
                        <thead>
                        <tr class="trader-info-title">
                            <th  colspan="2">
                                <strong>Statistics</strong>
                            </th>
                        </tr>
                        </thead>

                        <tbody>

                        <tr><td class="trader-info-stat"><strong><span class="fa fa-users"></span>
                           Followers
                        </strong></td><td><span class="trader-info-data">
                            <? print ($trader['followers']>0)?$trader['followers']:'NEW';?></span></td></tr>

                        <tr><td class="trader-info-stat"><strong><span class="fa fa-line-chart"></span>
                            Total growth
                        </strong></td><td><span class="trader-info-data" style="color:#<? print (floatval($trader['total_growth']) > 0)?'60870c':'ff0000'; ?>">
                            <? print number_format($trader['total_growth'],2);?>%</span></td></tr>

                        <tr><td class="trader-info-stat"><strong><span class="fa fa-calendar"></span>
                            Monthly growth
                        </strong></td><td><span class="trader-info-data" style="color:#<? print (floatval($trader['total_growth']) > 0)?'60870c':'ff0000'; ?>">
                            <? print number_format($trader['avg_monthly_growth'],2);?>%</span></td></tr>

                        <tr><td class="trader-info-stat"><strong><span class="fa fa-bar-chart"></span>
                            Max. drawdown
                        </strong></td><td><span class="trader-info-data" style="color:#ff0000;">
                            -<? number_format(print $trader['max_drawdown'],2);?>%</span></td></tr>



                        <tr><td class="trader-info-stat"><strong><span class="fa fa-trophy"></span>
                            Winning trade %
                        </strong></td><td><span class="trader-info-data" style="color:#60870c;">
                            <? print number_format($trader['win_percentage'],2);?>%</span></td></tr>

                        <tr><td class="trader-info-stat"><strong><span class="fa fa-money"></span>
                            Profit
                        </strong></td><td><span class="trader-info-data" style="color:#60870c;">
                            $<? print number_format($trader['profit'],2);?> </span></td></tr>

                        <tr><td class="trader-info-stat"><strong><span class="fa fa-bank"></span>
                            Initial deposit
                        </strong></td><td><span class="trader-info-data">
                            $<? print number_format($trader['initial_balance']) . ' ' . $trader['currency'];?></span></td></tr>


                        <tr><td class="trader-info-stat"><strong><span class="fa fa-bank"></span>
                            Account balance
                        </strong></td><td><span class="trader-info-data">
                            $<? print number_format($trader['master_balance']) . ' ' . $trader['currency'];?></span></td></tr>

                        <tr><td class="trader-info-stat"><strong><span class="fa fa-clock-o"></span>
                            Account age
                        </strong></td><td><span class="trader-info-data">
                            <? print $trader['account_age'];?></span></td></tr>



                        <tr><td class="trader-info-stat"><strong><span class="fa fa-magnet"></span>
                            Account leverage
                        </strong></td><td><span class="trader-info-data">
                            1:<? print $trader['leverage'];?></span></td></tr>



                        <tr><td class="trader-info-stat"><strong><span class="fa fa-cubes"></span>
                            Account type
                        </strong></td><td><span class="trader-info-data">
                            <? print $trader['account_description'];?></span></td></tr>



                        <tr><td class="trader-info-stat"><strong><span class="fa fa-gears"></span>
                            System Type
                        </strong></td><td><span class="trader-info-data">
                            <? print $trader['system_type'];?></span></td></tr>



                        <tr><td class="trader-info-stat"><strong><span class="fa fa-tachometer"></span>
                            Avg. trade length
                        </strong></td><td><span class="trader-info-data">
                            <? print $trader['avg_trade_length'];?></span></td></tr>




                        <tr><td class="trader-info-stat"><strong><span class="fa fa-calendar"></span>
                            Avg. trade/week
                        </strong></td><td><span class="trader-info-data">
                            <? print $trader['avg_trade_per_week'];?></span></td></tr>





                        <tr><td class="trader-info-stat"><strong><span class="fa fa-money"></span>
                            Min. investment
                        </strong></td><td><span class="trader-info-data">
                            $<? print $trader['min_investment'];?></span></td></tr>

                        <? if(strlen($trader['myfxbook']) > 0)  {?>
                        <tr style="display:none;"><td  colspan="2"class="" style="text-align:left;">
                            <a  href="<? print $trader['myfxbook'];?>" target="_new"><img src="/images/myfxbook_badge.png" style="width:140px;height:59px;display:none;"/></a>
                            <a  class="green button small alignright" target="_new" href="<? print $trader['myfxbook'];?>" style="display:none;">MyFxBook</a>

                        </td></tr>
                        <? } ?>




                        </tbody>
                    </table>



                    <div id="trader_chart_lg" class="chart-container two-third">
                        <h2>Daily Growth</h2>

                        <!--<canvas style="display:none;" id="trader_chart_large" width="600" height="250"></canvas>-->
                        <div id="trader_growth_chart_large"></div>
                    </div>
                    <script>
                        growth_large(<? print $trader['id']; ?>,'trader_growth_chart_large','<? print API_HOST;?>');
                    </script>


                    <div class="margin20"></div>

                    <div class="container" id="trade_table">
                        <?
                        $result = file_get_contents('http://' . API_HOST . '/trader/trades_open/?trader_id=' . $trader_id);
                        $response =json_decode($result,true);
                        if(isset($response['data']['open_trades']) && intval($trader['show_open_trades']) ==1) {?>

                        <h2 class="border-title">Open Trades<span></span></h2>
                        <table id="open_trades" class="stripe">
                            <thead>
                                <tr>
                                    <th>Open time</th>
                                    <th>Open Price</th>
                                    <th>Close Price</th>
                                    <th>Lots</th>
                                    <th>Type</th>
                                    <th>Pair</th>
                                    <th>Pips</th>
                                    <th>Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
                                //http://api.topforexsignal.com/trader/trades_open/?trader_id=10000

                                foreach($response['data']['open_trades'] as $trade)
                                {
                                    if(strpos(strtolower($trade['tx_item]']),"xau"))
                                        $decimal = 2;
                                    elseif(strpos(strtolower($trade['tx_item]']),"jpy"))
                                        $decimal = 3;
                                    else $decimal = 4;


                                    switch($trade['tx_type'])
                                    {
                                        case TX_TYPE_BUY:
                                            $tx_type = 'Buy';
                                            break;
                                        case TX_TYPE_SELL:
                                            $tx_type = 'Sell';
                                            break;
                                        default:
                                            $tx_type = "Other";
                                            break;
                                    }
                                    print '<tr>';
                                    print '<td>' . $trade['tx_topen'] .'</td>';
                                    print '<td>' . number_format(round($trade['tx_open_price'],$decimal),$decimal,".","") .'</td>';
                                    print '<td>' . number_format(round($trade['tx_current_price'],$decimal),$decimal,".","") .'</td>';
                                    //number_format(round($trade['tx_close_price'],4),2)

                                    print '<td>' . $trade['tx_size'] .'</td>';
                                    print '<td>' . $tx_type .'</td>';
                                    print '<td>' . $trade['tx_item'] .'</td>';
                                    print '<td>' . $trade['tx_pips'] .'</td>';
                                    print '<td>' . $trade['tx_profit'] .'</td>';
                                    print '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>


                        <div class="margin50"></div>
                        <? } ?>
                        <h2 class="border-title">Closed Trades<span></span></h2>

                        <table id="closed_trades" class="stripe">
                            <thead>
                            <tr>
                                <th>Open time</th>
                                <th>Close time</th>
                                <th>Open Price</th>
                                <th>Close Price</th>
                                <th>Lots</th>
                                <th>Type</th>
                                <th>Pair</th>
                                <th>Pips</th>
                                <th>Profit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                            //http://api.topforexsignal.com/trader/trades_open/?trader_id=10000
                            $result = file_get_contents('http://' . API_HOST . '/trader/trades_closed/?trader_id=' . $trader_id);
                            $response =json_decode($result,true);
                            foreach($response['data']['closed_trades'] as $trade)
                            {

                                if(strpos(strtolower($trade['tx_item]']),"xau"))
                                    $decimal = 2;
                                elseif(strpos(strtolower($trade['tx_item]']),"jpy"))
                                    $decimal = 3;
                                else $decimal = 4;

                                switch($trade['tx_type'])
                                {
                                    case TX_TYPE_BUY:
                                        $tx_type = 'Buy';
                                        break;
                                    case TX_TYPE_SELL:
                                        $tx_type = 'Sell';
                                        break;
                                    default:
                                        $tx_type = "Other";
                                        break;
                                }
                                // (strpos(strtolower($trade['tx_item']),'jpy'))?number_format(round($trade['tx_open_price'],4),4):number_format(round($trade['tx_open_price'],4),2)




                                print '<tr>';
                                print '<td>' . $trade['tx_topen'] .'</td>';
                                print '<td>' . $trade['tx_tclose'] .'</td>';
                                print '<td>' . number_format(round($trade['tx_open_price'],$decimal),$decimal,".","") .'</td>';
                                print '<td>' . number_format(round($trade['tx_close_price'],$decimal),$decimal,".","") .'</td>';
                                print '<td>' . $trade['tx_size'] .'</td>';
                                print '<td>' . $tx_type .'</td>';
                                print '<td>' . $trade['tx_item'] .'</td>';
                                print '<td>' . $trade['tx_pips'] .'</td>';
                                print '<td>' . $trade['tx_profit'] .'</td>';
                                print '</tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="margin50"></div>

                    <?
                        if(strlen($trader['myfxbook']) > 0)
                        {
                    ?>

                        <h1 id="bottomcta"  class="aligncenter" style="padding-top:20px;">
                            <a  href="http://<? print WWW_HOST ?>/signup/#1" class="button large aligncenter" style="font-size:20px;width:195px;">Forex Auto Copy this Signal!</a>
                        </h1>
                    <? } ?>


                <!-- START: Livefyre Embed -->
                <div id="livefyre-comments"></div>
                <script type="text/javascript" src="http://zor.livefyre.com/wjs/v3.0/javascripts/livefyre.js"></script>
                <script type="text/javascript">
                    (function () {
                        var articleId = fyre.conv.load.makeArticleId(null);
                        fyre.conv.load({}, [{
                            el: 'livefyre-comments',
                            network: "livefyre.com",
                            siteId: "372145",
                            articleId: articleId,
                            signed: false,
                            collectionMeta: {
                                articleId: articleId,
                                url: fyre.conv.load.makeCollectionUrl()
                            }
                        }], function() {});
                    }());
                </script>
                <!-- END: Livefyre Embed -->
            </div>
                </div>


        </div>
    </div>
</section>


