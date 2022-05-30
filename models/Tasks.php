<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property integer $id
 * @property string $header
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $completion_date
 * @property integer $priority
 * @property integer $creator
 * @property integer $responsible
 */
class Tasks extends ActiveRecord
{
    const TO_FULFILLMENT = 1;
    const PERFORMED = 2;
    const COMPLETED = 3;
    const CANCELLED = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks}}';
    }

    public function rules()
    {
        return [
            [
                ['header', 'description'], 'safe'
            ],
            [['completion_date', 'priority', 'status', 'responsible', 'creator'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'header' => 'Заголовок',
            'description' => 'Описание',
            'completion_date' => 'Дата окончания',
            'priority' => 'Приоретет',
            'status' => 'Статус',
            'creator' => 'Создатель',
            'responsible' => 'Ответственный',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

}