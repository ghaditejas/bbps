<link rel="stylesheet" href="/partnerpay/modules/resources/css/customs.css" type="text/css">
<div class="wrapper">
    <div class="container">
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
                            Wallet balance: <span class="amount-tx">Rs. 12000.00</span>
                        </div>
                        <div class="col-sm-4">
                            <a class="btn btn-success" href="javascript:void(0)">Add Topup</a>
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
                            <!-- <select class="opclist" id="providers">
                                <option value="">SELECT PROVIDER</option>
                            </select> -->
                            <!-- <ul class="opclist" id="providers">
                            </ul> -->
                        </div>
                        <div class="col-md-8 opclist-box">
                            <h4>Mobile Bill Payment</h4>
                            <div class="row">                           
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
                            </div>
                            <div class="col-sm-12 col-md-8"><div class="form-group">
                            <select class="form-control" id="providers" name="providers" onChange="getFields()">
                                <option value="">SELECT PROVIDER</option>
                            </select>
                            </div></div>
                            <div><input type="hidden" id="utility_name" name="utility_name" value=""></div>
                            <div class="row" id="bulk">
                                <div class="col-sm-12 col-md-8">
                                    <div class="form-group">                    
                                        <div class="file">
                                            <input type="file" id="bulk_upload" title="Bulk Upload" name="bulk_upload">
                                        </div>                 
                                    </div>
                                
                                <p class="text-center">OR</p>
                                </div>
                            </div>      
                            <div class="row" id="single">
                                <!-- <div class="col-sm-12 col-md-8">                            
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="mobile_no" name="mobile_no" placeholder="Enter Your Account Number">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-8">                            
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter your First Name">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-8">                            
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter your Last Name">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-8">                            
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter your Email">
                                        <div class="help-block"></div>
                                    </div>
                                </div> -->
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
        <!-- <script type="text/javascript" src="/partnerpay/modules/resources/js/jquery-2.2.3.min.js"></script> -->
        <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.js"></script>
        <script type="text/javascript" src="/partnerpay/modules/resources/js/validate.js"></script> -->
        <script type="text/javascript" src="/partnerpay/modules/resources/js/bootstrap.file-input.js"></script>
        <script type="text/javascript" src="/partnerpay/modules/resources/js/customs.js"></script>


        
        
        
        
        
        
    </div>
</div>
<script>
//  $('body').on('click', '#myonoffswitch5', function() {
//     var value=$("#myonoffswitch5").prop('checked');
//         if(value){
//             $("#instant").addClass("hidden");
//             $("#bulk").removeClass("hidden");
//         } else {
//             $("#bulk").addClass("hidden");
//             $("#instant").removeClass("hidden");
//         }
// });
</script>
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
            console.log(data);
            $('#providers').append('<option value="">SELECT PROVIDERS</option>');
           $.each(data, function (key, value) {
                    // var provider_list='<li><label class="radio-inline"><input type="radio" name="providers" id="providers" value="'+value.id+'">'+value.name+'</label></li>';
                    var provider_list='<option value="'+value.id+'">'+value.name+'</option>';
                    $('#providers').append(provider_list);
             });
      }
   });
}

function getFields(){
    var provider = $("#providers").val();
    if(provider){
     $.ajax({
      url: "/partnerpay/web/bbps/default/get_fields",  
      data: {provider_id: provider},
      type: "POST",
      dataType: "json",
      success: function(data) {
            $('#single').empty();
           $.each(data, function (key, value) {
                    var fields='<div class="col-sm-12 col-md-8"><div class="form-group"><input type="text" name="'+value+'" class="dynamic_field form-control" placeholder="Enter your '+value+'" value=""><span class="error"></span></div></div>';
                    $('#single').append(fields);
             });
             $('.dynamic_field').each(function(){
                 $(this).rules("add", { 
                    required:function(element){
                      return ($("#bulk_upload").val().length == 0);
                    },  
                    messages: {
                        required: "This Field is Required"
                    }
                });
             })
      }
   });
   }
}
</script>
