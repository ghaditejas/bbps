<?php 
function get_activity_records($mysqli,$school_id,$offset,$search){
    if($search != "null"){
    $filter = (json_decode($search,true));
    }
    $query = "SELECT * FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." AND rm.ACTIVITY_START_DATE LIKE '%".strtotime($filter['st_date'])."%' AND rm.ACTIVITY_END_DATE LIKE '%".strtotime($filter['ed_date'])."%' AND a.ACTIVITY_NAME LIKE '%".$filter['activity_name']."%' Group By a.ACTIVITY_NAME LIMIT ".$offset.", 2";
    // $query = "SELECT * FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." Group By a.ACTIVITY_NAME LIMIT ".$offset.", 2";
    // $query = "SELECT *,a.ACTIVITY_TYPE FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity_types as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." AND rm.ACTIVITY_START_DATE LIKE '%".strtotime($filter['st_date'])."%' AND rm.ACTIVITY_END_DATE LIKE '%".strtotime($filter['ed_date'])."%' AND a.ACTIVITY_TYPE LIKE '%".$filter['ACTIVITY_TYPE']."%' LIMIT ".$offset.", 2";
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
    $query = "SELECT COUNT(*)  as total FROM (SELECT a.`ACTIVITY_NAME` FROM `tbl_rj_activity_school_mapping` as rm Join tbl_rj_activity as a on rm.ACTIVITY_TYPE_ID = a.ACTIVITY_TYPE_ID where rm.`SCHOOL_ID`= ".$school_id." AND rm.ACTIVITY_START_DATE LIKE '%".strtotime($filter['st_date'])."%' AND rm.ACTIVITY_END_DATE LIKE '%".strtotime($filter['ed_date'])."%' AND a.ACTIVITY_NAME LIKE '%".$filter['activity_name']."%' Group By a.ACTIVITY_NAME) AS C";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res->fetch_assoc();
    }else{
        return false;    
    }
}

function add_activity_type($mysqli,$activity_type_name){
    $query="INSERT into tbl_rj_activity_types (ACTIVITY_TYPE) VALUES ('".$activity_type_name."')";
    if($mysqli->query($query) === TRUE){
        $activity_type_id = $mysqli->insert_id;
        return $activity_type_id;
    } else {
        return false;
    }
}

function add_activity($mysqli,$activity_type_id,$data){
    $query = "";
    foreach ($data['activity_name'] as $_k=>$_v){
        foreach($data['selclass'][$_k] as $_k1=>$_v2){
           $query = "INSERT into tbl_rj_activity (ACTIVITY_TYPE_ID,CLASS_ID,ACTIVITY_NAME,FEES,CREATED_ON) VALUES  ('".$activity_type_id."','".$data['selclass'][$_k][$_k1]."','".$data['activity_name'][$_k]."','".$data['fees'][$_k]."',".time().")";
           if($mysqli->query($query) === TRUE){
             continue;
            } else {
             return false;
            } 
        }
    }
    return true;
}

function add_activity_school_map($mysqli,$activity_type_id,$data,$config){
    if(isset($activity_type_id['pos_req'])){
        $pos_required = 1;
    }else{
        $pos_required = 0;
    }
    $query="INSERT into tbl_rj_activity_school_mapping (ACTIVITY_TYPE_ID,SCHOOL_ID,SCHOOL_YEAR_ID,ACTIVITY_CODE,ACTIVITY_START_DATE,ACTIVITY_END_DATE,CREATED_ON,MEAL_OPTION,EMAIL_CC,EMAIL_CONTENT,SMS_CONTENT,POS_REQUIRED,POS_UNIQUE_ID) VALUES ('".$activity_type_id."','".$config['school_details']['SCHOOL_ID']."','".$data['school_year_id']."','".$data['code']."','".strtotime($data['start_date'])."','".strtotime($data['end_date'])."','".time()."','".$data['meal_show']."','".$data['email_cc']."','".$data['email_content']."','".$data['sms_content']."','".$pos_required."','".$data['pos_unique_id']."')";
    if($mysqli->query($query) == TRUE){
        $activity_type_school_mapping_id = $mysqli->insert_id;;
        return $activity_type_school_mapping_id;
    } else {
        return false;
    }
}

function get_school_year($mysqli,$school_id){
    $query="SELECT SCHOOL_YEAR_ID,SCHOOL_YEAR from  tbl_school_year where SCHOOL_ID ='".$school_id."' AND CREATED_ON=(SELECT MAX(CREATED_ON) FROM tbl_school_year WHere SCHOOL_ID ='".$school_id."')";
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        $row = $Res->fetch_assoc();
        return $row;
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

function get_activity($mysqli,$activity_type_id){
    // $query = 'SELECT CLASS_ID,ACTIVITY_NAME,FEES from tbl_rj_activity where ACTIVITY_TYPE_ID = "'.$activity_type_id.'"';
    $query = 'SELECT CLASS_ID,ACTIVITY_NAME,FEES from tbl_rj_activity where ACTIVITY_TYPE_ID = "'.$activity_type_id.'" GROUP BY ACTIVITY_NAME';
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        return $Res;
    }else{
        return false;    
    }
}

function get_activity_type($mysqli,$activity_type_id){
    $query = 'SELECT *,rt.ACTIVITY_TYPE from tbl_rj_activity_school_mapping as rs JOIN tbl_rj_activity_types as rt on rs.ACTIVITY_TYPE_ID =rt.ACTIVITY_TYPE_ID where rs.ACTIVITY_TYPE_ID = "'.$activity_type_id.'"';
    $Res = $mysqli->query($query);
    if($Res->num_rows>0){
        $row = $Res->fetch_assoc();
        return $row;
    }else{
        return false;    
    }
}

function update_activity_map($mysqli,$activity_type_data){
    $query = 'UPDATE tbl_rj_activity_school_mapping SET ACTIVITY_END_DATE='.strtotime($activity_type_data["end_date"]).',UPDATED_ON='.time().' WHERE ACTIVITY_TYPE_ID="'.$activity_type_data["activity_type_id"].'"' ;
    if($mysqli->query($query) == TRUE){
        return true;
    } else {
        return false;
    }
}
?>