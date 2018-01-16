Установка
===

Устанавливаем зависимость:

```
composer require yii2lab/yii2-migration
```

Объявить конфиг в `main.php`:

```php
return [
	...
	'controllerMap' => [
		'migrate' => [
			'class' => 'dee\console\MigrateController',
			'migrationPath' => '@console/migrations',
			'generatorTemplateFiles' => [
				'create_table' => '@yii2lab/migration/yii/views/createTableMigration.php',
			],
		],
	],
	...
];
```
