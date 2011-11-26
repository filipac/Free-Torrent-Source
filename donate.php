<?php
$DISABLE_ADS= true;
require "include/bittorrent.php";
loggedinorreturn();
stdhead();
$do = $_GET['do'];
$header = '
<table class="main" width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="embedded">
<h2>Donate</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="10"><tr><td>
';
$footer = '
</td>
</tr>
</table></table>
';
if ($do == 'thanks') {
	echo $header;	
	echo 'Thank you for your purchase! Your transaction has been completed.<br> Please click <a href="sendmessage.php?receiver=1"><b>here</b></a> to send us the transaction id  so we can credit your account!.';
	echo $footer;
}else {global $payment_paypal_enable;if($payment_paypal_enable == 'yes'):
	collapses('payment-paypal','Paypal Donate');?>
<center><b><br>Click the PayPal button below if you wish to make a donation!</b></center>

<p>
 <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input type="hidden" name="cmd" value="_xclick">
  <input type="hidden" name="business" value="<?global $payment_paypal_address; echo $payment_paypal_address;?>">
  <p align="center">
<br>
  <u>Please select a Donation amount:</u>
  <br><br>
   <select name="amount">
   <?php
   global $payment_paypal_amounts,$payment_paypal_curency;
$_a = explode(":",$payment_paypal_amounts);
print_r($_a);
?>
<option value="">Other Donation Amount</option>
<?php
$cnt = (int)1;
foreach($_a as $a):
if($cnt == (int)1)
echo "<option value='$a' SELECTED>$a $payment_paypal_curency Donation </option>";
else
echo "<option value='$a'>$a $payment_paypal_curency Donation </option>";
$cnt++;
endforeach;
?>
</select>
    <input type="hidden" name="image_url" value="">
    <input type="hidden" name="shipping" value="0">
    <input type="hidden" name="currency_code" value="<?=$payment_paypal_curency?>">
    <input type="hidden" name="merchant_return_link" value="<?=$BASEURL;?>/donate.php?do=thanks">
    <input type="hidden" name="item_number" value="<?=$CURUSER['username'].'-'.$CURUSER['id']?>" />
	<input type="hidden" name="item_name" value="Donation from uid: <?=$CURUSER['id']?>" />
	<input type="hidden" name="quantity" value="1" />
	<input type="hidden" name="cancel_return" value="<?=$BASEURL;?>/donate.php">

<br>
</p><p align="center">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but6.gif" border="0" name="I1" alt="Make payments with PayPal - it's fast, free and secure!">
<br><br>
<b>After you have donated -- make sure to <a href="sendmessage.php?receiver=1"><font color="blue"><u><b>Send Us</b></u></font></a> the <font color="red">transaction id</font> so we can credit your account!</b>
</form>
<?php collapsee();endif;?><?php
global $payement_wire_enable;if($payement_wire_enable == 'yes'):
	echo _br;collapses('payment-wire','Wire Transfer');
echo get('payment_wire_details');
collapsee();
endif;
stdfoot();
}
?>