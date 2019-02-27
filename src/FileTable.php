<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 20.07.2018
 */

namespace Soft1c\OrmIblock;

use Bitrix\Main;
use  Bitrix\Main\Entity;

class FileTable extends Main\FileTable
{

	/**
	 * Returns entity object
	 *
	 * @return Entity\Base
	 */
	public static function getEntity()
	{
		$entity = parent::getEntity();

		$helper = $entity->getConnection()->getSqlHelper();

		$entity->addField(new Main\Entity\ExpressionField(
			'PATH',
			$helper->getConcatFunction('"/upload/"', '%s','"/"','%s'),
			array('SUBDIR', 'FILE_NAME')
		));

		$sizeField = new Entity\ExpressionField(
			'SIZE_FORMAT',
			'%s',
			'FILE_SIZE'
		);
		$sizeField->addFetchDataModifier(function ($value, $field, $data, $alias) {
			return \CFile::FormatSize($value, 3);
		});
		$entity->addField($sizeField);

		return $entity;
	}

}