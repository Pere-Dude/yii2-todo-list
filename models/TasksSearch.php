<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TasksSearch represents the model behind the search form of `app\models\Tasks`.
 */
class TasksSearch extends Tasks
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'completion_date', 'created_at', 'updated_at', 'priority', 'status', 'creator', 'responsible'], 'integer'],
            [['header', 'description'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Tasks::find();
        $today_date = strtotime(date('Y-m-d'));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 20,
            ],

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'completion_date' => $this->completion_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'priority' => $this->priority,
            'status' => $this->status,
            'creator' => $this->creator,
            'responsible' => $this->responsible,
        ]);

        $query->andFilterWhere(['like', 'header', $this->header])
            ->andFilterWhere(['like', 'description', $this->description]);


        //На сегодня
        if ($_GET["date_filter"] == 'today') {
            $query->andFilterWhere(['=', 'completion_date', $today_date]);
            if (!User::isAdmin()) {
                $query->andFilterWhere(['=', 'responsible', Yii::$app->user->id]);
            }
        }

        //На неделю
        if ($_GET["date_filter"] == 'week') {
            $query->andFilterWhere(['>=', 'completion_date', $today_date]);
            $query->andFilterWhere(['<', 'completion_date', time() + (7 * 24 * 60 * 60 + 1)]);
            if (!User::isAdmin()) {
                $query->andFilterWhere(['=', 'responsible', Yii::$app->user->id]);
            }
        }

        //На будущее
        if ($_GET["date_filter"] == 'future') {
            $query->andFilterWhere(['>', 'completion_date', $today_date]);
            if (!User::isAdmin()) {
                $query->andFilterWhere(['=', 'responsible', Yii::$app->user->id]);
            }
        }

        return $dataProvider;
    }
}
