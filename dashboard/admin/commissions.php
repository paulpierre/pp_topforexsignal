<?php
/**
 * Users
 *
 * @package Membership Manager Pro
 * @author wojoscripts.com
 * @copyright 2010
 * @version $Id: users.php, v2.00 2011-07-10 10:12:05 gewa Exp $
 */
if (!defined("_VALID_PHP"))
    die('Direct access to this location is not allowed.');

$traderCommission = json_decode(file_get_contents('http://'.((MODE=='local')?'api.fxparse':'api.topforexsignal.com') . '/report/commission/admin'),true);

?>

<section class="widget" id="tab-commissions">
    <header>
        <div class="row">
            <h1><i class="icon-time" ></i> Trader Commissions</h1>

        </div>
    </header>
    <div class="content">

        <div class="nextpayment">
            Trader scheduled commission payment is <?
            $day = intval(date("d"));
            $days = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));

            if($day == 15) { //Today is exactly the 14th

                $day_left = ' TODAY';
            } elseif($day > 15) { // Today is after the 14th
                $days_left = 'in ' . ($days - $day + 14) . ' days';

            } elseif($day < 15) { // Today is before the 14th
                $days_left = 'in ' . (14 - $day) . ' days';
            }


            ($days - intval(date("d")));
            print $days_left;
            ?>
        </div>

        <!-- Start Chart -->

        <table>
            <thead>
            <td>Trader ID</td>
            <td>Trader Name</td>
            <td>Pending Payout</td>
            <td>Total Paid Commissions</td>
            <td>Paypal Email</td>
            <td>Notes</td>
            <td>Action</td>




            </thead>

            <tbody>
            <?
            foreach($traderCommission['data']['traderData'] as $item)
            {
                ?>
            <tr id="trader<? print $item['id'];?>">
                <td><? print $item['id']; ?></td>
                <td><a href="http://www.topforexsignal.com/trader/<? print $item['user_name'];?>" target="_new"><? print $item['display_name'];?></a></td>
                <td style="text-align:center;<? if(floatval($item['pending_commissions']) > 0) print 'font-weight:bold;color:#009900;';?>">$<? print $item['pending_commissions'];?></td>
                <td style="text-align:center;"><? print '$' .floatval($item['paid_commissions']);?></td>
                <td style="max-width:150px;text-overflow:ellipsis;overflow:hidden;"><?  if ($item['email']) print $item['email']; else print '<b style="color:red">No email set</b>';?></td>
                <td><input id="note<? print $item['id'];?>" value="" name="notes"/></td>
                <td>
                    <? if(intval($item['pending_commissions']) > 0 ) { ?>
                    <span class="tbicon" style="width:auto;padding:5px 7px 5px 7px;background-color:#84b444;"> <a href="#" data-trader-email="<? print $item['email'];?>" data-trader-commission="<? print $item['pending_commissions'];?>" class="trader-approve" data-trader-name="<? print $item['display_name'];?>" data-trader-id="<? print $item['id']; ?>" class="tooltip Approve " data-title="Pay Commission"><i class="icon-ok"></i> Pay Trader</a> </span>
                    <? } ?>
                </td>


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

<script>
    $(document).ready(function () {

        $('a.trader-approve').click(function(e){
            e.preventDefault();
            var traderName = $(this).attr('data-trader-name');
            var traderCommission = $(this).attr('data-trader-commission');
            var traderID = $(this).attr('data-trader-id');
            var traderNote = $('#note' + traderID).val();
            var traderEmail = $(this).attr('data-trader-email');

            var commissionData = {
                trader_name:traderName,
                trader_commission:traderCommission,
                trader_id: traderID,
                trader_note:traderNote,
                trader_email:traderEmail
            };
            console.log( commissionData);


            if(confirm('Are you sure you want to pay ' + traderName + ' the commission of $' + traderCommission + '?'))
            {


                $.post("http://api.topforexsignal.com/trader/commission/pay_pending_commission", commissionData).done(function( data ) {
                    console.log(data.data);
                    if(data !=='undefined'){
                        alert(data.data.message);

                        if(data.code==1) location.reload();

                        return false;




                    }

                });

            }
        });

    });
</script>


