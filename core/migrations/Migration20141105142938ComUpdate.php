<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting the com_update asset rules to only allow supers
 **/
class Migration20141105142938ComUpdate extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$rules = array(
			'core.admin' => array(
				'Super Users' => 1
			)
		);

		$this->setAssetRules('com_update', $rules);
	}
}