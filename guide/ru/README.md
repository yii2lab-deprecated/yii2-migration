Миграции БД
===

## Объявление миграций

Если мы подгружаем композер-пакет, 
у которого имеются миграции, 
то необходимо объявить алиасы нахождения миграций пакета в параметрах.

Параметры миграций в файле `common/config/params.php`

```php
return [
	
	...

	'dee.migration.path' => [
		'@vendor/yii2module/yii2-rest-client/src/migrations',
		'@vendor/yii2lab/yii2-notify/src/migrations',
		'@vendor/yii2lab/yii2-qr/src/migrations',
	],
	'dee.migration.scan' => [
		'@domain',
	],
	
	...

];
```

где

* dee.migration.path - конкретные пути с классами миграций
* dee.migration.scan - общие пути для поиска миграций