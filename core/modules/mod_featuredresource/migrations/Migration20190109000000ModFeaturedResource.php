<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing featuredresource module
 **/
class Migration20190109000000ModFeaturedResource extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_featuredresource');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_featuredresource');
	}
}
