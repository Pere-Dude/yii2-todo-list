<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $header
 * @property string|null $description
 * @property int $completion_date
 * @property int $created_at
 * @property int $updated_at
 * @property int $priority
 * @property int $status
 * @property int $creator
 * @property int $responsible
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['header', 'description', 'date_filter', 'date_filter'], 'safe'],
            [['completion_date', 'priority', 'status', 'responsible', 'creator'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'header' => 'Заголовок',
            'description' => 'Описание',
            'completion_date' => 'Дата окончания',
            'priority' => 'Приоретет',
            'status' => 'Статус',
            'creator' => 'Создатель',
            'responsible' => 'Ответственный',
        ];
    }
}
