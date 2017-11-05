<?php

namespace yii2lab\migration\helpers;

use Yii;
use yii2lab\helpers\Helper;
use yii\helpers\ArrayHelper;
use yii2lab\helpers\yii\FileHelper;

class MigrationHelper {
	
	private static $aliases;
	private static $config;
	
	public static function setPath($config) {
		self::$config = $config;
		if(APP != CONSOLE) {
			return $config;
		}
		$config['params']['dee.migration.path'] = ArrayHelper::merge(
			$config['params']['dee.migration.path'], 
			self::getAliases()
		);
		return $config;
	}
	
	private static function getAliases() {
		self::$aliases = [];
		$apps = Helper::getApps();
		$apps = ArrayHelper::merge($apps, Helper::getApiSubApps());
		foreach($apps as $app) {
			self::getAppMigrations($app);
		}
		foreach(self::$config['params']['dee.migration.scan'] as $target) {
			self::scanMigrations($target);
		}
		$aliases = array_unique(self::$aliases);
		return $aliases;
	}

	private static function addMigrationsDir($dir) {
		if(is_dir($dir)) {
			self::$aliases[] = '@' . $dir;
		}
	}
	
	private static function scanMigrations($path) {
		$dir = Yii::getAlias($path);
		$pathList = FileHelper::findFiles($dir);
		foreach($pathList as $pathItem) {
			if(strpos($pathItem, 'migrations') !== false) {
				$alias = self::extractAlias($pathItem);
				self::addMigrationsDir($alias);
			}
		}
	}
	
	private static function extractAlias($pathItem) {
		$dirName = dirname($pathItem);
		$dirName = str_replace(ROOT_DIR . DS, '', $dirName);
		$dirName = str_replace('\\', '/', $dirName);
		return $dirName;
	}
	
	private static function getAppMigrations($app) {
		self::addMigrationsDir($app . '/migrations');
		$modules = Helper::getModules($app);
		foreach($modules as $module) {
			$dir = $app . '/modules/' . $module . '/migrations';
			self::addMigrationsDir($dir);
		}
	}
	
}
