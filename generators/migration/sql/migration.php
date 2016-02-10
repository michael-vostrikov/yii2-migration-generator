<?php

use yii\db\Schema;

/* @var $this yii\web\View */
/* @var $tableDefinitions yii\db\TableSchema[] */
/* @var $migrationName string */

function writeLine($spaceCount, $str)
{
    echo str_repeat(' ', $spaceCount);
    echo $str;
    echo "\n";
}


$tmp = array_keys($tableDefinitions);
$lastTableName = end($tmp);

echo "<?php\n";
?>

use yii\db\Schema;

class <?= $migrationName ?> extends yii\db\Migration
{
    public function up()
    {
<?php
        foreach ($tableDefinitions as $tableName => $tableDefinition) {
            writeLine(8, '$this->execute("');

            echo str_repeat(' ', 12);
            $tableDefinition['createTableSql'] = str_replace("\n", "\n".str_repeat(' ', 12), $tableDefinition['createTableSql']);
            echo addcslashes($tableDefinition['createTableSql'], '"');
            echo "\n";

            writeLine(8, '");');
        }
?>
    }

    public function down()
    {
<?php
        if (count($tableDefinitions) > 0) {
            writeLine(8, "\$this->execute('SET FOREIGN_KEY_CHECKS = 0');");
            if (count($tableDefinitions) > 1) echo "\n";

            foreach (array_reverse($tableDefinitions) as $tableName => $tableDefinition) {
                writeLine(8, "\$this->execute('DROP TABLE {$tableName}');");
            }

            if (count($tableDefinitions) > 1) echo "\n";
            writeLine(8, "\$this->execute('SET FOREIGN_KEY_CHECKS = 1');");
        }
?>
    }
}
