<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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