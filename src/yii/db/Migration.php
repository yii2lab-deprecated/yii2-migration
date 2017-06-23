<?php

namespace yii2lab\migration\yii\db;

use yii\db\Migration as YiiMigration;

class Migration extends YiiMigration
{

	protected function loadDumpSql($dump) {
		if($dump) {
		   	$db = Yii::$app->db;
			$transaction = $db->beginTransaction();
			try {
				$this->execute($dump);
				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollBack();
				throw $e;
			}
		}
	}
	
	public function loadDump()
	{
		if(method_exists($this, 'getDump')) {
			$dump = $this->getDump();
			if(is_array($dump)) {
				$this->myBatchInsert($dump);
			} else {
				$file = $dump;
				$fileParts = explode('/', $file);
				if(count($fileParts) == 1) {
					$file = $fileParts[0] . '/migrations/dump/' . $this->table;
				} elseif(count($fileParts) > 1) {
					$file = $fileParts[0] . '/modules/' . $fileParts[1] . '/migrations/dump/' . $this->table;
				}
				if(file_exists($file . '.php')) {
					$dump = require $file . '.php';
					$this->myBatchInsert($dump);
				} elseif(file_exists($file . '.sql')) {
					$dump = file_get_contents($file . '.sql');
					$dump = str_replace('{_table_}', $this->table, $dump);
					$this->loadDumpSql($dump);
				}
			}
		}
	}
	
	public function myBatchInsert($rows)
	{
		if(empty($rows) || !is_array($rows)) {
			return;
		}
		if(!empty($rows['columns'])) {
			$columns = $rows['columns'];
			unset($rows['columns']);
			foreach($rows as &$row) {
				if(is_array($row)) {
					$new = [];
					foreach($row as $key => $item) {
						$name = $columns[$key];
						$new[$name] = $item;
					}
					$row = $new;
				}
			}
		} else {
			$columns = array_keys($rows[0]);
		}
		$result = $this->batchInsert($this->table, $columns, $rows);
		return $result;
	}
	
}
