
<?php
    include('checksum.php');
    $username = '7595201';
    $password = 'MgUFY5em';
    $secret = '6KeetYvu2TSdsegD';
    $mercid = '21720';
    $alldata = $data['email'] . $data['fname'] . $data['lname'] . $data['total_amount'] .$register_id;
    $privatekey = Checksum::encrypt($username . ":|:" . $password, $secret);
    $checksum = Checksum::calculateChecksum($alldata . date('Y-m-d'), $privatekey);
?>
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
			    <form action="https://payments.airpay.co.in/pay/index.php" method="post" id="airpay_form">
                    <input type="hidden" name="privatekey" value="<?php echo $privatekey; ?>">
                    <input type="hidden" name="mercid" value="<?php echo $mercid; ?>">
				    <input type="hidden" name="orderid" value="<?php echo $register_id; ?>">
                    <input type="hidden" name="amount" value="<?php echo $data['total_amount']; ?>">
                    <input type="hidden" name="buyerEmail" value="<?php echo $data['email']; ?>">
                    <input type="hidden" name="buyerPhone" value=<?php echo $data['mob_no']; ?>>
                    <input type="hidden" name="buyerFirstName" value="<?php echo $data['fname']; ?>">
                    <input type="hidden" name="buyerLastName" value="<?php echo $data['lname']; ?>">
 		            <input type="hidden" name="currency" value="356">
		            <input type="hidden" name="isocurrency" value="INR">
                    <input type="hidden" name="checksum" value="<?php echo $checksum; ?>">
				    <input type="hidden" name="chmod" value= "<?php echo $data['payoption'] ?>">	
                    <input type="hidden" name="customvar" value="<?php echo "activity_automation|amount:".$data['amount'] ?>">
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