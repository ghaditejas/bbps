<link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css">
<div class="wrapper">
    <div class="container">
        <div>
            <h2 style="text-align: center;">ThankYou your data has been uploaded</h2>
            <div style="text-align: center;">
                Go to <a href="/partnerpay/web/bbps/default/listing"><b>listing</b></a> to pay the bill
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>
    <script>
    $(document).ready(function(){
        var provider_id = "<?php echo $provider; ?>";
        var upload_error = '<?php echo $upload_error; ?>';
        if(provider_id && upload_error){
            window.open('/partnerpay/web/bbps/default/download_csv_file?provider='+provider_id+'&errors='+upload_error);
          }
    });
    </script>
</div>
