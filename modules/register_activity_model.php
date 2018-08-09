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
?>