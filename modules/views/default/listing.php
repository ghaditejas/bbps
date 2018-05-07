<!-- <link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/partnerpay/modules/resources/css/dataTables.bootstrap.css">
<div class="container">
	<div class="page-header">
		<h4>Invoice Listing</h4>
		<div class="fieldstx">
				<a class="btn btn-default" href="./index.html">Back</a>
		</div>
		<!-- <div class="summary">Displaying <b>1-4</b> of <b>4</b> items.</div> -->
	</div>
	
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#alllist" aria-controls="alllist" role="tab" data-toggle="tab">All Invoice list</a></li>
		<li role="presentation"><a href="#dislist" aria-controls="dislist" role="tab" data-toggle="tab">Remover list</a></li>
	</ul>
	
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="alllist">
			<div class="tablebox">	
			<div class="table-responsive">
			<table id="all_invoice" class="table table-striped table-bordered text-center">
			<thead>
			<tr>
				<th class="text-center idnum">#</th>
				<th class="text-center">Provider Name</th>
                <th class="text-center">Utility Name</th>
				<!-- <th class="text-center">Issue Date</th>
				<th class="text-center">Due Date</th> -->
				<th class="text-center">Total Amount</th>
				<th class="text-center action">&nbsp;</th>
			</tr>
			<!-- <tr class="searchrow">
				<td class="idnum">&nbsp;</td>
				<td><input type="text" class="form-control searchid" ></td>
				<td><input type="text" class="form-control searchid" ></td>
				<td><input type="text" class="form-control searchid" ></td>
				<td><input type="text" class="form-control searchid" ></td>
				<td class="action">&nbsp;</td>
			</tr> -->
			</thead>
			
			<tbody>
                <?php $i=1; foreach($invoice_data as $data){?>
					<tr>
						<td class="idnum"><?=$i;?></td>
						<td><?php echo $data['provider_name'];?></td>
                        <td><?php echo $data['utility_name'];?></td>
						<!-- <td>23-03-2018</td>
						<td>31-03-2018</td> -->
						<td class="text-right" id="amount_<?=$data['INVOICE_ID']?>"><?php if($data['invoice_amount']==0){?>
                            <i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i>
                       <?php }else{echo $data['invoice_amount'];}?></td>
						<td class="action">
							<div class="bbox">
                            <?php if($data['PAYMENT_STATUS']=='success'){?>
                                <a href="javascript:void(0)" id="<?php echo $data['INVOICE_ID'];?>" class="btn btn-primary col col-md-3" >PAID</a>
                            <?php } else if($data['PAYMENT_STATUS']=='pending' && $data['recieved']==0){ ?>
                                <a href="/partnerpay/web/bbps/default/payment?invoice_id=<?php echo $data['INVOICE_ID']; ?>" id="<?php echo $data['INVOICE_ID'];?>" class="btn btn-primary col col-md-3">Pay Now</a>
                           <?php } else { ?>
                                <a class="btn btn-primary col col-md-3" id="<?php echo $data['INVOICE_ID'];?>" disabled="true">Pay Now</a>
                            <?php } ?>
                                <a href="javascript:void(0)" style="margin-left:25px" class="btn btn-primary col col-md-3" >DETAILS</a>
								<a href="javascript:void(0)" class="col col-md-3"><span class="glyphicon glyphicon-trash"></span></a>
							</div>
						</td>
					</tr> 
                <?php $i++; } ?>
			</tbody>

			
			</table>
			</div>
			</div>
			<!-- <nav class="pull-right">
			  <ul class="pager">
				<li><a href="#"><span class="glyphicon glyphicon-chevron-left"></a></li>
				<li><a href="#"><span class="glyphicon glyphicon-chevron-right"></a></li>
			  </ul>
			</nav> -->
		</div><!-- .tab-content close -->
		<div role="tabpanel" class="tab-pane" id="dislist">
		
		<form id="remove_filter" action="javascript:getRecords()">
			<div class="fliterbox">
				<div class="row">
					<div class="col-sm-6 col-md-4">
						<div class="form-group">
							<label>Select Biller</label>
							<select class="form-control" id="utility_select" onChange="getProviders()" name="utility">
							<option value="">SELECT UTILITY</option>
							<?php foreach($utility_data as $utility){?>
								<option value="<?=$utility['utility_id']?>"><?=$utility['utility_name']?></option>
							<?php  } ?>
							</select>		
						</div>
					</div>
					<div class="col-sm-6 col-md-4">
						<div class="form-group">
							<label>Select Categories </label>
							<select class="form-control" id="providers_select" name="providers">
								<option value="">Select Provider</option>
							</select>		
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6 col-md-4">
						<input type="submit" class="btn btn-primary fliterbtn" value="Submit">
					</div>
				</div>
			</div><!--close fliterbox -->
			</form>
		
			<div class="remover-table hidden" id="removed">	
			<div class="tablebox">	
			<div class="table-responsive">
			<table class="table table-striped table-bordered text-center" id="removed_data">
			<thead>
			<tr>
				<th class="text-center idnum">#</th>
				<th class="text-center idnum">Select all</th>
				<th class="text-center">Mobile Number</th>
				<th class="text-center">Issue Date</th>
				<th class="text-center">Due Date</th>
				<th class="text-center">Total Amount</th>
				<th class="text-center">Payment</th>
			</tr>
			<!-- <tr class="searchrow">
				<td class="idnum">&nbsp;</td>
				<td class="idnum">&nbsp;</td>
				<td><input type="text" class="form-control searchid" ></td>
				<td><input type="text" class="form-control searchid" ></td>
				<td><input type="text" class="form-control searchid" ></td>
				<td><input type="text" class="form-control searchid" ></td>
				<td class="action">&nbsp;</td>
			</tr> -->
			</thead>
			
			<tbody>
					<tr>
						<td class="idnum">1</td>
						<td><input type="checkbox" class="checkbox-inline"></td>
						<td>9876543210</td>
						<td>23-03-2018</td>
						<td>31-03-2018</td>
						<td class="text-right">Rs 18,000.00</td>
						<td><a href="#" target="_blank" class="btn btn-primary">Pay Now</a></td>
					</tr>
			</tbody>

			
			</table>
			</div>
			</div>
			<!-- <nav class="pull-right">
			  <ul class="pager">
				<li><a href="#"><span class="glyphicon glyphicon-chevron-left"></a></li>
				<li><a href="#"><span class="glyphicon glyphicon-chevron-right"></a></li>
			  </ul>
			</nav>   -->
			</div>
		</div><!-- .tab-content close -->
	</div><!-- .tab-content close -->	

