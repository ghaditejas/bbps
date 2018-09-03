<?php echo $header; 
while($row = $get_classes->fetch_assoc()){
     $option = $option.'<option value ="'.$row['CLASS_ID'].'">'.$row['NAME'].'</option>';
} 
?>
<link rel="stylesheet" href="resources/css/jquery-ui.css">

<div class="content">
    <div class="header">
        <h1 class="page-title">Add Activity</h1>
    </div>

    <ul class="breadcrumb">
        <li><a href="index.php">Home</a> <span class="divider">/</span></li>
        <li class="active"><a href="listactivity.php">List Activity</a> <span class="divider">/</span></li>
        <li class="active">Add Activity</li>
    </ul>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="well">
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane active in" id="home">
                        <form id="" name="" action="add_activity.php" method="post" class="form-horizontal">
                            <legend style="padding-left: 10px; width: auto;">Activity Details</legend>
                            <div class="control-group">
                                <label class="control-label" for="">Year<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="school_year" name="school_year" placeholder="Enter Year" class="input-xlarge" type="text" readonly="readonly" data-required="true" value="<?php if(isset($school_details['SCHOOL_YEAR'])){ echo ($school_details['SCHOOL_YEAR'])."-".($school_details['SCHOOL_YEAR']+1); }else if(isset($_POST['school_year'])){echo ($_POST['school_year'])."-".($_POST['school_year']+1); } ?>">
                                    <input id="school_year_id" name="school_year_id" placeholder="Enter Year" class="input-xlarge" type="hidden" data-required="true" value="<?php if(isset($school_details['SCHOOL_YEAR_ID'])){ echo $school_details['SCHOOL_YEAR_ID']; }else if(isset($_POST['school_year_id'])){echo $_POST['school_year_id']; } ?>">
                                    <div class="validationAlert text-error"><?php if(isset($error["school_year"])){echo $error['school_year'];} ?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Title<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="activity_title" name="activity_title" placeholder="Enter Title" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['activity_title'])){echo $_POST['activity_title'];} else if(isset($activity_type_map['ACTIVITY_TYPE'])){echo $activity_type_map['ACTIVITY_TYPE'];} else {echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error["activity_title"])){echo $error['activity_title'];} ?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Code<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="code" name="code" placeholder="Enter Code" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['code'])){echo $_POST['code'];} else if(isset($activity_type_map['ACTIVITY_CODE'])){echo $activity_type_map['ACTIVITY_CODE'];} else {echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error["code"])){echo $error['code'];} ?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="textinput">Start Date<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="st_date" name="start_date" placeholder="Enter Start Date" class="input-xlarge" type="text" data-required="true" autocomplete="off" readonly="" value = "<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];} else if(isset($activity_type_map['ACTIVITY_START_DATE'])){echo date('Y-m-d',$activity_type_map['ACTIVITY_START_DATE']);} else {echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error["start_date"])){echo $error['start_date'];} ?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="textinput">End Date<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="ed_date" name="end_date" placeholder="Enter End Date" class="input-xlarge" type="text" data-required="true" autocomplete="off" readonly="" value = "<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} else if(isset($activity_type_map['ACTIVITY_END_DATE'])){echo date('Y-m-d',$activity_type_map['ACTIVITY_END_DATE']);} else {echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error["end_date"])){echo $error['end_date'];} ?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Show Meal Option</label>
                                <label class="control-label" style="width: 60px;"><input type="radio" checked="checked" class="meal_show" name="meal_show" value="Y"> Yes</label>
                                <label class="control-label" style="width: 60px;"><input type="radio" class="meal_show" name="meal_show" value="N"> No</label>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Email CC<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="email_cc" name="email_cc" placeholder="Enter email cc" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['email_cc'])){echo $_POST['email_cc'];} else if(isset($activity_type_map['EMAIL_CC'])){echo $activity_type_map['EMAIL_CC'];} else {echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error["email_cc"])){echo $error['email_cc'];} ?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Email Content<font color="red">*</font></label>
                                <div class="controls">
                                    <textarea id="email_content" name="email_content" class="ckeditor"><?php if(isset($_POST['email_content'])){echo $_POST['email_content'];} else if(isset($activity_type_map['EMAIL_CONTENT'])){echo $activity_type_map['EMAIL_CONTENT'];} else {echo '';}?></textarea>
                                    <div class="validationAlert text-error"><?php if(isset($error["email_content"])){echo $error['email_content'];} ?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">SMS Content<font color="red">*</font></label>
                                <div class="controls">
                                    <textarea id="sms_content" name="sms_content" class="ckeditor"  data-required="true"><?php if(isset($_POST['sms_content'])){echo $_POST['sms_content'];} else if(isset($activity_type_map['SMS_CONTENT'])){echo $activity_type_map['SMS_CONTENT'];} else {echo '';}?></textarea>
                                    <div class="validationAlert text-error"><?php if(isset($error["sms_content"])){echo $error['sms_content'];} ?></div>
                                </div>
                            </div>
                            <legend style="padding-left: 10px; width: auto;">Categories</legend>
                            <div class="control-group">
                                <label class="control-label" for="textinput"> Classes <font
                                        color="red">*</font></label>
                                <div class="controls">
                                    <select name="selclass[0][]" id="selclass" class="jsMultipleSelect input-xlarge" multiple="multiple" data-required="true" data-placeholder="Select Classes">
                                    <?php echo $option; ?>
                                    </select>
                                    <div class="validationAlert text-error"><?php if(isset($error['selclass'][0])){ echo $error['selclass'][0];}?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Activity<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="activity" name="activity_name[]" placeholder="Enter Activity" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['activity_name'][0])){echo $_POST['activity_name'][0];} else if(isset($activity_name['activity_name'][0])){echo $activity_name['activity_name'][0];} else {echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error['activity_name'][0])){ echo $error['activity_name'][0];}?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Fees<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="fees" name="fees[]" placeholder="Enter Fees" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['fees'][0])){echo $_POST['fees'][0];} else if(isset($activity_name['fees'][0])){echo $activity_name['fees'][0];} else{echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error['fees'][0])){ echo $error['fees'][0];}?></div>
                                </div>
                            </div>
                            <div id="divisionDiv">
                                <?php if(isset($error['selclass'][1])||isset($error['fees'][1])||isset($error['activity_name'][1])||isset($activity_name['activity_name'][1])){
                                    if(isset($activity_name['activity_name'][1])){
                                        $data = $activity_name['activity_name'];
                                    }else{
                                        $data = $_POST['activity_name'];
                                    }
                                    foreach($data as $_k=>$_v){
                                        if($_k>0){?>
                                        <div class="activities" id="<?php echo $_k;?> ">
                                        <hr>
                                            <div class="genCont" style="max-width:487px;">
                                                <a href="javascript:void(0)" onclick="removeThisQuestion(this)" style="color:#b40000;text-decoration:none;font-size:15px; float:right; margin-bottom: 10px;"><i class="icon-remove"></i> Remove</a>
                                                <div class="clearfix"></div>
                                                <div class="control-group">
                                                    <label class="control-label" for="textinput"> Classes <font color="red">*</font></label>
                                                    <div class="controls">
                                                         <select name = "selclass[<?php echo $_k;?>][]" class = "jsMultipleSelect input-xlarge" multiple="multiple" data-required = "true" data-placeholder="Select Classes">
                                                            <?php echo $option?>
                                                        </select>
                                                        <div class="validationAlert text-error"><?php if(isset($error['selclass'][$_k])){ echo $error['selclass'][$_k];}?></div>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                     <label class="control-label" for="">Activity<font color="red">*</font></label>
                                                     <div class="controls"> 
                                                         <input id="activity_name" name="activity_name[]" placeholder="Enter Code" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['activity_name'][$_k])){echo $_POST['activity_name'][$_k];} else if(isset($activity_name['activity_name'][0])){echo $activity_name['activity_name'][0];}  else {echo '';}?>">
                                                         <div class="validationAlert text-error"><?php if(isset($error['activity_name'][$_k])){ echo $error['activity_name'][$_k];}?></div>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                     <label class="control-label" for="">Fees<font color="red">*</font></label>
                                                     <div class="controls">
                                                          <input id="fees" name="fees[]" placeholder="Enter Code" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['fees'][$_k])){echo $_POST['fees'][$_k];} else if(isset($activity_name['fees'][$_k])){echo $activity_name['fees'][$_k];} else {echo '';}?>">
                                                          <div class="validationAlert text-error"><?php if(isset($error['fees'][$_k])){ echo $error['fees'][$_k];}?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }}?>
                                <?php } ?>    
                            </div>
                            <div class="control-group">
                                <label class="control-label">&nbsp;</label>
                                <div class="controls">
                                    <button type="button" class="btn btn-warning" onclick="AddMoreInput();">Add More
                                    </button>
                                </div>
                            </div>
                            <legend style="padding-left: 10px; width: auto;">Payment Option</legend>
                            <div class="control-group">
                                <label class="control-label">Is POS required</label>
                                <label class="control-label" style="width: 60px;"><input type="checkbox" <?php if(isset($_POST['pos_req'])){echo "checked='checked'";}?> class="pos_req" id="pos_req" name="pos_req" value="1"> Yes</label>
                            </div>
                            <div class="control-group <?php if(isset($error['pos_unique_id'])){ echo ''; } else {echo 'hidden';}?>" id="pos_unique">
                                <label class="control-label" for="">Unique POS Id<font color="red">*</font></label>
                                <div class="controls">
                                    <input id="activity" name="pos_unique_id" placeholder="Enter Unique POS Id" class="input-xlarge" type="text" data-required="true" value = "<?php if(isset($_POST['activity_name'][0])){echo $_POST['activity_name'][0];} else if(isset($activity_name['activity_name'][0])){echo $activity_name['activity_name'][0];} else {echo '';}?>">
                                    <div class="validationAlert text-error"><?php if(isset($error['pos_unique_id'])){ echo $error['pos_unique_id'];}?></div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <input type="hidden" name="submit" value="1">
                                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php echo $footer; ?>
        </div>
    </div>
