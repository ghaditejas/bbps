
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.jqueryui.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css">
<!-- <link rel="stylesheet" href="/partnerpay/modules/resources/css/dataTables.bootstrap.css"> -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css"> -->

<div class="container">
<div class="page-header">
		<h4>Wallet History</h4>
		<div class="fieldstx">
				<a class="btn btn-default" href="/partnerpay/web/bbps/default/biller">Back</a>
		</div>
</div>
<div class="removed-table" id="removed">	
				<div class="tablebox non-tbbd">	
					<div class="table-responsive">
					<!-- <input type="button" class="btn btn-primary" value="Pay Selected" style=""> -->
			<table class="table table-striped table-bordered text-center" id="wallet_history">
			<thead>
			<tr>
				<th class="text-center idnum">#</th>
				<th class="text-center">Transaction Id</th>
				<th class="text-center">Due Date</th>
				<th class="text-center">Total Amount</th>
				<th class="text-center">Transaction Mode</th>
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
                <?php $i=1; ?>
                <?php foreach($wallet_history as $value) {?>
                    <tr>
                        <td><?= $i;?></td>
                        <td><?=  $value['APTRANSACTIONID'];?></td>
                        <td><?=  $value['TRANSACTIONTIME'];?></td>
                        <td><?=  $value['AMOUNT'];?></td>
                        <td><?=  $value['TXNMODE'];?></td>
                    </tr>
                <?php $i++;} ?>
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

        <script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>  	
        <script type="text/javascript" src="/partnerpay/modules/resources/js/bootstrap.file-input.js"></script>
        <script type="text/javascript" src="/partnerpay/modules/resources/js/jquery-ui/jquery-ui.min.js"></script>
        <script>
           $(document).ready(function(){
             $("#wallet_history").DataTable({
    				    "paging": true,
        				"searching": true,
        				'autowidth': true,
        				"ordering": false,
        				"lengthMenu": [10, 25, 50, 75, 100],
						"buttons": [
        						'selectAll',
        						'selectNone'
    							],
					 });
             $('div.dataTables_filter input').addClass('searchable')
            });
        </script>
        </div>