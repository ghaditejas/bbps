<?php echo $header; ?>
<link rel="stylesheet" href="resources/css/jquery-ui.css">

<div class="content">
    <div class="header">
        <h1 class="page-title">Register Activity</h1>
    </div>

    <ul class="breadcrumb">
        <li><a href="index.php">Home</a> <span class="divider">/</span></li>
        <li class="active">Register Activities</li>
    </ul>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="well">
                 <legend style="padding-left: 10px; width: auto;">Assigned Activty</legend>
                 <div class="tableWrapper">
                     <div class="#estyle#"><span id = "divmsg" ></span></div>
                     <table class="table table-striped table-hover table-condensed" id="example">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Activity Type</th>
                            <th>Register</th>
                        </tr>
                        </thead>
                        <tbody>
                           <?php  $i=1; ?>
                        <?php while($row = $record_list->fetch_assoc()){?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row['ACTIVITY_TYPE']; ?></td>
                                <td><input type="hidden" id="meal_<?php echo $row['ACTIVITY_TYPE_ID']?>" value="<?php echo $row['MEAL_OPTION']?>"><input type="hidden" id="pos_required_<?php echo $row['ACTIVITY_TYPE_ID']?>" value="<?php echo $row['POS_REQUIRED']?>"><button class="btn btn-primary register" onCLick="Register(this,'<?php echo $row['ACTIVITY_TYPE_ID']?>')">Register</button></td>
                            </tr>
                            <?php $i++; } ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <ul>
                        <?php
                            if($total_count['total'] % 2 == 0){
                                $total_pages = $total_count['total']/2;
                            } else {
                                $total_pages = floor($total_count['total']/2)+1; 
                        }?>
                        <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==1){echo 'javascript:void(0)';} else {echo 'register_activity.php?search='.$search.'&&page=1';}?>' class="<?php if(!isset($_GET['page']) || $_GET['page']==1){echo 'hidden';} ?>" id="first_page">First Page</a> </li>
                        <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==1){echo 'javascript:void(0)';} else if(!isset($_GET['page'])){echo 'javascript:void(0)';} else { echo 'register_activity.php?search='.$search.'&&page='.($_GET['page']-1);}?>' class="<?php if(!isset($_GET['page']) || $_GET['page']==1){echo 'hidden';} ?>">Prev Page</a> </li>
                        <?php for($i=1;$i<=$total_pages;$i++){
                            ?>
                        <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==$i){echo 'javascript:void(0)';} else if(!isset($_GET['page']) && $i==1){echo 'javascript:void(0)';} else { echo 'register_activity.php?search='.$search.'&&page='.$i;}?>' style ="<?php if(isset($_GET['page']) && $_GET['page']==$i){echo 'color:#000;';} else if(!isset($_GET['page']) && $i==1){echo 'color:#000;';} ?> ?>"><?php echo $i; ?></a></li>
                        <?php }?>
                        <li><a href='<?php if($total_pages<=1){echo 'javascript:void(0)';} else if(isset($_GET['page']) && $_GET['page']==($total_pages)){echo 'javascript:void(0)';} else if(!isset($_GET['page'])){echo 'register_activity.php?search='.$search.'&&page=2';} else { echo 'register_activity.php?search='.$search.'&&page='.($_GET['page']+1);}?>' class="<?php if((isset($_GET['page']) && $_GET['page']==$total_pages)||$total_pages==1){echo 'hidden';} ?>" >Next</a></li>
                        <li><a href='<?php if(isset($_GET['page']) && $_GET['page']==($total_pages)){echo 'javascript:void(0)';} else {echo 'register_activity.php?search='.$search.'&&page='.$total_pages;}?>' class="<?php if((isset($_GET['page']) && $_GET['page']==$total_pages)||$total_pages == 1){echo 'hidden';} ?>" id="last_page">Last Page</a></li>
                    </ul>
                </div>
                <div id="myTabContent" class="tab-content <?php  if(sizeof($error)>0){echo "";}else{echo 'hidden';}?>">
                    <div class="tab-pane active in" id="home">
                        <form id="pay" name="pay" action="payment.php" method="post" class="form-horizontal">
                            <legend style="padding-left: 10px; width: auto;">Student Details</legend>
                            <?php if($_SESSION['utype']=='school'){?>
                             <div class="control-group">
                                <label class="control-label" for="">GR No. <font color="red">*</font></label>
                                <div class="controls">
                                    <input id="gr_no" name="gr_no" onBlur="Student_details()" placeholder="Enter GR No" value="" class="input-xlarge" type="text" data-required="true" >
                                    <div class="validationAlert text-error"><?php echo $error['gr_no'];?></div>
                                </div>
                            </div>
                        <?php } ?>
                            <div class="control-group">
                                <label class="control-label" for="">First Name</label>
                                <div class="controls">
                                    <input id="fname" name="fname" placeholder="" class="input-xlarge"  type="text" data-required="true" readonly value="<?php if(isset($students_details['FIRST_NAME'])){echo $students_details['FIRST_NAME'];} else {echo "";}?>">
                                    <div class="validationAlert text-error"> <?php echo $error['fname'];?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Last Name</label>
                                <div class="controls">
                                    <input id="lname" name="lname" placeholder="" class="input-xlarge"   type="text" data-required="true" readonly value="<?php if(isset($students_details['LAST_NAME'])){echo $students_details['LAST_NAME'];} else {echo "";}?>">
                                    <div class="validationAlert text-error"><?php echo $error['lname'];?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Father Name</label>
                                <div class="controls">
                                    <input id="father_name" name="father_name" placeholder="" class="input-xlarge"   type="text" data-required="true" readonly value="<?php if(isset($students_details['father_name'])){echo $students_details['father_name'];} else {echo "";}?>">
                                    <div class="validationAlert text-error"><?php echo $error['father_name'];?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Email Id</label>
                                <div class="controls">
                                    <input id="email" name="email" placeholder="" class="input-xlarge" type="text" data-required="true" readonly value="<?php if(isset($students_details['EMAIL'])){echo $students_details['EMAIL'];} else {echo "";}?>">
                                    <div class="validationAlert text-error"><?php echo $error['email'];?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Mobile No</label>
                                <div class="controls">
                                    <input id="mob_no" name="mob_no" placeholder="" class="input-xlarge" type="text" data-required="true" readonly value="<?php if(isset($students_details['CONTACT_NO'])){echo $students_details['CONTACT_NO'];} else {echo "";}?>">
                                    <div class="validationAlert text-error"><?php echo $error['mob_no'];?></div>
                                </div>
                                <input type="hidden" id="student_class" name="student_class" value="<?php if(isset($students_details['CLASS_ID'])){echo $students_details['CLASS_ID'];} else if($_POST['student_class']){echo $_POST['student_class'];} else {echo "";}?>">
                                <input type="hidden" id="activity_type_id" name="activity_type_id" value="<?php if(isset($_POST['activity_type_id'])){echo $_POST['activity_type_id'];} ?>">
                                <input type="hidden" name="student_id" id="student_id" value="<?php if(isset($_SESSION['studentid'][0])){echo $_SESSION['studentid'][0]; }?>">
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Alternate Mobile No</label>
                                <div class="controls">
                                    <input id="mob_no2" name="mob_no2" placeholder="Enter Alternate Mobile No" value="" class="input-xlarge" type="text" data-required="true" >
                                    <div class="validationAlert text-error"><?php echo $error['mob_no2'];?></div>
                                </div>
                            </div>
                            <div id="meal">  
                            </div>
                             <legend style="padding-left: 10px; width: auto;">Activities</legend>
                            <div class="tableWrapper">
                                 <div class="#estyle#"><span id = "divmsg" ></span></div>
                                 <table class="table table-striped table-hover table-condensed" id="activities">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Select</th>
                                        <th>Activities</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <legend style="padding-left: 10px; width: auto;">Payment Details</legend>
                            <div class="control-group">
                                <label class="control-label" for="">Amount</label>
                                <div class="controls">
                                    <input id="amount" name="amount" placeholder="" value="0" class="input-xlarge" type="text" data-required="true" readonly>
                                    <div class="validationAlert text-error"><?php echo $error['amount'];?></div>
                                 </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="textinput">Payment Mode<font color="red">*</font></label>
                                <div class="controls">
                                    <select name="payoption" id="payoption" class="input-xlarge">
                                    <?php if($_SESSION['utype'] !='student'){ ?>
                                        <option value = "">Select Option</option>>
                                        <option value = "pushtopos">POS</option>
                                    <?php } else {?>
                                        <option value = "">Select Option</option>
                                        <option value = "pgcc">CC</option>
                                        <option value = "pgdc">DC</option>
                                        <option value = "nb">Net Banking</option>
                                    <?php } ?>
                                    </select>
                                    <div class="validationAlert text-error"><?php echo $error['payoption'];?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Service Charges</label>
                                <div class="controls">
                                    <input id="charge" name="charge" placeholder="" value="0" class="input-xlarge" type="text" data-required="true" readonly>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="">Total Amount</label>
                                <div class="controls">
                                    <input id="total_amount" name="total_amount" placeholder="" value="0" class="input-xlarge" type="text" data-required="true" readonly>
                                    <div class="validationAlert text-error"><?php echo $error['total_amount'];?></div>
                                </div>
                            </div>
                            <input type="hidden" name="submit" value="1">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Pay</button>
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

