<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for inserting timezone data into mysql
 **/
class Migration20140305130635Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Try to get elevated db access
		if (!$this->runAsRoot())
		{
			$return = new \stdClass();
			$return->error = new \stdClass();
			$return->error->type = 'fatal';
			$return->error->message = 'This migration requires elevated privileges. Please try running again as root.';
			return $return;
		}

		$this->db->select('mysql');

		// Get file
		$file = JPATH_ROOT . DS . 'installation' . DS . 'sql' . DS . 'mysql' . DS . 'tzinfo.sql';
		$contents = file_get_contents($file);

		$this->db->setQuery($contents);
		$this->db->query();
	}
}