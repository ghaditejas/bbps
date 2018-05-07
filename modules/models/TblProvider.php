<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "tbl_provider".
 *
 * @property integer $provider_id
 * @property integer $utility_id
 * @property string $provider_name
 * @property string $is_disabled
 * @property double $created_date
 * @property double $modifide_date
 */
class TblProvider extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $file;
    public static function tableName()
    {
        return 'tbl_provider';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['utility_id'], 'required'],
            [['utility_id'], 'integer'],
            [['is_disabled'], 'string'],
            [['created_date', 'modifide_date'], 'number'],
            [['provider_name','file_name'], 'string', 'max' => 50],
            [['file'],'file','extensions'=>"xls"]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_id' => 'Provider ID',
            'utility_id' => 'Utility ID',
            'provider_name' => 'Provider Name',
            'is_disabled' => 'Is Disabled',
            'created_date' => 'Created Date',
            'modifide_date' => 'Modifide Date',
            'bulk_upload'=>'file_name',
        ];
    }
}
