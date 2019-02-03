<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing featuredmember module
 **/
class Migration20190109000000ModFeaturedMember extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_featuredmember');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_featuredmember');
	}
}
