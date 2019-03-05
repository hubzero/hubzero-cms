<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing wishvoters module
 **/
class Migration20190109000000ModWishvoters extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_wishvoters');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_wishvoters');
	}
}
