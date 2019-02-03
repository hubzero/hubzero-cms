<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing breadcrumbs module
 **/
class Migration20190109000000ModBreadcrumbs extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_breadcrumbs');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_breadcrumbs');
	}
}
