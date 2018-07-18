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
use yii\validators\EmailValidator;
use yii\validators\RequiredValidator;
use kartik\mpdf\Pdf;

class DefaultController extends HController
{
  public $enableCsrfValidation = false;
  public function init()
  {
    parent::init();
    $data=Yii::$app->user->identity;
    if(!($data['USER_ID']) || $data['PARTNER_ID']!=23){
      $this->redirect('/partnerpay/web');
    }
  }
  
  public function actionIndex(){
    $data=Yii::$app->user->identity;
    $connection = Yii::$app->db;
    $query="SELECT count(distinct(`INVOICE_ID`)) as PAID FROM `tbl_provider_bill_details` WHERE `PAYMENT_STATUS`!= '' AND USER_ID=:user_id";
    $paid = $connection
    ->createCommand($query);
    $paid->bindValue(':user_id',$data['USER_ID']);
    $paid_count = $paid->queryAll();
    
    $query1="SELECT count(distinct(`INVOICE_ID`)) as UNPAID FROM `tbl_provider_bill_details` WHERE `PAYMENT_STATUS`= '' AND INVOICE_ID != 0 AND USER_ID=:user_id";
    $unpaid = $connection
    ->createCommand($query1);
    $unpaid->bindValue(':user_id',$data['USER_ID']);
    $unpaid_count = $unpaid->queryAll();
    
    $query2="SELECT count(distinct(b.INVOICE_ID)) as INVOICE,u.utility_name FROM `tbl_provider_bill_details` as b JOIN tbl_utility as u ON b.`UTILITY_ID` = u.utility_id WHERE b.USER_ID=:user_id AND INVOICE_ID != 0 GROUP BY b.UTILITY_ID";
    $total_invoice = $connection
    ->createCommand($query2);
    $total_invoice->bindValue(':user_id',$data['USER_ID']);
    $total_invoice_utility = $total_invoice->queryAll();
    return $this->render('dashboard',array('paid'=>$paid_count[0]['PAID'],'unpaid'=>$unpaid_count[0]['UNPAID'],'invoices'=>$total_invoice_utility));
  }
  
