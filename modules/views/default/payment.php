<link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css">
<div class="index">
	<div class="indexpage">
	<div class="container">
		<div class="logo" style="padding: 25px;">
				<div class="logoimg partnerpay"><img alt="Partnerpay" src="/partnerpay/modules/resources/images/partnerpay-logo.png"/></div>
				<div class="logoimg banklogo"><img alt="mastercard" src="/partnerpay/modules/resources/images/mastercard.png"/></div>
			
		</div>
		<h3><?=$provider;?></h3>
		
		<div class="row">
			<div class="col-md-12"> <div class="invoice-head"><b>Invoice No :</b> <?=$invoice_data[0]['INVOICE_ID'];?></div></div>
		</div>
		
		<div class="payformbox">
		
		<div class="row">
			<div class="download-wrap">
			<div class="col-sm-6 align-right">
				<div class="download-img"></div>
			</div>
			
		</div>
		</div>
		
		<div class="row">
			<div class="download-wrap">
				<div class="col-sm-6 align-right">
				<div class="download-img"></div></div>
		
			</div>
		</div>
       <form class="form" action="/partnerpay/web/bbps/default/pay" id="payment" method="post">
	   <div class="row">		
	   <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
	   <input type="hidden" name="invoice_no" id="invoice_no" value="<?=$invoice_data[0]['INVOICE_ID'];?>" />
			<!-- <div class="col-sm-6">
				<div class="form-group req">
					<div class="form-group field-client-first_name required">
						<label class="control-label" for="merchant-id">Merchant Name</label>
						<input type="text" id="merchant_name" class="form-control" name="merchant">
						<div class="help-block"></div>
					</div>
				</div>
			</div> -->
		
		
			<div class="col-sm-6">
				<div class="form-group req">
					<div class="form-group field-invoice-pay_amount required">
						<label class="control-label" for="invoice-pay_amount">Amount</label>
						<input type="text" class="form-control" value= "<?php if(isset($invoice_amount)){
                           echo  $invoice_amount;
                        }?>" name="bill_amount" id="bill_amount" readonly="readonly">
						<div class="help-block"></div>
					</div>
				</div>
			</div>
		
			<div class="col-sm-6">
				<div class="form-group req">
					<div class="form-group field-invoice-pay_amount required">
						<label class="control-label" for="invoice-pay_amount">PAYMENT MODE</label>
						<select class="form-control" name="payment_mode" id="payment_mode" onChange="applyCharge()">
						<option value="">SELECT PAYMENT MODE</option>
						<?php 
						 $modes = json_decode($charges['MODES'],true);?>
						<?php foreach($modes as $key=>$values) { ?>
							<option value="<?php echo  $key;?>"><?php echo $values;?></option>
						<?php } ?>
						</select>
						<div class="help-block"></div>
					</div>
				</div>
			</div>
			</div>
		<div class="row">

			<div class="col-sm-6">
				<div class="form-group req">
					<div class="form-group field-invoice-pay_amount required">
						<label class="control-label" for="invoice-pay_amount">Total Amount</label>
						<input type="text" class="form-control" name="total_amount" id="total_amount" readonly="readonly" value="<?php if(isset($invoice_amount)){
                           echo  $invoice_amount;
                        } ?>">
						<input type="hidden" class="form-control" name="invoice_amount" id="invoice_amount" value="<?php if(isset($invoice_amount)){
                           echo  $invoice_amount;
                        } ?>">
						<div class="help-block"></div>
					</div>
				</div>
			</div>
		
			<div class="clearfix"></div>
			
			<div class="col-sm-12">
					<a href="#listing" class="" data-toggle="modal" data-target="#listing" >View Invoice listing</a></label>
			</div>
			
			<div class="col-sm-12 ">
				<div class="form-group field-invoice-iagree">
					<input type="checkbox" id="tandc1"  name="agree" value="1">
					<label>I accept the 
					<a href="#tandc" class="tnclink required" data-toggle="modal" data-target="#tandc">terms and conditions.</a></label>
					<div class="help-block"></div>
				</div>
			</div>

			<div class="col-sm-6 btngroup">
				<button type="submit" id="pay" class="btn btn-primary">Pay</button>
			</div>
			<form>
		</div>	
		</form>
	
				
		<div class="bott-logo">
            <div class="logoimg partnerpay"><img alt="Partnerpay" src="/partnerpay/modules/resources/images/partnerpay-logo.png"></div>
        	        	<!-- <div class="logoimg banklogo"><img alt="Bank Logo" src="/uploads/bank_logo/6ee4dc7e4c64897ea8fce3457a097edc.JPG"></div> -->
			</div>
		</div>
			

	</div>
	</div><!--/close .indexpage-->
