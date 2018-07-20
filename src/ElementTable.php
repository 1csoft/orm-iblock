<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 11.07.2018
 */

namespace Soft1c\OrmIblock;

use Bitrix\Main;
use Bitrix\Iblock;
use Soft1c\OrmIblock\Property;

Main\Loader::includeModule('iblock');

class ElementTable extends Main\Entity\DataManager
{

	private static $operandsInProp = '<>=!%@';

	/** @var array|null */
	protected static $params = null;

	const ENTITY_BASE_NAME = 'OrmIblockElement';

	/**
	 * @method getEntity
	 * @param null $iblockId
	 *
	 * @return Main\Entity\Base
	 */
	public static function getEntity($iblockId = null)
	{
		$name = static::ENTITY_BASE_NAME.$iblockId;
		if (!Main\Entity\Base::isExists(__NAMESPACE__.'\\'.$name)){
			$entity = Main\Entity\Base::compileEntity($name, Iblock\ElementTable::getMap(), [
				'namespace' => __NAMESPACE__,
				'table_name' => 'b_iblock_element',
			]);
		} else {
			$entity = Main\Entity\Base::getInstance(__NAMESPACE__.'\\'.$name);
		}

		if ($iblockId){
			$entity->addField(new Main\Entity\ReferenceField(
				'PROPERTY',
				Property\Property::getEntity($iblockId),
				['=this.ID' => 'ref.IBLOCK_ELEMENT_ID']
			));
		}
		$entity->addField(new Main\Entity\ReferenceField(
			'PREVIEW_PICTURE_FILE',
			FileTable::getEntity(),
			['=this.PREVIEW_PICTURE' => 'ref.ID']
		));
		$entity->addField(new Main\Entity\ReferenceField(
			'DETAIL_PICTURE_FILE',
			FileTable::getEntity(),
			['=this.DETAIL_PICTURE' => 'ref.ID']
		));

		return $entity;
	}

	/**
	 * @method query
	 * @param null $iblockId
	 *
	 * @return Main\Entity\Query
	 */
	public static function query($iblockId = null)
	{
		if ((int)$iblockId == 0)
			$iblockId = (int)static::$params['filter']['IBLOCK_ID'];

		$query = new Query(static::getEntity($iblockId));


		return $query;
	}

	/**
	 * @method getList
	 * @param array $parameters
	 *
	 * @return Main\DB\Result
	 */
	public static function getList(array $parameters = array())
	{
		static::$params = $parameters;

		return parent::getList($parameters);
	}


	/**
	 * @method getProperty
	 * @param array $parameters
	 *
	 * @return Main\DB\Result
	 */
	public static function getProperty(array $parameters)
	{
		$iblockId = (int)$parameters['filter']['IBLOCK_ID'];
		$elementId = (int)$parameters['filter']['IBLOCK_ELEMENT_ID'];

		unset($parameters['filter']['IBLOCK_ELEMENT_ID']);

		$entityProperty = Property\Property::getEntity($iblockId);
		$refSelect = $parameters['select_ref'];
		unset($parameters['select_ref']);

		$propDataDB = Iblock\PropertyTable::getList($parameters);
		$propsData = [];

		$referenceVal = 'CODE';

		foreach ($parameters['filter'] as $code => $val) {
			$filter[$code] = self::clearOperands($val);
		}

		if ($filter['ID']){
			$referenceVal = $filter['ID'];
			$entityProperty->addField(new Main\Entity\ReferenceField(
				'_REF',
				Iblock\PropertyTable::getEntity(),
				['ref.ID' => ['?i', $referenceVal]]
			));
		} else {
			$entityProperty->addField(new Main\Entity\ReferenceField(
				'_REF',
				Iblock\PropertyTable::getEntity(),
				['ref.CODE' => ['?s', $referenceVal]]
			));
		}

		$queryProperty = new Main\Entity\Query($entityProperty);
		$queryProperty->addFilter('IBLOCK_ELEMENT_ID', $elementId);

		while ($rs = $propDataDB->fetch()) {
			$propsData[$rs['CODE']] = $rs;
		}

		if (count($refSelect) > 0){
			foreach ($refSelect as $code => $item) {
				$propsData[$item] = $item;
			}
		}

		$code = array_unique(array_keys($propsData));
		$queryProperty->setSelect($code);
		$queryProperty->addSelect('_REF', 'REF_');
		$res = $queryProperty->exec();

		$res->addFetchDataModifier(function ($data) use ($refSelect) {
			$result = [];

			$values = [];
			foreach ($data as $code => $value) {
				if (substr($code, 0, 4) === 'REF_'){
					$c = str_replace('REF_', '', $code);
					$result[$c] = $value;
				} else {

					if (count($refSelect) > 0){
						foreach ($refSelect as $codeRef => $v) {
							$values[$codeRef] = $value;
						}

					} else {
						$values[] = $value;
					}

				}
			}
			if (count($values) <= 1){
				$values = current($values);
			}
			$result["VALUE"] = $values;

			return $result;
		});

		return $res;
	}

	/**
	 * @method getPropertyByCode
	 * @param int $elementId
	 * @param int $iblockId
	 * @param string $code
	 * @param bool $select
	 *
	 * @return array|false
	 */
	public static function getPropertyByCode(int $elementId, int $iblockId, string $code, $select = false)
	{
		$filter = ['IBLOCK_ELEMENT_ID' => $elementId, 'IBLOCK_ID' => $iblockId, '=CODE' => $code];

		return self::fetchProperty($filter, $select);
	}

	/**
	 * @method fetchProperty
	 * @param array $filter
	 * @param array $select
	 *
	 * @return array|false
	 */
	private static function fetchProperty(array $filter, $select = [])
	{
		$params = [
			'filter' => $filter,
			'order' => ['SORT' => 'ASC', 'ID' => 'ASC'],
			'select' => ['*'],
		];

		if (is_array($select)){
			$params['select_ref'] = $select;
		}

		return static::getProperty($params)->fetch();
	}

	/**
	 * @method getPropertyById
	 * @param int $elementId
	 * @param int $iblockId
	 * @param int $id
	 * @param bool $select
	 *
	 * @return array|false
	 */
	public static function getPropertyById(int $elementId, int $iblockId, int $id, $select = false)
	{
		$filter = ['IBLOCK_ELEMENT_ID' => $elementId, 'IBLOCK_ID' => $iblockId, '=ID' => $id];

		return self::fetchProperty($filter, $select);
	}

	/**
	 * @method clearOperands
	 * @param $fieldName
	 *
	 * @return null|string|string[]
	 */
	private static function clearOperands($fieldName)
	{
		return preg_replace('/^['.self::$operandsInProp.']+/i', '', $fieldName);
	}
}