<script>
function get_activities(activity_type_id,student_class){
    $.ajax({
            url: 'register_activity.php',
            data: {activity_id:activity_type_id,sclass:student_class},
            type: 'post',
            dataType: 'JSON',
            success: function (output) {
                if (output!= "") {
                    $('#activities tbody').empty();
                    $.each(output,function(index,data){
                        var table_row = '<tr><td>'+(parseInt(index)+1)+'</td><td><label class="control-label" style="width: 60px; text-align: center"><input type="checkbox" onClick="checked_activity('+data.ACTIVITY_ID+')" class="activity_id" name="activity_id[]" id="activity_id_'+data.ACTIVITY_ID+'" value="'+data.ACTIVITY_ID+'"> </label></td><td>'+data.ACTIVITY_NAME+'</td><td><input type="hidden" id="fees_'+data.ACTIVITY_ID+'" value="'+data.FEES+'">Rs '+data.FEES+'/-</td></tr>'
                        $('#activities tbody').append(table_row);
                    });
                } else {
                    $('#activities tbody').empty();
                }
           }
        });
}

function Register(element,activity_type_id){
    $('.register').each(function(){
        $(this).removeAttr('disabled');
    })
    $(element).attr('disabled','disabled');
    $('#activity_type_id').val(activity_type_id);
    $('#meal').empty();
    if($('#meal_'+activity_type_id).val() == "y"){
        $('#meal').html('<div class="control-group"><label class="control-label" for="textinput">Meal<font color="red">*</font></label><div class="controls"><select name="mealoption" id="mealoption" class="input-xlarge"><option value = "">Select Option</option><option value = "veg">VEG</option><option value = "jain">JAIN</option></select><div class="validationAlert text-error"><?php echo $error['payoption'];?></div></div></div>');
    }
    $('#amount').val(0);
    $('#charge').val(0);
    $('#total_amount').val(0);
    var user ="<?php echo $_SESSION['utype']; ?>";
    if(user != 'student'){
        if($('#pos_required_'+activity_type_id).val()!=0){
            $('#myTabContent').removeClass('hidden');        
        }else{
            $('#myTabContent').addClass('hidden');        
        }
    }else{
    $('#myTabContent').removeClass('hidden');
    }
    var student_class =$('#student_class').val();
    if(student_class != ""){
       get_activities(activity_type_id,student_class);
    }
}

