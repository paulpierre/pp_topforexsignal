<?php
  /**
   * Account Profile
   *
   * @package Membership Manager Pro
   * @author wojoscripts.com
   * @copyright 2010
   * @version $Id: account.php, v2.00 2011-07-10 10:12:05 gewa Exp $
   */


  define("_VALID_PHP", true);
  require_once("init.php");
  
  if (!$user->logged_in)
      redirect_to("index.php");
	  
  $row = $user->getUserData();

$userLevel = isset($row->userlevel)?$row->userlevel:0;
if($userLevel==9) {redirect_to('http://' . DASHBOARD_HOST .'/admin/index.php');}

   //error_log(print_r($row,true));
  $mrow = $user->getUserMembership();
  $traderEmail = $row->email;
  $gatelist = $member->getGateways(true);
  $listpackrow = $member->getMembershipListFrontEnd();
  $traderID = $row->trader_id;
  $traderStatus =  ($user->getUserStatus($traderID))?$user->getUserStatus($traderID):0;
error_log(PHP_EOL.'trader status:' . $traderStatus.PHP_EOL);


/** ====================
 *  Retrieve Commissions
 *  ====================
 */

$traderCommission = json_decode(file_get_contents('http://'.((MODE=='local')?'api.fxparse':'api.topforexsignal.com') . '/report/commission/' . $traderID),true);



?>
<?php include("header.php");?>
<script type="text/javascript" src="../assets/js/flot/jquery.flot.min.js"></script>
<script type="text/javascript" src="../assets/js/flot/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="../assets/js/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="../assets/js/flot/jquery.flot.stack.js"></script>
<script type="text/javascript" src="../assets/js/flot/jquery.flot.axislabels.js"></script>


<script type="text/javascript" src="../assets/js/flot/excanvas.min.js"></script>

