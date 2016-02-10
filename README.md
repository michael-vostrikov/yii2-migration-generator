Yii 2 Migration Generator
=========================

Migration generator for Gii. Generates migration file for the specified database table.

Place these files into "common/modules/gii" directory.
Change Gii configuration in "backend/config/main-local.php"

```php
if (!YII_ENV_TEST) {
    ...

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = include(Yii::getAlias('@common/modules/gii/config.php'));
}
```
<br>

Example:

```sql
CREATE TABLE `log` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`transaction_id` INT(11) NOT NULL,
	`model_class_id` INT(11) NOT NULL,
	`model_id` INT(11) NOT NULL,
	`record_type_id` INT(11) NOT NULL,
	`record_time` DATETIME NOT NULL,
	`old_values` LONGTEXT NOT NULL COMMENT 'Old model field values in JSON',
	`new_values` LONGTEXT NOT NULL COMMENT 'New model field values in JSON',
	PRIMARY KEY (`id`),
	INDEX `find_models_by_record_time` (`model_class_id`, `record_type_id`, `record_time`),
	INDEX `find_model_records` (`model_class_id`, `model_id`, `record_time`),
	INDEX `FK_log_record_type` (`record_type_id`),
	INDEX `FK_log_transaction` (`transaction_id`),
	CONSTRAINT `FK_log_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`id`),
	CONSTRAINT `FK_log_record_model_class` FOREIGN KEY (`model_class_id`) REFERENCES `model_class` (`id`),
	CONSTRAINT `FK_log_record_type` FOREIGN KEY (`record_type_id`) REFERENCES `log_record_type` (`id`)
)
COMMENT='Log of model changes';
```
<br>

Result:

```php
<?php

use yii\db\Schema;

class m160210_040000_create_log extends yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%log}}', [
            'id' => $this->primaryKey()->notNull(),
            'transaction_id' => $this->integer()->notNull(),
            'model_class_id' => $this->integer()->notNull(),
            'model_id' => $this->integer()->notNull(),
            'record_type_id' => $this->integer()->notNull(),
            'record_time' => $this->dateTime()->notNull(),
            'old_values' => $this->getDb()->getSchema()->createColumnSchemaBuilder('LONGTEXT')->notNull() . ' COMMENT "Old model field values in JSON"',
            'new_values' => $this->getDb()->getSchema()->createColumnSchemaBuilder('LONGTEXT')->notNull() . ' COMMENT "New model field values in JSON"',
        ], 'COMMENT="Log of model changes"');

        $this->createIndex('find_models_by_record_time', '{{%log}}', 'model_class_id, record_type_id, record_time', false);
        $this->createIndex('find_model_records', '{{%log}}', 'model_class_id, model_id, record_time', false);
        $this->createIndex('FK_log_record_type', '{{%log}}', 'record_type_id', false);
        $this->createIndex('FK_log_transaction', '{{%log}}', 'transaction_id', false);


        $this->addForeignKey('FK_log_record_type', '{{%log}}', 'record_type_id', '{{%log_record_type}}', 'id', null, null);
        $this->addForeignKey('FK_log_record_model_class', '{{%log}}', 'model_class_id', '{{%model_class}}', 'id', null, null);
        $this->addForeignKey('FK_log_transaction', '{{%log}}', 'transaction_id', '{{%transaction}}', 'id', null, null);
    }

    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->dropTable('{{%log}}');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}

```
<br>

Example:

```sql
CREATE TABLE session
(
    id CHAR(40) NOT NULL PRIMARY KEY,
    expire INTEGER,
    data BLOB
)
```
<br>

Result:

```php
<?php

use yii\db\Schema;

class m160210_040000_create_session extends yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%session}}', [
            'id' => $this->getDb()->getSchema()->createColumnSchemaBuilder('CHAR(40)')->notNull() . ' PRIMARY KEY',
            'expire' => $this->integer(),
            'data' => $this->getDb()->getSchema()->createColumnSchemaBuilder('BLOB'),
        ]);
    }

    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->dropTable('{{%session}}');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}

```
<br>

There are 2 code templates - 'default' and 'sql'. Default template generates migration calls of migration funcitons. Sql template generates direct call of SQL code of 'SHOW CREATE TABLE' result.

```php
class m160210_043736_create_log extends yii\db\Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE {{%log}} (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `transaction_id` int(11) NOT NULL,
              `model_class_id` int(11) NOT NULL,
              `model_id` int(11) NOT NULL,
              `record_type_id` int(11) NOT NULL,
              `record_time` datetime NOT NULL,
              `old_values` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Old model field values in JSON',
              `new_values` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'New model field values in JSON',
              PRIMARY KEY (`id`),
              KEY `find_models_by_record_time` (`model_class_id`,`record_type_id`,`record_time`),
              KEY `find_model_records` (`model_class_id`,`model_id`,`record_time`),
              KEY `FK_log_record_type` (`record_type_id`),
              KEY `FK_log_transaction` (`transaction_id`),
              CONSTRAINT `FK_log_transaction` FOREIGN KEY (`transaction_id`) REFERENCES {{%transaction}} (`id`),
              CONSTRAINT `FK_log_record_model_class` FOREIGN KEY (`model_class_id`) REFERENCES {{%model_class}} (`id`),
              CONSTRAINT `FK_log_record_type` FOREIGN KEY (`record_type_id`) REFERENCES {{%log_record_type}} (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Log of model changes'
        ");
    }

    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->execute('DROP TABLE {{%log}}');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}
```
<br>

```php
class m160210_040000_create_session extends yii\db\Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE {{%session}} (
              `id` char(40) COLLATE utf8_unicode_ci NOT NULL,
              `expire` int(11) DEFAULT NULL,
              `data` blob,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
    }

    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->execute('DROP TABLE {{%session}}');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}
```
