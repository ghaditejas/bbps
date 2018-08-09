<?php
include_once('common.php');
include_once('secure.php');
include_once('include/register_activity_model.php');
// include_once('include/activity_model.php');
if($_SESSION['utype'] =='student'){
    include_once('secure_student.php');
    include_once('studentheader.php');
}else{
    include_once('secure_school.php');
    include_once('schoolheader.php');
}
include_once('footer.php');
if(isset($_POST['student_roll_no'])){
    $student_info = get_student_details_by_roll_no($mysqli,$_POST['student_roll_no'],$_SESSION['schoolid']);
    echo json_encode($student_info);
    exit;
}

if(isset($_POST['activity_id'])){
    $activities_list = get_activities($mysqli,$_POST['activity_id'],$_POST['sclass']);
    echo json_encode($activities_list);
    exit;
}
if(isset($_GET['page'])){
    $page = $_GET['page'];
} else {
    $page = 1;
}

if($_SESSION['utype'] =='student'){
    $students_details = get_student_details($mysqli,$_SESSION['uid'],$_SESSION['studentid'][0]); 
}
// $search_array = array();
// $search = json_encode($search_array);
$offset = ($page-1)*2;
    $record_list = get_activity_records($mysqli,$config['school_details']['SCHOOL_ID'],$offset,$search);
    $total_count = get_count($mysqli,$config['school_details']['SCHOOL_ID'],$search); 
include("templates/register_activity.php");
mysqli_close($mysqli);
?>