## Yii2 Gii Extended
- model template with TimestampBehavior and BlameableBehavior according to columns

### Usage
```
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'with-behaviors' => '@vendor/panwenbin/yii2-gii-extended/src/generators/model/default',
                ],
            ],
        ],
    ];
```