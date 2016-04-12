<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding plugins for Publication metadata
 **/
class Migration20160412200438PlgPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('publications', 'googlescholar');
		$this->addPluginEntry('publications', 'opengraph');
		$this->addPluginEntry('publications', 'dublincore');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('publications', 'googlescholar');
		$this->deletePluginEntry('publications', 'opengraph');
		$this->deletePluginEntry('publications', 'dublincore');
	}
}