<script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>
<script type="text/javascript" src="/partnerpay/modules/resources/js/bootstrap.file-input.js"></script>

<script>
    function fetchdata(invoice_id,csrf_token){
            $.ajax({
					type: "POST",
      				url: "/partnerpay/web/bbps/default/checking",
					data: {"id":invoice_id,"_csrf":csrf_token},
					// data_json:
      				success: function(data) {
		  					if(data){
								  $('#'+invoice_id).removeAttr('disabled');
								  $("#"+invoice_id).attr("href", "/partnerpay/web/bbps/default/payment?invoice_id="+invoice_id);
							} else {
								setInterval(fetchdata(invoice_id,csrf_token),5000);
							}
	  					}
            		 });
        }


    $(document).ready(function(){
     $("#all_invoice").DataTable({
        "paging": true,
        "searching": true,
        'autowidth': true,
        "ordering": false,
        "lengthMenu": [2, 5, 10, 25, 50, 75, 100],
	 });
	 $("#removed_data").DataTable({
        "paging": true,
        "searching": true,
        'autowidth': true,
        "ordering": false,
        "lengthMenu": [2, 5, 10, 25, 50, 75, 100],
     });
	 var invoice_id= <?php echo $invoice_id?>;
	 var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
	 setInterval(fetchdata(invoice_id,csrf_token),5000);	
     });
</script>
<script>
function getProviders(){
	var id= $("#utility_select").val();
	if(id){
     	$.ajax({
      		url: "/partnerpay/web/bbps/default/providers",  
      		data: {utility_id: id},
     		type: "POST",
      		dataType: "json",
      		success: function(data) {
            	$('#providers_select').empty();
				if(data.length>0){
           		$.each(data, function (key, value) {
                    var provider_list='<option value="'+value.id+'">'+value.name+'</option>';
                    $('#providers_select').append(provider_list);
             	});
				} else {
					var provider_list='<option value="">Select Provider</option>';
                    $('#providers_select').append(provider_list);
				}
      		}
   		})
	}
}
</script>
<script>
function getRecords(){
	var utility= $("#utility_select").val();
	var provider= $("#providers_select").val();
	var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
	$.ajax({
      		url: "/partnerpay/web/bbps/default/removed",  
      		data: {"utility_id": utility,"provider_id": provider,"_csrf":csrf_token},
     		type: "POST",
      		dataType: "json",
      		success: function(data) {
				  $('#removed').removeClass('hidden');
				  $('#removed_data tbody').empty();
				$.each(data, function (key, value) {
                    var remove_data='<tr><td class="idnum">'+(parseInt(key)+1)+'</td>'+
						'<td><input type="checkbox" class="checkbox-inline"></td>'+
						'<td>'+value.MOBILE_NO+'</td>'+
						'<td>'+value.ISSUE_DATE+'</td>'+
						'<td>'+value.DUE_DATE+'</td>'+
						'<td class="text-right">'+value.NET_AMOUNT+'</td>'+
						'<td><a href="/partnerpay/web/bbps/default/payment?invoice_id='+value.INVOICE_ID+'" class="btn btn-primary">Pay Now</a></td></tr>';;
                    $('#removed_data tbody').append(remove_data);
             	});
			}
   		})
}
</script>
</div>