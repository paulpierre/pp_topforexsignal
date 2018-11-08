<?php
  /**
   * Stripe Form
   *
   * @package Membership Manager Pro
   * @author wojoscripts.com
   * @copyright 2010
   * @version $Id: form.tpl.php, v2.00 2011-04-20 10:12:05 gewa Exp $
   */
  if (!defined("_VALID_PHP"))
    die('Direct access to this location is not allowed.');
?>
<form class="xform" method="post" id="stripe_form">
  <div class="row">
    <section class="col col-6">
      <label class="input"> <i class="icon-append icon-asterisk"></i>
        <input type="text" autocomplete="off" name="card-number" placeholder="Card Number">
      </label>
      <div class="note note-error">Card Number</div>
    </section>
    <section class="col col-2">
      <label class="input"> <i class="icon-append icon-asterisk"></i>
        <input type="text" autocomplete="off" name="card-cvc" placeholder="CVC">
      </label>
      <div class="note note-error">CVC</div>
    </section>
    <section class="col col-2">
      <label class="input"> <i class="icon-append icon-asterisk"></i>
        <input type="text" autocomplete="off" name="card-expiry-month" placeholder="MM">
      </label>
      <div class="note note-error">Expiration Month</div>
    </section>
    <section class="col col-2">
      <label class="input"> <i class="icon-append icon-asterisk"></i>
        <input type="text" autocomplete="off" name="card-expiry-year" placeholder="YYYY">
      </label>
      <div class="note note-error">Expiration Year</div>
    </section>
  </div>
  <footer>
    <button class="button" id="dostripe" name="dostripe" type="button">Submit Payment</button>
  </footer>
  <input type="hidden" name="amount" value="<?php echo $row->price;?>" />
  <input type="hidden" name="item_name" value="<?php echo $row->title;?>" />
  <input type="hidden" name="item_number" value="<?php echo $row->id;?>" />
  <input type="hidden" name="currency_code" value="<?php echo ($row2->extra2) ? $row2->extra2 : $core->currency;?>" />
  <input type="hidden" name="processStripePayment" value="1" />
</form>
<div id="msgholder2"></div>
<script type="text/javascript">
// <![CDATA[
$(document).ready(function () {
    $('#dostripe').on('click', function () {
		$(this).hide();
        var str = $("#stripe_form").serialize();
        $.ajax({
            type: "post",
            dataType: 'json',
            url: "gateways/stripe/ipn.php",
            data: str,
            success: function (json) {
				hideLoader();
                if (json.type == "success") {
                    window.location.href = SITEURL + '/account.php';
					$("#msgholder2").html(json.message);
                } else {
					$('#dostripe').show();
                    $("#msgholder2").html(json.message);
                }
            }
        });
        return false;
    });
});
// ]]>
</script> 