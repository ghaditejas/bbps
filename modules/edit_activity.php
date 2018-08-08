<?php
include_once('common.php');
include_once('secure.php');
include_once('secure_school.php');
include_once('include/activity_model.php');
include_once('schoolheader.php');
include_once('footer.php');

function validate ($data){
    $error_list = array();
    if($data['end_date']==""){
        $error_list['end_date'] = "End Date is required!";
    }
 
    return $error_list;
}


if($_POST["submit"]){
    $error =validate($_POST);
    if(sizeof($error)==0){
        $activity_type_update = update_activity_map($mysqli,$_POST);
        if($activity_type_update){
                $upload_success = "Activity Updated Successfully";
                $_SESSION['upload_success'] = $upload_success;
                header("Location: listactivity.php");
        }else{
            $error_upload = "Error While Updating Activity";
            $_SESSION['error_upload'] = $error_upload;
        }
    }
}

if($_GET['update_id']){
    $activity_type_map = get_activity_type($mysqli,$_GET['update_id']);
}
include("templates/edit_activity.php");
mysqli_close($mysqli);
?>