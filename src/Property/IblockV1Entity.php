<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Entity;
use Bitrix\Main;
use Soft1c\OrmIblock\ElementTable;
use Soft1c\OrmIblock\Query;

class IblockV1Entity extends Compiler
{

	const ENTITY_NAME = 'OrmIblockProperty';

	/**
	 * @method addSingleProperty
	 * @param array $arProp
	 * @param array $params
	 */
	protected function addSingleProperty(array $arProp, $params = [])
	{
		if(!in_array($arProp['CODE'], $params['props'])){
			return;
		}

		$this->entity->addField(new Entity\ReferenceField(
			$arProp['CODE'].'_VALUE',
			PropertyValueTable::getEntity(),
			[
				'=this.IBLOCK_ELEMENT_ID' => 'ref.IBLOCK_ELEMENT_ID',
				'ref.IBLOCK_PROPERTY_ID' => array('?i', $arProp['ID'])
			]
		));

		switch ($arProp['PROPERTY_TYPE']){
			case PropertyTable::TYPE_ELEMENT:
				$this->entity->addField(new Entity\ReferenceField(
					$arProp['CODE'].'_REF',
					ElementTable::getEntity($arProp['LINK_IBLOCK_ID']),
					['=this.'.$arProp['CODE'].'_VALUE.VALUE_NUM' => 'ref.ID' ]
				));
				$this->entity->addField(new Entity\ExpressionField(
					$arProp['CODE'],
					'%s',
					$arProp['CODE'].'_VALUE.VALUE'
				));
				break;
				// TODO Сделать референсы на остальные типы свойств
			default:

				$this->entity->addField(new Entity\ExpressionField(
					$arProp['CODE'],
					'%s',
					$arProp['CODE'].'_VALUE.VALUE'
				));
				break;
		}

	}

	/**
	 * @method initEntity
	 * @return Entity\Base
	 */
	public function initEntity()
	{
		$name = self::ENTITY_NAME;
		if (!Entity\Base::isExists(__NAMESPACE__.'\\'.$name)){
			$entity = Entity\Base::compileEntity($name, [], [
				'namespace' => __NAMESPACE__,
				'table_name' => 'b_iblock_element_property'
			]);
		} else {
			$entity = Entity\Base::getInstance(__NAMESPACE__.'\\'.$name);
		}
		$entity->addField(new Entity\IntegerField('IBLOCK_ELEMENT_ID'));

		Main\EventManager::getInstance()->addEventHandler(
			'main',
			Query::ON_BEFORE_EXEC.self::ENTITY_NAME,
			array($this, 'onBeforePropertyCompile')
		);

		return $entity;
	}

	/**
	 * @method onBeforePropertyCompile
	 * @param Main\Event $event
	 *
	 * @return Entity\EventResult
	 */
	public function onBeforePropertyCompile(Main\Event $event)
	{
		$result = new Entity\EventResult();

		/** @var Query $query */
		$query = $event->getParameter('QUERY');
		$params = [];
		$params += array_values($query->getSelect());
		$params += array_keys($query->getFilter());
		$params += array_keys($query->getOrder());
		$params += array_values($query->getGroup());

		$params = array_unique($params);
		$arProps = $this->Property->getPropertyData();

		$propertyParams = $this->modifySelectParams($params);

		foreach ($propertyParams as $value) {
			if($arProps->has($value) && $arProps->get($value)['MULTIPLE'] == 'N'){
				$this->addSingleProperty($arProps->get($value), ['props' => $propertyParams]);
			} elseif($arProps->has($value)) {
				$this->addMultiProperty($arProps->get($value));
			}
		}

		$query->addGroup('ID');

		$result->modifyFields(['ENTITY' => $this->entity]);

		if ($this->entity instanceof Main\Entity\Base){
			$entityBase = clone $this->getEntity();
			$entityBase->addField(
				new Main\Entity\ReferenceField(
					'PROPERTY',
					$this->entity,
					['=this.ID' => 'ref.IBLOCK_ELEMENT_ID']
				)
			);
			$this->setEntity($entityBase);
		}
		return $result;
	}

	/**
	 * @method modifySelectParams
	 * @param array $params
	 *
	 * @return array
	 */
	public function modifySelectParams(array $params = [])
	{
		$arProps = $this->Property->getPropertyData();

		$propertyParams = array_map(function ($el) {
			preg_match('#^PROPERTY.([^.]+)#i', $el, $value);
			if($value[1]){
				if(substr($value[1], -4, 4) === '_REF'){
					$v = explode('_REF', $value[1]);
					return $v[0];
				}
				return $value[1];
			}
		}, $params);

		TrimArr($propertyParams);

		if(count($propertyParams) == 0){
			$propertyParams = $arProps->getKeys();
		}

		return $propertyParams;
	}
}