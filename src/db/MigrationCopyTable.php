<?php

namespace yii2lab\migration\db;

use Yii;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii2lab\console\helpers\Output;

/**
 * Migration
 */
abstract class MigrationCopyTable extends Migration
{
	
	public $fromTable;
	public $toTable;
	
	abstract protected function insertRow($row);
	
	protected function batchInsertToNewTable($rows) {
		foreach($rows as $row) {
			$this->insertRow($row);
		}
	}
	
	protected function allRowsFromOldTable() {
		if(empty($this->fromTable)) {
			Output::block('Not configure parameter "fromTable"');
			return [];
		}
		try {
			$query = new Query;
			$query->from($this->fromTable);
			$rows = $query->all();
			$rows = ArrayHelper::toArray($rows);
			return $rows;
		} catch(\yii\db\Exception $e) {
			Output::block('Not found table "'.$this->fromTable.'"');
			return [];
		}
	}
	
	protected function insertRowToNewTable($row) {
		if(empty($this->toTable)) {
			Output::block('Not configure parameter "toTable"');
			exit;
		}
		try {
			Yii::$app->db->createCommand()->insert($this->toTable, $row)->execute();
		} catch(\Exception $e) {
			Output::line($e->getMessage());
		}
	}

}