  public function actionBiller()
  {
    $wallet_data_response = $this->get_wallet_balance();
    $utilities = TblUtility::find()->all();
    return $this->render('index',array('utilities'=>$utilities,'wallet_balance'=>$wallet_data_response['TRANSACTION']['WALLETBALANCE']));
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
  
  public function actionProviders(){
    $id=Yii::$app->request->post('utility_id');
    if($id){
      $providers = TblProvider::find()
      ->where(['utility_id' => $id ])
      ->andWhere(['is_disabled' => 'n'])
      ->all();
      $providers_list=array();
      $provider_data=array();
      foreach($providers as $key=>$value){
        $provider_data['id']=$value->BILLER_MASTER_ID;
        $provider_data['name']=$value->provider_name;
        $providers_list[]=$provider_data;
      }
      echo json_encode($providers_list);
    } else {
      echo "not found";
    }
  }
  
  public function actionPaying(){
    $fields = json_decode($this->actionGet_fields(Yii::$app->request->post('providers')),true);
    $error = array(); 
    // $upload_error = array();
    if($_FILES['bulk_upload']['tmp_name']){
      $uploadedFile_data = $this->upload();
      if($uploadedFile_data){
        if(Yii::$app->request->post('register')){
          // $ref_no=$this->archieve_data();
        }
        $bill_details=array();
        $data=array();
        $fields_data=array();
        $handle = fopen( Yii::$app->getBasePath()."/modules/resources/upload/".$uploadedFile_data['file_name'], "r");
        fgetcsv($handle);
        if(fgetcsv($handle, 1024, ",")){
          $new_handle = fopen( Yii::$app->getBasePath()."/modules/resources/upload/".$uploadedFile_data['file_name'], "r");
          fgetcsv($new_handle);
          while (($fileop = fgetcsv($new_handle, 1024, ",")) !== false) 
          {
            if(!preg_match('#^[A-Z]+$#i',$fileop[0]) || !preg_match('#^[A-Z]+$#i',$fileop[1]) || !filter_var($fileop[2], FILTER_VALIDATE_EMAIL) || $this->check_dynamic_validation($fileop)){
              $error = $fileop;
              $error[] = "Could not Import Details Has Validation Error";
              $upload_error[] = $error;
            }else if($this->check_account_id($fileop[3])){
              $error = $fileop;
              $error[] = "Account id already exist";
              $upload_error[] = $error;
            } else {
              $i=1;
              $data['fname']=$fileop[0];
              $data['lname']=$fileop[1];
              $data['email']=$fileop[2];
              $data['account_id']= $fileop[3];
              for($i=1;$i<sizeof($fields);$i++){
                $fields_data[$fields[$i]]=$fileop[$i+3];
              }
              $data['details'] = json_encode($fields_data);
              $data['billerid'] = Yii::$app->request->post('providers');
              $data['remark'] = Yii::$app->request->post('utility_name');
              $data['requestid'] = $this->bill_details($uploadedFile_data,$data);
              $bill_details[]=$data;
            }
          }
          $template="data_uploaded";
        } else {
          Yii::$app->getSession()->setFlash('error', "Empty File Uploaded");
          $wallet_data_response = $this->get_wallet_balance();
          return $this->render('index',array('utilities'=>TblUtility::find()->all(),'wallet_balance'=>$wallet_data_response['TRANSACTION']['WALLETBALANCE']));
        }
      } else{
        Yii::$app->getSession()->setFlash('error', "Error while uploading file");
        $wallet_data_response = $this->get_wallet_balance();
        return $this->render('index',array('utilities'=>TblUtility::find()->all(),'wallet_balance'=>$wallet_data_response['TRANSACTION']['WALLETBALANCE']));
      }
    } else {
      if(Yii::$app->request->post('register')){
        // $ref_no=$this->archieve_data();
      }
      $bill_details=array();
      $data=array();
      //$invoice_id = $this->invoice_create();
      $data['account_id']=Yii::$app->request->post(str_replace(' ','_',$fields[0]));
      if($this->check_account_id($data['account_id'])){
        Yii::$app->getSession()->setFlash('error', "Account Id already exist");
        return $this->render('index',array('utilities'=>TblUtility::find()->all()));
      }
      for($i=1;$i<sizeof($fields);$i++){
        $fields_data[$fields[$i]]=Yii::$app->request->post(str_replace(' ','_',$fields[$i]));
      }
      $data['fname']=Yii::$app->request->post('fname');
      $data['lname']=Yii::$app->request->post('lname');
      $data['email']=Yii::$app->request->post('email');
      $data['details'] = json_encode($fields_data);
      $data['billerid'] = Yii::$app->request->post('providers');
      $data['remark'] = Yii::$app->request->post('utility_name');
      $data['requestid'] = $this->bill_details(0,$data);
      $bill_details[]=$data;
      $template="data_uploaded";
    }
    $data=Yii::$app->user->identity;
    $connection = Yii::$app->db;
    $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
    $config = $connection
    ->createCommand($query);
    $config->bindValue(':partner_id',$data['PARTNER_ID']);
    $config_data = $config->queryAll();
    $chk = new Checksum();
    $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
    $checksum = md5($config_data[0]['AIRPAY_MERCHANT_ID']."~".$data['USER_ID']."~192.168.1.184/partnerpay/web/bbps/default/bill_data_response~192.168.1.184/partnerpay/web/bbps/default/account_register_response");
    $apidata=[
      'mercid'=>$config_data[0]['AIRPAY_MERCHANT_ID'],
      "customerid"=>$data['USER_ID'],
      'privatekey'=>$privatekey,
      'callbackurl'=>'192.168.1.184/partnerpay/web/bbps/billapi/bill_data_response',
      "returnurl"=>'192.168.1.184/partnerpay/web/bbps/billapi/account_register_response',
      "action"=>"ADD_BILLER",
      'checksum'=>$checksum,
      'bill_data'=>$bill_details,
    ];
    $api_data = json_encode($apidata);
    $url='https://devel-payments.airpayme.com/bbps/add_biller.php';
    $response= $this->api_call($url,$api_data);
    $log_data="ADD BILLER API RESPONSE : ".json_encode($response);
    $this->writeLog("Log_Data",$log_data);
    if($response->STATUS=="200"){
      return $this->render($template,array('provider'=>Yii::$app->request->post('providers'),'upload_error'=>json_encode($upload_error)));
    } else {
      return $this->render($template,array('provider'=>Yii::$app->request->post('providers'),'upload_error'=>json_encode($upload_error)));
    }
  }
  
  public function check_dynamic_validation($fileop){
    $validation = json_decode($this->actionGet_fields(Yii::$app->request->post('providers'),'validation'),true);
    for ($i=0;$i<sizeof($validation);$i++){
      if(preg_match('/'.$validation[$i].'/', $fileop[$i+3])){
        continue;
      } else {
        return true;
      }
    }
  }
  
  public function upload(){
    $uploadOk = 1;
    $target_dir = Yii::$app->getBasePath()."/modules/resources/upload/";
    $ext = pathinfo($_FILES["bulk_upload"]["name"], PATHINFO_EXTENSION);
    if($ext != "csv"){
      $uploadOk=0;
    }
    $new_name = time().'_'.Yii::$app->request->post('providers').'_'.Yii::$app->request->post('utility_name').'.'.$ext;
    $target_file = $target_dir.$new_name;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if ($uploadOk == 0) {
      Yii::$app->getSession()->setFlash('error', "Error while uploading file");
      return $this->render('index',array('utilities'=>TblUtility::find()->all()));
    } else {
      if (move_uploaded_file($_FILES["bulk_upload"]["tmp_name"], $target_file)) {
        
        $model = new TblProviderBillUploadDetails();
        $model->XLS_NAME=$new_name;
        $model->MODIFIED_DATE=date("Y-m-d");
        if($model->save()){
          $data = array();
          $data['file_name']=$new_name;
          $data['inserted_id']=$model->getPrimaryKey();
          return $data;
        } else{
          return false;
        }
      } else {
        return false;
      }
    }
    
  }
  
  public function archieve_data(){
    $data=Yii::$app->user->identity;
    $connection = Yii::$app->db;
    $query1="SELECT * FROM tbl_provider_bill_details WHERE USER_ID=:user_id AND IS_REGISTER=:is_register AND PAYMENT_STATUS !=:payment_status";
    $registered = $connection
    ->createCommand($query1);
    $registered->bindValue(':user_id',$data['USER_ID']);
    $registered->bindValue(':is_register','y');
    $registered->bindValue(':payment_status','');
    $registered_data = $registered->queryAll();
    if(sizeof($registered_data)){
      $query="INSERT into tbl_archived_provider_bill_details SELECT * FROM tbl_provider_bill_details WHERE USER_ID=:user_id AND IS_REGISTER=:is_register AND PAYMENT_STATUS <>:payment_status";
      $archieve = $connection
      ->createCommand($query);
      $archieve->bindValue(':user_id',$data['USER_ID']);
      $archieve->bindValue(':is_register','y');
      $archieve->bindValue(':payment_status','');
      $archieve_data = $archieve->execute();
      if($archieve_data){
        $query2="DELETE FROM tbl_provider_bill_details WHERE USER_ID=:user_id AND IS_REGISTER=:is_register AND PAYMENT_STATUS <>:payment_status";
        $registered = $connection
        ->createCommand($query2);
        $registered->bindValue(':user_id',$data['USER_ID']);
        $registered->bindValue(':is_register','y');
        $registered->bindValue(':payment_status','');
        $registered_data = $registered->execute();
      }
      return $registered_data[0]['REF_NO'];
    } else{
      return "";
    }
  }
  
  public function invoice_create(){
    $model = new TblProviderInvoice();
    $model->STATUS="pending";
    $model->MODIFIED_DATE=date("Y-m-d");
    if($model->save()){
      $invoice_id=$model->getPrimaryKey();
      return $invoice_id;
    }
  }
  
  public function bill_details($uploadedFile_data,$data){
    $model= new TblProviderBillDetails();
    // if(Yii::$app->request->post('register')){
      $model->IS_REGISTER='y';
      $connection = Yii::$app->db;
      $query="SELECT REF_NO from tbl_registered_account where ACCOUNT_NO=:account_no AND PROVIDE_ID=:provider_id AND UTILITY_ID=:utility_id AND IS_REGISTERED=1";
      $check_registered = $connection
      ->createCommand($query);
      $check_registered->bindValue(':account_no',$data['account_id']);
      $check_registered->bindValue(':provider_id',Yii::$app->request->post('providers'));
      $check_registered->bindValue(':utility_id',Yii::$app->request->post('utility_name'));
      $check_registered_data = $check_registered->queryAll();
      if(sizeof($check_registered_data)==0){
        $insert_query="INSERT into tbl_registered_account (UTILITY_ID,PROVIDE_ID,ACCOUNT_NO) VALUES (:utility_id,:provider_id,:account_no)";
        $insert_registered = $connection
        ->createCommand($insert_query);
        $insert_registered->bindValue(':account_no',$data['account_id']);
        $insert_registered->bindValue(':provider_id',Yii::$app->request->post('providers'));
        $insert_registered->bindValue(':utility_id',Yii::$app->request->post('utility_name'));
        $insert_registered_data = $insert_registered->execute();
      }
      // } else {
        //   $model->IS_REGISTER='y';
        // }
        $model->PROVIDER_ID=Yii::$app->request->post('providers');
        $model->UTILITY_ID=Yii::$app->request->post('utility_name');
        if($uploadedFile_data['inserted_id']){
          $model->PROVIDER_BILL_UPLOAD_DETAILS_ID=$uploadedFile_data['inserted_id'];
        }
        $model->ACCOUNT_NO=$data['account_id'];
        $model->DETAILS=$data['details'];
        $model->FNAME = $data['fname'];
        $model->LNAME = $data['lname'];
        $model->EMAIL = $data['email'];
        $user_data=Yii::$app->user->identity;
        $model->USER_ID= $user_data['USER_ID'];
        // $model->INVOICE_ID=$invoice_id;
        if($model->save(false)){
          $billing_details_id=$model->getPrimaryKey();
          return $billing_details_id;
        }
        
      }
      
      public function actionListing($invoice_id=""){
        $connection = Yii::$app->db;
        $query="SELECT utility_id,utility_name from tbl_utility where is_disabled='n'";
        $utility = $connection->createCommand($query);
        $utility_data= $utility->queryAll();
        return  $this->render('listing',array('utility_data'=>$utility_data));
      }  
      
      public function actionPaid_invoice(){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        $all_invoice = $connection
        ->createCommand('Select SUM(AMOUNT) as invoice_amount,b.INVOICE_ID,b.PROVIDER_ID,p.provider_name,b.PAYMENT_STATUS,b.PROVIDER_BILL_DETAILS_ID,u.utility_name from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID JOIN tbl_utility as u on b.UTILITY_ID=u.utility_id where b.UTILITY_ID=:utility_id AND b.PROVIDER_ID=:provider_id AND b.USER_ID=:userid AND b.PAYMENT_STATUS!="" AND INVOICE_ID!=0 GROUP BY INVOICE_ID ORDER BY INVOICE_ID DESC');
        $all_invoice->bindValue(':userid', $data['USER_ID']);
        $all_invoice->bindValue(':provider_id', Yii::$app->request->post('provider_id'));
        $all_invoice->bindValue(':utility_id', Yii::$app->request->post('utility_id'));
        $all_invoice_data = $all_invoice->queryAll();
        echo json_encode($all_invoice_data);
      }
      
      public function actionUnpaid_invoice(){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        $all_unpaid_invoice = $connection
        ->createCommand('Select SUM(AMOUNT) as invoice_amount,b.INVOICE_ID,b.PROVIDER_ID,p.provider_name,b.PAYMENT_STATUS,b.PROVIDER_BILL_DETAILS_ID,u.utility_name from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID JOIN tbl_utility as u on b.UTILITY_ID=u.utility_id where b.UTILITY_ID=:utility_id AND b.PROVIDER_ID=:provider_id AND b.USER_ID=:userid AND b.PAYMENT_STATUS ="" AND INVOICE_ID!=0 GROUP BY INVOICE_ID ORDER BY INVOICE_ID DESC');
        $all_unpaid_invoice->bindValue(':userid', $data['USER_ID']);
        $all_unpaid_invoice->bindValue(':provider_id', Yii::$app->request->post('provider_id'));
        $all_unpaid_invoice->bindValue(':utility_id', Yii::$app->request->post('utility_id'));
        $all_unpaid_invoice_data = $all_unpaid_invoice->queryAll();
        echo json_encode($all_unpaid_invoice_data);
      }
      
      public function actionRegisteration_pending_failed(){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        $registeration_failed = $connection
        ->createCommand('Select b.ACCOUNT_NO,b.PROVIDER_ID,p.provider_name,u.utility_name from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID JOIN tbl_utility as u on b.UTILITY_ID=u.utility_id where b.UTILITY_ID=:utility_id AND b.PROVIDER_ID=:provider_id AND b.USER_ID=:userid AND (b.PAYMENT_STATUS ="fail" OR b.RESPONSE_NOT_RECIEVED=1) ORDER BY PROVIDER_BILL_DETAILS_ID DESC');
        $registeration_failed->bindValue(':userid', $data['USER_ID']);
        $registeration_failed->bindValue(':provider_id', Yii::$app->request->post('provider_id'));
        $registeration_failed->bindValue(':utility_id', Yii::$app->request->post('utility_id'));
        $registeration_failed_data = $registeration_failed->queryAll();
        echo json_encode($registeration_failed_data);
      }
      
      public function actionPayment($invoice_id){
        $connection = Yii::$app->db;
        $invoice = $connection
        ->createCommand("Select b.AMOUNT,b.RESPONSE_NOT_RECIEVED,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,b.DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID where b.INVOICE_ID=:invoice_id AND b.REMOVED='n'");
        $invoice->bindValue(':invoice_id', $invoice_id);
        $invoice_data = $invoice->queryAll();
        $sum = $this->calculate_sum($invoice_data);
        $data=Yii::$app->user->identity;
        $get_charges = $connection
        ->createCommand("Select CHARGES,MODES from tbl_charges where USER_ID=:user_id");
        $get_charges->bindValue(':user_id', $data['USER_ID']);
        $get_charges_data = $get_charges->queryAll();
        $wallet = $this->get_wallet_balance();
        return $this->render('payment',array('invoice_amount'=>$sum,'invoice_data'=>$invoice_data,'provider'=>$invoice_data[0]['provider_name'],'charges'=>$get_charges_data[0],'wallet_balance'=>$wallet['TRANSACTION']['WALLETBALANCE']));
      }
      
      public function calculate_sum($data){
        $sum=0;
        foreach($data as $value){
          $sum = $sum + $value['AMOUNT'];
        }
        return $sum;
      }
      
      public function actionDeletemobile(){
        $connection = Yii::$app->db;
        $invoice_mobile_delete = $connection->createCommand()
        ->update('tbl_provider_bill_details', ['REMOVED' => 'y','INVOICE_ID'=> " "], 'INVOICE_ID='.Yii::$app->request->post('invoice_id').' AND ACCOUNT_NO='.Yii::$app->request->post('mobile_no'))->execute();
        echo $invoice_data;
        if($invoice_mobile_delete){
          $invoice = $connection
          ->createCommand("Select b.AMOUNT,b.RESPONSE_NOT_RECIEVED,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,b.DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id where b.INVOICE_ID=:invoice_id AND b.REMOVED='n'");
          $invoice->bindValue(':invoice_id', Yii::$app->request->post('invoice_id'));
          $invoice_data = $invoice->queryAll();
          $sum = $this->calculate_sum($invoice_data);
          echo json_encode(['sum'=>$sum]);
        } else {
          echo false;
        }
      }
      
      public function actionUnpaid(){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        if(Yii::$app->request->post('from_date') && Yii::$app->request->post('to_date')){
          $query="SELECT b.PROVIDER_BILL_DETAILS_ID,b.AMOUNT,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,DATE_FORMAT(b.DUE_DATE,'%d/%m/%Y')as DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID where b.UTILITY_ID=:utility_id AND b.PROVIDER_ID=:provider_id AND USER_ID=:user_id AND RESPONSE_NOT_RECIEVED=0 AND PAYMENT_STATUS='' AND DUE_DATE >=:from_date AND DUE_DATE <=:to_date ORDER BY DUE_DATE ASC";
        } else {
          $query="SELECT b.PROVIDER_BILL_DETAILS_ID,b.AMOUNT,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,DATE_FORMAT(b.DUE_DATE,'%d/%m/%Y')as DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID where b.UTILITY_ID=:utility_id AND b.PROVIDER_ID=:provider_id AND USER_ID=:user_id AND PAYMENT_STATUS='' AND RESPONSE_NOT_RECIEVED=0  ORDER BY DUE_DATE ASC";
        }
        $unpaid = $connection->createCommand($query);
        $unpaid->bindValue(':utility_id', Yii::$app->request->post('utility_id'));
        $unpaid->bindValue(':provider_id', Yii::$app->request->post('provider_id'));
        if(Yii::$app->request->post('from_date') && Yii::$app->request->post('to_date')){
          $unpaid->bindValue(':from_date', Yii::$app->request->post('from_date'));
          $unpaid->bindValue(':to_date', Yii::$app->request->post('to_date'));
        }
        $unpaid->bindValue(':user_id', $data['USER_ID']);
        $unpaid_data= $unpaid->queryAll();
        echo json_encode($unpaid_data);
      }
      
      public function actionPay(){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
        $config = $connection
        ->createCommand($query);
        $config->bindValue(':partner_id',$data['PARTNER_ID']);
        $config_data = $config->queryAll();
        $chk = new Checksum();
        $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
        $buyerEmail = trim($data['EMAIL']);
        $buyerPhone = trim( $data['MOBILE']);
        $buyerFirstName = trim($data['FIRST_NAME']);
        $buyerLastName = trim($data['LAST_NAME']);
        $amount = trim(Yii::$app->request->post('invoice_amount'));
        if(Yii::$app->request->post('invoice_no')!=""){
          $orderid = trim(Yii::$app->request->post('invoice_no'));
        }else{
          $orderid = trim(rand());
        }
        $alldata   = $buyerEmail.$buyerFirstName.$buyerLastName.$amount.$orderid;
        $checksum = $chk->calculateChecksum($alldata.date('Y-m-d'),$privatekey);
        
        return $this->render('airpay_payment',array('payment_data'=>Yii::$app->request->post(),"key"=>$privatekey,"checksum"=>$checksum,"mechant_id"=>$config_data[0]['AIRPAY_MERCHANT_ID'],"token"=>$data['WALLET_TOKEN'],'orderid'=>$orderid));
      }
      
      public function actionPaymentresponse(){
      // print_r($_POST);
      //   exit;
        $model = new TblTranscationDetails();
        $model->INVOICE_ID = $_POST['TRANSACTIONID'];
        $model->AIRPAY_ID = $_POST['APTRANSACTIONID'];
        $model->PAYMENT_DATE = date('Y-m-d');
        $model->TOTAL_AMOUNT = $_POST['AMOUNT'];
        $model->FINAL_AMOUNT_RECIEVED = $_POST['AMOUNT'];
        $model->PAYMENT_STATUS = $_POST['TRANSACTIONPAYMENTSTATUS'];
        $model->PAYMENT_STATUS_CODE = $_POST['TRANSACTIONSTATUS'];
        $model->PAY_METHOD = $_POST['TRANSACTIONTYPE'];
        $model->PAY_MODE = $_POST['CHMOD'];
        $model->UPDATED_ON= date('Y-m-d');
        $model->save();
        if($_POST['TRANSACTIONPAYMENTSTATUS']=='SUCCESS'){
          if(isset($_POST['WALLETBALANCE'])){
            $this->redirect($_POST['CUSTOMVAR']);
          } else{
            $connection = Yii::$app->db;
            $invoice = $connection
            ->createCommand("SELECT AMOUNT,BILL_ID,RESPONSE_NOT_RECIEVED,PROVIDER_ID,UTILITY_ID,INVOICE_ID,DUE_DATE,ACCOUNT_NO from tbl_provider_bill_details WHERE INVOICE_ID=:invoice_id AND REMOVED='n'");
            $invoice->bindValue(':invoice_id', $_POST['TRANSACTIONID']);
            $invoice_data = $invoice->queryAll();
            $bill_details=array();
            foreach($invoice_data as $value){
              $status= $connection->createCommand()->update('tbl_provider_bill_details', ['PAYMENT_STATUS' => 'pending'], 'INVOICE_ID='.$_POST['TRANSACTIONID'].' AND ACCOUNT_NO='.$value['ACCOUNT_NO'])->execute();
              $data['viewbillresponseid']=$value['BILL_ID'];
              $sum=array($value);
              $data['amount']=$this->calculate_sum($sum);
              $bill_details[]=$data;
            }
            $data2=Yii::$app->user->identity;
            $connection = Yii::$app->db;
            $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
            $config = $connection
            ->createCommand($query);
            $config->bindValue(':partner_id',$data2['PARTNER_ID']);
            $config_data = $config->queryAll();
            $chk = new Checksum();
            $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
            $checksum = md5($config_data[0]['AIRPAY_MERCHANT_ID'].'~'.$_POST['AMOUNT'].'~'.Yii::$app->request->post('APTRANSACTIONID'));
            $apidata=[
              'private_key'=>$privatekey,
              'mercid'=>$config_data[0]['AIRPAY_MERCHANT_ID'],
              'callbackurl'=>'192.168.1.184/partnerpay/web/bbps/billapi/paymentstatus',
              'checksum'=>$checksum,
              'airpay_id'=>Yii::$app->request->post('APTRANSACTIONID'),
              'amountsum'=>$_POST['AMOUNT'],
              'makepaymentdata'=>$bill_details,
            ];
            $api_data = json_encode($apidata);
            $url='https://devel-payments.airpayme.com/bbps/makePayment.php';
            $response= $this->api_call($url,$api_data);
            $log_data = "MAKE PAYMENT DATA RESPONSE : ".json_encode($response);
            $this->writeLog("Log_Data",$log_data);
            return $this->render('thankyou');
          }
        }else{
          $response = "PAYMENT FAILED";
          return $this->render('thankyou'); 
        }
        
      }
      
      public function actionAdd_mobile(){
        $invoice_id = $this->invoice_create();
        foreach(Yii::$app->request->post('provider_bill_details_id')as $value){
          $connection = Yii::$app->db;  
          $connection->createCommand()
          ->update('tbl_provider_bill_details', ['INVOICE_ID'=>$invoice_id,'REMOVED'=>'n'], 'PROVIDER_BILL_DETAILS_ID='.$value)
          ->execute();
        }
        echo $invoice_id;
      }
      
      public function actionAdd_instant_to_archieve(){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        $query1="SELECT * FROM tbl_provider_bill_details WHERE DATE(MODIFIED_DATE) = DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND IS_REGISTER='n' AND PAYMENT_STATUS='success' or PAYMENT_STATUS= 'failed'";
        $registered = $connection
        ->createCommand($query1);
        $registered_data = $registered->queryAll();
        if(sizeof($registered_data)){
          $query="INSERT into tbl_archived_provider_bill_details SELECT * FROM tbl_provider_bill_details WHERE DATE(MODIFIED_DATE) = DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND IS_REGISTER='n' AND PAYMENT_STATUS='success' or PAYMENT_STATUS= 'failed'";
          $archieve = $connection
          ->createCommand($query);
          $archieve_data = $archieve->execute();
          if($archieve_data){
            $query2="DELETE FROM tbl_provider_bill_details WHERE DATE(MODIFIED_DATE) = DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND IS_REGISTER='n' AND PAYMENT_STATUS='success' or PAYMENT_STATUS= 'failed'";
            $registered = $connection
            ->createCommand($query2);
            $registered_data = $registered->execute();
          }
        }
      }  
      
      public function notification($invoice_id){
        $connection = Yii::$app->db;
        $checkresponse = $connection
        ->createCommand("SELECT Count(b.PROVIDER_BILL_DETAILS_ID) as bill_recieved, MOBILE from  tbl_provider_bill_details as b INNER JOIN tbl_user_master as u on u.USER_ID = b.USER_ID  where INVOICE_ID=:invoice_id AND RESPONSE_NOT_RECIEVED=0");
        $checkresponse->bindValue(':invoice_id', $invoice_id);
        $checkresponse_data = $checkresponse->queryAll();
        if($checkresponse_data[0]['bill_recieved']%5==0){
          $signature = 'airpay';
          $msg="RECIEVED BILL DETAILS OF ".$checkresponse_data[0]['bill_recieved']." MOBILE NUMBERS";
          
          $sms_data = \Yii::$app->params['sms']['data'];
          $sms_data = str_replace('{{{phone_number}}}', $checkresponse_data[0]['MOBILE'], $sms_data);
          $sms_data = str_replace('{{{message}}}', urlencode($msg), $sms_data);
          $sms_data = str_replace('{{{signature}}}', ($signature), $sms_data);
          
          $ch = curl_init(\Yii::$app->params['sms']['url'] . $sms_data);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $response = curl_exec($ch);
          curl_close($ch);
          return $response;
        } else {
          $msg="RECIEVED BILL DETAILS OF ".$checkresponse_data[0]['bill_recieved']." MOBILE NUMBERS";
          return $msg;
        }
      } 
      
      public function api_call($url,$api_data,$wallet=""){
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $headers=array();
        $headers[] = 'User-Agent: '. $user_agent;
        if($wallet){
          $headers[] = 'Content-Type: multipart/form-data';
        } else {
          $headers[] = 'Content-Type: application/json';
        }
        $headers[] = 'Cache-Control: no-cache';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$api_data);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        // print_r(json_decode($curl_response,true));
        // exit;
        return json_decode($curl_response,true);
      }
      
