<?php
/**
 * Configuration
 *
 * @package Membership Manager Pro
 * @author wojoscripts.com
 * @copyright 2010
 * @version $Id: config.php, v2.00 2011-07-10 10:12:05 gewa Exp $
 */

$currency_pairs = array(
    'AUD/CAD',
    'AUD/CHF',
    'AUD/JPY',
    'AUD/NZD',
    'AUD/USD',
    'CAD/CHF',
    'CAD/JPY',
    'CHF/JPY',
    'CHF/NOK',
    'CHF/SEK',
    'EUR/AUD',
    'EUR/CAD',
    'EUR/CHF',
    'EUR/CZK',
    'EUR/DKK',
    'EUR/GBP',
    'EUR/HUF',
    'EUR/JPY',
    'EUR/NOK',
    'EUR/NZD',
    'EUR/PLN',
    'EUR/RON',
    'EUR/SEK',
    'EUR/TRY',
    'EUR/USD',
    'GBP/AUD',
    'GBP/CAD',
    'GBP/CHF',
    'GBP/JPY',
    'GBP/NZD',
    'GBP/SEK',
    'GBP/USD',
    'HKD/JPY',
    'NOK/JPY',
    'NZD/CAD',
    'NZD/CHF',
    'NZD/JPY',
    'NZD/USD',
    'SEK/JPY',
    'SGD/JPY',
    'TRY/JPY',
    'UKOIL',
    'USD/CAD',
    'USD/CHF',
    'USD/CNH',
    'USD/CZK',
    'USD/DKK',
    'USD/HKD',
    'USD/HUF',
    'USD/ILS',
    'USD/JPY',
    'USD/MXN',
    'USD/NOK',
    'USD/PLN',
    'USD/RON',
    'USD/RUB',
    'USD/SEK',
    'USD/SGD',
    'USD/TRY',
    'USD/ZAR',
    'USDCHF',
    'USOIL',
    'XAG/EUR',
    'XAG/USD',
    'XAU/EUR',
    'XAU/USD',
    'ZAR/JPY'
);

if (!defined("_VALID_PHP"))
    die('Direct access to this location is not allowed.');

$html = json_decode(file_get_contents('http://'.((MODE=='local')?'api.fxparse':'api.topforexsignal.com') . '/signal'),true);
//error_log($html);
?>
<style type="text/css">

    .input-error {
        border:2px solid red !important;
    }
    .signal-id  span {
        font-weight:bold;color:#2184be;
    }

    .signal-id button {
        margin:0;
    }

</style>

<form class="xform" id="admin_form" method="post">
    <header>Send a signal</header>
    <div class="row">
        <section class="col col-3">
            <label class="input">Signal Status</label>
            <select name="signal_status" id="signal-status" >
                <option value="2" selected="selected">Active</option>
                <option value="1" >Expired</option>
            </select>
        </section>
        <section class="col col-3">
            <label class="input">Action</label>
            <select name="signal_action" id="signal-action">
                <option value="1" selected="selected">Buy</option>
                <option value="2">Sell</option>
                <option value="3" >Buy Limit</option>
                <option value="4" >Sell Limit</option>
                <option value="5" >Buy Stop</option>
                <option value="6" >Sell Stop</option>
            </select>
        </section>
        <section class="col col-3">
            <label class="input">Currency Pair</label>

                <select id="signal-pair" name="signal_pair">
                    <?
                    foreach($currency_pairs as $cp)
                    {
                        ?>
                        <option value="<? print $cp;?>"><? print $cp;?></option>
                        <?
                    }
                    ?>
                 </select>

            </label>
        </section>
        <section class="col col-3">
            <label class="input">Market Price</label>

            <label class="input">
                <input type="text" id="signal-price" name="signal_price" value="" placeholder="Amount to trade">
            </label>
        </section>
    </div>
    <div class="row">
        <section class="col col-3">
            <label class="input">Take Profit</label>

            <label class="input">
                <input type="text" id="signal-tp" name="signal_tp" value="" placeholder="Take Profit">
            </label>
        </section>

        <section class="col col-3">
            <label class="input">Stop Loss</label>

            <label class="input">
                <input type="text" id="signal-sl" name="signal_sl" value="" placeholder="Stop Loss">
            </label>
        </section>
        <section class="col col-3">
            <label class="input">Result</label>

            <label class="input">
                <input type="text" id="signal-result" name="signal_sl" value="" placeholder="Pips result">
            </label>
        </section>

        <section class="col col-3">
            <label class="input">Signal Provider</label>
            <select name="signal-trader" id="signal-trader">
               <?
                $trader_list = $html['data']['traders'];
                foreach($trader_list as $item)
                {
                   ?>
                    <option value="<? print $item['trader_id']; ?>"><? print $item['trader_full_name']; ?></option>
                    <?
                }
                ?>
            </select>
        </section>
        <section class="col col-2">
            <label class="input">Win/Loss</label>
            <select id="signal-winloss" name="signal-winloss">
                <option value="0" selected="selected">Pending</option>
                <option value="1">Win</option>
                <option value="2">Loss</option>

            </select>
        </section>
    </div>
    <hr />

    <div class="row">
        <section class="col col-15">
            <button class="button" id="add-signal-button"> Send signal<span><i class="icon-ok"></i></span></button>
        </section>
    </div>

    <hr style="margin-top:30px;margin-bottom:30px;"/>

    <header>Signal Archive</header>


