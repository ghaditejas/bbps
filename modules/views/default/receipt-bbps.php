<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- <title>Receipt</title> -->
</head>

<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Verdana, Geneva, sans-serif;font-size:14px;">
		<tr>
			<td align="center">
				<table width="800" border="0" cellspacing="0" cellpadding="0" style="border:3px solid #f0925e;">
					<tr>
						<td align="center" valign="top">
							<table width="800" border="0" cellspacing="0" cellpadding="5" style="background-color: #f47920;">
								<tr>
									<td align="left" valign="top" width="400">
										<img src="http://www.airpay.co.in/resources/images/airpay-payment-processing-services-logo.png" style="max-width:135px; background-color: #fff; max-height:50px;" alt="Bank Logo">
									</td>
									<td align="right" valign="top" width="400">
										<img src="/partnerpay/modules/resources/images/bbps-logo.png" style="max-width: 135px; max-height:50px;" alt="fastbank">
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td valign="top" width="100%" style="background-color: #f7f2e3; border-bottom:1px solid #e9e6dd">
							<table width="800" border="0" cellspacing="0" cellpadding="10">
								<tr>
									<td align="center" valign="top" style="font-size:14px; line-height:18px;">
										Payment Confirmation - Receipt<br />
									</td>
								</tr>
						  </table>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<table width="800" border="0" cellspacing="0" cellpadding="20">
								<tr>
									<td align="center" valign="top" style="font-size:13px; line-height:18px;">
										Thank you. We have received your payment request. Please note your Payment ID/Reference number for any queries. <br />
									</td>
								</tr>
						  </table>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<table width="800" border="0" cellspacing="0" cellpadding="10" style="border-collapse: collapse;font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333;">
								<tr align="left" style="background-color: #eeeeee; border:2px solid #e5e5e5">
									<td align="left" valign="top" width="100%"><?php echo $receipt['provider_name'];; ?></td>
								</tr>
							</table>
							<table width="800" border="0" cellspacing="0" cellpadding="10" style="border-collapse: collapse;font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333;">
								<tr>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Status</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $receipt['PAYMENT_STATUS'];?></td>
								</tr>
								<tr style="background-color: #ececec">
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Name of the Customer</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $receipt['FNAME']." ".$receipt['LNAME'];?></td>
								</tr>
								<tr>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Consumer Number</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $receipt['ACCOUNT_NO'];?></td>
								</tr>
								<tr style="background-color: #ececec">
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Mobile Number</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $receipt['ACCOUNT_NO'];?></td>
								</tr>
								<tr>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Payment Mode</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Wallet</td>
								</tr>
								<tr style="background-color: #ececec">
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Payment Through</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Internet</td>
								</tr>
								<tr>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Bill Date</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo date('d-m-Y',strtotime($receipt['DUE_DATE']));?></td>
								</tr>
								<tr style="background-color: #ececec">
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Transaction Id</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $receipt['AIRPAY_ID'];?></td>
								</tr>
								<tr>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Bill Amount</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $receipt['AMOUNT'];?></td>
								</tr>
								<tr style="background-color: #ececec">
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Customer Convenience Fee</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $charge;?></td>
								</tr>
								<tr>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Total Amount</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo $charge+$receipt['AMOUNT'];?></td>
								</tr>
								<tr style="background-color: #ececec">
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;">Transaction Date and Time</td>
									<td align="left" valign="top" style="border:1px solid #ccc; width: 50%;"><?php echo date('d-m-Y',strtotime($receipt['CREATED_ON']));?></td>
								</tr>
							</table>
						</td>
					</tr>
					<!-- <tr>
						<td align="center" valign="top">
							<table width="800" border="0" cellspacing="20" cellpadding="5">
								<tr>
									<td align="right" valign="top" style="font-size:13px; line-height:18px;">
										<button type="button" onclick="" style="font-size:13px; line-height:18px; margin-right:10px; cursor: pointer;">Make Another Payment</button>
										<button type="button" onclick=""style="font-size:13px; line-height:18px; margin-right:10px; cursor: pointer;">Print</button> <br />
									</td>
								</tr>
						  </table>
						</td>
					</tr> -->
				</table>
			</td>
		</tr>
    </table>
</body>

</html>