      public function actionGet_fields($provider_id="",$validation=""){
        if($provider_id==""){
          $provider_id=Yii::$app->request->post('provider_id');
        }
        $connection = Yii::$app->db;
        $query="SELECT FIELDS ,VALIDATIONS from tbl_provider WHERE BILLER_MASTER_ID=:biller_master_id";
        $get_fields = $connection
        ->createCommand($query);
        $get_fields->bindValue(':biller_master_id',$provider_id);
        $get_fields_data = $get_fields->queryAll();
        $fields= explode('|',$get_fields_data[0]['FIELDS']);
        if(Yii::$app->request->post('provider_id')){
          $validtions = explode('::',$get_fields_data[0]['VALIDATIONS']);
          $fields_validation = array();
          foreach($fields as $key=>$value){
            $field['field']=$value;
            $field['validation']=$validtions[$key];
            $fields_validation[] = $field;
          }
          return json_encode($fields_validation);
        } else {
          if($validation == "" ){
            return json_encode($fields);
          } else {
            return json_encode(explode('::',$get_fields_data[0]['VALIDATIONS']));
          }
        }
      }
      
      /* cron */
      public function actionGet_billerid(){
        // $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
        $config = $connection
        ->createCommand($query);
        $config->bindValue(':partner_id',$data['PARTNER_ID']);
        $config_data = $config->queryAll();
        $chk = new Checksum();
        $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
        $data = [
          "privatekey"=>$privatekey,
          "checksum"=>"",
          "mercid"=>"245",
        ];
        $api_data=json_encode($data);
        $url="https://devel-payments.airpayme.com/bbps/getBillerId.php";
        $billerdata = $this->api_call($url,$api_data);
        foreach($billerdata['BILLERDATA'] as $value){
          $connection = Yii::$app->db;
          $query="SELECT utility_id from tbl_utility where utility_name=:utility";
          $check_utility = $connection->createCommand($query);
          $check_utility->bindValue(':utility',$value['BILLER_CATEGORY']);
          $check_utility_data = $check_utility->queryAll();
          if(sizeof($check_utility_data)>0){
            $utility_id=$check_utility_data[0]['utility_id'];
          }else{
            $data=Yii::$app->user->identity;
            $query1="INSERT into tbl_utility (utility_name,user_id) VALUES (:utility_name,:user)";
            $check_utility = $connection
            ->createCommand($query1);
            $check_utility->bindValue(':utility_name',$value['BILLER_CATEGORY']);
            $check_utility->bindValue(':user',1);
            $check_utility_data = $check_utility->execute();
            $utility_id = $connection->getLastInsertID();
          }
          $query3='SELECT provider_id from tbl_provider where BILLER_MASTER_ID = :biller_master_id';
          $check_provider=$connection->createCommand($query3);
          $check_provider->bindValue(':biller_master_id',$value['BILLER_MASTER_ID']);
          $check_provider_data = $check_provider->execute();
          if($check_provider_data){
            $query4 = "UPDATE tbl_provider SET FIELDS=:fields,VALIDATIONS=:validations WHERE BILLER_MASTER_ID=:biller_master_id";
            $update_provider=$connection->createCommand($query4);
            $update_provider->bindValue(':biller_master_id',$value['BILLER_MASTER_ID']);
            $update_provider->bindValue(':fields',$value['FIELDNAMES']);
            $update_provider->bindValue(':validations',$value['VALIDATION']);
            $update_provider_data = $update_provider->execute();
          }else{  
            $query2 = "INSERT into tbl_provider (utility_id,provider_name,FIELDS,BILLER_MASTER_ID,VALIDATIONS) SELECT * FROM (SELECT :utility_id,:provider_name,:fields,:biller_master_id,:validations) AS tmp
            WHERE NOT EXISTS (
              SELECT provider_name FROM tbl_provider WHERE provider_name = :provider_name
              )";
              $provider_update=$connection->createCommand($query2);
              $provider_update->bindValue(':utility_id',$utility_id);
              $provider_update->bindValue(':provider_name',$value['BILLER_NAME']);
              $provider_update->bindValue(':fields',$value['FIELDNAMES']);
              $provider_update->bindValue(':biller_master_id',$value['BILLER_MASTER_ID']);
              $provider_update->bindValue(':validations',$value['VALIDATION']);
              $provider_update_data = $provider_update->execute();
            }
            // print_r($provider_update_data);
          }
        }
        