function Student_details(){
    var id = $('#gr_no').val();
    if(id != ""){
    $.ajax({
            url: 'register_activity.php',
            data: {student_roll_no: id},
            type: 'post',
            dataType: 'JSON',
            success: function (output) {
            if (output!= "") {
                $('#fname').val(output.FIRST_NAME);
                $('#lname').val(output.LAST_NAME);
                $('#father_name').val(output.father_name);
                $('#email').val(output.EMAIL);
                $('#mob_no').val(output.CONTACT_NO);
                $('#student_class').val(output.CLASS_ID);
                $('#student_id').val(output.STUDENT_ID);
                get_activities($('#activity_type_id').val(),output.CLASS_ID);
            }else{
                $('#fname').val(' ');
                $('#lname').val(' ');
                $('#father_name').val(' ');
                $('#email').val(' ');
                $('#mob_no').val(' ');
                $('#activities tbody').empty();
            }
           }
        });
    }
}

function checked_activity(id){
    var amount = parseInt($('#amount').val());
    if($('#activity_id_'+id).is(":checked")){
        amount = amount + parseInt($('#fees_'+id).val());
    }else{
        amount = amount - parseInt($('#fees_'+id).val());
    }
    $('#amount').val(amount);
    charge();
}

$('#payoption').change(function(){
    charge();
})

function charge(){
    var pay_mode = $('#payoption').val();
    var amount = $('#amount').val();
    if(pay_mode != ""){
        if(pay_mode == "pgcc"){
            var charges = parseInt(amount)*1.5/100;
        }else if(pay_mode == "pgdc"){
            var charges = parseInt(amount)*1.75/100;
        }else if(pay_mode == "nb"){
            var charges = parseInt(amount)*1.25/100;
        }else{
            var charges = parseInt(amount)*2.0/100;
        }
        $('#charge').val(charges);
        $('#total_amount').val(parseInt(amount)+charges);
    }else{
        $('#charge').val(0);
        $('#total_amount').val(amount);
    }

}


</script>

</body>

</html>