</div>

<div class="modal fade" id="listing" tabindex="-1" role="dialog" aria-labelledby="listingLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Invoice listing</h4>
      </div>
      <div class="modal-body">
		<div class="tableboxpopup">	
			<div class="table-responsive">
			<table class="table table-striped table-bordered text-center">
			<thead>
			<tr>
				<th class="text-center idnum">#</th>
				<th class="text-center">Account No.</th>
				<th class="text-center">Due Date</th>
				<th class="text-center">Amount</th>
				<th class="text-center action">&nbsp;</th>
			</tr>
			</thead>
			
			<tbody>
                <?php
                    $i=1;
                foreach($invoice_data as $invoice_value) {?>
					<tr id="<?=$invoice_value['ACCOUNT_NO'];?>">
						<td class="idnum"><?=$i;?></td>
						<td><?=$invoice_value['ACCOUNT_NO'];?></td>
						<td><?=date("d-m-Y",strtotime($invoice_value['DUE_DATE']));?></td>
						<td class="text-right"><?=$invoice_value['AMOUNT'];?></td>
						<td class="action">
							<div class="bbox">
								<a onClick="remove_mobile('<?=$invoice_value['ACCOUNT_NO'];?>','<?=$invoice_value['INVOICE_ID'];?>')"><span class="glyphicon glyphicon-trash"></span></a>
							</div>
						</td>
					</tr>
                <?php } ?>
			</tbody>
			</table>
			</div>
			</div>
		
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>  
<script>
function applyCharge(){
	var amount = $('#bill_amount').val();
	var charges  = <?php echo $charges['CHARGES']; ?>;  
	var charge_mode =  $('#payment_mode').val();
	taxRate = 0.18;
	calculatedAmount = (charges[charge_mode] * amount) / 100;
    b_chgs = calculatedAmount * taxRate;
    tot_amt = parseFloat(amount) + parseFloat(calculatedAmount) + parseFloat(b_chgs);
	$('#total_amount').val(parseFloat(tot_amt).toFixed(2));
	$('#invoice_amount').val(parseFloat(tot_amt).toFixed(2));
}

 function remove_mobile(mobile_no,invoice_id){
    var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
    $.ajax({
					type: "POST",
      				url: "/partnerpay/web/bbps/default/deletemobile",
					data: {"invoice_id":invoice_id,"mobile_no":mobile_no,"_csrf":csrf_token},
					dataType:"json",
      				success: function(data) {
                            if(data){
								if(data.sum==0){
									window.location.href = '/partnerpay/web/bbps/default/listing';
								}
								var charges  = <?php echo $charges['CHARGES']; ?>;  
								var charge_mode =  $('#payment_mode').val();
								calculatedAmount = (charges[charge_mode] * data.sum) / 100;
    							b_chgs = calculatedAmount * taxRate;
    							tot_amt = parseFloat(data.sum) + parseFloat(calculatedAmount) + parseFloat(b_chgs);
								$('#invoice_amount').val(parseFloat(tot_amt).toFixed(2));
								$('#total_amount').val(parseFloat(tot_amt).toFixed(2))
								$('#bill_amount').val(data.sum);
                                $('#'+mobile_no).remove();
                            }
	  					}
            		 });
 }
</script>