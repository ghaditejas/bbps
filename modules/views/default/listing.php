<!-- <link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- <link rel="stylesheet" href="/partnerpay/modules/resources/css/dataTables.bootstrap.css"> -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.jqueryui.min.css">
<div class="container">
	
	<div class="page-header">
		<h4>Invoice Listing</h4>
		<div class="fieldstx">
				<a class="btn btn-default" href="/partnerpay/web/bbps/default/">Back</a>
		</div>
	</div>
			<div class="fliterbox">
			  <form id="remove_filter" action="javascript:loadData()">
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
			 </form>
			</div><br><!--close fliterbox -->
	<div id="tabs" class="hidden">		
	<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active"><a href="#dislist" id="unpaidlist" aria-controls="dislist" role="tab" data-toggle="tab">Unpaid bill list</a></li>
		<li role="presentation"><a href="#alllist" id="allinvoice" aria-controls="alllist" role="tab" onClick="allInvoice()" data-toggle="tab">All Invoice list</a></li>
	</ul>
	<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="dislist">
				<div class="row">
							<div class="col-sm-6 col-md-4">
								<div class="form-group req">
									<div class="form-group required">
										<label class="control-label" for="">From Date</label>
										<input type="text" id="from_date" onChange="getRecords()" class="form-control datepicker1" name="frmdate" readonly>
										<div class="help-block"></div>
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group req">
									<div class="form-group required">
										<label class="control-label" for="merchant-id">To Date</label>
										<input type="text" id="to_date" onChange="getRecords()" class="form-control datepicker2" name="todate" readonly>
										<div class="help-block"></div>
									</div>
								</div>
							</div>
				</div>
		
		
			<div class="remover-table hidden" id="removed">	
				<div class="tablebox">	
					<div class="table-responsive">
					<!-- <input type="button" class="btn btn-primary" value="Pay Selected" style=""> -->
			<table class="table table-striped table-bordered text-center" id="removed_data">
			<thead>
			<tr>
				<th class="text-center idnum">#</th>
				<th class="text-center idnum"><input type="checkbox" id="select_all" class="checkbox-inline">Select all</th>
				<th class="text-center">Account Number</th>
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

	
		<div role="tabpanel" class="tab-pane" id="alllist">
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
		<!-- .tab-content close -->	
	</div>
	</div>
	<div class="modal fade" id="listing" tabindex="-1" role="dialog" aria-labelledby="listingLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Invoice listing</h4>
                </div>
                <div class="modal-body">
                    <div class="tablebox">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center" id="paid_invoice">
                                <thead>
                                    <tr>
                                        <th class="text-center idnum">#</th>
                                        <th class="text-center">Account Number</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-center">Total Amount</th>
                                        <th class="text-center action">Action</th>
                                    </tr>
                                    <!-- <tr class="searchrow">
                                        <td class="idnum">&nbsp;</td>
                                        <td><input type="text" class="form-control searchid"></td>
                                        <td><input type="text" class="form-control searchid"></td>
                                        <td><input type="text" class="form-control searchid"></td>
                                        <td><input type="text" class="form-control searchid"></td>
                                        <td><input type="text" class="form-control searchid"></td>
                                        <td class="action">&nbsp;</td>
                                    </tr> -->
                                </thead>

                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>  	
<script type="text/javascript" src="/partnerpay/modules/resources/js/bootstrap.file-input.js"></script>
<script type="text/javascript" src="/partnerpay/modules/resources/js/jquery-ui/jquery-ui.min.js"></script>

<script type="text/javascript">

		jQuery('.datepicker1').datepicker({"dateFormat":"yy-mm-dd"});
		jQuery('.datepicker2').datepicker({"dateFormat":"yy-mm-dd"});
	
	</script>
