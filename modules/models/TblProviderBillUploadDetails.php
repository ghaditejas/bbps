<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "tbl_provider_bill_upload_details".
 *
 * @property integer $PROVIDER_BILL_UPLOAD_DETAILS_ID
 * @property string $XLS_NAME
 * @property string $CREATED_DATE
 * @property string $MODIFIED_DATE
 */
class TblProviderBillUploadDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_provider_bill_upload_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['XLS_NAME', 'MODIFIED_DATE'], 'required'],
            [['CREATED_DATE', 'MODIFIED_DATE'], 'safe'],
            [['XLS_NAME'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PROVIDER_BILL_UPLOAD_DETAILS_ID' => 'Provider  Bill  Upload  Details  ID',
            'XLS_NAME' => 'Xls  Name',
            'CREATED_DATE' => 'Created  Date',
            'MODIFIED_DATE' => 'Modified  Date',
        ];
    }
}
