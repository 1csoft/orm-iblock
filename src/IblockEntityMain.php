<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 01.03.2019
 */

namespace Soft1c\OrmIblock;


use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Objectify\EntityObject;

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

	/**
	 * @method compileEntity
	 * @param string $entityName
	 * @param null $fields
	 * @param array $parameters
	 *
	 * @return IblockEntityMain
	 */
	public static function compileEntity($entityName, $fields = null, $parameters = array())
	{
		$classCode = '';
		$classCodeEnd = '';

		if (strtolower(substr($entityName, -5)) !== 'table')
		{
			$entityName .= 'Table';
		}

		// validation
		if (!preg_match('/^[a-z0-9_]+$/i', $entityName))
		{
			throw new Main\ArgumentException(sprintf(
				'Invalid entity className `%s`.', $entityName
			));
		}

		/** @var DataManager $fullEntityName */
		$fullEntityName = $entityName;

		// namespace configuration
		if (!empty($parameters['namespace']) && $parameters['namespace'] !== '\\')
		{
			$namespace = $parameters['namespace'];

			if (!preg_match('/^[a-z0-9\\\\]+$/i', $namespace))
			{
				throw new Main\ArgumentException(sprintf(
					'Invalid namespace name `%s`', $namespace
				));
			}

			$classCode = $classCode."namespace {$namespace} "."{";
			$classCodeEnd = '}'.$classCodeEnd;

			$fullEntityName = '\\'.$namespace.'\\'.$fullEntityName;
		}

		$parentClass = !empty($parameters['parent']) ? $parameters['parent'] : DataManager::class;

		// build entity code
		$classCode = $classCode."class {$entityName} extends \\".$parentClass." {";
		$classCodeEnd = '}'.$classCodeEnd;

		if (!empty($parameters['table_name']))
		{
			$classCode .= 'public static function getTableName(){return '.var_export($parameters['table_name'], true).';}';
		}

		if (!empty($parameters['uf_id']))
		{
			$classCode .= 'public static function getUfId(){return '.var_export($parameters['uf_id'], true).';}';
		}

		if (!empty($parameters['default_scope']))
		{
			$classCode .= 'public static function setDefaultScope($query){'.$parameters['default_scope'].'}';
		}

		if (isset($parameters['parent_map']) && $parameters['parent_map'] == false)
		{
			$classCode .= 'public static function getMap(){return [];}';
		}

		if(isset($parameters['object_parent']) && is_a($parameters['object_parent'], EntityObject::class, true))
		{
			$classCode .= 'public static function getObjectParentClass(){return '.var_export($parameters['object_parent'], true).';}';
		}

		// create entity
		eval($classCode.$classCodeEnd);

		$entity = self::getInstance($fullEntityName);

		// add fields
		if (!empty($fields))
		{
			foreach ($fields as $fieldName => $field)
			{
				$entity->addField($field, $fieldName);
			}
		}

		return $entity;
	}
}
