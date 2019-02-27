<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Main\Entity\Base;

interface IProperty
{
	/**
	 * @method getPropertyData
	 * @return \Soft1c\OrmIblock\ParametersBag
	 */
	public function getPropertyData();

	/**
	 * @method setIblockId
	 * @param int $iblockId
	 *
	 * @return void
	 */
	public function setIblockId($iblockId);

	/**
	 * @method getIblockId
	 * @return int
	 */
	public function getIblockId();


}