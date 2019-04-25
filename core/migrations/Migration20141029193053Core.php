<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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
