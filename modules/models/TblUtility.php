<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "tbl_utility".
 *
 * @property integer $utility_id
 * @property string $utility_name
 * @property integer $user_id
 * @property string $is_disabled
 * @property double $created_date
 * @property double $modified_data
 */
class TblUtility extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_utility';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['is_disabled'], 'string'],
            [['created_date', 'modified_data'], 'number'],
            [['utility_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'utility_id' => 'Utility ID',
            'utility_name' => 'Utility Name',
            'user_id' => 'User ID',
            'is_disabled' => 'Is Disabled',
            'created_date' => 'Created Date',
            'modified_data' => 'Modified Data',
        ];
    }
}
