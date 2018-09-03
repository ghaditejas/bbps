<?php
function get_report_data($mysqli,$school_id,$id,$offset,$search){
    $query = "SELECT ar.MOBILE,ar.PAYMENT_STATUS,ar.EMAIL,ar.AMOUNT,ar.CHMOD,ar.RESERVATION_ID,t.ACTIVITY_TYPE,s.ROLL_NUMBER,s.CLASS_ID,s.DIVISION_ID,sc.NAME,sd.DIVISION FROM tbl_rj_activity_reservation as ar JOIN tbl_rj_activity_types as t on ar.ACTIVITY_TYPE_ID = t.ACTIVITY_TYPE_ID JOIN tbl_student as s on s.STUDENT_ID=ar.STUDENT_ID JOIN tbl_school_class as sc on s.CLASS_ID=sc.CLASS_ID JOIN tbl_school_division as sd on s.DIVISION_ID = sd.DIVISION_ID WHERE ar.SCHOOL='".$school_id."' AND s.SCHOOL_ID='".$school_id."'";
    if($id!=""){
        $query = $query . "AND ar.STUDENT_ID='".$id."'"; 
    }
    if($search != "null"){
        $filter = (json_decode($search,true));
        // $query = $query . "AND s.FIRST_NAME LIKE '%".$filter['fname']."%' AND s.LAST_NAME LIKE '%".$filter['lname']."%' AND t.ACTIVITY_TYPE LIKE '%".$filter['activity_name']."%'";
        $query = $query . "AND t.ACTIVITY_TYPE LIKE '%".$filter['activity_name']."%'";
        if($filter['paytype']!=""){
            $query = $query . " AND ar.PAYMENT_STATUS ='".$filter['paytype']."'";
        }
        if($filter['selclass']!= ""){
            $query = $query . " AND s.CLASS_ID ='".$filter['selclass']."'";
        }
        if($filter['seldiv']!= ""){
            $query = $query . " AND s.DIVISION_ID ='".$filter['seldiv']."'";
        }
    }
    $query = $query . "LIMIT ".$offset.", 2";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res;
    }else{
        return false;    
    }
}

function get_report_data_count($mysqli,$school_id,$id,$search){
    $query = "SELECT COUNT(ar.RESERVATION_ID) as total FROM tbl_rj_activity_reservation as ar JOIN tbl_rj_activity_types as t on ar.ACTIVITY_TYPE_ID = t.ACTIVITY_TYPE_ID JOIN tbl_student as s on s.STUDENT_ID=ar.STUDENT_ID JOIN tbl_school_class as sc on s.CLASS_ID=sc.CLASS_ID JOIN tbl_school_division as sd on s.DIVISION_ID = sd.DIVISION_ID WHERE ar.SCHOOL='".$school_id."' AND s.SCHOOL_ID='".$school_id."'";
    if($id!=""){
        $query = $query . "AND ar.STUDENT_ID='".$id."'"; 
    }
    if($search != "null"){
        $filter = (json_decode($search,true));
        // $query = $query . "AND s.FIRST_NAME LIKE '%".$filter['fname']."%' AND s.LAST_NAME LIKE '%".$filter['lname']."%' AND t.ACTIVITY_TYPE LIKE '%".$filter['activity_name']."%'";
        $query = $query . "AND t.ACTIVITY_TYPE LIKE '%".$filter['activity_name']."%'";
        if($filter['paytype']!=""){
            $query = $query . " AND ar.PAYMENT_STATUS ='".$filter['paytype']."'";
        }
        if($filter['selclass']!= ""){
            $query = $query . " AND s.CLASS_ID ='".$filter['selclass']."'";
        }
        if($filter['seldiv']!= ""){
            $query = $query . " AND s.DIVISION_ID ='".$filter['seldiv']."'";
        }
    }
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res->fetch_assoc();
    }else{
        return false;    
    }
}

function get_classes($mysqli,$school_id){
    $query="SELECT CLASS_ID,NAME from  tbl_school_class where SCHOOL_ID ='".$school_id."'";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res;
    }else{
        return false;    
    }
}

function get_division($mysqli,$school_id){
    $query="SELECT DIVISION_ID,DIVISION from  tbl_school_division where SCHOOL_ID ='".$school_id."'";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res;
    }else{
        return false;    
    }
}

function get_receipt_data($mysqli,$registeration_id){
    $query = "SELECT ar.MOBILE,ar.PAYMENT_STATUS,ar.EMAIL,ar.AMOUNT,ar.CHARGES,ar.CHMOD,ar.UPDATED_ON,ar.RESERVATION_ID,t.ACTIVITY_TYPE,s.FIRST_NAME,s.LAST_NAME,s.ADDRESS,s.ROLL_NUMBER,s.CLASS_ID,s.DIVISION_ID,sc.NAME,sd.DIVISION FROM tbl_rj_activity_reservation as ar JOIN tbl_rj_activity_types as t on ar.ACTIVITY_TYPE_ID = t.ACTIVITY_TYPE_ID JOIN tbl_student as s on s.STUDENT_ID=ar.STUDENT_ID JOIN tbl_school_class as sc on s.CLASS_ID=sc.CLASS_ID JOIN tbl_school_division as sd on s.DIVISION_ID = sd.DIVISION_ID WHERE ar.RESERVATION_ID='".$registeration_id."'";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res->fetch_assoc();
    }else{
        return false;    
    }
}

function get_registeration_activity($mysqli,$registeration_id){
    $query = "SELECT s.ACTIVITY_NAME,s.FEES FROM tbl_rj_activity_reservation as ar JOIN tbl_rj_activity_reservation_mapping as t on ar.RESERVATION_ID = t.RESERVATION_ID JOIN tbl_rj_activity as s on s.ACTIVITY_ID=t.ACTIVITY_ID JOIN tbl_school_class as sc on s.CLASS_ID=sc.CLASS_ID WHERE ar.RESERVATION_ID='".$registeration_id."'";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res;
    }else{
        return false;    
    }
}
?>