<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="container">
	<div class="page-header">
    <center>
    <table width="500px;">
	    <tr>
		    <td align="center" valign="middle">Do Not Refresh or Press Back <br/> Redirecting to Airpay</td>
	    </tr>
        <tr>
            <td align="center" valign="middle"><i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i></td>
        </tr>
	    <tr>
		    <td align="center" valign="middle">
            <?php $data=Yii::$app->user->identity;
                ?>
			    <form action="https://devel-payments.airpayme.com/pay/index.php" method="post" id="airpay_form">
                    <input type="hidden" name="privatekey" value="<?php echo $key; ?>">
                    <input type="hidden" name="mercid" value="<?php echo $mechant_id; ?>">
				    <input type="hidden" name="orderid" value="<?php echo $orderid; ?>">
                    <input type="hidden" name="amount" value="<?php echo $payment_data['invoice_amount']; ?>">
                    <input type="hidden" name="buyerEmail" value="<?php echo $data['EMAIL']; ?>">
                    <input type="hidden" name="buyerPhone" value=<?php echo $data['MOBILE']; ?>>
                    <input type="hidden" name="buyerFirstName" value="<?php echo $data['FIRST_NAME']; ?>">
                    <input type="hidden" name="buyerLastName" value="<?php echo $data['LAST_NAME']; ?>">
 		            <input type="hidden" name="currency" value="356">
		            <input type="hidden" name="isocurrency" value="INR">
                    <input type="hidden" name="checksum" value="<?php echo $checksum; ?>">
				    <input type="hidden" name="chmod" value= "<?php echo $payment_data['payment_mode'] ?>">	
                    <?php if($payment_data['payment_mode'] == "ppc") {?>
                        <input type="hidden" name="wallet" value= "0">	
                        <input type="hidden" name="token" value= "<?php echo $token; ?>">
                    <?php } ?>
                    <?php if(isset($payment_data['customvar'])) {?>
                        <input type="hidden" name="wallet" value= "1">	
                        <input type="hidden" name="token" value= "<?php echo $token; ?>">
                        <input type="hidden" name="customvar" value="<?php echo $payment_data['customvar']?>">
                    <?php } else { ?>
                        <input type="hidden" name="customvar" value="BBPS">
                           <?php  } ?>
                </form>
		    </td>
        </tr>
    </table>
    </center>
    </div>
</div>
<script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>
<script>
$(document).ready(function(){
    setTimeout(function(){
        $("#airpay_form").submit()
    }, 5000);
});
</script>