<script>
    $(document).ready(function(){
	 	$('#select_all').click(function(){
			 $('.checkbox').prop('checked',$(this).prop('checked'));
	 	});
	 	$('body').on('click', 'input.checkbox:checkbox', function() {
			if (!this.checked) {
            	$("#select_all").prop('checked', false);
         	}
		});
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
function loadData(){
	$('#tabs').removeClass('hidden');
	var active_tab = $('.tab-pane.active').attr('id');
	if(active_tab == 'dislist'){
		getRecords();
	} else if(active_tab == 'alllist'){
		allInvoice();
	}
}

function getRecords(){
	var utility= $("#utility_select").val();
	var provider= $("#providers_select").val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
	if(utility && provider && from_date && to_date){
		$.ajax({
      		url: "/partnerpay/web/bbps/default/unpaid",  
      		data: {"utility_id": utility,"provider_id": provider,'from_date':from_date,'to_date':to_date,"_csrf":csrf_token},
     		type: "POST",
      		dataType: "json",
      		success: function(data) {
				  $('#removed').removeClass('hidden');
				  if($.fn.DataTable.isDataTable( '#removed_data' )){
				  	$("#removed_data").DataTable().destroy();
				  }
				  $('#removed_data tbody').empty();
				$.each(data, function (key, value) {
                    var remove_data='<tr><td class="idnum">'+(parseInt(key)+1)+'</td>'+
						'<td><input type="checkbox" name="checkbox_bill[]" class="checkbox" value="'+value.PROVIDER_BILL_DETAILS_ID+'" class="checkbox-inline"></td>'+
						'<td>'+value.ACCOUNT_NO+'</td>'+
						'<td>'+value.DUE_DATE+'</td>'+
						'<td class="text-right">'+value.AMOUNT+'</td>'+
						'<td><a href="javascript:void(0)" onClick="pay()" class="btn btn-primary">Pay Now</a></td></tr>';
                    $('#removed_data tbody').append(remove_data);
             	});
				 $("#removed_data").DataTable({
    				    "paging": true,
        				"searching": true,
        				'autowidth': true,
        				"ordering": false,
        				"lengthMenu": [2, 5, 10, 25, 50, 75, 100],
						"buttons": [
        						'selectAll',
        						'selectNone'
    							],
     				});
			}
		  });
		}	
}
</script>
<script>
function pay(){
	var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
	var arr = [];
        $('input.checkbox:checkbox:checked').each(function () {
            arr.push($(this).val());
        });
		if(arr.length>0){
			$.ajax({
      			url: "/partnerpay/web/bbps/default/add_mobile",  
      			data: {"provider_bill_details_id":arr,"_csrf":csrf_token},
     			type: "POST",
      			dataType: "json",
      			success: function(data) {
					window.location.href = '/partnerpay/web/bbps/default/payment?invoice_id='+data;
				}
   			});
		} else {
			alert("Please select a Mobile Number");
		}
}

function getDetails(invoice_id){
	var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
	$.ajax({
      			url: "/partnerpay/web/bbps/default/get_invoice_data",  
      			data: {"invoice_id":invoice_id,"_csrf":csrf_token},
     			type: "POST",
      			dataType: "json",
      			success: function(data) {
					  $('#paid_invoice tbody').empty();
					  $.each(data, function (key, value) {
						var paid_invoice_data = '<tr><td class="idnum">'+(parseInt(key)+1)+'</td><td>'+value.ACCOUNT_NO+'</td><td>'+value.DUE_DATE+'</td><td class="text-right">'+value.AMOUNT+'</td><td class="action"><div class="bbox"><a href="/partnerpay/web/bbps/default/generate_bill_receipt?bill_details_id='+value.PROVIDER_BILL_DETAILS_ID+'" target="_blank" class="btn btn-success"><span>RECEIPT</span></a></div></td></tr>';
						$('#paid_invoice tbody').append(paid_invoice_data);
					  });
				  }
   				});
}

function allInvoice(){
	var utility= $("#utility_select").val();
	var provider= $("#providers_select").val();
	var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
	if(utility && provider){
		$.ajax({
      		url: "/partnerpay/web/bbps/default/paid_invoice",  
      		data: {"utility_id": utility,"provider_id": provider,"_csrf":csrf_token},
     		type: "POST",
      		dataType: "json",
      		success: function(data) {
				  $('#all_invoice').removeClass('hidden');
				  if($.fn.DataTable.isDataTable( '#all_invoice' )){
				  	$("#all_invoice").DataTable().destroy();
				  }
				  $('#all_invoice tbody').empty();
				$.each(data, function (key, value) {
                    var all_invoice_data='<tr><td class="idnum">'+(parseInt(key)+1)+'</td><td>'+value.provider_name+'</td><td>'+value.utility_name+'</td><td class="text-right" id="amount_'+value.INVOICE_ID+'">'+value.invoice_amount+'</td><td class="action"><div class="bbox"><a href="#listing" style="margin-left:25px" data-toggle="modal" onClick="getDetails('+value.INVOICE_ID+')" class="btn btn-primary" >DETAILS</a></div></td></tr>';
                    $('#all_invoice tbody').append(all_invoice_data);
             	});
				 $("#all_invoice").DataTable({
    				    "paging": true,
        				"searching": true,
        				'autowidth': true,
        				"ordering": false,
        				"lengthMenu": [2, 5, 10, 25, 50, 75, 100],
						"buttons": [
        						'selectAll',
        						'selectNone'
    							],
     				});
			}
		  });
		}
}
</script>
</div>