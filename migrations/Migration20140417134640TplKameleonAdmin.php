<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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