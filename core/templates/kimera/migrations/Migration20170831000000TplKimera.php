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
 * Migration script for adding entry for Template - Kimera plugin
 **/
class Migration20170831000000TplKimera extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addTemplateEntry('kimera', 'kimera', 0, 1, 1, null, 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteTemplateEntry('kimera', 0);
	}
}
