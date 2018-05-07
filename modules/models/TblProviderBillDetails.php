<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "tbl_provider_bill_details".
 *
 * @property integer $provider_bill_details_id
 * @property integer $PROVIDER_BILL_UPLOAD_DETAILS_ID
 * @property integer $PROVIDER_ID
 * @property string $ref_no
 * @property string $register_biller_flag
 * @property string $removed
 * @property string $IS_REGISTER
 * @property double $issue_date
 * @property double $due_date
 * @property double $amount
 * @property double $created_date
 * @property double $modified_date
 * @property string $FNAME
 * @property string $LNAME
 * @property integer $UTILITY_ID
 * @property string $EMAIL
 * @property integer $MOBILE_NO
 */
class TblProviderBillDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_provider_bill_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PROVIDER_BILL_UPLOAD_DETAILS_ID', 'PROVIDER_ID', 'FNAME', 'LNAME', 'UTILITY_ID', 'EMAIL', 'MOBILE_NO'], 'required'],
            [['PROVIDER_BILL_UPLOAD_DETAILS_ID', 'PROVIDER_ID', 'UTILITY_ID'], 'integer'],
            [['register_biller_flag', 'removed', 'IS_REGISTER'], 'string'],
            [['issue_date', 'due_date', 'amount', 'created_date', 'modified_date'], 'number'],
            [['ref_no'], 'string', 'max' => 50],
            [['FNAME', 'LNAME', 'EMAIL','MOBILE_NO'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_bill_details_id' => 'Provider Bill Details ID',
            'PROVIDER_BILL_UPLOAD_DETAILS_ID' => 'Provider  Bill  Upload  Details  ID',
            'PROVIDER_ID' => 'Provider  ID',
            'ref_no' => 'Ref No',
            'register_biller_flag' => 'Register Biller Flag',
            'removed' => 'Removed',
            'IS_REGISTER' => 'Is  Register',
            'issue_date' => 'Issue Date',
            'due_date' => 'Due Date',
            'amount' => 'Amount',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
            'FNAME' => 'Fname',
            'LNAME' => 'Lname',
            'UTILITY_ID' => 'Utility  ID',
            'EMAIL' => 'Email',
            'MOBILE_NO' => 'Mobile  No',
        ];
    }
}
