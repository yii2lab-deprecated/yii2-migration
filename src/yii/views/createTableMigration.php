<?php

/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */
/* @var $table string the name table */
/* @var $fields array the fields */
/* @var $foreignKeys array the foreign keys */

echo "<?php\n";
if (!empty($namespace)) {
	echo "\nnamespace {$namespace};\n";
}
?>

use yii2lab\migration\db\MigrationCreateTable as Migration;

/**
* Handles the creation of table `<?= $table ?>`.
<?= $this->render('@yii/views/_foreignTables.php', [
	'foreignKeys' => $foreignKeys,
]) ?>
*/
class <?= $className ?> extends Migration
{
	public $table = '{{%<?= $table ?>}}';

	/**
	 * @inheritdoc
	 */
	public function getColumns()
	{
	<?= $this->render('_createTable', [
		'table' => $table,
		'fields' => $fields,
		'foreignKeys' => $foreignKeys,
	])?>
	}

	public function afterCreate()
	{
		<?= $this->render('_addForeignKeys', [
			'table' => $table,
			'foreignKeys' => $foreignKeys,
		])?>

	}

}
