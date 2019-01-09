<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing sliding_panes module
 **/
class Migration20190109000000ModSlidingPanes extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_sliding_panes');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_sliding_panes');
	}
}
