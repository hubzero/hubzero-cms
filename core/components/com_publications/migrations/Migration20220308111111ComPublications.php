<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding ftp download document path to params in publication component 
 **/
class Migration20220308111111ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{			
			$publicationParams = Component::params('com_publications');
			$publicationParams->set('ftp_doc', 'https://purr.purdue.edu/kb/projects/access-datasets-using-ftp-client');
			$table = '#__extensions';
			$params = $publicationParams->toString();
			if ($this->db->tableExists($table) && $this->db->tableHasField($table, 'params'))
			{
				$query = "UPDATE `$table` SET `params`='$params' WHERE `name`='com_publications'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$publicationParams = Component::params('com_publications');
		$publicationParams->set('ftp_doc', null);
		$table = '#__extensions';
		$params = $publicationParams->toString();
		if ($this->db->tableExists($table) && $this->db->tableHasField($table, 'params'))
		{
			$query = "UPDATE `$table` SET `params`='$params' WHERE `name`='com_publications'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