        public function actionDownload_csv_file($provider,$errors=""){
          $fields[0]='First Name';
          $fields[1]='Last Name';
          $fields[2]='Email';
          $i = 3;
          $field = json_decode($this->actionGet_fields($provider),true);
          foreach($field as $value){
            $fields[$i] = $value;
            $i++;
          }
          if($errors==""){
            $name = 'SampleFormat.csv';
            $field[]=$fields;
          } else {
            $upload_error = json_decode($errors,true);
            $name = 'ERRORS.csv';
            $field = array();
            $field[]=$fields;
            foreach($upload_error as $key=>$value){
              $field[]= $value;
            } 
          }
          header('Content-Type: text/csv');
          header('Content-Disposition: attachment; filename='. $name);
          header('Pragma: no-cache');
          header("Expires: 0");
          
          $outstream = fopen("php://output", "w");
          foreach($field as $key=>$value){
            fputcsv($outstream, $value);
          }
          fclose($outstream);
          // exit;
        }
        public function actionGenerate_bill_receipt($bill_details_id) {
          $connection = Yii::$app->db;
          $query="SELECT b.BANK_REF_PAYMENT_NUMBER,b.ACCOUNT_NO,b.PAYMENT_STATUS,b.AMOUNT,b.FNAME,b.DUE_DATE,b.LNAME,b.EMAIL,tr.PAY_MODE,tr.CREATED_ON,tr.AIRPAY_ID,p.provider_name from tbl_provider_bill_details as b JOIN tbl_transcation_details as tr on tr.INVOICE_ID = b.INVOICE_ID  JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID where b.PROVIDER_BILL_DETAILS_ID=:bill_details_id";
          $receipt = $connection
          ->createCommand($query);
          $receipt->bindValue(':bill_details_id',$bill_details_id);
          $receipt_data = $receipt->queryAll();
          $taxRate = 0.18;
          $data=Yii::$app->user->identity;
          $get_charges = $connection
          ->createCommand("Select CHARGES,MODES from tbl_charges where USER_ID=:user_id");
          $get_charges->bindValue(':user_id', $data['USER_ID']);
          $get_charges_data = $get_charges->queryAll();
          $charge = json_decode($get_charges_data[0]['CHARGES'],true);
          $calculatedAmount = ($charge[$receipt_data[0]['PAY_MODE']] * $receipt_data[0]['AMOUNT']) / 100;
          $b_chgs = $calculatedAmount * $taxRate;
          $total_charge = $calculatedAmount+$b_chgs;
          $content = $this->renderPartial('receipt-bbps',array('receipt'=>$receipt_data[0],'charge'=>round($total_charge,2)));
          $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            'options' => ['title' => 'BBPS RECEIPT'],
            'methods' => [ 
              'SetHeader'=>['BBPS RECEIPT'], 
              'SetFooter'=>['{PAGENO}'],
              ]
              ]);
              return $pdf->render(); 
            }
            
            public function actionGet_invoice_data(){
              $connection = Yii::$app->db;
              $query="SELECT PROVIDER_BILL_DETAILS_ID,ACCOUNT_NO,PAYMENT_STATUS,AMOUNT,DUE_DATE from tbl_provider_bill_details where INVOICE_ID=:invoice_id";
              $invoice_data = $connection
              ->createCommand($query);
              $invoice_data->bindValue(':invoice_id',Yii::$app->request->post('invoice_id'));
              $invoice_recieved_data = $invoice_data->queryAll();
              echo json_encode($invoice_recieved_data);
              exit;
            }
            
            public function actionPayment_amount_check(){
              $connection = Yii::$app->db;
              $invoice = $connection
              ->createCommand("Select b.AMOUNT,b.RESPONSE_NOT_RECIEVED,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,b.DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID where b.INVOICE_ID=:invoice_id AND b.REMOVED='n'");
              $invoice->bindValue(':invoice_id', Yii::$app->request->post('invoice_id'));
              $invoice_data = $invoice->queryAll();
              $sum = $this->calculate_sum($invoice_data);
              $data=Yii::$app->user->identity;
              $get_charges = $connection
              ->createCommand("Select CHARGES from tbl_charges where USER_ID=:user_id");
              $get_charges->bindValue(':user_id', $data['USER_ID']);
              $get_charges_data = $get_charges->queryAll();
              $payment_data = array();
              $payment_data['sum']=$sum;
              $payment_data['charges']=$get_charges_data[0]['CHARGES'];
              echo json_encode($payment_data);
              exit;
            }
            
            public function check_account_id($account_no){
              $connection = Yii::$app->db;
              $check_account_id = $connection
              ->createCommand("Select ACCOUNT_NO from tbl_provider_bill_details where ACCOUNT_NO=:account_no AND REMOVED='n'");
              $check_account_id->bindValue(':account_no', $account_no);
              $check_account_id_data = $check_account_id->queryAll();
              if(sizeof($check_account_id_data)){
                return true;
              }else{
                return false;
              }
            }
            
            public function actionWallet_top_up(){
              $data=Yii::$app->user->identity;
              $connection = Yii::$app->db;
              $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
              $config = $connection
              ->createCommand($query);
              $config->bindValue(':partner_id',$data['PARTNER_ID']);
              $config_data = $config->queryAll();
              $chk = new Checksum();
              $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
              $api_data=[
                "txnmode"=>"credit",
                "mercid"=>$config_data[0]['AIRPAY_MERCHANT_ID'],
                "token"=>$data['WALLET_TOKEN'],
                "walletUser"=>$data['EMAIL'],
                "orderid"=>"255",
                "amount"=>"100",
                "mer_dom"=>base64_encode("http://localhost"),
                "outputFormat"=>"json",
                "checksum"=>md5($config_data[0]['AIRPAY_MERCHANT_ID'].$data['WALLET_TOKEN'].$data['EMAIL']."credit255100".date('Y-m-d').$privatekey),
                "privatekey"=>$privatekey,
                "wallet"=>1,
              ];
              $url="https://devel-payments.airpayme.com/wallet/api/walletTxn.php";
              $wallet_top_up_response = $this->api_call($url,$api_data,1);
              echo (json_encode($wallet_top_up_response['TRANSACTION']));
              exit;
            }
            
            public function get_wallet_balance(){
              $data=Yii::$app->user->identity;
              $connection = Yii::$app->db;
              $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
              $config = $connection
              ->createCommand($query);
              $config->bindValue(':partner_id',$data['PARTNER_ID']);
              $config_data = $config->queryAll();
              $chk = new Checksum();
              $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
              $wallet_data["mercid"]=$config_data[0]['AIRPAY_MERCHANT_ID'];
              $wallet_data["walletUser"]=$data['EMAIL'];
              $wallet_data["token"]=$data['WALLET_TOKEN'];
              $wallet_data["outputFormat"]="json";
              $wallet_data["privatekey"] = $privatekey;
              $wallet_data["checksum"]=md5($wallet_data["mercid"].$wallet_data["token"].$wallet_data["walletUser"].date('Y-m-d').$wallet_data["privatekey"]);
              $url="https://devel-payments.airpayme.com/wallet/api/walletBalance.php";
              $wallet_data_response = $this->api_call($url,$wallet_data,1);
              return $wallet_data_response; 
            }
            
            public function actionView_wallet_history(){
              $data=Yii::$app->user->identity;
              $connection = Yii::$app->db;
              $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
              $config = $connection
              ->createCommand($query);
              $config->bindValue(':partner_id',$data['PARTNER_ID']);
              $config_data = $config->queryAll();
              $chk = new Checksum();
              $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
              $api_data =  [
                "mercid"=>$config_data[0]['AIRPAY_MERCHANT_ID'],
                "token"=>$data['WALLET_TOKEN'],
                "privatekey"=>$privatekey,
                "walletUser"=>$data['EMAIL'],
                "displayorder"=>'desc',
                "displayrec"=>"10000",
                "displaypage"=>"",
                "outputFormat"=>"json",
                "checksum"=>md5($config_data[0]['AIRPAY_MERCHANT_ID'].$data['WALLET_TOKEN'].$data['EMAIL']."desc10000".date('Y-m-d').$privatekey),
              ];
              $url="https://devel-payments.airpayme.com/wallet/api/walletHistory.php";
              $wallet_data_response = $this->api_call($url,$api_data,1);
              return $this->render('wallet_history_listing',array('wallet_history'=>$wallet_data_response['TRANSACTION']['WALLETTXNS']['WALLETTXN']));    
            }

            public function actionWallet_topup(){

            }
          }
          