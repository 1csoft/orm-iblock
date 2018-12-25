<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Entity;
use Bitrix\Main\FileTable;
use Soft1c\OrmIblock\ElementTable;
use Bitrix\Main;

abstract class Compiler
{
	protected $Property;

	/** @var Entity\Base */
	protected $entity;

	protected $iblockId;

	/**
	 * PropertyCompiler constructor.
	 *
	 * @param $Property
	 */
	public function __construct(IProperty $Property)
	{
		$this->Property = $Property;
		$this->iblockId = $this->Property->getIblockId();
		$this->entity = $this->initEntity();
	}

	abstract public function initEntity();

	/**
	 * @method getEntity - get param entity
	 * @return Entity\Base
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @method setEntity - set param Entity
	 * @param Entity\Base $entity
	 */
	public function setEntity($entity)
	{
		$this->entity = $entity;
	}

	/**
	 * @method addSingleProperty
	 * @param array $arProp
	 * @param array $params
	 *
	 * @return void
	 */
	protected abstract function addSingleProperty(array $arProp, $params = []);

	/**
	 * @method addMultiProperty
	 * @param array $arProp
	 *
	 * @return void
	 */
	protected function addMultiProperty(array $arProp)
	{
		$params = [
			'title' => $arProp['NAME'],
			'column_name' => 'PROPERTY_'.$arProp['ID'],
			'required' => $arProp['IS_REQUIRED'] == 'Y' ? true : false,
			'serialized' => true,
		];
		$field = new Entity\TextField($arProp['CODE'], $params);

		$utmManger = new UtmManager($arProp['IBLOCK_ID']);

		$field->addFetchDataModifier(function ($value, $field, $data, $alias) use ($arProp, $utmManger) {
			if (!$value || count($value) == 0){
				$result = $utmManger->modifierResultMulti($data, $arProp);
			} else {
				$result = $value;
			}

			return $result;
		});

		$this->entity->addField($field);
		$utmEntity = $utmManger->getEntity();

		$buildFrom = $buildFrom = $arProp['CODE'].'_REF'.'.VALUE';

		switch ($arProp['PROPERTY_TYPE']){
			case PropertyTable::TYPE_ELEMENT:
				$refIblock = new Entity\ReferenceField(
					'ELEMENT',
					ElementTable::getEntity(),
					array(
						'=this.VALUE_NUM' => 'ref.ID',
//						'this.IBLOCK_PROPERTY_ID' => ['?i', $arProp['ID']]
					)
				);
				$utmEntity->addField($refIblock);
				$buildFrom = $arProp['CODE'].'_REF'.'.VALUE_NUM';
				break;
			case PropertyTable::TYPE_LIST:
				$utmEntity->addField(new Entity\ReferenceField(
					'ENUM',
					\Bitrix\Iblock\PropertyEnumerationTable::getEntity(),
					array(
						'=this.VALUE_ENUM'=>'ref.ID',
//						'=this.IBLOCK_PROPERTY_ID' => ['?i', $arProp['ID']]
					)
				));
				$buildFrom = $arProp['CODE'].'_REF'.'.VALUE_ENUM';
				break;
			case PropertyTable::TYPE_FILE:
				$utmEntity->addField(new Entity\ReferenceField(
					'FILE',
					FileTable::getEntity(),
					array(
						'=this.VALUE'=>'ref.ID',
//						'=this.IBLOCK_PROPERTY_ID' => ['?i', $arProp['ID']],
					)
				));
				break;
			case PropertyTable::TYPE_SECTION:
				$utmEntity->addField(new Entity\ReferenceField(
					'SECTION',
					SectionTable::getEntity(),
					array(
						'=this.VALUE'=>'ref.ID',
//						'=this.IBLOCK_PROPERTY_ID' => ['?i', $arProp['ID']],
					)
				));
				$buildFrom = $arProp['CODE'].'_REF'.'.VALUE_NUM';
				break;
			default:
//				$utmEntity->addField(new Entity\StringField('VALUE'));
				break;
		}

		$referenceField = new Entity\ReferenceField(
			$arProp['CODE'].'_REF',
			$utmEntity,
			array(
				'=this.IBLOCK_ELEMENT_ID' => 'ref.IBLOCK_ELEMENT_ID',
				'ref.IBLOCK_PROPERTY_ID' => ['?i', $arProp['ID']]
			));

		$this->entity->addField($referenceField);
		$this->entity->addField(new Entity\ExpressionField(
			$arProp['CODE'].'_VALUE',
			'%',
			$buildFrom
		));
	}

	/**
	 * @method addFetchDataModifier
	 * @param Entity\Field $field
	 * @param array $arProp
	 *
	 * @return void
	 */
	protected function addFetchDataModifier(Entity\Field $field, array $arProp = [])
	{
		switch (strtoupper($arProp['USER_TYPE'])) {
			case 'HTML':
				$field->addFetchDataModifier(function ($value, $field, $data, $alias) {

					$result = null;
					if (strlen($value) > 0){
						$res = unserialize($value);
						$result = $res['TEXT'];
					}

					return $result;
				});
				break;
		}

		if ($arProp['PROPERTY_TYPE'] == PropertyTable::TYPE_NUMBER){
			$field->addFetchDataModifier(function ($value, $field, $data, $alias) {
				$result = 0;
				$val = explode('.', $value);

				if (count($val) > 1){
					if (intval($val[1]) > 0)
						$result = floatval($value);
					else
						$result = intval($value);
				} else {
					$result = intval($value);
				}

				return $result;
			});
		}
	}

	/**
	 * @method build
	 * @return Entity\Base
	 */
	public function build()
	{
		foreach ($this->Property->getPropertyData()->getIterator() as $code => $arProp) {
			if ($arProp['MULTIPLE'] == 'N'){
				$this->addSingleProperty($arProp);
			} else {
				$this->addMultiProperty($arProp);
			}
		}

		return $this->entity;
	}

	protected function getReferenceIblock(array $arProp)
	{
		$linkIblock = (int)$arProp['LINK_IBLOCK_ID'];
		if ($linkIblock > 0){
			$refEntity = 'Soft1c\OrmIblock\OrmIblockElement'.$linkIblock;
			if (!Entity\Base::isExists($refEntity)){
				$field = new Entity\ReferenceField(
					$arProp['CODE'].'_REF',
					ElementTable::getEntity($linkIblock),
					['=this.'.$arProp['CODE'] => 'ref.ID', ['ref.IBLOCK_ID' => array('?i', $linkIblock)]]
				);
			} else {
				$refEntity = Entity\Base::getInstance($refEntity);
				$field = new Entity\ReferenceField(
					$arProp['CODE'].'_REF',
					$refEntity,
					['=this.'.$arProp['CODE'] => 'ref.ID', ['ref.IBLOCK_ID' => array('?i', $linkIblock)]]
				);
			}
		} else {
			$field = new Entity\ReferenceField(
				$arProp['CODE'].'_REF',
				ElementTable::getEntity(),
				['=this.'.$arProp['CODE'] => 'ref.ID']
			);
		}

		return $field;
	}

}