<?


    $signals = $html['data']['signals'];
    if(count($signals) > 0)
    {
    foreach($signals as $item)
    {
?>
        <div class="row signal-item" id="<? print $item['signal_id'];?>">
            <section class="col col-2 signal-id">
           <span style="float:left;">ID:<? print $item['signal_id'];?> <br/>Date: <? print date('m-d-y',strtotime($item['signal_date']));?><span>
           <button class="button signal-update" style="margin:0;float:left;">Update</button>
            </section>
            <section class="col col-2">
                <label class="input">Signal Status</label>
                <select name="signal-status" >
                    <option value="2" <? if($item['signal_status'] == 2) print 'selected="selected"'; ?>>Active</option>
                    <option value="0" <? if($item['signal_status'] == 0) print 'selected="selected"'; ?>>Expired</option>

                </select>

                <label class="input">Action</label>
                <select name="signal-action">
                    <option value="1" <? if($item['signal_action'] == 1) print 'selected="selected"'; ?>>Buy</option>
                    <option value="2" <? if($item['signal_action'] == 2) print 'selected="selected"'; ?>>Sell</option>
                    <option value="3" <? if($item['signal_action'] == 3) print 'selected="selected"'; ?>>Buy Limit</option>
                    <option value="4" <? if($item['signal_action'] == 4) print 'selected="selected"'; ?>>Sell Limit</option>
                    <option value="5" <? if($item['signal_action'] == 5) print 'selected="selected"'; ?>>Buy Stop</option>
                    <option value="6"<? if($item['signal_action'] == 6) print 'selected="selected"'; ?>>Sell Stop</option>
                </select>
            </section>
            <section class="col col-2">
                <label class="input"> Pair</label>
                    <select name="signal-pair">
                        <?
                            foreach($currency_pairs as $cp)
                            {
                                ?>
                                <option value="<? print $cp; ?>" <? if($item['signal_pair'] == $cp) print 'selected="selected"'?>><? print $cp; ?></option>
                                <?
                            }

                        ?>
                    </select>

                <label class="input">Price</label>
                <label class="input">
                    <input type="text" name="signal-price" value="<? print $item['signal_price'];?>" placeholder="Price">
                </label>
            </section>
            <section class="col col-2">
                <label class="input">TP

                    <input type="text" name="signal-tp" value="<? print $item['signal_tp'];?>" placeholder="TP">
                </label>

                <label class="input">SL
                    <input type="text" name="signal-sl" value="<? print $item['signal_sl'];?>" placeholder="SL">
                </label>
            </section>
            <section class="col col-2">
                <label class="input">Result
                    <input type="text" name="signal-result" value="<? print $item['signal_result'];?>" placeholder="Result">
                </label>


                <label class="input">Win/Loss</label>
                <select name="signal-winloss">
                    <option value="0" <? if($item['signal_winloss'] == 0) print 'selected="selected"'; ?>>None</option>
                    <option value="1" <? if($item['signal_winloss'] == 1) print 'selected="selected"'; ?>>Win</option>
                    <option value="2" <? if($item['signal_winloss'] == 2) print 'selected="selected"'; ?>>Loss</option>
                </select>
            </section>
            <section class="col col-2">
                <label class="input">Trader: </label>
                <select name="signal-trader"">
                    <?
                    $trader_list = $html['data']['traders'];
                    foreach($trader_list as $t_item)
                    {
                        ?>
                        <option value="<? print $t_item['trader_id']; ?>" <? if(intval($t_item['trader_id']) == intval($item['trader_id'])) print ' selected="selected"'; ?>><? print $t_item['trader_full_name']; ?></option>
                        <?
                    }
                    ?>
                </select>

                <label class="input">Date:
                    <input type="text" name="signal-date" value="<? print $item['signal_date'];?>" placeholder="Date">
                </label>
            </section>


        </div>
            <hr/>
    <?
}
    } else {
        print 'No signals available';
    }
    ?>







</form>

