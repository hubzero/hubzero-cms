<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding kameleon template
 **/
class Migration20140417134640TplKameleonAdmin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$styles = array(
			'header' => 'dark',
			'theme'  => 'salmon'
		);

		$this->addTemplateEntry('kameleon', 'kameleon (admin)', 1, 1, 0, $styles);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteTemplateEntry('kameleon', 1);
	}
}