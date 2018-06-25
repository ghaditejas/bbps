<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>BBPPS</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="color:#181818;font-family:Verdana, Geneva, sans-serif;font-size:12px; line-height:16px;">
		<tr>
			<td align="center" valign="top">    
				<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" style="border:0px solid #666;">
					<tr>
						<td valign="top">        
							<table width="750" border="0" align="left" cellpadding="5" cellspacing="0" style="border:1px solid #dddddd;">
								<tr>
									<td valign="top">
									<img src="http://www.airpay.co.in/resources/images/airpay-payment-processing-services-logo.png" width="142" height="62">
									</td>
								</tr>
							</table> 
						</td>
					</tr>
					<tr>
						<td valign="top" style="border-style:solid; border-color:#3498db; border-width:0px 0px 2px 0px;">&nbsp;</td>
					</tr>
					<tr>
						<td>        
							<table width="100%" border="0" cellspacing="0" cellpadding="8" style="border-collapse: collapse;border-style:solid; border-color:#dddddd; border-width:1px 1px;">
								<tr>
									<td width="438" rowspan="4" align="left" valign="top" style="border-style:solid; border-color:#dddddd; border-width:0 0px 1px;">
										<table width="100%" border="0" cellspacing="0" cellpadding="3" style="border-collapse: collapse;border-style:solid; border-color:#dddddd; border-width:0px 0px;">
											<tr>
												<td width="160" valign="top">Name of the customer</td>
												<td width="5" valign="top"> :</td>
												<td><?php echo $receipt['FNAME']." ".$receipt['LNAME']; ?></td>
											</tr>
											<tr>
												<td width="160" valign="top">Biller Name</td>
												<td width="5" valign="top"> :</td>
												<td><?php echo $receipt['FNAME']." ".$receipt['LNAME']; ?></td>
											</tr>
											<tr>
												<td width="160" valign="top">BBPS Transaction Ref ID </td>
												<td width="5" valign="top"> :</td>
												<td><?php echo $receipt['BANK_REF_PAYMENT_NUMBER']?></td>
											</tr>
											<tr>
												<td width="160" valign="top">Consumer ID </td>
												<td width="5" valign="top"> :</td>
												<td>#conid#</td>
											</tr>
										</table>
										<br /><br />
										<table width="100%" border="0" cellspacing="0" cellpadding="3" style="border-collapse: collapse;border-style:solid; border-color:#dddddd; border-width:0px 0px;">
											<tr>
												<td width="160" valign="top">Mobile Number </td>
												<td width="5" valign="top"> :</td>
												<td><?php echo $receipt['ACCOUNT_NO']?></td>
											</tr>
											<tr>
												<td width="160" valign="top">Email </td>
												<td width="5" valign="top"> :</td>
												<td><?php echo $receipt['EMAIL'] ?></td>
											</tr>
										</table>
									</td>
									<td width="126" valign="top" style="border-style:solid; border-color:#dddddd; border-width:0 1px 1px;">Bill Date</td>
									<td width="172" align="left" valign="top" style="border-style:solid; border-color:#dddddd; border-width:0 1px 1px;">#billdate#</td>
								</tr>
								<tr>
									<td width="126" valign="top" style="border-style:solid; border-color:#dddddd; border-width:0 1px 1px;">Date and Time of Bill Payment Transaction </td>
									<td width="172" align="left" valign="top" style="border-style:solid; border-color:#dddddd; border-width:0 1px 1px;">#transdate#</td>
								</tr>
								<tr>
									<td width="126" valign="top" style="border-style:solid; border-color:#dddddd; border-width:0 1px 1px;">Biller Status </td>
									<td width="172" align="left" valign="top" style="border-style:solid; border-color:#dddddd; border-width:0 1px 1px;"><?php echo $receipt['PAYMENT_STATUS']?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table width="750" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td align="center" style="background:#3498db; border-bottom: 2px solid #1674b1;"><h4 style="font-size:22px; font-weight:normal;color:#fff; line-height:42px;margin:0;">Receipt</h4></td>
							</tr>
							</table>
						</td>
				    </tr>
					<tr>
						<td valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top">          
							<table width="100%" border="0" cellspacing="0" cellpadding="8" style="border-collapse: collapse;border-style:solid; border-color:#888; border-width:1px 1px;">
							  <tr>
								<td width="47" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 0px 1px;"><strong>Sr. No.</strong></td>
								<td valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><strong>Description </strong></td>
								<td width="330" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><strong></strong></td>
							  </tr>
							  <tr>
								<td width="47" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 0px 1px;">1</td>
								<td valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><strong>Payment Mode</strong></td>
								<td width="330" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><?php echo $receipt['PAY_MODE']?></td>
							  </tr>
							  <tr>
								<td width="47" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 0px 1px;">2</td>
								<td valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><strong>Payment Channel</strong></td>
								<td width="330" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;">#paymentchannel#</td>
							  </tr>
							  <tr>
								<td width="47" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 0px 1px;">3</td>
								<td valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><strong>Bill Amount</strong></td>
								<td width="330" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><?php echo $receipt['AMOUNT']?></td>
							  </tr>
							  <tr>
								<td width="47" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 0px 1px;">4</td>
								<td valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><strong>Customer Convenience Fee</strong></td>
								<td width="330" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><?php echo $charge ?></td>
							  </tr>
							  <tr>
								<td width="47" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 0px 1px;">5</td>
								<td valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><strong>Total Payment Amount</strong></td>
								<td width="330" align="left" valign="top" style="border-style:solid; border-color:#888; border-width:0 1px 1px;"><?php echo $receipt['AMOUNT']+$charge;?></td>
							  </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td valign="top">&nbsp;</td>
					</tr> 
					<tr>
						<td valign="top">This is a computer generated receipt hence no signature is required.</td>
					</tr>     
					<tr>
						<td valign="top">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>  
	</table>
</body>
</html>
