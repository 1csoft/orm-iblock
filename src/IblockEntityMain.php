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
	protected $iblockId = null;

	/**
	 * IblockEntityMain constructor.
	 *
	 * @param int|null $iblockId
	 */
	public function __construct(int $iblockId = null)
	{
		$this->iblockId = $iblockId;
	}


	/**
	 * @method getIblockId - get param iblockId
	 * @return int|null
	 */
	public function getIblockId()
	{
		return $this->iblockId;
	}

	/**
	 * @method setIblockId - set param IblockId
	 * @param int|null $iblockId
	 */
	public function setIblockId($iblockId)
	{
		$this->iblockId = $iblockId;
	}

	/**
	 * @method isClone
	 * @return bool
	 */
	public function isClone()
	{
		return $this->isClone;
	}
}
