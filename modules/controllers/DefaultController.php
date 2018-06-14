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

class DefaultController extends HController
{
  public $enableCsrfValidation = false;
	public function actionIndex()
	{
		$data=Yii::$app->user->identity;
		// if(!($data['USER_ID'])){
      //   $this->redirect('/web');
      // }
      $utilities = TblUtility::find()->all();
      return $this->render('index',array('utilities'=>$utilities));
    }
    
    public function writeLog($filepath, $mid, $data) {
      $filename = $filepath . $mid . '.log';
      if (!file_exists($filename)) 
      {
       mkdir($filename, 0777, true);
      }
      echo $filename;
      $log_file_data = $filename.'/log_' . date('d-M-Y') . '.log';
      file_put_contents($log_file_data, $data ."\n", FILE_APPEND);
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
      if($_FILES['bulk_upload']['tmp_name']){
        $uploadedFile_data = $this->upload();
        if($uploadedFile_data){
          if(Yii::$app->request->post('register')){
            $ref_no=$this->archieve_data();
          }
          /*$data=Yii::$app->user->identity;
          $connection = Yii::$app->db;
          $query="SELECT INVOICE_ID from tbl_provider_bill_details WHERE PROVIDER_ID=:provider_id AND UTILITY_ID=:utility_id AND USER_ID=:user_id AND PAYMENT_STATUS=:payment_us";
          $check_invoice = $connection
          ->createCommand($query);
          $check_invoice->bindValue(':provider_id',Yii::$app->request->post('providers'));
          $check_invoice->bindValue(':utility_id',Yii::$app->request->post('utility_name'));
          $check_invoice->bindValue(':user_id',$data['USER_ID']);
          $check_invoice->bindValue(':payment_us','');
          $get_invoice_data = $check_invoice->queryAll();
          if(sizeof($get_invoice_data)==0){
            $invoice_id = $this->invoice_create();
          } else {
            $invoice_id= $get_invoice_data[0]['INVOICE_ID'];
          }*/
          $bill_details=array();
          $data=array();
          $fields_data=array();
          $handle = fopen( Yii::$app->getBasePath()."/modules/resources/upload/".$uploadedFile_data['file_name'], "r");
          fgetcsv($handle);
          while (($fileop = fgetcsv($handle, 1024, ",")) !== false) 
          {
            $i=1;
            $data['account_id']= $fileop[0];
            for($i=1;$i<sizeof($fields);$i++){
              $fields_data[$fields[$i]]=$fileop[$i];
            }
            $data['details'] = json_encode($fields_data);
            $data['billerid'] = Yii::$app->request->post('providers');
            $data['remark'] = Yii::$app->request->post('utility_name');
            $data['request_id'] = $this->bill_details($uploadedFile_data,$data);
            $bill_details[]=$data;
          }
          $template="data_uploaded";
        } else{
          echo "Error while uploading file";
        }
      } else {
        // $validator = new RequiredValidator();
        // if ($validator->validate($email, $error)) {
          if(Yii::$app->request->post('register')){
            $ref_no=$this->archieve_data();
          }
          $bill_details=array();
          $data=array();
          //$invoice_id = $this->invoice_create();
          $data['account_id']=Yii::$app->request->post(str_replace(' ','_',$fields[0]));
          for($i=1;$i<sizeof($fields);$i++){
            $fields_data[$fields[$i]]=Yii::$app->request->post(str_replace(' ','_',$fields[$i]));
          }
          $data['details'] = json_encode($fields_data);
          $data['billerid'] = 1;
          $data['remark'] = Yii::$app->request->post('utility_name');
          $data['request_id'] = $this->bill_details(0,$data);
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
        $apidata=[
          // "requestid"=>$invoice_id,
          // 'mercid'=>$config_data[0]['AIRPAY_MERCHANT_ID'],
          'mercid'=>243,
          "customerid"=>1,
          // 'private_key'=>$privatekey,
          'private_key'=>'',
          'callbackurl'=>'192.168.1.184/partnerpay/web/bbps/default/bill_data_response',
          "returnurl"=>'192.168.1.184/partnerpay/web/bbps/default/account_register_response',
          "action"=>"ADD_BILLER",
          'checkSum'=>"",
          'bill_data'=>$bill_details,
        ];
        // echo "<pre>";
        $api_data = json_encode($apidata);
        $url='https://devel-payments.airpayme.com/bbps/add_biller.php';
        $log_path=realpath(Yii::$app->basePath)."/modules/resources/log/";
        $response= $this->api_call($url,$api_data);
        $log_data="ADD BILLER API RESPONSE : ".json_encode($response);
        $this->writeLog($log_path,"Log_Data",$log_data);
        if($response->STATUS=="200"){
          // return $this->render($template,array('invoice_id'=>$invoice_id));
              return $this->render($template);
        } else {
          // return $this->render($template,array('invoice_id'=>$invoice_id));
          return $this->render($template);
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
          echo "Sorry, your file was not uploaded.";
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
        $query1="SELECT * FROM tbl_provider_bill_details WHERE USER_ID=:user_id AND IS_REGISTER=:is_register AND PAYMENT_STATUS <>:payment_status";
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
        if(Yii::$app->request->post('register')){
          $model->IS_REGISTER='y';
          $connection = Yii::$app->db;
          $query="SELECT REF_NO from tbl_registered_account where ACCOUNT_NO=:account_no AND PROVIDE_ID=:provider_id AND UTILITY_ID=:utility_id AND IS_REGISTERED=1";
          $check_registered = $connection
          ->createCommand($query);
          $check_registered->bindValue(':account_no',$data['account_id']);
          $check_registered->bindValue(':provider_id',Yii::$app->request->post('providers'));
          $check_registered->bindValue(':utility_id',Yii::$app->request->post('utility_name'));
          $check_registered_data = $check_registered->queryAll();
          if(sizeof($check_registered_data)>0){
            echo "WOrking";
            exit;
          } else {
            $insert_query="INSERT into tbl_registered_account (UTILITY_ID,PROVIDE_ID,ACCOUNT_NO) VALUES (:utility_id,:provider_id,:account_no)";
            $insert_registered = $connection
            ->createCommand($insert_query);
            $insert_registered->bindValue(':account_no',$data['account_id']);
            $insert_registered->bindValue(':provider_id',Yii::$app->request->post('providers'));
            $insert_registered->bindValue(':utility_id',Yii::$app->request->post('utility_name'));
            $insert_registered_data = $insert_registered->execute();
          }
        } else {
          $model->IS_REGISTER='n';
        }
        $model->PROVIDER_ID=Yii::$app->request->post('providers');
        $model->UTILITY_ID=Yii::$app->request->post('utility_name');
        if($uploadedFile_data['inserted_id']){
          $model->PROVIDER_BILL_UPLOAD_DETAILS_ID=$uploadedFile_data['inserted_id'];
        }
        $model->ACCOUNT_NO=$data['account_id'];
        $model->DETAILS=$data['details'];
        $data=Yii::$app->user->identity;
        $model->USER_ID= $data['USER_ID'];
        // $model->INVOICE_ID=$invoice_id;
        if($model->save(false)){
          $billing_details_id=$model->getPrimaryKey();
          return $billing_details_id;
        }
        
      }
      
      public function actionAccount_register_response(){
        $post = Yii::$app->request->rawBody;
        $data2 = json_decode($post);
        $log_path = realpath(Yii::$app->basePath)."/modules/resources/log/";
        $log_data = " REGISTER DATA RESPONSE : ".$post;
        $this->writeLog($log_path,"Log_Data",$log_data);
        $model= new TblProviderBillDetails();
        $connection = Yii::$app->db;  
        $get_provider = $connection->createCommand('Select PROVIDER_ID from tbl_provider_bill_details where ACCOUNT_NO=:account_no AND INVOICE_ID=:invoice_no');
        $get_provider->bindValue(':account_no',$data2->ACCOUNTID);
        $get_provider->bindValue(':invoice_no',$data2->REQUESTNUMBER);
        $get_provider_data =  $get_provider->queryAll();
        $status = $connection->createCommand()
        ->update('tbl_registered_account', ['REF_NO'=>$data2->BILLERACCOUNTID,'IS_REGISTERED'=>1], 'ACCOUNT_NO='.$data2->ACCOUNTID.' AND PROVIDE_ID='.$get_provider_data[0]['PROVIDER_ID'])
        ->execute();
        if($status){
          return json_encode(['status'=>200,"message"=>"UPLOADED SUCCESSFULLY"]);
        } else {
          return json_encode(['status'=>400,"message"=>"ERROR IN UPLOADINf"]);
        }
      }
      
      public function actionBill_data_response(){
        // $data=Yii::$app->user->identity;
        // $connection = Yii::$app->db;
        // $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
        // $config = $connection
        // ->createCommand($query);
        // $config->bindValue(':partner_id',$data['PARTNER_ID']);
        // $config_data = $config->queryAll();
        // $chk = new Checksum();
        // $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
        
        //   $this->enableCsrfValidation = false;
        //   echo "asdads";
        $log_path = realpath(Yii::$app->basePath)."/modules/resources/log/";
        $post = Yii::$app->request->rawBody;
        $log_data = "Bill DATA RESPONSE : ".$post;
        $this->writeLog($log_path,"Log_Data",$log_data);
        $data2 = json_decode($post);
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
      
      public function actionListing($invoice_id=""){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        $all_invoice = $connection
        ->createCommand('Select AMOUNT as invoice_amount,b.RESPONSE_NOT_RECIEVED as recieved,b.PROVIDER_ID,p.provider_name,b.PAYMENT_STATUS,b.PROVIDER_BILL_DETAILS_ID,u.utility_name from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.BILLER_MASTER_ID JOIN tbl_utility as u on b.UTILITY_ID=u.utility_id where b.USER_ID=:userid AND b.REMOVED="n"');
        $all_invoice->bindValue(':userid', $data['USER_ID']);
        $all_invoice_data = $all_invoice->queryAll();
        $query="SELECT utility_id,utility_name from tbl_utility where is_disabled='n'";
        $utility = $connection->createCommand($query);
        $utility_data= $utility->queryAll();
        //return  $this->render('listing',array('invoice_id'=>$invoice_id,'invoice_data'=>$all_invoice_data,'utility_data'=>$utility_data));
        return  $this->render('listing',array('invoice_data'=>$all_invoice_data,'utility_data'=>$utility_data));
      }  
      
     /* public function actionChecking(){
        $connection = Yii::$app->db;
        $checkresponse = $connection
        ->createCommand("Select SUM(AMOUNT) as invoice_amount,SUM(RESPONSE_NOT_RECIEVED) as recieved from  tbl_provider_bill_details where INVOICE_ID=:invoice_id");
        $checkresponse->bindValue(':invoice_id', Yii::$app->request->post('id'));
        $checkresponse_data = $checkresponse->queryAll();
        if($checkresponse_data[0]['recieved']==0){
          echo json_encode($all_invoice_data);
        } else {
          echo false;
        }
      }*/
      
      public function actionPayment($invoice_id){
        $connection = Yii::$app->db;
        $invoice = $connection
        ->createCommand("Select b.AMOUNT,b.RESPONSE_NOT_RECIEVED,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,b.DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id where b.INVOICE_ID=:invoice_id AND b.REMOVED='n'");
        $invoice->bindValue(':invoice_id', $invoice_id);
        $invoice_data = $invoice->queryAll();
        $sum = $this->calculate_sum($invoice_data);
        return $this->render('payment',array('invoice_amount'=>$sum,'invoice_data'=>$invoice_data,'provider'=>$invoice_data[0]['provider_name']));
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
        ->update('tbl_provider_bill_details', ['REMOVED' => 'y'], 'INVOICE_ID='.Yii::$app->request->post('invoice_id').' AND ACCOUNT_NO='.Yii::$app->request->post('mobile_no'))->execute();
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
      
      public function actionRemoved(){
        $data=Yii::$app->user->identity;
        $connection = Yii::$app->db;
        // $query="SELECT b.PROVIDER_BILL_DETAILS_ID,b.AMOUNT,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,DATE_FORMAT(b.DUE_DATE,'%d/%m/%Y')as DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id where b.UTILITY_ID=:utility_id AND b.REMOVED='y' AND b.PROVIDER_ID=:provider_id AND USER_ID=:user_id";
        $query="SELECT b.PROVIDER_BILL_DETAILS_ID,b.AMOUNT,b.PROVIDER_ID,p.provider_name,b.INVOICE_ID,DATE_FORMAT(b.DUE_DATE,'%d/%m/%Y')as DUE_DATE,b.ACCOUNT_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id where b.UTILITY_ID=:utility_id AND b.PROVIDER_ID=:provider_id AND USER_ID=:user_id AND RESPONSE_NOT_RECIEVED=0 AND PAYMENT_STATUS=''";
        $removed = $connection->createCommand($query);
        $removed->bindValue(':utility_id', Yii::$app->request->post('utility_id'));
        $removed->bindValue(':provider_id', Yii::$app->request->post('provider_id'));
        $removed->bindValue(':user_id', $data['USER_ID']);
        $removed_data= $removed->queryAll();
        echo json_encode($removed_data);
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
        // $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
        $privatekey ='91e747ae8fd76152ddba23ae7547e614bdea61d6356a3db679380b1ec4a8b2da';
        $buyerEmail = trim($data['EMAIL']);
        $buyerPhone = trim("9869478152");
        $buyerFirstName = trim($data['FIRST_NAME']);
        $buyerLastName = trim($data['LAST_NAME']);
        $amount = trim(Yii::$app->request->post('invoice_amount'));
        $orderid = trim(Yii::$app->request->post('invoice_no'));
        $alldata   = $buyerEmail.$buyerFirstName.$buyerLastName.$amount.$orderid;
        $checksum = $chk->calculateChecksum($alldata.date('Y-m-d'),$privatekey);
        
        return $this->render('airpay_payment',array('payment_data'=>Yii::$app->request->post(),"key"=>$privatekey,"checksum"=>$checksum,"mechant_id"=>$config_data[0]['AIRPAY_MERCHANT_ID']));
      }
      
      public function actionPaymentresponse(){
        $model= new TblTranscationDetails();
        $model->INVOICE_ID=$_POST['TRANSACTIONID'];
        $model->AIRPAY_ID=$_POST['APTRANSACTIONID'];
        $model->PAYMENT_DATE=date('Y-m-d');
        $model->TOTAL_AMOUNT= $_POST['AMOUNT'];
        $model->FINAL_AMOUNT_RECIEVED=$_POST['AMOUNT'];
        $model->PAYMENT_STATUS=$_POST['TRANSACTIONPAYMENTSTATUS'];
        $model->PAYMENT_STATUS_CODE=$_POST['TRANSACTIONSTATUS'];
        $model->PAY_METHOD= $_POST['TRANSACTIONTYPE'];
        $model->UPDATED_ON= date('Y-m-d');
        $model->save();
        if($_POST['TRANSACTIONPAYMENTSTATUS']=='SUCCESS'){
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
          $apidata=[
            // 'Invoice_no'=>Yii::$app->request->post('TRANSACTIONID'),
            // 'profile_id'=>'',
            // 'utitlity_id'=>$invoice_data[0]['UTILITY_ID'],
            // 'provider_id'=>$invoice_data[0]['PROVIDER_ID'],
            'private_key'=>'',
            'mercid'=>245,
            'callbackurl'=>'192.168.1.184/partnerpay/web/bbps/default/paymentstatus',
            'checkSum'=>'',
            'airpay_id'=>Yii::$app->request->post('APTRANSACTIONID'),
            'amountsum'=>$_POST['AMOUNT'],
            'makepaymentdata'=>$bill_details,
          ];
          $api_data = json_encode($apidata);
          $url='https://devel-payments.airpayme.com/bbps/makePayment.php';
          $response= $this->api_call($url,$api_data);
          $log_path = realpath(Yii::$app->basePath)."/modules/resources/log/";
          $this->writeLog($log_path,"Log_Data",$api_data);
          $log_data = " MAKE PAYMENT DATA RESPONSE : ".json_encode($response);
          $this->writeLog($log_path,"Log_Data",$log_data);
          return $this->render('thankyou');
        }else{
          $response = "PAYMENT FAILED";
          return $this->render('thankyou'); 
        }
        
      }
      
      public function actionPaymentstatus(){
        $post = Yii::$app->request->rawBody;
        echo "RESULT:";
        print_r($post);
        $log_path = realpath(Yii::$app->basePath)."/modules/resources/log/";
        $log_data = " MAKE PAYMENT DATA RESPONSE : ".$post;
        $this->writeLog($log_path,"Log_Data",$log_data);
        exit;
        if($post){
          $data=Yii::$app->user->identity;
          $connection = Yii::$app->db;
          $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
          $config = $connection
          ->createCommand($query);
          $config->bindValue(':partner_id',$data['PARTNER_ID']);
          $config_data = $config->queryAll();
          $chk = new Checksum();
          $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
          if(false){
            return Yii::$app->response->statusCode = 401;
          } else {
            foreach($post->BankResponse as $value){
              $connection = Yii::$app->db;  
              $connection->createCommand()
              ->update('tbl_provider_bill_details', ['PAYMENT_STATUS'=>$value['status']], 'MOBILE_NO='.$value->billnumber.' AND INVOICE_ID='.$post->Invoice_no)
              ->execute();
            }
            return Yii::$app->response->statusCode = 200;
          }
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
      
      public function api_call($url,$api_data){
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$api_data);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        print_r($curl_response);
        // exit;
        return json_decode($curl_response,true);
      }
      
      public function actionGet_fields($provider_id=""){
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
          return json_encode($fields);
        }
      }
      
      public function actionGet_billerid(){
        $data=Yii::$app->user->identity;
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
        // echo "<pre>";
        // print_r($billerdata);
        // exit;
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
            $check_utility->bindValue(':user',$data['USER_ID']);
            $check_utility_data = $check_utility->execute();
            $utility_id = $connection->getLastInsertID();
          }
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
            print_r($provider_update_data);
          }
        }
        
        public function actionDownload_csv_file($provider){
          $fields = json_decode($this->actionGet_fields($provider),true);
          $name = md5(uniqid() . microtime(TRUE) . mt_rand()). '.csv';
          header('Content-Type: text/csv');
          header('Content-Disposition: attachment; filename='. $name);
          header('Pragma: no-cache');
          header("Expires: 0");
          
          $outstream = fopen("php://output", "w");
          fputcsv($outstream, $fields);
          fclose($outstream);
          exit;
        }

      /* public function actionVerify_register_biller(){
        $connection = Yii::$app->db;
        $query="Select b.INVOICE_ID,b.PROVIDER_ID,b.ACCOUNT_NO,b.USER_ID from tbl_provider_bill_details as b JOIN tbl_registered_account as r on b.ACCOUNT_NO=r.ACCOUNT_NO where r.IS_REGISTERED=1";
        $verify_register = $connection
        ->createCommand($query);
        $verify_register_data = $config->queryAll();
        foreach($verify_register as $key=>$value){
          $verify_request_data = [  
              "requestid"=>"2",
              "privatekey"=>"",
              "mercid"=>"245",
              "checksum"=>"",
              "customerid"=>$value['USER_ID'],
              "billerid"=>"1",
              "account_id"=>"7738894562"
            ];
          }
       } 

       /*public function actionVerify_bill_details(){
         
       } 
       
       public function actionVerify_make_payment(){
         
       } */

      }
      