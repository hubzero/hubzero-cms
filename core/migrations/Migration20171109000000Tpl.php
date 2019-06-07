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
 * Migration script for removing tmeplates no longer supported
 **/
class Migration20171109000000Tpl extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteTemplateEntry('hubbasic2012', 0);
		$this->deleteTemplateEntry('hubbasic2013', 0);
		$this->deleteTemplateEntry('baselayer', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addTemplateEntry('hubbasic2012', 'hubbasic2012', 0, 0, 0, null, 1);
		$this->addTemplateEntry('hubbasic2013', 'hubbasic2013', 0, 0, 0, null, 1);
		$this->addTemplateEntry('baselayer', 'baselayer', 0, 0, 0, null, 1);
	}
}
