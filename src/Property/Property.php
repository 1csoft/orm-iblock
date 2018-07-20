<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Main\Entity;
use Bitrix\Main;

class Property
{
	/** @var Entity\Base */
	protected static $entity;

	/**
	 * @method getEntity
	 * @param $iblockId
	 *
	 * @return Entity\Base
	 */
	public static function getEntity($iblockId)
	{
		$property = new PropertyData($iblockId);
		$class = self::compile($property);

		return static::$entity[$class];
	}

	/**
	 * @method compile
	 * @param IProperty $property
	 *
	 * @return Entity\DataManager|string
	 */
	public static function compile(IProperty $property)
	{
		$props = $property->getPropertyData();
		$p = $props->current();
		if ($p['VERSION'] == 2){
			$compiler = new IblockV2Entity($property);
		} else {
			$compiler = new IblockV1Entity($property);
		}

		$entity = $compiler->build();
		/** @var string $class */
		$class = $entity->getDataClass();
		static::$entity[$class] = $entity;

		return $class;
	}

	public function getPropertyValue(array $filter = [])
	{

	}

	public static function registerEventHandler()
	{
//		$eventManager = Main\EventManager::getInstance();
//		$eventManager->registerEventHandlerCompatible('main', '');
//		PR($eventManager, 1);


	}
}