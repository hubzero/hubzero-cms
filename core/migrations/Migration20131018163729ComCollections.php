<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Re-add collections component entry to fix up instances where it was only partially added in the Joomla 2.5 version
 **/
class Migration20131018163729ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Collections');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Collections');
	}
}
