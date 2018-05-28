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
		// print_r($data);
		// exit;
		// if(!($data['USER_ID'])){
      //   $this->redirect('/web');
      // }
      $utilities = TblUtility::find()->all();
      return $this->render('index',array('utilities'=>$utilities));
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
          $provider_data['id']=$value->provider_id;
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
          $data=Yii::$app->user->identity;
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
          }
          $bill_details=array();
          $data=array();
          $fields_data=array();
          $handle = fopen( Yii::$app->getBasePath()."/modules/resources/upload/".$uploadedFile_data['file_name'], "r");
          fgetcsv($handle);
          while (($fileop = fgetcsv($handle, 1024, ",")) !== false) 
          {
            $i=1;
            $data['account_id']= $fileop[0];
            // $data['fname'] = $fileop[0];
            // $data['lname'] = $fileop[1];
            // $data['email']= $fileop[2];
            // $data['mobile'] = $fileop[3];
            for($i=1;$i<sizeof($start);$i++){
              $fields_data[$fields[$i]]=$fileop[$i];
            }
            $data['details']=json_encode($fields_data);
            $data['billerid']=Yii::$app->request->post('providers');
            $data['remark']=Yii::$app->request->post('utility_name');
            $bill_details[]=$data;
            $this->bill_details($uploadedFile_data,$invoice_id,$data);
          }
          // return $this->render('data_uploaded',array('invoice_id'=>$invoice_id));
        } else{
          echo "Error while uploading file";
        }
      } else {
        // $email = '';
        // $validator = new RequiredValidator();
        
        // if ($validator->validate($email, $error)) {
          //   echo 'Email is valid.';
          // } else {
            //   echo $error;
            // }
            // exit;
            if(Yii::$app->request->post('register')){
              $ref_no=$this->archieve_data();
            }
            $invoice_id = $this->invoice_create();
            $bill_details['account_id']=Yii::$app->request->post(str_replace(' ','_',$fields[0]));
            // $bill_details['fname']=Yii::$app->request->post('fname');
            // $bill_details['lname']=Yii::$app->request->post('lname');
            // $bill_details['email']=Yii::$app->request->post('email');
            // $bill_details['mobile']=Yii::$app->request->post('mobile_no');
            for($i=1;$i<sizeof($fields);$i++){
              $fields_data[$fields[$i]]=Yii::$app->request->post(str_replace(' ','_',$fields[$i]));
            }
            $bill_details['details']=json_encode($fields_data);
            $bill_details['billerid']=Yii::$app->request->post('providers');
            $bill_details['remark']=Yii::$app->request->post('utility_name');
            $this->bill_details(0,$invoice_id,$bill_details);
            return $this->render('loading',array('invoice_id'=>$invoice_id));
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
          $api_data=[
            // 'Invoice_no'=>$invoice_id,
            "requestid"=>'',
            'mercid'=>$config_data[0]['AIRPAY_MERCHANT_ID'],
            // 'utitlity_id'=>Yii::$app->request->post('utility_name'),
            // 'provide_id'=>Yii::$app->request->post('providers'),
            'private_key'=>$privatekey,
            'retunr_url'=>'192.168.1.127/partnerpay/web/bbps/default/get_bill_response',
            "callbackurl"=>'192.168.1.127/partnerpay/web/bbps/default/account_register_response',
            "action"=>"ADD_BILLER",
            'checkSum'=>"",
            'bill_data'=>$bill_details,
          ];
          print_r(json_encode($api_data));
          exit;
          // echo "<pre>";
          // print_r(json_encode($api_data));
          // $curl = curl_init('https://devel-payments.airpayme.com/bbps/bulk_process_invoice.php');
          // curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
          // curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
          // curl_setopt($curl, CURLOPT_POST, true);
          // curl_setopt($ch, CURLOPT_POSTFIELDS, $api_data);
          // $curl_response = curl_exec($curl);
          // curl_close($curl); 
          // print_r($output);
          // exit;
          return $this->render('data_uploaded',array('invoice_id'=>$invoice_id));
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
          $query1="SELECT * FROM tbl_provider_bill_details WHERE USER_ID=:user_id AND IS_REGISTER=:is_register";
          $registered = $connection
          ->createCommand($query1);
          $registered->bindValue(':user_id',$data['USER_ID']);
          $registered->bindValue(':is_register','y');
          $registered_data = $registered->queryAll();
          if(sizeof($registered_data)){
            $query="INSERT into tbl_archived_provider_bill_details SELECT * FROM tbl_provider_bill_details WHERE USER_ID=:user_id AND IS_REGISTER=:is_register";
            $archieve = $connection
            ->createCommand($query);
            $archieve->bindValue(':user_id',$data['USER_ID']);
            $archieve->bindValue(':is_register','y');
            $archieve_data = $archieve->execute();
            if($archieve_data){
              $query2="DELETE FROM tbl_provider_bill_details WHERE USER_ID=:user_id AND IS_REGISTER=:is_register";
              $registered = $connection
              ->createCommand($query2);
              $registered->bindValue(':user_id',$data['USER_ID']);
              $registered->bindValue(':is_register','y');
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
        
        public function bill_details($uploadedFile_data,$invoice_id,$data){
          $model= new TblProviderBillDetails();
          if(Yii::$app->request->post('register')){
            $model->IS_REGISTER='y';
            $connection = Yii::$app->db;
            $query="SELECT REF_NO from tbl_registered_account where ACCOUNT_NO=:account_no AND PROVIDE_ID=:provider_id AND UTILITY_ID=:utility_id AND IS_REGISTERED=1";
            $check_registered = $connection
            ->createCommand($query);
            $check_registered->bindValue(':account_no',$data['mobile']);
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
              $insert_registered->bindValue(':account_no',$data['mobile']);
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
          // $model->FNAME=$data['fname'];
          // $model->LNAME=$data['lname'];
          // $model->EMAIL=$data['email'];
          // $model->MOBILE_NO=$data['mobile'];
          $data=Yii::$app->user->identity;
          $model->USER_ID= $data['USER_ID'];
          $model->INVOICE_ID=$invoice_id;
          if($model->save(false)){
            $billing_details_id=$model->getPrimaryKey();
            // $invoice_bill= new TblInvoiceBillDetails();
            // $invoice_bill->INVOICE_ID=$invoice_id;
            // $invoice_bill->PROVIDER_BILL_DETAILS_ID=$billing_details_id;
            // $invoice_bill->INVOICE_GENERATED_DATE=date("Y-m-d");
            // $invoice_bill->PAYMENT_STATUS="pending";
            // $invoice_bill->MODIFIED_DATE=date("Y-m-d");
            // $invoice_bill->save();
          }
          
        }
        
        public function actionAccount_register_response(){
          $post = Yii::$app->request->rawBody;
          $data2 = json_decode($post);
          $model= new TblProviderBillDetails();
          foreach($data2->BankResponse as $value){
            $connection = Yii::$app->db;  
            $connection->createCommand()
            ->update('tbl_registered_account', ['REF_NO'=>$value->Register_no,'IS_REGISTERED'=>1], 'ACCOUNT_NO='.$value->billnumber.' AND PROVIDER_ID='.$data2->Invoice_no)
            ->execute();
          }
        }
        
        public function actionBill_data_response(){
          $data=Yii::$app->user->identity;
          $connection = Yii::$app->db;
          $query="SELECT AIRPAY_MERCHANT_ID,AIRPAY_USERNAME,AIRPAY_PASSWORD,AIRPAY_SECRET_KEY from tbl_partner_master WHERE PARTNER_ID=:partner_id";
          $config = $connection
          ->createCommand($query);
          $config->bindValue(':partner_id',$data['PARTNER_ID']);
          $config_data = $config->queryAll();
          $chk = new Checksum();
          $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
          
          //   $this->enableCsrfValidation = false;
          //   echo "asdads";
          $post = Yii::$app->request->rawBody;
          $data2 = json_decode($post);
          $model= new TblProviderBillDetails();
          foreach($data2->BankResponse as $value){
            $connection = Yii::$app->db;  
            $connection->createCommand()
            ->update('tbl_provider_bill_details', ['ISSUE_DATE'=>date('Y-m-d H:i:s',strtotime($value->validation_date)),'DUE_DATE'=>date('Y-m-d H:i:s',strtotime($value->billduedate)),'EARLY_DISCOUNT'=>$value->early_billdiscount,'LATE_FEE'=>$value->late_payment_charges,'EARLY_DUE_DATE'=>date('Y-m-d H:i:s',strtotime($value->early_billduedate)),'NET_AMOUNT'=>$value->net_billamount,'AMOUNT'=>$value->billamount,'REF_NO'=>$value->validationid,'RESPONSE_NOT_RECIEVED'=>0], 'MOBILE_NO='.$value->billnumber.' AND INVOICE_ID='.$data2->Invoice_no)
            ->execute();
          }
          $msg=$this->notification($data2->Invoice_no);
          if(isset($msg)){
            return Yii::$app->response->statusCode = 200;
          } else {
            return Yii::$app->response->statusCode = 401;
          }
        }
        
        public function actionListing($invoice_id=""){
          $data=Yii::$app->user->identity;
          $connection = Yii::$app->db;
          $all_invoice = $connection
          ->createCommand('Select SUM(b.NET_AMOUNT) as invoice_amount,SUM(b.RESPONSE_NOT_RECIEVED) as recieved,b.PROVIDER_ID,p.provider_name,b.PAYMENT_STATUS,b.INVOICE_ID,u.utility_name from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id JOIN tbl_utility as u on b.UTILITY_ID=u.utility_id where b.USER_ID=:userid AND b.REMOVED="n" GROUP BY INVOICE_ID Order By INVOICE_ID DESC');
          $all_invoice->bindValue(':userid', $data['USER_ID']);
          $all_invoice_data = $all_invoice->queryAll();
          $query="SELECT utility_id,utility_name from tbl_utility where is_disabled='n'";
          $utility = $connection->createCommand($query);
          $utility_data= $utility->queryAll();
          return  $this->render('listing',array('invoice_id'=>$invoice_id,'invoice_data'=>$all_invoice_data,'utility_data'=>$utility_data));
        }  
        
        public function actionChecking(){
          $connection = Yii::$app->db;
          $checkresponse = $connection
          ->createCommand("Select SUM(NET_AMOUNT) as invoice_amount,SUM(RESPONSE_NOT_RECIEVED) as recieved from  tbl_provider_bill_details where INVOICE_ID=:invoice_id");
          $checkresponse->bindValue(':invoice_id', Yii::$app->request->post('id'));
          $checkresponse_data = $checkresponse->queryAll();
          if($checkresponse_data[0]['recieved']==0){
            echo json_encode($all_invoice_data);
          } else {
            echo false;
          }
        }
        
        public function actionPayment($invoice_id){
          $connection = Yii::$app->db;
          $invoice = $connection
          ->createCommand("Select b.NET_AMOUNT,b.RESPONSE_NOT_RECIEVED,b.PROVIDER_ID,p.provider_name,b.ISSUE_DATE,b.INVOICE_ID,b.DUE_DATE,b.EARLY_DUE_DATE,b.EARLY_DISCOUNT,b.LATE_FEE,b.MOBILE_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id where b.INVOICE_ID=:invoice_id AND b.REMOVED='n'");
          $invoice->bindValue(':invoice_id', $invoice_id);
          $invoice_data = $invoice->queryAll();
          $sum = $this->calculate_sum($invoice_data);
          return $this->render('payment',array('invoice_amount'=>$sum,'invoice_data'=>$invoice_data,'provider'=>$invoice_data[0]['provider_name']));
        }
        
        public function calculate_sum($data){
          $sum=0;
          foreach($data as $value){
            if(strtotime("now")>strtotime($value['DUE_DATE'])){
              $sum = $sum + $value['NET_AMOUNT'] + $value['LATE_FEE'];
            } else if(strtotime("now")<strtotime($value['EARLY_DUE_DATE'])) {
              $sum = $sum + $value['NET_AMOUNT'] - $value['EARLY_DISCOUNT'];
            }else{
              $sum = $sum + $value['NET_AMOUNT'];
            }
          }
          return $sum;
        }
        
        public function actionDeletemobile(){
          $connection = Yii::$app->db;
          $invoice_mobile_delete = $connection->createCommand()
          ->update('tbl_provider_bill_details', ['REMOVED' => 'y'], 'INVOICE_ID='.Yii::$app->request->post('invoice_id').' AND MOBILE_NO='.Yii::$app->request->post('mobile_no'))->execute();
          echo $invoice_data;
          if($invoice_mobile_delete){
            $invoice = $connection
            ->createCommand("Select b.NET_AMOUNT,b.RESPONSE_NOT_RECIEVED,b.PROVIDER_ID,p.provider_name,b.ISSUE_DATE,b.INVOICE_ID,b.DUE_DATE,b.EARLY_DUE_DATE,b.EARLY_DISCOUNT,b.LATE_FEE,b.MOBILE_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id where b.INVOICE_ID=:invoice_id AND b.REMOVED='n'");
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
          $query="SELECT b.PROVIDER_BILL_DETAILS_ID,b.NET_AMOUNT,b.PROVIDER_ID,p.provider_name,DATE_FORMAT(b.ISSUE_DATE,'%d/%m/%Y') as ISSUE_DATE,b.INVOICE_ID,DATE_FORMAT(b.DUE_DATE,'%d/%m/%Y')as DUE_DATE,b.EARLY_DUE_DATE,b.EARLY_DISCOUNT,b.LATE_FEE,b.MOBILE_NO from tbl_provider_bill_details as b JOIN tbl_provider as p on b.PROVIDER_ID=p.provider_id where b.UTILITY_ID=:utility_id AND b.REMOVED='y' AND b.PROVIDER_ID=:provider_id AND USER_ID=:user_id";
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
          $privatekey =$chk->encrypt($config_data[0]['AIRPAY_USERNAME'].":|:".$config_data[0]['AIRPAY_PASSWORD'], $config_data[0]['AIRPAY_SECRET_KEY']);
          $buyerEmail = trim($data['EMAIL']);
          $buyerPhone = trim("9869478152");
          $buyerFirstName = trim($data['FIRST_NAME']);
          $buyerLastName = trim($data['LAST_NAME']);
          $amount = trim("356.00");
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
            ->createCommand("SELECT NET_AMOUNT,RESPONSE_NOT_RECIEVED,PROVIDER_ID,UTILITY_ID,ISSUE_DATE,INVOICE_ID,DUE_DATE,EARLY_DUE_DATE,EARLY_DISCOUNT,LATE_FEE,MOBILE_NO from tbl_provider_bill_details WHERE INVOICE_ID=:invoice_id AND REMOVED='n'");
            $invoice->bindValue(':invoice_id', $_POST['TRANSACTIONID']);
            $invoice_data = $invoice->queryAll();
            $bill_details=array();
            foreach($invoice_data as $value){
              $status= $connection->createCommand()->update('tbl_provider_bill_details', ['PAYMENT_STATUS' => 'pending'], 'INVOICE_ID='.$_POST['TRANSACTIONID'].' AND MOBILE_NO='.$value['MOBILE_NO'])->execute();
              $data['account_id']=$value['MOBILE_NO'];
              $data['ref_no']=$value['REF_NO'];
              $sum=array($value);
              $data['amount']=$this->calculate_sum($sum);
              $bill_details[]=$data;
            }
            $api_data=[
              'Invoice_no'=>Yii::$app->request->post('TRANSACTIONID'),
              'profile_id'=>'',
              'utitlity_id'=>$invoice_data[0]['UTILITY_ID'],
              'provide_id'=>$invoice_data[0]['PROVIDER_ID'],
              'private_key'=>'',
              'retunr_url'=>'',
              'checkSum'=>'',
              'airpay_id'=>Yii::$app->request->post('APTRANSACTIONID'),
              'payment_data'=>$bill_details,
            ];
            //  print_r(json_encode($api_data));
            //  $curl = curl_init('192.168.1.127/partnerpay/web/bbps/default/paymentstatus');
            // curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
            // curl_setopt($curl, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $api_data);
            // $curl_response = curl_exec($curl);
            // curl_close($curl); 
            // print_r($output);
            // exit;
            return $this->render('thankyou');
          }else{
            return $this->render('error'); 
          }
          
        }
        
        public function actionPaymentstatus(){
          $post = Yii::$app->request->rawBody;
          echo "RESULT:";
          print_r($post);
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
            //sms functionality;
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
            // print_r($checkresponse_data[0]['bill_recieved']%5);
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
          return json_decode($curl_response,true);
        }
        
        public function actionGet_fields($provider_id=""){
          if($provider_id==""){
            $provider_id=Yii::$app->request->post('provider_id');
          }
          $connection = Yii::$app->db;
          $query="SELECT FIELDS from tbl_provider WHERE PROVIDER_ID=:provider_id";
          $get_fields = $connection
          ->createCommand($query);
          $get_fields->bindValue(':provider_id',$provider_id);
          $get_fields_data = $get_fields->queryAll();
          return json_encode(explode('|',$get_fields_data[0]['FIELDS']));
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
          echo "<pre>";      
          print_r($billerdata['BILLERDATA']);
        }
      }
      