<div id="dashboard-tabs">
    <ul>
        <li><a href="#tab-statistics">Statistics</a></li>
        <li><a href="#tab-commissions">Commissions</a></li>
        <li><a href="#tab-settings">Settings</a></li>
    </ul>
    <div id="tab-statistics">

        <? if($traderStatus>1) { ?>
        <div class="row grid_24">
            <div class="col grid_6">
                <div class="pagetip stats"><i class="icon-group" style="color:#999;"></i>
                    <div class="pull-right"> Total Followers <br>
                        <b class="pull-right" id="total-followers">--</b> <br>
                    </div>
                </div>
            </div>

            <div class="col grid_6">
                <div class="pagetip stats"><i class="icon-arrow-up" style="color:#999;"></i>
                    <div class="pull-right">Winning/Total Trades <br>
                        <b class="pull-right" id="total-winning-trades">----</b> <br>
                    </div>
                </div>
            </div>
            <div class="col grid_6">
                <div class="pagetip stats"><i class="icon-bar-chart" style="color:#999;"></i>
                    <div class="pull-right"> Profit Volume<br>
                        <b class="pull-right" id="profit-volume">---.--</b> <br>
                    </div>
                </div>
            </div>
            <div class="col grid_6">
                <div class="pagetip stats"><i class="icon-money" style="color:#999;"></i>
                    <div class="pull-right"> Commissions<br>
                        <b class="pull-right" id="total-commissions">$---.--</b> <br>
                    </div>
                </div>
            </div>
        </div>
        <section class="widget">
            <header>
                <div class="row">
                    <h1 id="chartLabel"><i class="icon-money" ></i> Commissions</h1>
                    <aside>
                        <ul class="settingsnav">
                            <li> <a href="#" data-hint="Last 30 days" id="rangeHint" class="minilist1 hint--left hint--add hint--always hint--rounded"><span class="icon-reorder"></span></a>
                                <div id="settingslist2">
                                    <ul class="sub" id="date-range" data-type="last30d">
                                        <li><i class="icon-calendar pull-left"></i> <a href="#" data-type="currentMonth" class="btn-date">This month</a></li>
                                        <li><i class="icon-calendar pull-left"></i> <a href="#" data-type="lastMonth" class="btn-date">Last month</a></li>
                                        <li><i class="icon-calendar pull-left"></i> <a href="#" data-type="thisYear" class="btn-date">This year</a></li>

                                        <li><i class="icon-calendar pull-left"></i> <a href="#" data-type="last7d" class="btn-date">Last 7 days</a></li>
                                        <li><i class="icon-calendar pull-left"></i> <a href="#" data-type="last14d" class="btn-date">Last 14 days</a></li>
                                        <li><i class="icon-calendar pull-left"></i> <a href="#" data-type="last30d" class="btn-date">Last 30 days</a></li>
                                        <li style="display:none;"><i class="icon-calendar pull-left"></i> <a href="#" data-type="last6m" class="btn-date">Last 6 Months</a></li>
                                        <li style="display:none;"><i class="icon-calendar pull-left"></i> <a href="#" data-type="last12m" class="btn-date">Last Year</a></li>

                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </aside>
                    <aside style="float:left;position:relative;left:15px;top:3px;">
                        <ul class="settingsnav">
                            <li> <a href="#" style="display:inline-block;" class="minilist2"><span class="icon-angle-down"></span></a>
                                <div id="settingslist3">
                                    <ul class="sub" id="chart-type" data-type="commission">
                                        <li><i class="icon-money pull-left"></i> <a href="#" class="btn-render-chart" data-type="commissions">Commissions</a></li>
                                        <li><i class="icon-bar-chart pull-left"></i> <a href="#" class="btn-render-chart" data-type="volume">Trade Volume</a></li>
                                        <li><i class="icon-arrow-up pull-left"></i> <a href="#" class="btn-render-chart" data-type="profit">Profitable Trades</a></li>
                                        <!--  <li><i class="icon-exchange pull-left"></i> <a href="#" class="btn-render-chart" data-type="transactions">Total Trades</a></li>-->


                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </aside>
                </div>

            </header>
            <div class="content2">
                <!-- Start Chart -->
                <div class="box">
                    <div id="chart-daily-trade-volume" class="chart" style="height:400px;width:100%;"></div>
                    <div id="chart-daily-profitable-trades" class="chart" style="height:400px;width:100%;"></div>
                    <div id="chart-daily-total-trades" class="chart" style="height:400px;width:100%;"></div>
                    <div id="chart-daily-transactions" class="chart" style="height:400px;width:100%;"></div>
                    <div id="chart-daily-commissions" class="chart" style="height:400px;width:100%;"></div>

                </div>
                <!-- End Chart /-->
                <hr />

            </div>
        </section>




        <? } elseif($traderStatus == 1) { ?>
        <br>
        <div class="bgorange"><p><span class="icon-info-sign"></span><i class="close icon-remove-circle"></i>Your account details are currently pending approval</p></div>

        <form class="xform" class="admin_form" method="post">
            <div class="row">
                <section class="col col-6">
                    <h1 style="color:#555;">
                        This is where you can see your account activity. It looks like
                        your details were submitted to Top Forex Signal. Please allow
                        48 hours for the approval process.
                    </h1>
                </section>

                <section class="col col-6 pull-right">
                    <img src="assets/images/img-pending.png"/>
                </section>
            </div>

        </form>
        <? } elseif($traderStatus == 0) { ?>
        <br>
        <div class="bgred"><p><span class="icon-info-sign"></span><i class="close icon-remove-circle"></i>Your account details have not been submitted yet. Please complete your profile in the "Settings" section.</p></div>

        <form class="xform" class="admin_form" method="post">
            <div class="row">
                <section class="col col-6">
                    <h1 style="color:#555;">
                        To complete registration, please tap on the "Settings" tab above. Once you've submitted
                        your MT4 investor login and password, it will take 48 hours to process your account.
                        We will send you an email once complete.
                    </h1>
                </section>

                <section class="col col-6 pull-right">
                    <img src="assets/images/img-pending.png"/>
                </section>
            </div>

        </form>
        <? }   ?>





    </div>



    <div id="tab-commissions">

        <? if($traderStatus>1) { ?>
        <br/>
        <div class="row grid_24">
            <div class="col grid_8">
                <div class="pagetip stats"><i class="icon-money" style="color:#999;"></i>
                    <div class="pull-right">Pending Commissions <br>
                        <b class="pull-right" id="paid-commissions">$<? print $traderCommission['data']['traderData']['commissions_pending']?></b> <br>
                    </div>
                </div>
            </div>


            <div class="col grid_8">
                <div class="pagetip stats"><i class="icon-money" style="color:#999;"></i>
                    <div class="pull-right">Approved Commissions <br>
                        <b class="pull-right" id="unpaid-commissions"><? if(floatval($traderCommission['data']['traderData']['commissions_approved'])>0) print '$'. $traderCommission['data']['traderData']['commissions_approved']; else print '<span style="color:#c0c0c0;">N/A</span>';?></b> <br>
                    </div>
                </div>
            </div>

            <div class="col grid_8">
                <div class="pagetip stats"><i class="icon-time" style="color:#999;"></i>
                    <div class="pull-right">Paid Commissions <br>
                        <b class="pull-right" id="pid-commissions"><? if(floatval($traderCommission['data']['traderData']['commissions_paid'])>0) print '$' . $traderCommission['data']['traderData']['commissions_paid']; else print '<span style="color:#c0c0c0;">N/A</span>'; ?></b> <br>
                    </div>
                </div>
            </div>

        </div>
        <section class="widget">
            <header>
                <div class="row">
                    <h1><i class="icon-time" ></i> Commission History</h1>
                    <span style="float:right;color:yellow;"><i class="icon-info"> Commissions are paid the 15th of every month.</i></span>

                </div>

            </header>
            <div class="content">

                <? $fields = explode('::',$row->custom_fields);
                if($fields[4]) {?>
                <div class="nextpayment">
                    Your next scheduled commission payment is <?
                    $day = intval(date("d"));
                    $days = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));

                    if($day == 15) { //Today is exactly the 14th

                        $day_left = ' being paid today';
                    } elseif($day > 15) { // Today is after the 14th
                        $days_left = 'in ' . ($days - $day + 14) . ' days';

                    } elseif($day < 15) { // Today is before the 14th
                        $days_left = 'in ' . (14 - $day) . ' days';
                    }


                    ($days - intval(date("d")));
                    print $days_left . ' to ' . $fields[4];
                    ?>
                </div>
                <? } else { ?>
                <div class="nextpayment" style="background-color:#f4bcbc;color:#bc1818;">
                    You must save your Paypal email address in order to get paid. Please go into <a href="/?#tab-settings">Settings</a> to complete.

                </div>
                <? } ?>

                <!-- Start Chart -->

                    <table>
                        <thead>
                            <td>Date</td>
                            <td>Description</td>
                            <td>Payout to</td>
                            <td>Type</td>
                            <td>Status</td>
                            <td>Amount</td>

                        </thead>

                        <tbody>

                            <?
                                foreach($traderCommission['data']['traderData']['commissions_history'] as $item)
                                {
                                    ?>
                                <tr>
                                    <td><? print $item['date'];?></td>
                                    <td><? print $item['payout_notes'];?></td>
                                    <td><? print $item['payout_email'];?></td>
                                    <td class="payment"><? if($item['payout_method'] == 1) print '<img src="assets/images/paypal.png"/>'; else print 'Unknown';?></td>
                                    <?
                                        switch($item['payout_status'])
                                        {
                                            default:
                                            case 0:
                                                $status='pending';
                                                break;
                                            case 1:
                                                $status='paid';
                                                break;
                                            case 2:
                                                $status='error';
                                                break;
                                        }
                                        ?>
                                    <td class="status <? print $status;?>"><? print strtoupper($status);?></td>
                                    <td  class="payout">$<? print $item['payout_amount'];?></td>

                                </tr>
                                    <?

                                }

                            ?>




                        </tbody>

                    </table>
                <!-- End Chart /-->
                <hr />

            </div>
        </section>




        <? } elseif($traderStatus == 1) { ?>
        <br>
        <div class="bgorange"><p><span class="icon-info-sign"></span><i class="close icon-remove-circle"></i>Your account details are currently pending approval</p></div>

        <form class="xform" class="admin_form" method="post">
            <div class="row">
                <section class="col col-6">
                    <h1 style="color:#555;">
                        This is where you can see your account activity. It looks like
                        your details were submitted to Top Forex Signal. Please allow
                        48 hours for the approval process.
                    </h1>
                </section>

                <section class="col col-6 pull-right">
                    <img src="assets/images/img-pending.png"/>
                </section>
            </div>

        </form>
        <? } elseif($traderStatus == 0) { ?>
        <br>
        <div class="bgred"><p><span class="icon-info-sign"></span><i class="close icon-remove-circle"></i>Your account details have not been submitted yet. Please complete your profile in the "Settings" section.</p></div>

        <form class="xform" class="admin_form" method="post">
            <div class="row">
                <section class="col col-6">
                    <h1 style="color:#555;">
                        To complete registration, please tap on the "Settings" tab above. Once you've submitted
                        your MT4 investor login and password, it will take 48 hours to process your account.
                        We will send you an email once complete.
                    </h1>
                </section>

                <section class="col col-6 pull-right">
                    <img src="assets/images/img-pending.png"/>
                </section>
            </div>

        </form>
        <? }   ?>





    </div>



    <div id="tab-settings">

        <p class="bluetip"><i class="icon-lightbulb icon-3x pull-left"></i> Here you can update your user info<br>
            Fields marked <i class="icon-append icon-asterisk"></i> are required.</p>
        <form class="xform" id="admin_form" method="post">
            <header>Manage Your Account<span>User Account Edit <i class="icon-double-angle-right"></i> <?php echo $row->username;?></span></header>
            <div class="row">
                <section class="col col-6">
                    <label class="input state-disabled"> <i class="icon-prepend icon-user"></i> <i class="icon-append icon-asterisk"></i>
                        <input type="text" disabled="disabled" name="username" readonly value="<?php echo $row->username;?>" placeholder="Username">
                    </label>
                    <div class="note note-error">Username</div>
                </section>
                <section class="col col-6">
                    <label class="input"> <i class="icon-prepend icon-lock"></i> <i class="icon-append icon-asterisk"></i>
                        <input type="password" name="password" placeholder="********">
                    </label>
                    <div class="note note-info">Leave it empty unless changing the password</div>
                </section>
            </div>
            <div class="row">
                <section class="col col-4">
                    <label class="input"> <i class="icon-prepend icon-envelope-alt"></i> <i class="icon-append icon-asterisk"></i>
                        <input type="text" name="email" value="<?php echo $row->email;?>" placeholder="Email">
                    </label>
                    <div class="note note-error">Email</div>
                </section>
                <section class="col col-4">
                    <label class="input"> <i class="icon-prepend icon-user"></i>
                        <input type="text" name="fname" value="<?php echo $row->fname;?>" placeholder="First Name">
                    </label>
                    <div class="note note-error">First Name</div>
                </section>
                <section class="col col-4">
                    <label class="input"> <i class="icon-prepend icon-user"></i>
                        <input type="text" name="lname" value="<?php echo $row->lname;?>" placeholder="Last Name">
                    </label>
                    <div class="note note-error">Last Name</div>
                </section>
            </div>
            <div class="row">
                <section class="col col-4">
                    <label class="radio">
                        <input type="radio" name="newsletter" value="1" <?php getChecked($row->newsletter, 1); ?>>
                        <i></i>Yes</label>
                    <label class="radio">
                        <input type="radio" name="newsletter" value="0" <?php getChecked($row->newsletter, 0); ?>>
                        <i></i>No</label>
                    <div class="note">Newsletter Subscriber</div>
                </section>
                <section class="col col-5">
                    <label class="input">
                        <input name="avatar" type="file" class="fileinput"/>
                    </label>
                    <div class="note">User Avatar</div>
                </section>
                <section class="col col-3"> <img src="thumbmaker.php?src=<?php echo UPLOADURL;?><?php echo ($row->avatar) ? $row->avatar : "blank.png";?>&amp;w=<?php echo $core->thumb_w;?>&amp;h=<?php echo $core->thumb_h;?>&amp;s=1&amp;a=t1" alt="" title="" class="avatar" /> </section>
            </div>
            <?php echo $core->rendertCustomFields('profile', $row->custom_fields);?>
            <div class="row">
                <section class="col col-6">
                    <label class="input state-disabled"> <i class="icon-prepend icon-calendar"></i>
                        <input type="text" name="created" disabled="disabled" readonly value="<?php echo $row->cdate;?>" placeholder="Email">
                    </label>
                    <div class="note">Registration Date:</div>
                </section>
                <section class="col col-6">
                    <label class="input state-disabled"> <i class="icon-prepend icon-calendar"></i>
                        <input type="text" name="lastlogin" disabled="disabled" readonly value="<?php echo $row->ldate;?>" placeholder="First Name">
                    </label>
                    <div class="note">Last Login</div>
                </section>
            </div>
            <footer>
                <button class="button" name="doupdate" type="submit">Update Profile<span><i class="icon-ok"></i></span></button>
            </footer>
        </form>
        <!--
        <table class="myTable">
            <thead>
            <tr>
                <th><strong>Current Membership</strong></th>
                <th><strong>Valid Until</strong></th>
            </tr>
            </thead>
            <?php if($row->membership_id == 0) :?>
            <tr>
                <td>No Membership</td>
                <td>--/--</td>
            </tr>
            <?php else:?>
            <tr>
                <td><strong> <?php echo $mrow->title;?> </strong></td>
                <td><strong> <?php echo $mrow->expiry;?> </strong></td>
            </tr>
            <?php endif;?>
        </table>-->
        <?php if($listpackrow):?>