</div>


<script src="resources/lib/bootstrap/js/bootstrap.js"></script>
<script src="resources/js/jquery-ui.js"></script>
<script src="resources/js/ckeditor/ckeditor.js"></script>

<script type="text/javascript">
    $(function() {
        $("#st_date").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });

        $("#ed_date").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });


    // var idno = 1;

    function AddMoreInput() {
        //alert("hi");
        idno = $('#divisionDiv').children('.activities').last().attr('id');
        var mainDiv = document.getElementById('divisionDiv');
        // Create a new div
        var innerDiv = document.createElement('div');
        // Set the attribute for created new div like here I am assigning Id attribure.
        innerDiv.setAttribute('id',parseInt(idno)+1);
        //innerDiv.setAttribute("class", "dynamicques"+idno);
        // Create text node to insert in the created Div
        var option = '<?php echo $option ?>';
        var generatedContent = '<hr>';
        generatedContent += '<div class="genCont" style="max-width:487px;"><a href="javascript:void(0)" onclick="removeThisQuestion(this)" style="color:#b40000;text-decoration:none;font-size:15px; float:right; margin-bottom: 10px;"><i class="icon-remove"></i> Remove</a><div class="clearfix"></div><div class="control-group"> <label class="control-label" for="textinput"> Classes <font color="red">*</font></label><div class="controls"> <select name = "selclass['+ idno +'][]" class = "jsMultipleSelect input-xlarge" multiple="multiple" data-required = "true" data-placeholder="Select Classes">'+option+'</select><div class="validationAlert text-error"></div></div></div><div class="control-group"> <label class="control-label" for="">Activity<font color="red">*</font></label><div class="controls"> <input id="activity_name" name="activity_name[]" placeholder="Enter Code" class="input-xlarge" type="text" data-required="true"><div class="validationAlert text-error"></div></div></div><div class="control-group"> <label class="control-label" for="">Fees<font color="red">*</font></label><div class="controls"> <input id="fees" name="fees[]" placeholder="Enter Code" class="input-xlarge" type="text" data-required="true"><div class="validationAlert text-error"></div></div></div></div>';

        // Inserting content to created Div by innerHtml
        innerDiv.innerHTML = generatedContent;
        // Appending this complete div to main div area.
        mainDiv.appendChild(innerDiv);

        // idno++;

        $(".jsMultipleSelect").select2();

    }

    function removeThisQuestion(element) {
        $(element).parent().parent().remove();

        // mainDiv is a variable to store the object of main area Div.
        ///var mainDiv = document.getElementById('divisionDiv');
        // get the div object with get Id to remove from main div area.
        ///var innerDiv = document.getElementById('divqId' + idnum);
        // Removing element from main div area.
        // mainDiv.removeChild(innerDiv);
        // $('form').parsley().destroy();
        // $('form').parsley();

    }

    $('#pos_req').click(function(){
       if($('#pos_req').prop('checked')){
           $('#pos_unique').removeClass('hidden');
       }else{
            $('#pos_unique').addClass('hidden');
       }
    })
</script>

</body>

</html>