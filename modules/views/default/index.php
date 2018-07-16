<link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css">

<div class="wrapper">
    <div class="container">
    <?php if(Yii::$app->session->hasFlash('error')){?>
        <div class="alert alert-error alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php }?>
        <div class="page-header">
            <h4>Bharat Bill Payment System</h4>
            <div class="fieldstx closetab">
                    <a class="btn btn-default" href="javascript:void(0)">Back</a>
            </div>
        </div>
        
                            
                            
        
        <div class="row">
        
            <div class="col-md-4 wallet-wrap">
                <div class="wallet">
                    <div class="row">
                        <div class="col-sm-8">
                            Wallet balance: <span class="amount-tx" id="wallet_amount">Rs. <?php echo $wallet_balance;?></span>
                        </div>
                        <div class="col-sm-4">
                            <a class="btn btn-success" href="javascript:walletTopUp()">Add Topup</a>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-sm-12">
                        <a class="viewHist" href="/partnerpay/web/bbps/default/view_wallet_history">View History</a>
                        </div>
                    </div>          
                </div>          
            </div>
            
            <div class="col-md-8">
                <div class="opcwrap">
                <ul class="thlist">
                    <?php 
                    foreach($utilities as $key=>$val) { 
                        ?>
                    <li>
                        <div class="opcbox">
                            <a href="javascript:void(0)" onClick='setUtility(<?php echo $val->utility_id;?>,"<?php echo $val->utility_name;?>")'>
                                <div class="<?php echo "timg i-".strtolower(substr($val->utility_name,0,3))?>"></div>
                                <h3 class="thh"><?php  echo $val->utility_name; ?></h3>
                            </a>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            
                
                <form id="bill_details" action="/partnerpay/web/bbps/default/paying" method="post" enctype="multipart/form-data">
                    <div class="opcrow">
                    <div class="row">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                        <div class="col-md-4 opclist-warp">
                            <ul class="opclist" id="providers">
                            </ul>
                        </div>
                        <div class="col-md-8 opclist-box">
                            <h4>Mobile Bill Payment</h4>
                            <!-- <div class="row">                           
                                <div class="col-sm-12 col-md-8 usertx">
                                    <label>Register this user</label>
                                    <div class="yesnoswitch">
                                    <input type="checkbox" name="register" class="yesnoswitch-checkbox" id="myonoffswitch5" checked="">
                                    <label class="yesnoswitch-label" for="myonoffswitch5">
                                        <span class="yesnoswitch-inner"></span>
                                        <span class="yesnoswitch-switch"></span>
                                    </label>
                                    </div>
                                </div>
                            </div> -->
                            <div><input type="hidden" id="utility_name" name="utility_name" value=""></div>
                            <div class="row" id="bulk">
                                <div class="col-sm-12 col-md-8">
                                    <div class="form-group">                    
                                        <div class="file">
                                            <input type="file" id="bulk_upload" title="Bulk Upload" name="bulk_upload">
                                        </div>   
                                        <span id="errbulk_upload"></span>              
                                    </div>
                                    <div id="download_csv">

                                    </div>
                                <p class="text-center">OR</p>
                                </div>
                            </div>      
                            <div class="row" id="single">
                                <div class="col-sm-12 col-md-8">                            
                                    <div class="form-group">
                                        <input type="text" class="form-control single" id="fname" name="fname" placeholder="Enter your First Name">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-8">                            
                                    <div class="form-group">
                                        <input type="text" class="form-control single" id="lname" name="lname" placeholder="Enter your Last Name">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-8">                            
                                    <div class="form-group">
                                        <input type="text" class="form-control single" id="email" name="email" placeholder="Enter your Email">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-8">
                                    <div class="form-group">                    
                                    <!-- <input type="submit" class="btn btn-primary lg-btn" id="submitform" value="Submit"> -->
                                    <input type="submit" class="btn btn-primary lg-btn" value="Submit" name="submitButton">
                                    </div>
                                </div>
                            </div>
                        </div>          
                    </div>
                    </div>
                    </form>
                
                </div>
            </div>      
        </div>
      

        <script type="text/javascript" src="/partnerpay/modules/resources/js/jquery.js"></script>
        <script type="text/javascript" src="/partnerpay/modules/resources/js/bootstrap.file-input.js"></script>
        <script type="text/javascript" src="/partnerpay/modules/resources/js/customs.js"></script>


        
        
        
        
        
        
    </div>
</div>
<script>
function setUtility(id,name){
    $("#utility_name").val(id);
     $.ajax({
      url: "/partnerpay/web/bbps/default/providers",  
      data: {utility_id: id},
      type: "POST",
      dataType: "json",
      success: function(data) {
            $('#providers').empty();
           $.each(data, function (key, value) {
                    var provider_list='<li><label class="radio-inline"><input type="radio" onClick="getFields()"';
                    if(key == 0 ){
                        provider_list = provider_list+'checked="checked"';
                    } 
                    provider_list = provider_list+'name="providers" id="providers" value="'+value.id+'">'+value.name+'</label></li>';
                    $('#providers').append(provider_list);
             });
             getFields();
      }
   });
}

function getFields(){
    var provider = $("input[name=providers]:checked").val();
    if(provider){
     $.ajax({
      url: "/partnerpay/web/bbps/default/get_fields",  
      data: {provider_id: provider},
      type: "POST",
      dataType: "json",
      success: function(data) {
            $('.dynamic').remove();
           $.each(data, function (key, value) {
                    var fields='<div class="col-sm-12 col-md-8 dynamic"><div class="form-group"><input type="text" name="'+value.field+'" class="dynamic_field form-control single" placeholder="Enter your '+value.field+'" value=""><span class="error"></span></div></div>';
                    $('#single').append(fields);
                 $('input[name="'+value.field+'"]').rules("add", { 
                    required:function(element){
                      return ($("#bulk_upload").val().length == 0);
                    },  
                    regex: new RegExp(value.validation),
                    messages: {
                        required: value.field+" is Required",
                        regex: 'Please Provide Proper '+value.field,
                    }
                });
            });
             $('#download_csv').empty().append("<a href='/partnerpay/web/bbps/default/download_csv_file?provider="+provider+"' target='_blank' >Download Sample Format</a>")
             $('#bulk_upload').val('');
             $('#bulk_upload').removeAttr('disabled');
             $('.file-input-name').html('');
             $('.single').each(function(){
                 $(this).removeAttr('disabled');
                 $(this).val('');
            })
      }
   });
   }
}

$(document).on('change','#bulk_upload',function(){
    if($('#bulk_upload').val()!=""){
        $('.single').attr('disabled','true');
    }else{
        $('.single').removeAttr('disabled');
    }
});

$(document).on('change','.single',function(){
    $('.single').each(function(){
        console.log($(this).val());
        if($(this).val()!=''){
            $('#bulk_upload').attr('disabled','true');  
            return false;
        }else{
            $('#bulk_upload').removeAttr('disabled'); 
        }
    })
})

$(document).on('change','.single',function(){
    $('.single').each(function(){
        console.log($(this).val());
        if($(this).val()!=''){
            $('#bulk_upload').attr('disabled','true');  
            return false;
        }else{
            $('#bulk_upload').removeAttr('disabled'); 
        }
    })
})

function walletTopUp(){
    $.ajax({
      url: "/partnerpay/web/bbps/default/wallet_top_up",  
      dataType: "json",
      success: function(data) {
          if(data.TRANSACTIONSTATUS == 200){
              $('#wallet_amount').empty().html('Rs. '+data.WALLETBALANCE);
          } else {
              alert ("Error in Top Up Process please try again later")
          }
      }
   });
}
</script>
