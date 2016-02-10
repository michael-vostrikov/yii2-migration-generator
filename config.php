<?php

return [
    'class' => 'yii\gii\Module',
    'generators' => [
        'migration' => [
            'class' => 'common\modules\gii\generators\migration\Generator',
            'templates' => [
                'default' => '@common/modules/gii/generators/migration/default',
                'sql' => '@common/modules/gii/generators/migration/sql',
            ],
        ],
    ]
];