<section class="widget">
  <header>
      <div class="row">
          <h1><i class="icon-reorder"></i> Select Your Membership</h1>
      </div>
  </header>
  <div class="content2">
    <ul id="plans">
        <?php foreach ($listpackrow as $prow):?>
        <li class="plan">
            <h2><?php echo $prow->title;?></h2>
            <p class="price"><?php echo $core->formatMoney($prow->price);?> / <span><?php echo $prow->days . ' ' .$member->getPeriod($prow->period);?></span></p>
            <p class="recurring">Recurring <?php echo ($prow->recurring) ? 'Yes' : 'No';?></p>
            <p class="desc"><?php echo $prow->description;?></p>
            <?php if($prow->price == 0):?>
            <p class="pbutton"><a class="add-cart" data-id="item_<?php echo $prow->id.':FREE';?>">Activate Membership</a></p>
            <?php else:?>
            <?php if($gatelist):?>
                <?php foreach($gatelist as $grow):?>
                    <?php if ($grow->active):?>
                        <?php if ($prow->recurring and !$grow->is_recurring):?>
                            <?php break;?>
                            <?php else:?>
                            <p class="pbutton"><a class="add-cart" data-id="item_<?php echo $prow->id.':'.$grow->id;?>"><i class="icon-dollar pull-left"></i> <?php echo $grow->displayname;?> </a></p>
                            <?php endif;?>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <?php endif;?>
        </li>
        <?php endforeach;?>
    </ul>
        <div id="show-result"> </div>
        <?php endif;?>
    </div>
    </section>
        </div>

