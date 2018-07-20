<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 11.07.2018
 */

namespace Soft1c\OrmIblock\Property;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main;
use Soft1c\OrmIblock\IblockException;
use Soft1c\OrmIblock\ParametersBag;

class PropertyData implements IProperty
{
	/** @var null|int */
	protected $iblockId = null;

	/** @var string */
	const CACHE_DIR = '/orm_iblock/property';

	/** @var string */
	protected $cacheId = 'property_';

	/** @var int */
	protected $cacheTime = 86400;

	/** @var string */
	protected $cacheDir = '';

	/** @var Main\Entity\Base */
	protected $entity;

	protected $entityName;

	/** @var null|array */
	protected $property = null;

	/**
	 * Property constructor.
	 *
	 * @param int|null $iblockId
	 */
	public function __construct(int $iblockId)
	{
		$this->setIblockId($iblockId);
		$this->setCacheTime($this->cacheTime * 30);
		$this->setCacheId($this->cacheId.$this->iblockId);
		$this->setCacheDir(static::CACHE_DIR.'/'.$this->getIblockId());

		Main\Loader::includeModule('iblock');
	}

	/**
	 * @method getPropertyData
	 * @return ParametersBag
	 */
	public function getPropertyData()
	{
		$cache = Main\Data\Cache::createInstance();
		$initCache = $cache->initCache(
			$this->getCacheTime(),
			$this->getCacheId(),
			$this->getCacheDir()
		);
		if (is_null($this->property)){
			if (!$initCache){
				$obProperty = PropertyTable::query()
					->setSelect([
						'ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'XML_ID', 'FILE_TYPE',
						'LINK_IBLOCK_ID', 'IS_REQUIRED', 'VERSION', 'USER_TYPE',
					])
					->setFilter(['IBLOCK_ID' => $this->getIblockId(), 'ACTIVE' => 'Y'])
					->exec();

				while ($prop = $obProperty->fetch()) {
					$this->property[$prop['CODE']] = $prop;
				}

				$cache->startDataCache();
				$cache->endDataCache($this->property);
			} else {
				$this->property = $cache->getVars();
			}
		}

		return new ParametersBag($this->property);
	}

	/**
	 * @method getIblockId - get param iblockId
	 * @return int
	 */
	public function getIblockId()
	{
		return (int)$this->iblockId;
	}

	/**
	 * @method setIblockId - set param IblockId
	 * @param int $iblockId
	 *
	 * @throws IblockException\PropertyException
	 */
	public function setIblockId($iblockId)
	{
		$iblockId = (int)$iblockId;
		if ($iblockId == 0)
			throw new IblockException\PropertyException('iblockId is null');

		$this->iblockId = $iblockId;
	}

	/**
	 * @method getCacheId - get param cacheId
	 * @return string
	 */
	public function getCacheId(): string
	{
		return $this->cacheId;
	}

	/**
	 * @method setCacheId - set param CacheId
	 * @param string $cacheId
	 */
	public function setCacheId($cacheId)
	{
		$this->cacheId = $cacheId;
	}

	/**
	 * @method getCacheTime - get param cacheTime
	 * @return int
	 */
	public function getCacheTime():int
	{
		return $this->cacheTime;
	}

	/**
	 * @method setCacheTime - set param CacheTime
	 * @param int $cacheTime
	 */
	public function setCacheTime($cacheTime)
	{
		$this->cacheTime = $cacheTime;
	}

	/**
	 * @method getCacheDir - get param cacheDir
	 * @return string
	 */
	public function getCacheDir(): string
	{
		return $this->cacheDir;
	}

	/**
	 * @method setCacheDir - set param CacheDir
	 * @param string $cacheDir
	 */
	public function setCacheDir($cacheDir)
	{
		$this->cacheDir = $cacheDir;
	}

	/**
	 * @method clearPropertyCache
	 * @param int $iblockId
	 */
	public static function clearPropertyCache(int $iblockId):void
	{
		$prop = new static($iblockId);
		Main\Data\Cache::createInstance()->clean(
			$prop->getCacheId(),
			$prop->getCacheDir()
		);
	}

	public function registerEventHandler()
	{
		$eventManager = Main\EventManager::getInstance();
//		$eventManager->registerEventHandlerCompatible('main', '');
	}

}