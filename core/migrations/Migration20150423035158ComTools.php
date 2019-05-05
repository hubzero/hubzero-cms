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
 * Migration script for adding zones parameters
 **/
class Migration20150423035158ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		if ($mwdb->tableExists('zones') && !$mwdb->tableHasField('zones', 'params') && $mwdb->tableHasField('zones', 'description'))
		{
			$query = "ALTER TABLE `zones` ADD `params` TEXT NULL AFTER `description`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		if ($mwdb->tableExists('zones') && $mwdb->tableHasField('zones', 'params'))
		{
			$query = "ALTER TABLE `zones` DROP `params`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
