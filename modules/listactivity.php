<?php
include_once('common.php');
include_once('secure.php');
include_once('secure_school.php');
include_once('include/activity_model.php');
include_once('schoolheader.php');
include_once('footer.php');
include_once('template.class.php');
$schoolid = $_SESSION['schoolid'];
if(isset($_GET['page'])){
    $page = $_GET['page'];
} else {
    $page = 1;
}
$search_array = array();
$search_array['test']=123;
if($_POST['search']){
    $search_array = $_POST;
    $page = 1;
}else{
    if($_GET['search']=="null"){
        $search_array = null;
    }else{
        $search_array = json_decode($_GET['search']);
    }
}
$search = json_encode($search_array);

    $offset = ($page-1)*2;
    $record_list = get_activity_records($mysqli,$config['school_details']['SCHOOL_ID'],$offset,$search);
    $total_count = get_count($mysqli,$config['school_details']['SCHOOL_ID'],$search); 
include("templates/listactivity.php");
// $template->replace('header', $header);
// $template->replace('notificationlist', $notificationlist);
// $template->replace('printopt', $printopt);
// $template->replace('hide', $hide);
// $template->replace('footer', $footer);
// $template->replace('school_logo', $school_logo);
// $template->replace('school_url', $school_url);
// $template->replace('school_name', $school_name);
// $template->replace("pag", $pag);
// $template->publish();   
mysqli_close($mysqli);
?>