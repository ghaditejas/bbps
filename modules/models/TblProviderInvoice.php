<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "tbl_provider_invoice".
 *
 * @property integer $INVOICE_ID
 * @property string $STATUS
 * @property string $CREATED_DATE
 * @property string $MODIFIED_DATE
 */
class TblProviderInvoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_provider_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['STATUS', 'MODIFIED_DATE'], 'required'],
            [['CREATED_DATE', 'MODIFIED_DATE'], 'safe'],
            [['STATUS'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'INVOICE_ID' => 'Invoice  ID',
            'STATUS' => 'Status',
            'CREATED_DATE' => 'Created  Date',
            'MODIFIED_DATE' => 'Modified  Date',
        ];
    }
}
