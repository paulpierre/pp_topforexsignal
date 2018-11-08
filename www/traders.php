        	<!-- home section Starts here -->
        	<section id="traders" class="content">
                <div class="main-title">
                        <h2>Our Traders</h2>
                </div>
                <div class="margin30"></div>


                <div class="container">
                    <div class="welcome">
                        <div class="margin10"></div>


                        <?php

                        $result = file_get_contents('http://' . API_HOST . '/trader');
                        $traders = json_decode($result,true);
                        //aasort($traders['data']['traders],'user_name');
                        foreach($traders['data']['traders'] as $item)
                        {
                        ?>

                            <div class="trader-container" data-user-name="<? print $item['user_name'];?>">
                                <span class="one-third column  trader-stats">
                                   <div class="trader-name"><h2 class="trader-title"><? print $item['full_name']?><i><br/><? print $item['system_type'];?></i></h2></div>
                                   <div class="growth-30day">Total growth: <strong class="green"><? print number_format($item['total_growth'],2);?>%</strong></div>

                                   <div class="growth-30day">Growth in the last 30 days: <strong class="green"><? print number_format($item['30day_growth'],2);?>%</strong></div>
                                   <div class="maxdd">Max Drawdown: <strong class="red">-<? print $item['max_drawdown'];?>%</strong></div>
                                   <div class="growth-avg">Average monthly growth: <strong class="green"><? print number_format($item['avg_monthly_growth'],2);?>%</strong></div>
                                   <div class="running-weeks">Account age:<strong><? print $item['account_age'];?></strong></div>
                                   <div class="running-weeks"><span class="fa fa-users"></span>  Followers: <strong><? print ($item['followers']>0)?$item['followers']:'NEW';?></strong></div>
                                     <div  class="running-weeks" style="border:none; display:none;">
                                         <? if($item['myfxbook']) { ?><a  class="green button small alignright"  target="_new" href="<? print $item['myfxbook'];?>">MyFxBook</a> <? } ?>
                                     </div>
                                    <div class="margin10"></div>
                                </span>
                                <span class="one-third column trader-action">

                                    <div class="trader-live">

                                    </div>
                                    <div class="trader-details">
                                       <a href="/trader/<? print $item['user_name'];?>" class="button medium">View Details</a>
                                        <br/>
                                        <a href="/signup" class="button medium copysignal">Copy Signal</a>
                                        <!--<a style="display:block;margin-top:10px;" href="<? print $item['myfxbook'];?>" target="_new"><img src="/images/myfxbook_badge.png" style="width:125px;height:53px;"/></a>-->


                                    </div>
                                </span>
                                <span class="one-fourth column trader-chart" style="float:right;">
                                    <div>Performance</div>
                                   <!--<img src="/images/chart.png" width="215" height="125"/>-->
                                    <div class="chart-container-traders">
                                        <canvas id="trader_chart_large-<? print $item['id']?>"  class="alignright" width="300" height="200"></canvas>
                                    </div>
                                    <script>
                                        growth_small(<? print $item['id']; ?>,'trader_chart_large-<? print $item['id']; ?>','<? print API_HOST;?>','small');
                                    </script>
                                </span>
                            </div>

                        <?
                        }
                        ?>


                    </div>
                </div>
            </section>
