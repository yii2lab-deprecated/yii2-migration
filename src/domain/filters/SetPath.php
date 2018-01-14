<?php

namespace yii2lab\migration\domain\filters;

use common\enums\app\AppEnum;
use Yii;
use yii\base\BaseObject;
use yii2lab\helpers\Helper;
use yii2lab\helpers\ModuleHelper;
use yii2lab\helpers\yii\FileHelper;
use yii2lab\designPattern\filter\interfaces\FilterInterface;
use yii2mod\helpers\ArrayHelper;

class SetPath extends BaseObject implements FilterInterface {

	public $path = [];
	public $scan = [];
	
	private $aliases;
	
	public function isEnabled() {
		return APP == CONSOLE;
	}
	
	public function run($config) {
		$config['params']['dee.migration.scan'] = ArrayHelper::merge(
			ArrayHelper::getValue($config, 'params.dee.migration.scan', []),
			$this->scan
		);
		$config['params']['dee.migration.path'] = ArrayHelper::merge(
			ArrayHelper::getValue($config, 'params.dee.migration.path', []),
			$this->getAliases($config),
			$this->path
		);
		return $config;
	}
	
	private function getAliases($config) {
		$this->aliases = [];
		$apps = AppEnum::values();
		$apps = ArrayHelper::merge($apps, Helper::getApiSubApps());
		foreach($apps as $app) {
			$this->getAppMigrations($app);
		}
		if(!empty($config['params']['dee.migration.scan'])) {
			$scanAliases = $config['params']['dee.migration.scan'];
			if(!empty($scanAliases)) {
				foreach($scanAliases as $target) {
					$this->scanMigrations($target);
				}
			}
		}
		$aliases = array_unique($this->aliases);
		return $aliases;
	}
	
	private function addMigrationsDir($dir) {
		if(is_dir($dir)) {
			$this->aliases[] = '@' . $dir;
		}
	}
	
	private function scanMigrations($path) {
		$dir = Yii::getAlias($path);
		$pathList = FileHelper::findFiles($dir);
		foreach($pathList as $pathItem) {
			if(strpos($pathItem, 'migrations') !== false) {
				$alias = $this->extractAlias($pathItem);
				$this->addMigrationsDir($alias);
			}
		}
	}
	
	private function extractAlias($pathItem) {
		$dirName = dirname($pathItem);
		$dirName = str_replace(ROOT_DIR . DS, '', $dirName);
		$dirName = str_replace('\\', '/', $dirName);
		return $dirName;
	}
	
	private function getAppMigrations($app) {
		$this->addMigrationsDir($app . '/migrations');
		$modules = ModuleHelper::allNamesByApp($app);
		foreach($modules as $module) {
			$dir = $app . '/modules/' . $module . '/migrations';
			$this->addMigrationsDir($dir);
		}
	}
}
