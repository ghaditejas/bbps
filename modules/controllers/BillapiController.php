<?php

namespace app\modules\controllers;

use Yii;
use yii\web\UploadedFile;
use app\helpers\Checksum;
use yii\base\Hcontroller;
use app\modules\models\TblUtility;
use app\modules\models\TblProvider;
use app\modules\models\TblProviderBillUploadDetails;
use app\modules\models\TblProviderInvoice;
use app\modules\models\TblProviderBillDetails;
use app\modules\models\TblInvoiceBillDetails;
use app\modules\models\TblTranscationDetails;

class BillapiController extends Hcontroller
{
  public $enableCsrfValidation = false;
  public function actionIndex()
  {
    return $this->render('index');
  }
  
  public function writeLog($mid, $data) {
    $filepath = realpath(Yii::$app->basePath)."/modules/resources/log/";
    $filename = $filepath . $mid . '.log';
    if (!file_exists($filename)) 
    {
      mkdir($filename, 0777, true);
    }
    $data = $data.PHP_EOL;
    $log_file_data = $filename.'/log_' . date('d-M-Y') . '.log';
    file_put_contents($log_file_data, $data , FILE_APPEND);
  }

  public function actionAccount_register_response(){
    $post = Yii::$app->request->rawBody;
    $data2 = json_decode($post);
    $log_data = "REGISTER DATA RESPONSE : ".$post;
    $this->writeLog("Log_Data",$log_data);
    $model= new TblProviderBillDetails();
    $connection = Yii::$app->db; 
    $query="SELECT p.AIRPAY_MERCHANT_ID,p.AIRPAY_USERNAME,p.AIRPAY_PASSWORD,p.AIRPAY_SECRET_KEY from tbl_partner_master as p JOIN tbl_user_master as u ON p.PARTNER_ID = u.PARTNER_ID WHERE u.USER_ID=:user_id";
    $config = $connection
    ->createCommand($query);
    $config->bindValue(':user_id',$data->CUSTOMERID);
    $config_data = $config->queryAll();
    $chk = new Checksum();
    $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
    $checksum = md5($data2->BBPS_REGISTER_RES_ID."~".$data2->BILLERACCOUNTID."~".$data2->USER_ID."~".$data2->CUSTOMER_ID."~".$data2->REFERENCE_NUMBER);
    if(($privatekey !== $data2->PRIVATEKEY || $checksum != $data2->CHECKSUM) && $data->STATUS == 200){
      return json_encode(['status'=>400,"message"=>"Error in Authentication"]);
    } else {
      $get_provider = $connection->createCommand('Select PROVIDER_ID from tbl_provider_bill_details where ACCOUNT_NO=:account_no AND PROVIDER_BILL_DETAILS_ID=:provider_bill_details_id');
      $get_provider->bindValue(':account_no',$data2->ACCOUNTID);
      $get_provider->bindValue(':provider_bill_details_id',$data2->REQUESTNUMBER);
      $get_provider_data =  $get_provider->queryAll();
      if($data2->STATUS == 200){
        $status = $connection->createCommand()
        ->update('tbl_registered_account', ['REF_NO'=>$data2->BILLERACCOUNTID,'IS_REGISTERED'=>1], 'ACCOUNT_NO='.$data2->ACCOUNTID.' AND PROVIDE_ID='.$get_provider_data[0]['PROVIDER_ID'])
        ->execute();
        if($status){
          return json_encode(['status'=>200,"message"=>"UPLOADED SUCCESSFULLY"]);
        } else {
          return json_encode(['status'=>400,"message"=>"ERROR IN UPLOADING"]);
        }
      }else {
        $status = $connection->createCommand()
        ->update('tbl_registered_account', ['REF_NO'=>'-','IS_REGISTERED'=>1], 'ACCOUNT_NO='.$data2->ACCOUNTID.' AND PROVIDE_ID='.$get_provider_data[0]['PROVIDER_ID'])
        ->execute();
        $update_tbl_provider = $connection->createCommand()
        ->update('tbl_provider_bill_details', ['PAYMENT_STATUS'=>'failed','RESPONSE_NOT_RECIEVED'=>1], 'ACCOUNT_NO='.$data2->ACCOUNTID.' AND PROVIDER_ID='.$get_provider_data[0]['PROVIDER_ID'])
        ->execute();
        if($status && $update_tbl_provider){
          return json_encode(['status'=>200,"message"=>"UPLOADED SUCCESSFULLY"]);
        } else {
          return json_encode(['status'=>400,"message"=>"ERROR IN UPLOADING"]);
        }
      }
    }
  }
  
