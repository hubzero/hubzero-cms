<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Showcase module
 **/
class Migration20180217000000TplBmc extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
    // name, name, client (0=site, 1=admin), enabled, default, styles, protected (0=site)
		$this->addTemplateEntry('bmc', 'bmc', 0, 1, 1, null, 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteTemplateEntry('bmc', 0);
	}
}
