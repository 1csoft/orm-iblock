## Orm для инфоблоков битрикс
Пока разработчики собираются или не собираются что-то делать на тему D7 для инфблоков, приходится как-то выкручиавться.
Эта библиотека не беитриксовый модуль, устаналивается через композер.

### Вот что он умеет
* Делать выборки по инфоблокам и свойствам в привычном синтаксисе Entity\DataManager D7
* Фильтровать по инфоблокам и свойствам, по полям привязанных эелементов и по свойствам этих же привязанных элементов(только если в настройках св-ва указан тип и ID инфблока привязки)
* Работать как Element::getList так и через new Query()
* Получать отдельные св-ва элемента по коду или ид св-ва.
* Строить сущности через OrmIblock\ElementTable::getEntity($iblock_id) со свойствами и без, которые потом использовать в полях Reference
* Имеет поддержку инфоблоков v1.

#### Установка 
`composer require 1csoft/orm-iblock`

#### Пользование
```php
require_once(<path to vendor/autoload.php>);
use Soft1c\OrmIblock;

$rs = OrmIblock\ElementTable::getList([
	'filter' => [
        'IBLOCK_ID' => $iblock, 
        'PROPERTY.DISTRIBUTION_TYPE_ENUM.XML_ID'=>'ONLINE' // фильтруем записи по XMK_ID списочного св-ва
	],
	'select' => [
		'ID',
		'ADDITIONAL_MIN' =>'PROPERTY.ADDITIONAL_MIN', // HTML св-во
		'PRICES' => 'PROPERTY.PRODUCT_PRICE_REF.PROPERTY.PRISE_FOR', // св-во привяззаного элемента
		'DISTRIBUTION_TYPE' => 'PROPERTY.DISTRIBUTION_TYPE' // списочное св-во - тутвернется только ID варианта списка
	],
	'limit' => 20
]);
while ($item = $rs->fetch()){
	$result[] = $item;
}

dump($result);
```
Использование в ситаксисе query 
```php
$q = OrmIblock\ElementTable::query($iblock);
$rs = $q
	->setSelect([
		'ID', 'NAME',
		'DISTRIBUTION_TYPE' => 'PROPERTY.DISTRIBUTION_TYPE_ENUM.VALUE', // получение значения варианта списка
		'DISTRIBUTION_LINK' => 'PROPERTY.DISTRIBUTION_LINK',
		'PREVIEW_PICTURE',
		'SRC' => 'PREVIEW_PICTURE_FILE.PATH', // вернется путь картинки от корня сайта
		'FILE_SIZE' => 'PREVIEW_PICTURE_FILE.FILE_SIZE' // человекопонятный размер файла
	])
	->setFilter([
		'IBLOCK_ID' => $iblock,
		'=ID' => 20071776,
	])
	->setOrder(['ID' => 'ASC'])
;
$result = $rs->exec()->fetch();
dump($result);
```
