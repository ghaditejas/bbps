<?php
include_once('common.php');
include_once('secure.php');
include_once('include/activity_report_model.php');
include_once('include/mail_function.php');
require_once("mpdf60/mpdf.php");
if($_SESSION['utype'] =='student'){
    include_once('secure_student.php');
    include_once('studentheader.php');
    $student_id = $_SESSION['studentid'][0];
}else{
    $student_id ="";
    include_once('secure_school.php');
    include_once('schoolheader.php');
}
include_once('footer.php');
if(isset($_POST['send_mail'])){
    $mpdf=new mPDF();
    foreach($_POST['registerations'] as $_k=>$_v){
        $url = 'http://localhost/schoolpay/activity_receipt.php?activity_reg_id='.$_v;
        $mpdf->WriteHTML(file_get_contents($url));
        $content = $mpdf->Output('Registeration_Receipt.pdf', 'S');
        $attachment = $content;
        $content = chunk_split(base64_encode($content));
        $filename = 'Registeration_Receipt.pdf';
        $mail = sendSMTPEmail(['tejaisbest@gmail.com'=>'tejas'], 'test', 'test', $attachment, $filename, 'test');
        echo $mail;
        exit;
    }
    
}
if(isset($_POST['get_details_id'])){
    $registeration_details = array();
    $get_details = get_receipt_data($mysqli,$_POST['get_details_id']);
    $get_registeration = get_registeration_activity($mysqli,$_POST['get_details_id']);
    $registeration_details['user_details'] = $get_details;
    while($row = $get_registeration->fetch_assoc()){
        $registeration_details['activity_details'][]=$row;
    }
    echo json_encode($registeration_details);
    die;
}
if(isset($_GET['page'])){
    $page = $_GET['page'];
} else {
    $page = 1;
}
$search_array = array();
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

$offset = ($page-1)*2;
$search = json_encode($search_array);
$report_data = get_report_data($mysqli,$_SESSION['schoolid'],$student_id,$offset,$search);
$total_count = get_report_data_count($mysqli,$_SESSION['schoolid'],$student_id,$search);
$get_classes = get_classes($mysqli,$config['school_details']['SCHOOL_ID']);
$get_division = get_division($mysqli,$config['school_details']['SCHOOL_ID']);
include("templates/activity_report.php");
mysqli_close($mysqli);
?>