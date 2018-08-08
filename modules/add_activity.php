<?php
include_once('common.php');
include_once('secure.php');
include_once('secure_school.php');
include_once('schoolheader.php');
include_once('footer.php');
include_once('include/activity_model.php');
include_once('template.class.php');
$error = array();
function validate ($data){
    $error_list = array();
    if (!preg_match ('#^[A-Z ]+$#i',$data['activity_title'])) {
        $error_list['activity_title'] = "Title must only contain letters!";
    } 
    if(! preg_match(('#^[0-9]+$#i'),$data['code'])){
        $error_list['code'] = "Code must only contain number!";
    }
    if(! preg_match(('#^[0-9]+$#i'),$data['code'])){
        $error_list['code'] = "Code must only contain number!";
    }
    if($data['start_date']==""){
        $error_list['start_date'] = "Start Date is required!";
    }
    if($data['end_date']==""){
        $error_list['end_date'] = "End Date is required!";
    }
    if(sizeof($data['payoption'])==0){
        $error_list['payoption'] = "Pay option is required!";
    }

    $class = $_POST['selclass'];
    $activity_name = $_POST['activity_name'];
    $fees =$_POST['fees'];
    if(sizeof($activity_name)>1){
        foreach ($activity_name as $_k => $_val) {
            if (!preg_match ('#^[A-Z ]+$#i',$_val)) {
                $error_list['activity_name'][$_k] = "Activity name must contain only alphabets";
            }
            if(! preg_match(('/^[0-9]+(.[0-9]{1,2})?$/'),$fees[$_k])){
                $error_list['fees'][$_k] = "Fees must contain only number";
            }
            if(sizeof($class[$_k])==0){
                $error_list['selclass'][$_k] = "Class is required!";
            }
        }
    } else{
        if (!preg_match ('#^[A-Z ]+$#i',$activity_name[0])) {
            $error_list['activity_name'][0] = "Activity name must contain only alphabets";
        }
        if(! preg_match(('/^[0-9]+(.[0-9]{1,2})?$/'),$fees[0])){
            $error_list['fees'][0] = "Fees must contain only number";
        }
        if(sizeof($class[0])==0){
            $error_list['selclass'][0] = "Class is required!";
        }
    }

    return $error_list;
}

if($_POST["submit"]){
    $error =validate($_POST);
    if(sizeof($error)==0){
        $activity_type_id = add_activity_type($mysqli,$_POST['activity_title']);
        if($activity_type_id){
            $activity_school_mapping = add_activity_school_map($mysqli,$activity_type_id,$_POST,$config);
            $activity = add_activity($mysqli,$activity_type_id,$_POST);
            if($activity){
                $upload_success = "Activity Created Successfully";
                $_SESSION['upload_success'] = $upload_success;
                header("Location: listactivity.php");
            } else {
                $error_upload = "Error While Creating Activity";
                $_SESSION['error_upload'] = $error_upload;
            }
        }else{
            $error_upload = "Error While Creating Activity";
            $_SESSION['error_upload'] = $error_upload;
        }
    }
}

if(isset($_GET['id'])){
    $activity_type_map = get_activity_type($mysqli,$_GET['id']);
    $activity = get_activity($mysqli,$_GET['id']);
    $activity_name=array();
    $i=0;
    while($row = $activity->fetch_assoc()){
       $activity_name['activity_name'][$i]=$row['ACTIVITY_NAME'];
       $activity_name['fees'][$i]=$row['FEES'];
       $i++;
    }
}
$get_classes = get_classes($mysqli,$config['school_details']['SCHOOL_ID']);
$school_details = get_school_year($mysqli,$config['school_details']['SCHOOL_ID']);
// $school_logo = $config['school_details']['LOGO_PATH'];
// $school_url = $config['school_details']['SCHOOL_URL'];
// $school_name = $config['school_details']['NAME'];
// $template = new Template;
include("templates/add_activity.php");
// $template->replace('header', $header);
// $template->replace('notificationlist', $notificationlist);
// $template->replace('printopt', $printopt);
// $template->replace('hide', $hide);
// $template->replace('footer', $footer);
// $template->replace('school_logo', $school_logo);
// $template->replace('school_url', $school_url);
// $template->replace('school_name', $school_name);
// $template->replace("pag", $pag);
// $template->replace('school_details_year',$school_details['SCHOOL_YEAR']);
// $template->replace('school_details_id',$school_details['SCHOOL_YEAR_ID']);
// $template->publish();
mysqli_close($mysqli);
?>