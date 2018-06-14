<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "tbl_invoice_bill_details".
 *
 * @property integer $ID
 * @property integer $PROVIDER_BILL_DETAILS_ID
 * @property integer $INVOICE_ID
 * @property string $INVOICE_GENERATED_DATE
 * @property string $PAYMENT_STATUS
 * @property string $CREATED_DATE
 * @property string $MODIFIED_DATE
 */
class TblInvoiceBillDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_invoice_bill_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PROVIDER_BILL_DETAILS_ID', 'INVOICE_GENERATED_DATE', 'PAYMENT_STATUS', 'MODIFIED_DATE'], 'required'],
            [['PROVIDER_BILL_DETAILS_ID', 'INVOICE_ID'], 'integer'],
            [['INVOICE_GENERATED_DATE', 'CREATED_DATE', 'MODIFIED_DATE'], 'safe'],
            [['PAYMENT_STATUS'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'PROVIDER_BILL_DETAILS_ID' => 'Provider  Bill  Details  ID',
            'INVOICE_ID' => 'Invoice  ID',
            'INVOICE_GENERATED_DATE' => 'Invoice  Generated  Date',
            'PAYMENT_STATUS' => 'Payment  Status',
            'CREATED_DATE' => 'Created  Date',
            'MODIFIED_DATE' => 'Modified  Date',
        ];
    }
}
