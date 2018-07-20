<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Iblock\PropertyTable;

class IblockV2Entity extends Compiler
{

	protected function addSingleProperty(array $arProp = [], $params = [])
	{
		$field = false;
		$params = [
			'title' => $arProp['NAME'],
			'column_name' => 'PROPERTY_'.$arProp['ID'],
			'required' => $arProp['IS_REQUIRED'] == 'Y' ? true : false,
		];
		switch ($arProp['PROPERTY_TYPE']) {
			case PropertyTable::TYPE_NUMBER:
				$field = new Entity\IntegerField($arProp['CODE'], $params);
				break;
			case PropertyTable::TYPE_ELEMENT:
				$this->entity->addField(new Entity\IntegerField($arProp['CODE'], $params));
				$this->entity->addField($this->getReferenceIblock($arProp));
				break;
			case PropertyTable::TYPE_LIST:
				$this->entity->addField(new Entity\IntegerField($arProp['CODE'], $params));
				$this->entity->addField(new Entity\ReferenceField(
					$arProp['CODE'].'_ENUM',
					PropertyEnumerationTable::getEntity(),
					['=this.'.$arProp['CODE'] => 'ref.ID']
				));
				break;
			case PropertyTable::TYPE_SECTION:
				$this->entity->addField(new Entity\IntegerField($arProp['CODE'], $params));
				$this->entity->addField(new Entity\ReferenceField(
					$arProp['CODE'].'_SECTION',
					SectionTable::getEntity(),
					['=this.'.$arProp['CODE'] => 'ref.ID']
				));
				break;
				break;
			default:
				$field = new Entity\StringField($arProp['CODE'], $params);
				break;
		}

		if ($field instanceof Entity\Field){
			$this->addFetchDataModifier($field, $arProp);
			$this->entity->addField($field);
		}
	}

	/**
	 * @method initEntity
	 * @return Entity\Base
	 */
	public function initEntity()
	{
		$name = 'OrmIblockProperty'.$this->Property->getIblockId();
		if (!Entity\Base::isExists(__NAMESPACE__.'\\'.$name)){
			$entity = Entity\Base::compileEntity($name, [], [
				'namespace' => __NAMESPACE__,
				'table_name' => 'b_iblock_element_prop_s'.$this->iblockId,
			]);
			$entity->addField(new Entity\IntegerField('IBLOCK_ELEMENT_ID', [
				'title' => 'IBLOCK_ELEMENT_ID',
				'column_name' => 'IBLOCK_ELEMENT_ID',
				'required' => true,
				'primary' => true
			]));
			$entity->addField(new Entity\ExpressionField('ID', '%', 'IBLOCK_ELEMENT_ID'));
		} else {
			$entity = Entity\Base::getInstance(__NAMESPACE__.'\\'.$name);
		}

		return $entity;
	}

}