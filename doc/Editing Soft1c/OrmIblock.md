### ElementTable

�.�. ���� ����� ���������� �� Bitrix\Main\Entity\DataManager, �� ����� ��� ����������� ��������.

ElementTable::getEntity() - ���������� �������� ���������. ���� �������� � ��� �� ��������� - ������� �������� �� ����������.
```php
Soft1c\OrmIblock\ElementTable::getEntity($iblock_id);
```
� ���� ������ ����� ��������� ���� PROPERTY ���� Reference �� ������� �������� ��-� ��. ������, � ����������� �� ������ ��, ����� ����� ���� ��
������� b_iblock_element_prop_s ��� �� b_iblock_element_property.


ElementTable::query($iblock_id = null) - ��������� ������� Query, ���� �������� �� ��, �� �������� ����� ���������� ElementTable::getEntity.

ElementTable::getList(array $parameters = array()) - ����������� getList, ��������� ��� ���� ���������, ��� � 
Bitrix\Main\Entity\DataManager::getList.

�������:
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
��������� ��-� � ������� ���� ����� �������� PROPERTY.CODE, ��� CODE - ���������� ��� ��-��. 
���� ���� � ��-�� ���, ����� ������� ����������. ���, ��-�� ��� ���� - ��� ������ �����.
PROPERTY.ADDITIONAL_MIN - ����� ������� ������� ��������� ��� html ��������.
��� ��������� ����� � ��-� ����������� ��������� ����� ������������ �������� `PROPERTY.CODE_REF`, ��� CODE - ���������� ��� ��-��,
� ��������� _REF ���������� ��� ��� �� ������ �������� � �������� �� ������� � ������ ��.
�.� �� ����� �������� ���� �������� `PROPERTY.PRODUCT_PRICE_REF.NAME` � �.�., ��� ��-�� `PROPERTY.PRODUCT_PRICE_REF.PROPERTY.PRISE_FOR`
������ ���� PRISE_FOR - ��� ���� �������� �� ������ ��, �� ����� ����� ���������� ������� - `PROPERTY.PRODUCT_PRICE_REF.PROPERTY.PRISE_FOR.NAME`
�� ������� ������ ����� ������� ������� �� �����. 
��-�� ���� ������ ���������� ����� �� �������, ������ ������ ��������� _REF ���� ������������ _ENUM.
`PROPERTY.DISTRIBUTION_TYPE_ENUM.VALUE` ��� `PROPERTY.DISTRIBUTION_TYPE_ENUM.XML_ID`.
��� ��� ������� �������� �� ���� ���������� getList.
������, ��� ���������� �� `PROPERTY.DISTRIBUTION_TYPE_ENUM.VALUE` ���� ��������, ��� ���� VALUE - ��� ������ � �� ��� ��� ������� � ��.
���� �������� � ��������� ����� ��������. ��� ������� �� ����� � ������� �����������.



ElementTable::getProperty(array $parameters) - ���������� ����� ��������� ������ ��� ���� ������� �������� �� ����������
DataManager::getList. ���� ����� �������� `Bitrix\Iblock\PropertyTable::getList($parameters)`, � ����� ���� ��������
Entity ������� �� ����������� ����������� ��������� � �������� � ������������ � PropertyTable. ������, � $parameters ����������� ������ ���� �����
```php
$iblockId = (int)$parameters['filter']['IBLOCK_ID'];
$elementId = (int)$parameters['filter']['IBLOCK_ELEMENT_ID'];
```
���� ����� ����� ���������� �����, � �� ������ � �������� ���������� �������. ����� ������������ ��� �������:

ElementTable::getPropertyByCode(int $elementId, int $iblockId, string $code, $select = false) - ��������� ��-�� ��������
�� ��� ����������� ����.

ElementTable::getPropertyById(int $elementId, int $iblockId, string $code, $select = false) - ��������� ��-�� ��������
�� ��� ID.

� ����� ������� ���� �������� $select � � ���� ����� ������� ����� ���� ������������� �������, ���� ������� ��-�� ���� 
�������� � ������� ��������, ��������� ��� ������ ��������. ������������� �������� ����� �������� ��� ������, �� ����
��� �������� � � $select ������� �����-���� ��� ����, �� �������� ������ ���� ��������. ��� ����������� ���������� 
������������� �������� � ��, ��� ��� ������� � getList, ��� � � ���� ������. ����� ���� � ������� ��� �������.
��������� $select ���������� ���������� getList.
```php
$result = OrmIblock\ElementTable::getPropertyByCode(20071776, $iblock, 'PUBLISHERS_RUS', [
	'PUBLISHERS_RUS_NAME' => 'PUBLISHERS_RUS_REF.NAME'
]);
```
PUBLISHERS_RUS - ��-�� �������� � ������� ��, � �� ����� �������� �� ������ ��� ��, �� � �������� PUBLISHERS_RUS_REF.NAME

```php
$result = OrmIblock\ElementTable::getPropertyByCode(20071776, $iblock, 'PUBLISHERS_RUS');
```
���� ��� ������ ��� ���� ������� ��������� ��-� � ��� ��������
