<?php

use yii\db\Schema;

/* @var $this yii\web\View */
/* @var $tableDefinitions yii\db\TableSchema[] */
/* @var $migrationName string */
/* @var $tableOptions string */


function writeLine($spaceCount, $str)
{
    echo str_repeat(' ', $spaceCount);
    echo $str;
    echo "\n";
}


$tmp = array_keys($tableDefinitions);
$lastTableName = end($tmp);

$tableOptions = addcslashes($tableOptions, "'");


echo "<?php\n";
?>

use yii\db\Schema;

class <?= $migrationName ?> extends yii\db\Migration
{
    public function up()
    {
<?php
        foreach ($tableDefinitions as $tableName => $tableDefinition) {
            writeLine(8, "\$this->createTable('{$tableName}', [");

            foreach ($tableDefinition['columns'] as $columnName => $columnDefinition) {
                writeLine(12, "'$columnName' => " . implode('', $columnDefinition) . ",");
            }

            $currentTableOptions = $tableOptions;
            if ($tableDefinition['tableComment']) {
                $tableComment = addcslashes($tableDefinition['tableComment'], "'");
                $currentTableOptions .= " COMMENT=\"{$tableComment}\"";
                $currentTableOptions = trim($currentTableOptions);
            }

            writeLine(8, "]" . ($currentTableOptions ? ", '{$currentTableOptions}'" : '') . ");");


            if ($tableDefinition['indexes']) echo "\n";
            foreach ($tableDefinition['indexes'] as $keyName => $keyDefinition) {
                writeLine(8, "\$this->createIndex('{$keyName}', '{$tableName}', '" . implode(', ', $keyDefinition['columns']) . "', {$keyDefinition['isUnique']});");
            }


            if ($tableName != $lastTableName) echo "\n\n";
        }

        $foreignKeyExists = false;
        foreach ($tableDefinitions as $tableName => $tableDefinition) {
            if ($tableDefinition['foreignKeys']) {
                if (!$foreignKeyExists) { $foreignKeyExists = true; echo "\n"; }
                echo "\n";
            }

            // strange thing but if we want to get the same sql as in "SHOW CREATE TABLE", then we should add foreign keys in reverse order
            foreach (array_reverse($tableDefinition['foreignKeys']) as $foreignKeyName => $foreignKeyDefinition) {
                $onDelete = $foreignKeyDefinition['onDelete'];
                $onUpdate = $foreignKeyDefinition['onUpdate'];
                $onDelete = ($onDelete ? "'{$onDelete}'" : 'null');
                $onUpdate = ($onUpdate ? "'{$onUpdate}'" : 'null');
                writeLine(8, "\$this->addForeignKey('{$foreignKeyName}', '{$tableName}', '{$foreignKeyDefinition['column']}', '{$foreignKeyDefinition['foreignTable']}', '{$foreignKeyDefinition['foreignColumn']}', {$onDelete}, {$onUpdate});");
            }
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
                writeLine(8, "\$this->dropTable('{$tableName}');");
            }

            if (count($tableDefinitions) > 1) echo "\n";
            writeLine(8, "\$this->execute('SET FOREIGN_KEY_CHECKS = 1');");
        }
?>
    }
}
