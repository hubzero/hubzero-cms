<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding the welcome template
 **/
class Migration20141029193053Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$styles = array(
			'flavor'   => '',
			'template' => 'hubbasic2013'
		);

		$this->addTemplateEntry('welcome', 'welcome', 0, 1, 0, $styles);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteTemplateEntry('welcome', 0);
	}
}