</div>


<?php echo Core::doForm("processUser","ajax/controller.php");?>
<script type="text/javascript">
// <![CDATA[

var fxTickSize = [1,'day'];
var fxDataSize = 0;
function getChart(range) {

    console.log('getChart:' + range + ' url:' + 'http://<? print API_HOST ?>/report/dashboard/<? print $traderID ?>/');

    $.ajax({
        type: 'GET',
        url: 'http://<? print API_HOST ?>/report/dashboard/<? print $traderID ?>/',
        data : {
            'range' : range
        },
        dataType: 'json',
        async: false,
        success: function (json) {

            console.log(json);
            var daily_trade_volume = {
                shadowSize: 0,
                lines: {
                    show: true,
                    fill: true,
                    lineWidth: 3
                },
                series:{lines:{show:true},points:{show:true,radius:7}},

                grid: {
                    mouseActiveRadius:50,
                    borderColor:'#fff',
                    autoHighlight:true,
                    borderWidth:10,
                    minBorderMargin:20,
                    backgroundColor: { colors: ["#eee", "#fff","#fff","#fff","#fff"] },
                    hoverable:true
                },
                xaxis: {
                    mode:'time',
                    timeformat: "%m/%d",
                    tickSize:fxTickSize

                    //autoscaleMargin:.1
                },yaxis:{label:'Trade Volume'}
            };

            var daily_profitable_trades =
            {

                series: {
                    stack: true,
                    lines: {
                        show: true,
                        fill: true,
                        steps: false
                    },
                    bars: {
                        show: false
                    }},
                grid: {
                    mouseActiveRadius:50,
                    borderColor:'#fff',
                    autoHighlight:true,
                    borderWidth:10,
                    minBorderMargin:20,
                    backgroundColor: { colors: ["#eee", "#fff","#fff","#fff","#fff"] },
                    hoverable:true
                },

                xaxis: {
                    mode:'time',
                    tickSize:fxTickSize,
                    tickLength: 10,
                    timeformat: "%m/%d"
                },yaxis:{
                label:'Profitable Trades',
                axisLabelUseCanvas: true
                }


            };
/*
            var daily_total_trades= {
                shadowSize: 0,
                lines: {
                    show: true,
                    fill: true,
                    lineWidth: 3
                },
                series:{lines:{show:true},points:{show:true,radius:7}},

                grid: {
                    mouseActiveRadius:50,
                    borderColor:'#fff',
                    autoHighlight:true,
                    borderWidth:10,
                    minBorderMargin:20,
                    backgroundColor: { colors: ["#eee", "#fff","#fff","#fff","#fff"] },
                    hoverable:true
                },
                xaxis: {

                    mode:'time',
                    tickSize:fxTickSize,
                    timeformat: "%m/%d"
                    //autoscaleMargin:.1
                },yaxis:{label:'Transactions'}
            };
*/
            var daily_commissions = {

                shadowSize: 0,
                lines: {
                    show: true,
                    fill: true,
                    lineWidth: 3
                },
                series:{lines:{show:true},points:{show:true,radius:7}},

                grid: {
                    mouseActiveRadius:50,
                    borderColor:'#fff',
                    autoHighlight:true,
                    borderWidth:10,
                    minBorderMargin:20,
                    backgroundColor: { colors: ["#eee", "#fff","#fff","#fff","#fff"] },
                    hoverable:true,
                    labelMargin:30

                },
                xaxis: {
                    mode:'time',
                    tickSize:fxTickSize,
                    timeformat: "%m/%d"

                   // autoscaleMargin:.001
                },yaxis :{
                tickFormatter:function(val,axis){return "$" + Math.round(val * 100)/100},
                label:'Commission'
                }
            };


            var chartData = json.data.traderData;

            fxDataSize = chartData.daily_trade_volume.order.data.length;

            console.log('data size: ' + fxDataSize  +  ' tickSize:' + fxTickSize);


            $.plot($('#chart-daily-trade-volume'),[{label:'Trade Volume',data:chartData.daily_trade_volume.order.data,color:'#93d128'}], daily_trade_volume);
            $.plot($('#chart-daily-profitable-trades'), [{label:'Profitable Trades',data:chartData.daily_profitable_trades.order.data,color:'#0077FF'},{label:'Unprofitable Trades',data:chartData.daily_unprofitable_trades.order.data,color:'#DE000F'}], daily_profitable_trades);
            $.plot($('#chart-daily-commissions'), [{label:'Commission',data:chartData.daily_commissions.order.data,color:'#93d128'}], daily_commissions);



            $('#total-followers').text(chartData.total_followers);
            //$('#total-trade-volume').text(chartData.total_trade_volume);
            $('#total-winning-trades').html('<span style="color:#7cbf1c">' + chartData.total_profitable_trades + '</span>/' + chartData.total_trade_count);
            $('#total-commissions').text('$'+ parseFloat(chartData.total_commissions));
            $('#profit-volume').text(parseFloat(chartData.total_profitable_volume));
            $('.chart').hide();

            var chartType = $('#chart-type').attr('data-type');



            switch(chartType)
            {
                default:
                case 'commissions':
                    $('#chart-daily-commissions').show();

                    break;

                case 'volume':
                    $('#chart-daily-trade-volume').show();
                    break;

                case 'profit':
                    $('#chart-daily-profitable-trades').show();
                    break;
/*
                case 'transactions':
                    $('#chart-daily-total-trades').show();
                    break;
*/
            }




        }
    });
}






