<?php 
include_once('common.php');
// include_once('secure.php');
include_once('include/activity_report_model.php');
/*if($_SESSION['utype'] =='student'){
    include_once('secure_student.php');
    include_once('studentheader.php');
    $student_id = $_SESSION['studentid'][0];
}else{
    $student_id ="";
    include_once('secure_school.php');
    include_once('schoolheader.php');
}
include_once('footer.php');*/
include_once('template.class.php');
$receipt_data = get_receipt_data($mysqli,$_GET['activity_reg_id']);
$student_name = $receipt_data['FIRST_NAME']." ".$receipt_data['LAST_NAME'];
$student_address = $receipt_data['ADDRESS'];
$contact_no = $receipt_data['MOBILE'];
$emailid = $receipt_data['EMAIL'];
$payment_date = date('d-m-Y',$receipt_data['UPDATED_ON']);
$class = $receipt_data['NAME'];
$roll_number = $receipt_data['ROLL_NUMBER'];
$Description = $receipt_data['ACTIVITY_TYPE'];
$Amount = $receipt_data['AMOUNT']-$receipt_data['CHARGES'];
$service_charges = $receipt_data['CHARGES'];
$AMOUNT = $receipt_data['AMOUNT'];
if($receipt_data['PAYMENT_STATUS']== 'Y'){
    $Paid_status = "Paid";
}else{
    $Paid_status = "Unpaid";
}
$payment_ref = $receipt_data['APTRANSACTIONID'];
$template = new Template;
$template->load("templates/registeration_receipt.html");
$template->replace('student_name', $student_name);
$template->replace("student_address",$student_address);
$template->replace("contact_no",$contact_no);
$template->replace("emailid",$emailid);
$template->replace("payment_date",$payment_date);
$template->replace('class', $class);
$template->replace('school_logo', $school_logo);
$template->replace('school_url', $school_url);
$template->replace('school_name', $school_name);
$template->replace('roll_number', $roll_number);
$template->replace('Description', $Description);
$template->replace('Amount', $Amount);
$template->replace('service_charges', $service_charges);
$template->replace('AMOUNT', $AMOUNT);
$template->replace('Paid_status', $Paid_status);
$template->replace('payment_ref', $payment_ref);
$template->publish();
?>