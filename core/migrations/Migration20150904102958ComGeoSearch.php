<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding GeoSearch component
 **/
class Migration20150904102958ComGeoSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('geosearch', null, 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('geosearch');
	}
}