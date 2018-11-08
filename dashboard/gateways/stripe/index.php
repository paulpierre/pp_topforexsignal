<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
</head>

<body id="mainbody">
<form action="" method="POST" id="payment-form">
  <table cellpadding="0" cellspacing="0" class="display">
    <thead>
      <tr>
        <th colspan="2">Purchase Summary - New Membership</th>
      </tr>
    </thead>
    <tr>
      <th><strong>Card Number</strong></th>
      <td><input type="text" size="20" autocomplete="off" class="card-number" name="card-number" /></td>
    </tr>
    <tr>
      <th><strong>CVC</strong></th>
      <td><input type="text" size="4" autocomplete="off" class="card-cvc" name="card-cvc" /></td>
    </tr>
    <tr>
      <th><strong>Expiration (MM/YYYY)</strong></th>
      <td><input type="text" size="2" class="card-expiry-month" name="card-expiry-month" />
        &nbsp;/&nbsp;
        <input type="text" size="4" class="card-expiry-year" name="card-expiry-year" /></td>
    </tr>
    <tr>
      <th><button type="submit" class="submit-button">Submit Payment</button></th>
    </tr>
  </table>
  <input type="hidden" name="amount" value="12.99" />
  <input type="hidden" name="description" value="Extra platinum" />
  <input type="hidden" name="item_name" value="Platinum Membership" />
  <input type="hidden" name="business" value="alex@mail.com" />
  <input type="hidden" name="item_number" value="38" />
  <input type="hidden" name="currency_code" value="CAD" />
</form>
<div id="msgholder"></div>
<script type="text/javascript">
// <![CDATA[
$(document).ready(function () {
    $('#mainbody').on("click", ".submit-button", function () {
        var str = $("#payment-form").serialize();
        str += '&action=deleteMulti';
        $.ajax({
            type: "post",
            url: "ipn_new.php",
            data: str,
            //beforeSend: requestDefault,
            success: function (html) {
             $("#msgholder").html(html);
            }
        });
        return false;
    });
});
// ]]>
</script>
</body>
</html>
