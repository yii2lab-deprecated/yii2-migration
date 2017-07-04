<?php
namespace yii2lab\migration\db;

use Yii;
use yii\db\Migration;

/**
 * Migration
 */
class MigrationCreateTable extends Migration
{
	
	protected $tableOptions;

	protected $table;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		
		$this->initTableName();
		$this->initBugFix();
	}
	
	public function getTableOptions($engine = 'InnoDB') {
		return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=' . $engine;
	}
	
	private function initBugFix() {
		switch (Yii::$app->db->driverName) {
			case 'mysql':
				$this->tableOptions = $this->getTableOptions();
				break;
			case 'pgsql':
				$this->tableOptions = null;
				break;
			default:
				throw new \RuntimeException('Your database is not supported!');
		}
	}
	
	private function initTableName() {
		if(!empty($this->table)) {
			return;
		}
		$className = basename(get_class($this));
		$classNameArr = explode('_', $className);
		$classNameArrStriped = array_slice($classNameArr, 3, -1);
		$this->table = implode('_', $classNameArrStriped);
	}
	
	public function myCreateTable($columns, $options = null)
	{
		if(method_exists($this, 'beforeCreate')) {
			$this->beforeCreate();
		}
		$options = $options ? $options : $this->tableOptions;
		
		
		$tableSchema = Yii::$app->db->schema->getTableSchema($this->table);
		if ($tableSchema === null) {
			$result = parent::createTable($this->table, $columns, $options);
		}
		
		if(method_exists($this, 'afterCreate')) {
			$this->afterCreate();
		}
		return true;
	}
	
	public function myDropTable()
	{
		if(method_exists($this, 'beforeDrop')) {
			$this->beforeDrop();
		}
		$result = parent::dropTable($this->table);
		if(method_exists($this, 'afterDrop')) {
			$this->afterDrop();
		}
		return $result;
	}
	
	public function pureTableName($table = null)
	{
		if(empty($table)) {
			$table = $this->table;
		}
		$table = str_replace(['{','}','%'], '', $table);
		return $table;
	}
	
	private function generateNameForKey($type, $name, $data = null) {
		return $type . '-' . $name . '-' . hash('crc32b', serialize($data));
	}
	
	public function myAddForeignKey($columns, $refTable, $refColumns, $delete = null, $update = null)
	{
		$name = $this->generateNameForKey('fk', $this->pureTableName(), [$columns, $refTable, $refColumns]);
		return $this->addForeignKey($name, $this->table, $columns, $refTable, $refColumns, $delete, $update);
	}
	
	public function myCreateIndex($columns, $unique = false)
	{
		$columns = is_array($columns) ? $columns : [$columns];
		$type = $unique ? 'uni' : 'idx';
		$name = $this->generateNameForKey($type, $this->pureTableName(), $columns);
		return parent::createIndex($name, $this->table, $columns, $unique);
	}
	
	public function myAddPrimaryKey($columns)
	{
		$columns = is_array($columns) ? $columns : [$columns];
		$name = $this->generateNameForKey('pk', $this->pureTableName(), $columns);
		return parent::addPrimaryKey($name, $this->table, $columns);
	}
	
	public function myCreateIndexUnique($columns)
	{
		return $this->myCreateIndex($columns, true);
	}
	
	public function getDump()
	{
		return COMMON;
	}
	
	public function up()
	{
		$this->myCreateTable($this->getColumns());
	}
	
	public function down()
	{
		$this->myDropTable();
	}

}
