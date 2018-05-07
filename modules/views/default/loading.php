<style>
  
.flexed-container{
    position: absolute;
    top: 40%;
    left: 45%;
    display: flex;
    flex-direction: row;
}
.boxes{
    display: block;
    margin: 10px;
    border: 1em solid #000000;
    opacity: 0;
}
.one{
    animation: box-appear 3s linear infinite;
}
.two{
    animation: box-appear 3s linear .5s infinite;
}
.three{
    animation: box-appear 3s linear 1s infinite;
}
@keyframes box-appear{
    0%{
        opacity: 0;
    }
    16%{
        opacity: 1;
    }
    48%{
        opacity: 1;
    }
    64%{
        opacity: 0;
    }
    100%{
        opacity: 0;
    }
}
</style>
<div class="flexed-container">
  <span class="boxes one"></span>
  <span class="boxes two"></span>
  <span class="boxes three"></span>
</div>
<script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>
<script>
    function fetchdata(invoice_id,csrf_token){
        $.ajax({
			type: "POST",
      		url: "/partnerpay/web/bbps/default/checking",
			data: {"id":invoice_id,"_csrf":csrf_token},
      		success: function(data) {
		  		if(data){
			    	window.location.href = '/partnerpay/web/bbps/default/payment?invoice_id='+invoice_id;
				} else {
		    		setInterval(fetchdata(invoice_id,csrf_token),5000);
				}
              }
            });
        }

    $(document).ready(function(){
        var invoice_id= <?php echo $invoice_id?>;
	    var csrf_token = "<?php echo Yii::$app->request->getCsrfToken()?>";
		setInterval(fetchdata(invoice_id,csrf_token),5000);
    });
</script>