<script type="text/javascript">
    // <![CDATA[
    $(document).ready(function () {
    var typeclass;
    var serverURL  = 'http://<? print(MODE == 'prod')?'api.topforexsignal.com':'api.fxparse'?>';


        function notify(o)
        {
            var delay = (typeof o.delay !== 'undefined' && o.delay>0)? o.delay:3000;
            switch(o.type)
            {
                case 'red':typeclass='redtip';break;
                case 'orange':typeclass='orangetip';break;
                case 'blue':typeclass='bluetip'; break;
                default:case 'green':typeclass='greentip';break;
            }
            var h = '<div id="signal-notification" style="display:none;float:left;padding-top:20px;z-index:9999;position:fixed;width:100%;text-align:center;left:0;top:0;"><p style="box-shadow:0px 5px 20px #555;width:50%;margin:auto !important" class=" ' + typeclass+ '">' + o.msg + '</p></div>';
            $('body').append(h);
            var b = setTimeout(function(){$('div#signal-notification').fadeIn('slow')},100);
            var a = setTimeout(function(){$('div#signal-notification').fadeOut('slow',function(){$(this).remove();})},delay);

        }



        $('html').on('click','button.signal-update',function(e){
            //update button
            e.preventDefault();
            _parent = $(this).parent().parent().parent().parent();


            var signal_id = parseInt($(_parent).attr('id'));
            var signal_status =$(_parent).find('select[name=signal-status]');
            var signal_action = $(_parent).find('select[name=signal-action]');
            var signal_pair = $(_parent).find('select[name=signal-pair]');
            var signal_price = $(_parent).find('input[name=signal-price]');
            var signal_tp = $(_parent).find('input[name=signal-tp]');
            var signal_sl = $(_parent).find('input[name=signal-sl]');
            var signal_result = $(_parent).find('input[name=signal-result]');
            var signal_trader = $(_parent).find('select[name=signal-trader]');
            var signal_winloss = $(_parent).find('select[name=signal-winloss]');
            var signal_date = $(_parent).find('input[name=signal-date]');




            if($(signal_pair).val().length <4) $(signal_pair).addClass('input-error');
            if($(signal_price).val().length <1) $(signal_price).addClass('input-error');
            if($(signal_tp).val().length <1) $(signal_tp).addClass('input-error');
            if($(signal_sl).val().length <1) $(signal_sl).addClass('input-error');

            if($(signal_pair).val().length <4 || $(signal_price).val().length <1 ||$(signal_tp).val().length <1 ||$(signal_sl).val().length <1)
            {
                notify({type:'red',msg:'There was an error submitting this signal, please check the areas higlighted in red',delay:5000});
                return false;
            } else {
                $('input').removeClass('input-error');
            }
            var o = {
                'signal_id':signal_id,
                'signal_status':$(signal_status).val(),
                'signal_action':$(signal_action).val(),
                'signal_pair':$(signal_pair).val(),
                'signal_price':$(signal_price).val(),
                'signal_tp':$(signal_tp).val(),
                'signal_winloss':$(signal_winloss).val(),
                'trader_id':$(signal_trader).val(),
                'signal_sl':$(signal_sl).val(),
                'signal_result':$(signal_result).val(),
                'signal_date':$(signal_date).val()


            }

            console.log('update: ' + JSON.stringify(o));
            $.post( serverURL + "/signal/update",
                o
            )
                    .done(function( data ) {
                        console.log('server:' + data);

                        if(data.response == 1)
                        {
                            notify({type:'green',msg:data.data.message,delay:5000});
                            var a = setTimeout(function(){location.reload();},1000);

                        } else {
                            notify({type:'red',msg:data.data.message,delay:5000});
                        }

                    });

            return true;
        });

        $('#add-signal-button').click(function(e){

            var signal_status = $('#signal-status');
            var signal_action = $('#signal-action');
            var signal_pair = $('#signal-pair');
            var signal_price = $('#signal-price');
            var signal_tp = $('#signal-tp');
            var signal_sl = $('#signal-sl');
            var signal_result = $('#signal-result');
            var signal_trader = $('#signal-trader');
            var signal_winloss = $('#signal-winloss');


            var isValidPrice = ($(signal_price).val().length >0 && $(signal_price).val().match(/^-?\d*(\.\d+)?$/));
            var isValidTP =  ($(signal_tp).val().length >0 && $(signal_tp).val().match(/^-?\d*(\.\d+)?$/));
            var isValidSL =  ($(signal_sl).val().length >0 && $(signal_sl).val().match(/^-?\d*(\.\d+)?$/));;

            $('input').removeClass('input-error');


            if(!isValidPrice) $(signal_price).addClass('input-error');
            if(!isValidTP) $(signal_tp).addClass('input-error');
            if(!isValidSL) $(signal_sl).addClass('input-error');

            if(!isValidPrice || !isValidTP || !isValidSL)
            {
                notify({type:'red',msg:'There was an error submitting this signal, please check the areas higlighted in red',delay:5000});
                return false;
            }
            var o = {
                'signal_status':$(signal_status).val(),
                'signal_action':$(signal_action).val(),
                'signal_pair':$(signal_pair).val(),
                'signal_tp':$(signal_tp).val(),
                'signal_sl':$(signal_sl).val(),
                'signal_price':$(signal_price).val(),
                'signal_winloss':$(signal_winloss).val(),
                'trader_id':$(signal_trader).val()

            }
            console.log('POST:'  + JSON.stringify(o));

            $.post( serverURL + "/signal/add",o
            )
            .done(function( data ) {
                console.log('server:' + data);

                if(data.response == 1)
                {
                    notify({type:'green',msg:data.data.message,delay:5000});
                    //var a = setTimeout(function(){location.reload();},1000);


                } else {
                    notify({type:'red',msg:data.data.message,delay:5000});
                }

            });
        });

    });
    // ]]>
</script>