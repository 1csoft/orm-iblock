## Orm ��� ���������� �������
���� ������������ ���������� ��� �� ���������� ���-�� ������ �� ���� D7 ��� ���������, ���������� ���-�� �������������.
��� ���������� �� ������������ ������, �������������� ����� ��������.

### ��� ��� �� �����
* ������ ������� �� ���������� � ��������� � ��������� ���������� Entity\DataManager D7
* ����������� �� ���������� � ���������, �� ����� ����������� ���������� � �� ��������� ���� �� ����������� ���������(������ ���� � ���������� ��-�� ������ ��� � ID �������� ��������)
* �������� ��� Element::getList ��� � ����� new Query()
* �������� ��������� ��-�� �������� �� ���� ��� �� ��-��.
* ������� �������� ����� OrmIblock\ElementTable::getEntity($iblock_id) �� ���������� � ���, ������� ����� ������������ � ����� Reference
* ����� ��������� ���������� v1.

#### ��������� 
`composer require 1csoft/orm-iblock`

#### �����������
```php
require_once(<path to vendor/autoload.php>);
use Soft1c\OrmIblock;

$rs = OrmIblock\ElementTable::getList([
	'filter' => [
        'IBLOCK_ID' => $iblock, 
        'PROPERTY.DISTRIBUTION_TYPE_ENUM.XML_ID'=>'ONLINE' // ��������� ������ �� XMK_ID ���������� ��-��
	],
	'select' => [
		'ID',
		'ADDITIONAL_MIN' =>'PROPERTY.ADDITIONAL_MIN', // HTML ��-��
		'PRICES' => 'PROPERTY.PRODUCT_PRICE_REF.PROPERTY.PRISE_FOR', // ��-�� ������������ ��������
		'DISTRIBUTION_TYPE' => 'PROPERTY.DISTRIBUTION_TYPE' // ��������� ��-�� - ����������� ������ ID �������� ������
	],
	'limit' => 20
]);
while ($item = $rs->fetch()){
	$result[] = $item;
}

dump($result);
```
������������� � ��������� query 
```php
$q = OrmIblock\ElementTable::query($iblock);
$rs = $q
	->setSelect([
		'ID', 'NAME',
		'DISTRIBUTION_TYPE' => 'PROPERTY.DISTRIBUTION_TYPE_ENUM.VALUE', // ��������� �������� �������� ������
		'DISTRIBUTION_LINK' => 'PROPERTY.DISTRIBUTION_LINK',
		'PREVIEW_PICTURE',
		'SRC' => 'PREVIEW_PICTURE_FILE.PATH', // �������� ���� �������� �� ����� �����
		'FILE_SIZE' => 'PREVIEW_PICTURE_FILE.FILE_SIZE' // ���������������� ������ �����
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