  public function actionBill_data_response(){
    $post = Yii::$app->request->rawBody;
    $log_data = "Bill DATA RESPONSE : ".$post;
    $this->writeLog("Log_Data",$log_data);
    $data2 = json_decode($post);
    $connection = Yii::$app->db;
    $query="SELECT p.AIRPAY_MERCHANT_ID,p.AIRPAY_USERNAME,p.AIRPAY_PASSWORD,p.AIRPAY_SECRET_KEY from tbl_partner_master as p JOIN tbl_user_master as u ON p.PARTNER_ID = u.PARTNER_ID WHERE u.USER_ID=:user_id";
    $config = $connection
    ->createCommand($query);
    $config->bindValue(':user_id',$data2->CUSTOMER_ID);
    $config_data = $config->queryAll();
    $chk = new Checksum();
    $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
    $checksum = md5($data2->USER_ID."~".$data2->CUSTOMER_ID."~".$data2->ACCOUNTID."~".$data2->BILLAMOUNT."~".$data2->BILLID."~".$data2->BILLDUEDATE."~".$data2->BILLNUMBER."~".$data2->BILLERNAME."~".$data2->REGISTERID."~".$data2->BILLRSPID."~".$data2->REQUESTNUMBER);
    if($privatekey != $data2->PRIVATEKEY || $checksum != $data2->CHECKSUM){
      return json_encode(['status'=>400,"message"=>"Error in Authentication"]);
    } else {
      $model= new TblProviderBillDetails();
      $connection = Yii::$app->db;  
      $connection->createCommand()
      ->update('tbl_provider_bill_details', ['DUE_DATE'=>date('Y-m-d H:i:s',strtotime($data2->BILLDUEDATE)),'AMOUNT'=>$data2->BILLAMOUNT,'REF_NO'=>$data2->REGISTERID,'BANK_BILL_ID'=>$data2->BILLID,'BILL_NUMBER'=>$data2->BILLNUMBER,'BILL_ID'=>$data2->BILLRSPID,'RESPONSE_NOT_RECIEVED'=>0], 'ACCOUNT_NO='.$data2->ACCOUNTID.' AND PROVIDER_BILL_DETAILS_ID='.$data2->REQUESTNUMBER)
      ->execute();
      $msg=$this->notification($data2->Invoice_no);
      if(isset($msg)){
        return json_encode(['status'=>200,"message"=>"UPLOADED SUCCESSFULLY"]);
      } else {
        return json_encode(['status'=>400,"message"=>"ERROR IN UPLOADING"]);
      }
    }
  }
  
  public function actionPaymentstatus(){
    $post = Yii::$app->request->rawBody;
    $log_data = "MAKE PAYMENT DATA RESPONSE : ".$post;
    $this->writeLog("Log_Data",$log_data);
    $data2 = json_decode($post);
    $posted_checksum = $data2->CHECKSUM;
    unset($data2->CHECKSUM);
    $checksum = md5(json_encode($data2));
    if($posted_checksum != $checksum){
      $this->writeLog("Log_Data","CHECKSUM ERROR");
      return json_encode(['status'=>400,"message"=>"Error in Authentication"]);
    } else {
      if($data2->STATUS == 'Y'){
        $status = "success";
      } else if($data2->STATUS == 'N') {
        $status = "fail";
      } else {
        $status = "pending";
      }
      $connection = Yii::$app->db;  
      $update_status = $connection->createCommand()
      ->update('tbl_provider_bill_details', ['PAYMENT_STATUS'=>$status,'BANK_REF_PAYMENT_NUMBER'=>$data2->BANKREFNUMBER], 'ACCOUNT_NO='.$data2->AUTHENTICATOR.' AND BILL_ID='.$data2->VIEW_BILL_RSP_ID)
      ->execute();
      if($data2->STATUS == 'N'){
        $query2 = "INSERT into tbl_provider_bill_details (PROVIDER_BILL_UPLOAD_DETAILS_ID,PROVIDER_ID,REF_NO,REGISTER_BILLER_FLAG,REMOVED,IS_REGISTER,AMOUNT,UTILITY_ID,USER_ID,RESPONSE_NOT_RECIEVED,ACCOUNT_NO,DETAILS,BANK_BILL_ID,BILL_NUMBER,BILL_ID,DUE_DATE,FNAME,LNAME,EMAIL)  SELECT PROVIDER_BILL_UPLOAD_DETAILS_ID,PROVIDER_ID,REF_NO,REGISTER_BILLER_FLAG,REMOVED,IS_REGISTER,AMOUNT,UTILITY_ID,USER_ID,RESPONSE_NOT_RECIEVED,ACCOUNT_NO,DETAILS,BANK_BILL_ID,BILL_NUMBER,BILL_ID,DUE_DATE,FNAME,LNAME,EMAIL FROM tbl_provider_bill_details WHERE ACCOUNT_NO=:account_no AND BILL_ID =:bill_id";
        $insert_fail_account =  $connection->createCommand($query2);
        $insert_fail_account->bindValue(':account_no',$data2->AUTHENTICATOR);
        $insert_fail_account->bindValue(':bill_id',$data2->VIEW_BILL_RSP_ID);
        $insert_fail_account_data = $insert_fail_account->execute();
      }
      if($update_status){
        return json_encode(['status'=>200,"message"=>"UPLOADED SUCCESSFULLY"]);
      } else{
        return json_encode(['status'=>400,"message"=>"ERROR IN UPLOADING"]);
      }
    }
  }
}
