<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Admin menu module
 **/
class Migration20190109000000ModAdminmenu extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_adminmenu', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_adminmenu');
	}
}
