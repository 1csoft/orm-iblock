<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main;
use Bitrix\Main\Entity;
use Soft1c\OrmIblock\ElementTable;

/**
 * Class PropertyValueTable
 * @package Soft1c\OrmIblock\Property
 *
 *
create table b_iblock_element_property
(
ID                 int auto_increment
primary key,
IBLOCK_PROPERTY_ID int                    not null,
IBLOCK_ELEMENT_ID  int                    not null,
VALUE              text                   not null,
VALUE_TYPE         char(4) default 'text' not null,
VALUE_ENUM         int                    null,
VALUE_NUM          decimal(18, 4)         null,
DESCRIPTION        varchar(255)           null
);

create index ix_iblock_element_prop_enum
on b_iblock_element_property (VALUE_ENUM, IBLOCK_PROPERTY_ID);

create index ix_iblock_element_prop_num
on b_iblock_element_property (VALUE_NUM, IBLOCK_PROPERTY_ID);

create index ix_iblock_element_property_1
on b_iblock_element_property (IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID);

create index ix_iblock_element_property_2
on b_iblock_element_property (IBLOCK_PROPERTY_ID);
 */
class PropertyValueTable extends Entity\DataManager
{
	/**
	 * Returns DB table name for entity
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iblock_element_property';
	}

	/**
	 * Returns entity map definition.
	 */
	public static function getMap()
	{
		return [
			'ID' => new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true
			]),
			'IBLOCK_PROPERTY_ID' => new Entity\IntegerField('IBLOCK_PROPERTY_ID', [
				'required' => true
			]),
			'IBLOCK_ELEMENT_ID' => new Entity\IntegerField('IBLOCK_ELEMENT_ID', [
				'required' => true
			]),
			'VALUE' => new Entity\TextField('VALUE'),
			'VALUE_TYPE' => new Entity\StringField('VALUE_TYPE', [
				'default_value' => 'text'
			]),
			'VALUE_ENUM' => new Entity\IntegerField('VALUE_ENUM'),
			'VALUE_NUM' => new Entity\FloatField('VALUE_NUM'),
			'DESCRIPTION' => new Entity\StringField('DESCRIPTION'),
			'PROPERTY' => new Entity\ReferenceField(
				'PROPERTY',
				PropertyTable::getEntity(),
				['=this.IBLOCK_PROPERTY_ID' => 'ref.ID']
			),
			'CODE' => new Entity\ExpressionField('CODE', '%', 'PROPERTY.CODE'),
			/*'ELEMENT' => new Entity\ReferenceField(
				'ELEMENT',
				ElementTable::getEntity(),
				['=this.IBLOCK_ELEMENT_ID' => 'ref.ID']
			)*/
		];
	}

}