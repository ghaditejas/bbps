<link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css">
<div class="wrapper">
    <div class="container">
        <div class="merlogo-head text-center">
                <img src="/partnerpay/modules/resources/images/bbps-logo.png" alt="bbpslogo">
            </div>
        <div>
            <h2 style="text-align: center;">Thank you your data has been uploaded</h2>
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
        var utility = '<?php echo $utility; ?>';
        console.log(upload_error);
        if(provider_id && upload_error != 'null'){
            window.open('/partnerpay/web/bbps/default/download_csv_file?provider='+provider_id+'&utility='+utility+'&errors='+upload_error);
          }
    });
    </script>
</div>
