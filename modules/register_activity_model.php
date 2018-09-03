<?php 

function get_activity_records($mysqli,$school_id,$offset,$search){
    if($search != "null"){
    $filter = (json_decode($search,true));
    }
    // $query = "SELECT * FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." AND rm.ACTIVITY_START_DATE LIKE '%".strtotime($filter['st_date'])."%' AND rm.ACTIVITY_END_DATE LIKE '%".strtotime($filter['ed_date'])."%' AND a.ACTIVITY_NAME LIKE '%".$filter['activity_name']."%' Group By a.ACTIVITY_NAME LIMIT ".$offset.", 2";
    // $query = "SELECT * FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." Group By a.ACTIVITY_NAME LIMIT ".$offset.", 2";
    $query = "SELECT *,a.ACTIVITY_TYPE FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity_types as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." AND rm.ACTIVITY_START_DATE LIKE '%".strtotime($filter['st_date'])."%' AND rm.ACTIVITY_END_DATE LIKE '%".strtotime($filter['ed_date'])."%' AND a.ACTIVITY_TYPE LIKE '%".$filter['ACTIVITY_TYPE']."%' LIMIT ".$offset.", 2";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res;
    }else{
        return false;    
    }
}

function get_count($mysqli,$school_id,$search){
    if($search != "null"){
        $filter = (json_decode($search,true));
        }
    $query = "SELECT COUNT(*)  as total FROM (SELECT a.`ACTIVITY_TYPE` FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity_types as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." AND rm.ACTIVITY_START_DATE LIKE '%".strtotime($filter['st_date'])."%' AND rm.ACTIVITY_END_DATE LIKE '%".strtotime($filter['ed_date'])."%' AND a.ACTIVITY_TYPE LIKE '%".$filter['activity_name']."%') AS C";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res->fetch_assoc();
    }else{
        return false;    
    }
}

function get_student_details($mysqli,$school_login_id,$student_id){
    $query = "SELECT *,sg.NAME as father_name FROM  tbl_student as s JOIN tbl_student_guardian as sg ON s.STUDENT_ID=sg.STUDENT_ID WHERE s.STUDENT_LOGIN_ID='".$school_login_id."' AND s.STUDENT_ID='".$student_id."' AND sg.GUARDIAN_TYPE='1'"; 
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res->fetch_assoc();
    }else{
        return false;    
    }
}

function get_student_details_by_roll_no($mysqli,$roll_no,$school_id){
    $query = "SELECT *,sg.NAME as father_name FROM  tbl_student as s JOIN tbl_student_guardian as sg ON s.STUDENT_ID=sg.STUDENT_ID WHERE s.ROLL_NUMBER='".$roll_no."' AND s.SCHOOL_ID='".$school_id."' AND sg.GUARDIAN_TYPE='1'"; 
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res->fetch_assoc();
    }else{
        return false;    
    }
}

function get_activities($mysqli,$activity_id,$student_class){
    $query = "SELECT * FROM tbl_rj_activity WHERE ACTIVITY_TYPE_ID='".$activity_id."' AND CLASS_ID='".$student_class."'"; 
    $Res = $mysqli->query($query);
    $data_array = array();
    if($Res->num_rows>0){
        while($row=$Res->fetch_assoc()){
            $data_array[]=$row;
        }
        return $data_array;
    }else{
        return false;    
    }
}

function get_airpay_details($mysqli,$activity_type_id){
    $query = "SELECT * FROM tbl_rj_activity_school_mapping WHERE ACTIVITY_TYPE_ID='".$activity_type_id."'"; 
    $Res = $mysqli->query($query);
    $data_array = array();
    if($Res->num_rows>0){
        return $Res->fetch_assoc();
    }else{
        return false;    
    }
}

function add_register_data($mysqli,$register_data,$school_id,$meal){
    $query = "INSERT into tbl_rj_activity_reservation (STUDENT_ID,SCHOOL,AMOUNT,CHMOD,EMAIL,MOBILE,PAYMENT_STATUS,ACTIVITY_TYPE_ID,MEAL,CHARGES) VALUES ('".$register_data['student_id']."','".$school_id."','".$register_data['total_amount']."','".$register_data['payoption']."','".$register_data['email']."','".$register_data['mob_no']."','N','".$register_data['activity_type_id']."','".$meal."','".$register_data['charge']."')";
        if($mysqli->query($query) === TRUE){
            return $mysqli->insert_id;
        }else{
            return false;    
        }
}

function add_register_data_map($mysqli,$activity_id,$register_id){
    foreach($activity_id as $_k=>$_v){
        $query = "INSERT into tbl_rj_activity_reservation_mapping (RESERVATION_ID,ACTIVITY_ID) VALUES ('".$register_id."','".$_v."')";
            if($mysqli->query($query) === TRUE){
                $inserted_id[] = $mysqli->insert_id;
                continue;
            }else{
                return false;    
            }
        }
    return true;
}

function transaction_status($mysqli,$transaction_details,$status){
    $query = "UPDATE tbl_rj_activity_reservation set APTRANSACTIONID='".$transaction_details['APTRANSACTIONID']."', TRANSACTIONSTATUS='".$transaction_details['TRANSACTIONSTATUS']."', MESSAGE='".$transaction_details['MESSAGE']."', PAYMENT_STATUS='".$status."', CUSTOM_DATA='".$transaction_details['CUSTOMVAR']."', ap_SecureHash='".$transaction_details['ap_SecureHash']."' WHERE RESERVATION_ID='".$transaction_details['TRANSACTIONID']."'";
    if($mysqli->query($query) === TRUE){
        return true;
    } else {
        return false;
    }
}
?>