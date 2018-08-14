<?php
include_once('common.php');
include_once('secure.php');
include_once('include/register_activity_model.php');
if($_SESSION['utype'] =='student'){
    include_once('secure_student.php');
    include_once('studentheader.php');
}else{
    include_once('secure_school.php');
    include_once('schoolheader.php');
}
include_once('footer.php');
$error = array();
function validate ($data){
    $error_list = array();
    if(!(isset($data['gr_no']) && $data['gr_no']!='')){
        $error_list['gr_no'] = "Roll No. is required!";
    }
    if (!preg_match ('#^[A-Z]+$#i',$data['fname'])) {
        $error_list['fname'] = "First name must only contain letters!";
    }
    if (!preg_match ('#^[A-Z]+$#i',$data['lname'])) {
        $error_list['lname'] = "Last name must only contain letters!";
    }
    if (!preg_match ('#^[A-Z ]+$#i',$data['father_name'])) {
        $error_list['father_name'] = "Father name must only contain letters!";
    } 
    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
        $error_list['email'] = "Invalid email id!";
    }
    if(! preg_match(('#^[0-9]+$#i'),$data['mob_no'])){
        $error_list['mob_no'] = "Code must only contain number!";
    }
    if($data['amount'] == '0'){
        $error_list['amount'] = "Invalid amount!";
    }
    if($data['total_amount'] == '0'){
        $error_list['total_amount'] = "Invalid amount!";
    }
    if($data['payoption'] == ''){
        $error_list['payoption'] = "Payment mode is required!";
    }
    return $error_list;
}

if($_POST["submit"]){
    $error =validate($_POST);
    if(sizeof($error)==0){
        $register_id = add_register_data($mysqli,$_POST,$config['school_details']['SCHOOL_ID']);
        if(sizeof($register_id)>0){
            $activity_register_mapping = add_register_data_map($mysqli,$register_id);
            if($activity_register_mapping){
                $data = $_POST;
                $airpay_details = get_airpay_details($mysqli,$_POST['activity_type_id']);
                $fail = 1;
                include('templates/payment.php');
            } else {
                $fail=0;
            }
        }else{
            $fail=0;
        }
    }else{
        $fail=0;
    }
}
if(!$fail){
    $error_upload = "Error While Registering Activity";
    $_SESSION['error_upload'] = $error_upload;
    $offset = 0;
    $record_list = get_activity_records($mysqli,$config['school_details']['SCHOOL_ID'],$offset,$search);
    $total_count = get_count($mysqli,$config['school_details']['SCHOOL_ID'],$search); 
    include('templates/register_activity.php');
}
mysqli_close($mysqli);
?>