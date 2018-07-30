<link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css">
<div class="container">
      
		<div class="page-header">
            <div class="merlogo-head">
                <img src="/partnerpay/modules/resources/images/bbps-logo.png" alt="bbpslogo">
            </div>
			<h4>Bharat Bill Payment System</h4>
			<div class="fieldstx">
                    <a class="btn btn-default" href="/partnerpay/web/bbps/default/biller">Add Biller</a>
                    <a class="btn btn-default" href="/partnerpay/web/bbps/default/listing">Invoice</a>
			</div>
		</div>
		<div class="row">
                <div class="col-md-3" id="total">
                    <div class="total-countbox">
                        <div class="total-counttx"><h3><span>Total</span>  </h3></div>
                        <div class="total-count"><?php echo $paid+$unpaid ;?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="total-countbox">
                        <div class="total-counttx"><h3><span>Total</span> Paid invoices </h3></div>
                        <div class="total-count"><?php echo $paid;?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="total-countbox">
                        <div class="total-counttx"><h3><span>Total</span> unpaid invoices </h3></div>
                        <div class="total-count"><?php echo $unpaid ;?></div>
                    </div>
                </div>
                <!-- <div class="col-md-3">
                    <div class="total-countbox">
                        <div class="total-counttx"><h3><span>Total</span> Approved invoices </h3></div>
                        <div class="total-count">4</div>
                    </div>
                </div> -->
        </div>
		
		<div class="totaltrans">
		<div class="row">
			<div class="col-sm-12 col-md-9">
				<h2>Total Invoices</h2>
				<div class="row">
                <?php foreach($invoices as $key=>$value) {?>
					<div class="col-sm-4">
					<div class="transbox">
						<!-- <i><img alt="Mobilepic" src="resources/images/i-mob.png"/></i> -->
						<p><?php echo $value['utility_name']; ?></p>
						<h3><?php echo $value['INVOICE']; ?></h3>
					</div>
					</div>
                <?php  } ?>
				</div>
			</div>
		</div>
        <script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>
        <script>
        $('#total').click(function(){
            $('.totaltrans').css('display','block');
        })
        </script>
		</div>