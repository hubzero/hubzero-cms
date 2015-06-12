<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing signed nature of uses and max_uses fields
 **/
class Migration20150225150953ComTools extends Base
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

		$info = $mwdb->getTableColumns('host', false);

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'max_uses') && $info['max_uses']->Type == 'int(11) unsigned')
		{
			$query = "ALTER TABLE `host` MODIFY COLUMN `max_uses` int(11) NOT NULL DEFAULT 0";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'uses') && $info['uses']->Type == 'int(11) unsigned')
		{
			$query = "ALTER TABLE `host` MODIFY COLUMN `uses` INT(11) NOT NULL DEFAULT 0";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}