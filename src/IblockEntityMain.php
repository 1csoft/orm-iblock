<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 01.03.2019
 */

namespace Soft1c\OrmIblock;


use Bitrix\Main\ORM\Entity;

class IblockEntityMain extends Entity
{

	/** @var null|int */
	protected static $iblockId = null;

	/**
	 * @method getIblockId - get param iblockId
	 * @return int|null
	 */
	public static function getIblockId()
	{
		return self::$iblockId;
	}

	/**
	 * @method setIblockId - set param IblockId
	 * @param int|null $iblockId
	 */
	public static function setIblockId($iblockId)
	{
		self::$iblockId = $iblockId;
	}
}