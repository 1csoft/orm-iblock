### ElementTable

Т.к. этот класс наследован от Bitrix\Main\Entity\DataManager, он имеет все возможности родителя.

**ElementTable::getEntity()** - построение сущности инфоблока. Если передать в нее ид инфоблока - соберет сущность со свойствами.
```php
Soft1c\OrmIblock\ElementTable::getEntity($iblock_id);
```
В этом случае будет добавлено поле PROPERTY типа Reference на таблицу значений св-в ИБ. Причем, в зависимости от версии ИБ, связь будет либо на
таблицы b_iblock_element_prop_s или на b_iblock_element_property.


**ElementTable::query($iblock_id = null)** - получение объекта Query, если передать ид ИБ, то действие будет аналогично ElementTable::getEntity.

**ElementTable::getList(array $parameters = array())** - стандартный getList, принимает все теже параметры, что и 
Bitrix\Main\Entity\DataManager::getList.

Примеры:
```php
$rs = OrmIblock\ElementTable::getList([
	'filter' => [
	    'IBLOCK_ID' => $iblock, 
	    '=ACTIVE'=>'Y'
	],
	'select' => [
		'ID',
		'ADDITIONAL_MIN' =>'PROPERTY.ADDITIONAL_MIN',
		'PRICES' => 'PROPERTY.PRODUCT_PRICE_REF.PROPERTY.PRISE_FOR',
		'DISTRIBUTION_TYPE' => 'PROPERTY.DISTRIBUTION_TYPE'
	],
	'limit' => 20
]);
```
Получение св-в в выборке идет через референс PROPERTY.CODE, где CODE - символьный код св-ва. 
Если кода у св-ва нет, будет брошено исключение. Ибо, св-ва без кода - это полная хрень.
PROPERTY.ADDITIONAL_MIN - будет выбрано простое строковое или html значение.
Для получение полей и св-в привязанных элементов нужно использовать референс `PROPERTY.CODE_REF`, где CODE - символьный код св-ва,
а приставка _REF показывает что это не просто значение а референс на таблицу с другим ИБ.
Т.о мы можем получить поля элемента `PROPERTY.PRODUCT_PRICE_REF.NAME` и т.д., или св-ва `PROPERTY.PRODUCT_PRICE_REF.PROPERTY.PRISE_FOR`
Причем если PRISE_FOR - это тоже привязка на другой ИБ, то далее можно продолжать цепочку - `PROPERTY.PRODUCT_PRICE_REF.PROPERTY.PRISE_FOR.NAME`
Но строить совсем дикие длинные цепочки не стоит. 
Св-ва типа список получаются таким же образом, только вместо приставки _REF надо использовать _ENUM.
`PROPERTY.DISTRIBUTION_TYPE_ENUM.VALUE` или `PROPERTY.DISTRIBUTION_TYPE_ENUM.XML_ID`.
Все эти правила доступны во всех параметрах getList.
Однако, при фильтрации по `PROPERTY.DISTRIBUTION_TYPE_ENUM.VALUE` надо понимать, что поле VALUE - это строка и на ней нет индекса в БД.
Тоже касается и остальных полей привязок. Все выборки на страх и совесть программера.



**ElementTable::getProperty(array $parameters)** - внутренний метод получение одного или всех свойств элемента по параметрам
DataManager::getList. Этот метод выдирает `Bitrix\Iblock\PropertyTable::getList($parameters)`, а перед этим собирает
Entity таблицы со значениеями конкретного инфоблока и элемента и пристегивает к PropertyTable. Посему, в $parameters обязательно должны быть ключи
```php
$iblockId = (int)$parameters['filter']['IBLOCK_ID'];
$elementId = (int)$parameters['filter']['IBLOCK_ELEMENT_ID'];
```
Этот метод редко приходится юзать, я бы сказал в довольно экзотичных задачах. Более используются его обертки:

**ElementTable::getPropertyByCode(int $elementId, int $iblockId, string $code, $select = false)** - получение св-ва елемента
по его символьному коду.

**ElementTable::getPropertyById(int $elementId, int $iblockId, string $code, $select = false)** - получение св-ва елемента
по его ID.

В обоих методах есть параметр $select и в него можно указать какие поля дополнительно выдрать, если текущее св-во есть 
привязка к другому элементу, категории или списку значений. Множественное свойство будет показано как обычно, но если
оно привязка и в $select указать какое-либо его поле, то вернется только одно значение. Это особенности построения 
множественной привязки к ИБ, как для выборки в getList, так и в этом случае. Может быть в будущем это поменяю.
Синтаксис $select аналогичен синтаксису getList.
```php
$result = OrmIblock\ElementTable::getPropertyByCode(20071776, $iblock, 'PUBLISHERS_RUS', [
	'PUBLISHERS_RUS_NAME' => 'PUBLISHERS_RUS_REF.NAME'
]);
```
PUBLISHERS_RUS - св-во привязка к другому ИБ, и мы хотим получить не только его ИД, но и название PUBLISHERS_RUS_REF.NAME

```php
$result = OrmIblock\ElementTable::getPropertyByCode(20071776, $iblock, 'PUBLISHERS_RUS');
```
Этот код вернет все поля таблицы описалова св-в и его значение
