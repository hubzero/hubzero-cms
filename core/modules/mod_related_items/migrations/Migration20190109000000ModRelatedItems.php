<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing related_items module
 **/
class Migration20190109000000ModRelatedItems extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_related_items');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_related_items');
	}
}
