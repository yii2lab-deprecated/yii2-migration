<?php

namespace yii2lab\migration\helpers;

use yii2lab\helpers\Helper;
use yii\helpers\ArrayHelper;

class MigrationHelper {
	
	private static $aliases;
	
	public static function setPath($config) {
		if(APP != CONSOLE) {
			return $config;
		}
		$config['params']['dee.migration.path'] = ArrayHelper::merge(
			$config['params']['dee.migration.path'], 
			self::getAliases()
		);
		return $config;
	}
	
	private function getAliases() {
		self::$aliases = [];
		$apps = Helper::getApps();
		$apps = ArrayHelper::merge($apps, Helper::getApiSubApps());
		foreach($apps as $app) {
			self::getAppMigrations($app);
		}
		return self::$aliases;
	}

	private function addMigrationsDir($dir) {
		if(is_dir($dir)) {
			self::$aliases[] = '@' . $dir;
		}
	}
	
	private function getAppMigrations($app) {
		self::addMigrationsDir($app . '/migrations');
		$modules = Helper::getModules($app);
		foreach($modules as $module) {
			$dir = $app . '/modules/' . $module . '/migrations';
			self::addMigrationsDir($dir);
		}
	}
	
}
