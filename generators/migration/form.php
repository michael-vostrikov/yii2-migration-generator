<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\migration\Generator */

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'migrationName');
echo $form->field($generator, 'migrationPath');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'tableOptions');
echo $form->field($generator, 'db');
?>

<script>
    <?php ob_start(); ?>

    // migration generator: translate table name to migration name
    $('#migration-generator #generator-tablename').on('blur', function () {
        var tableName = $(this).val();
        var migrationName = $('#generator-migrationname').val();

        if (migrationName === '') migrationName += 'create';

        if ((migrationName.match(/.*create$/)) && tableName) {
            if (tableName === '*' || tableName.indexOf(',') != -1) tableName = 'tables';
            migrationName += '_' + tableName;
            $('#generator-migrationname').val(migrationName).blur();
        }
    });

    <?php $this->registerJs(ob_get_clean()); ?>
</script>
