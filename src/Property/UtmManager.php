<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 16.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Soft1c\OrmIblock\IblockException\PropertyException;

class UtmManager
{

	protected $iblockId = null;

	/**
	 * UtmManager constructor.
	 *
	 * @param int $iblockId
	 *
	 * @throws PropertyException
	 */
	public function __construct($iblockId = 0)
	{
		if(intval($iblockId) == 0)
			throw new PropertyException('Iblock id is null');

		$this->iblockId = $iblockId;
	}


	/**
	 * Returns entity object
	 *
	 * @return Entity\Base
	 */
	public function getEntity()
	{
		$name = 'OrmIblockElementUtm'.$this->iblockId;
		if (!Main\Entity\Base::isExists(__NAMESPACE__.'\\'.$name)){
			$entity = Main\Entity\Base::compileEntity($name, static::getMap(), [
				'namespace' => __NAMESPACE__,
				'table_name' => 'b_iblock_element_prop_m'.$this->iblockId,
			]);
		} else {
			$entity = Main\Entity\Base::getInstance(__NAMESPACE__.'\\'.$name);
		}

		return $entity;
	}

	/**
	 * Returns DB table name for entity
	 *
	 * @return string
	 */
	public function getTableName()
	{
		return 'b_iblock_element_prop_m'.$this->iblockId;
	}

	/**
	 * @method getMap
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
			)),
			new Entity\IntegerField('IBLOCK_ELEMENT_ID', array(
				'required' => true,
			)),
			new Entity\IntegerField('IBLOCK_PROPERTY_ID', array(
				'required' => true,
			)),
			new Entity\TextField('VALUE', array(
				'required' => true,
			)),
			new Entity\IntegerField('VALUE_ENUM', array(
			)),
			new Entity\FloatField('VALUE_NUM', array(

			)),
			new Entity\StringField('DESCRIPTION', array(
//				'validation' => array(__CLASS__, 'validateDescription'),
			)),
		);
	}

	/**
	 * @method isUts
	 * @return bool
	 */
	public static function isUts()
	{
		return false;
	}

	/**
	 * @method isUtm
	 * @return bool
	 */
	public static function isUtm()
	{
		return true;
	}

	/**
	 * @method modifierResultMulti
	 * @param array $data
	 * @param array $arProp
	 *
	 * @return bool
	 */
	public function modifierResultMulti(array $data, array $arProp)
	{
		$result = false;
		$arRes = static::getMultiValues($data, $arProp);
		if($arRes){
			foreach ($arRes as $k => $arVal) {
				if(isset($arVal['ENUM']))
					$result['VALUE'][$k] = $arVal['ENUM'];
				else
					$result['VALUE'][$k] = $arVal['VALUE'];
				$result['DESCRIPTION'][$k] = $arVal['DESCRIPTION'];
				$result['ID'][$k] = $arVal['ID'];
			}

			if($result)
				static::updateMultiValues($data, $arProp, $result);

			return $result;
		}
		return false;
	}

	/**
	 * @method getMultiValues
	 * @param array $data
	 * @param array $arProp
	 *
	 * @return array
	 */
	public static function getMultiValues($data = [], $arProp = [])
	{
		$strSql =  "SELECT ID, VALUE, DESCRIPTION
					FROM b_iblock_element_prop_m" . $arProp['IBLOCK_ID'] . "
						WHERE
							IBLOCK_ELEMENT_ID = " . $data['ID'] . "
						AND IBLOCK_PROPERTY_ID = " . $arProp['ID'] . "
					ORDER BY ID";

		return \Bitrix\Main\Application::getConnection()->query($strSql)->fetchAll();
	}

	/**
	 * @method updateMultiValues
	 * @param array $data
	 * @param array $arProp
	 * @param array $result
	 *
	 * @return \Bitrix\Main\DB\Result
	 */
	public static function updateMultiValues(array $data, $arProp = [], array $result)
	{
		$resUpdate = null;
		$connect = \Bitrix\Main\Application::getConnection();
		$resultStr = serialize($result);
		$sTableUpdate = 'b_iblock_element_prop_s'.$arProp['IBLOCK_ID'];
		$strPrepare = $connect->getSqlHelper()->prepareAssignment($sTableUpdate, 'PROPERTY_'.$arProp['ID'], $resultStr);
		$strSqlUpdate = "
			UPDATE b_iblock_element_prop_s".$arProp['IBLOCK_ID']."
			SET ".$strPrepare." WHERE IBLOCK_ELEMENT_ID = ".intval($data["ID"]);

		$resUpdate = $connect->query($strSqlUpdate);

		return $resUpdate;
	}
}