$(document).ready(function () {

    var status = <? print is_numeric($traderStatus)?$traderStatus:0; ?>;

    var x = <? print is_numeric($userLevel)?$userLevel:0; ?>;
    if(x < 9)
    {
        $('input[placeholder="4X Solutions Account ID"]').parent().parent().hide();
        $('input[placeholder="MT4 Investor Password"]').attr("type","password");
    }

    $('a.btn-render-chart').click(function(e){
       var chartType = $(this).attr('data-type');
        e.preventDefault();
        $("#settingslist2,#settingslist3").hide();
        $('#chart-type').attr('data-type',chartType);
        switch(chartType)
        {
            default:
            case 'commissions':
                $('#chartLabel').html(' <i class="icon-money" ></i> Commissions');
                $('.chart').fadeOut('fast',function(){$('#chart-daily-commissions').fadeIn('fast')});
                break;

            case 'volume':
                $('#chartLabel').html(' <i class="icon-bar-chart" ></i> Trade Volume');
                $('.chart').fadeOut('fast',function(){$('#chart-daily-trade-volume').fadeIn('fast')});
                break;

            case 'profit':
                $('#chartLabel').html(' <i class="icon-arrow-up" ></i> Profitable Trades');
                $('.chart').fadeOut('fast',function(){$('#chart-daily-profitable-trades').fadeIn('fast')});
                break;

            case 'transactions':
                $('#chartLabel').html(' <i class="icon-exchange" ></i> Transactions');
                $('.chart').fadeOut('fast',function(){$('#chart-daily-total-trades').fadeIn('fast')});
                break;
        }
    });


    $("#plans").gridalicious({
        selector: 'li',
        width: 200,
        animate: true
    });
    $("body").on("click", "a.add-cart", function () {
        $.ajax({
            type: "POST",
            url: "ajax/controller.php",
            data: 'addtocart=' + $(this).attr('data-id').replace('item_', ''),
            success: function (msg) {
                $("#show-result").html(msg);
            }
        });
        return false;
    });
    $( "#dashboard-tabs" ).tabs();



    if(status>1) getChart('last30d');

    $('a.btn-date').click(function(e){
        e.preventDefault();
        $("#settingslist2,#settingslist3").hide();

        var range = $(this).attr('data-type');
        $('#date-range').attr('data-type',range);


        if(fxDataSize < 30) var i=1; else var i = 7;

        switch(range)
        {
            case 'currentMonth':
                $('a#rangeHint').attr('data-hint','Current month');
                fxTickSize = [3,'day'];
                break;
            case 'lastMonth':
                $('a#rangeHint').attr('data-hint','Last month');
                fxTickSize = [3,'day'];
                break;
            case 'thisYear':
                $('a#rangeHint').attr('data-hint','Current year');
                fxTickSize = [i,'month'];
                break;

            case 'last30d':
                $('a#rangeHint').attr('data-hint','Last 30 days');
                fxTickSize = [3,'day'];

                break;
            case 'last7d':
                $('a#rangeHint').attr('data-hint','Last 7 days');
                fxTickSize = [i,'day'];
                break;


            case 'last14d':
                $('a#rangeHint').attr('data-hint','Last 14 days');
                fxTickSize = [i,'day'];

                break;
            case 'last6m':
                $('a#rangeHint').attr('data-hint','Last 6 months');
                if(fxDataSize < 30) var i=7;

                fxTickSize = [i,'day'];
                break;
            case 'last12m':
                $('a#rangeHint').attr('data-hint','Last 12 months');
                fxTickSize = [i,'month'];
                break;
        }

        getChart(range);
    });


    $("<div id='tooltip'></div>").css({
        position: "absolute",
        display: "none",
        border: "1px solid #fdd",
        padding: "10px",
        "background-color": "#000"
    }).appendTo("body");

    $(".chart").bind("plothover", function (event, pos, item) {
            console.log('hover item:' + item);
            if (item) {
                var d = new Date(item.datapoint[0]);
                var x = (d.getUTCMonth()+1) + '/' + d.getUTCDate(),
                        y = item.datapoint[1].toFixed(2);

                var prefix = '';
                if((item.series.yaxis.ticks[0].label.indexOf('$') >-1))
                {
                    prefix = '$';
                }
                $("#tooltip").html(item.series.label + " of " + prefix + y + " on " + x)
                        .css({top: item.pageY-50, left: item.pageX-88})
                        .fadeIn(200);
            } else {
                $("#tooltip").hide();
            }
    });

    $(".chart").bind("plotclick", function (event, pos, item) {
        if (item) {
            $("#clickdata").text(" - click point " + item.dataIndex + " in " + item.series.label);
            plot.highlight(item.series, item.datapoint);
        }
    });



});
// ]]>
</script>
<?php include("footer.php");?>