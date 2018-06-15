<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "tbl_transcation_details".
 *
 * @property integer $ID
 * @property integer $INVOICE_ID
 * @property integer $AIRPAY_ID
 * @property string $PAYMENT_DATE
 * @property double $TOTAL_AMOUNT
 * @property double $FINAL_AMOUNT_RECIEVED
 * @property string $PAYMENT_STATUS
 * @property integer $PAYMENT_STATUS_CODE
 * @property string $PAY_METHOD
 * @property string $CREATED_ON
 * @property string $UPDATED_ON
 */
class TblTranscationDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_transcation_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['INVOICE_ID', 'AIRPAY_ID', 'PAYMENT_DATE', 'TOTAL_AMOUNT', 'FINAL_AMOUNT_RECIEVED', 'PAYMENT_STATUS', 'PAYMENT_STATUS_CODE', 'PAY_METHOD', 'PAY_MODE' , 'UPDATED_ON'], 'required'],
            [['INVOICE_ID', 'AIRPAY_ID', 'PAYMENT_STATUS_CODE'], 'integer'],
            [['PAYMENT_DATE', 'CREATED_ON', 'UPDATED_ON'], 'safe'],
            [['TOTAL_AMOUNT', 'FINAL_AMOUNT_RECIEVED'], 'number'],
            [['PAYMENT_STATUS', 'PAY_METHOD'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'INVOICE_ID' => 'Invoice  ID',
            'AIRPAY_ID' => 'Airpay  ID',
            'PAYMENT_DATE' => 'Payment  Date',
            'TOTAL_AMOUNT' => 'Total  Amount',
            'FINAL_AMOUNT_RECIEVED' => 'Final  Amount  Recieved',
            'PAYMENT_STATUS' => 'Payment  Status',
            'PAYMENT_STATUS_CODE' => 'Payment  Status  Code',
            'PAY_METHOD' => 'Pay  Method',
            'CREATED_ON' => 'Created  On',
            'UPDATED_ON' => 'Updated  On',
            'PAY_MODE' => 'pay mode';
        ];
    }
}
