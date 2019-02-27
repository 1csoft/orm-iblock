<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 17.07.2018
 */

namespace Soft1c\OrmIblock;

use Bitrix\Main;
use Soft1c\OrmIblock\Property\IblockV1Entity;

class Query extends Main\ORM\Query\Query
{
	const ON_BEFORE_EXEC = 'OnBeforeExec';

	public function buildQuery()
	{
		if(Main\Loader::includeModule('workflow') && !array_key_exists('WF_PARENT_ELEMENT_ID', $this->getFilter())){
			if($this->getEntity()->hasField('WF_PARENT_ELEMENT_ID'))
				$this->addFilter('WF_PARENT_ELEMENT_ID', false);
		}


		$event = new Main\Event('main', self::ON_BEFORE_EXEC.IblockV1Entity::ENTITY_NAME, ['QUERY' => $this]);
		$event->send();

		return parent::buildQuery();
	}

	public function setEntity(Main\Entity\Base $entity)
	{
		$this->entity = $entity;
	}
}