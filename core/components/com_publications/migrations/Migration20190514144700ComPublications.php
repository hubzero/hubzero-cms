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
 * Migration script for adding ftpBlacklist to publication params
 **/
class Migration20190514144700ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$publicationParams = Component::params('com_publications');
		$publicationParams->set('sftptypeblacklist', 'series, databases');
		$table = '#__extensions';
		$params = $publicationParams->toString();
		if ($this->db->tableExists($table) && $this->db->tableHasField($table, 'params'))
		{
			$query = "UPDATE `$table` SET `params`='$params' WHERE `name`='com_publications'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$publicationParams = Component::params('com_publications');
		$publicationParams->set('sftptypeblacklist', null);
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
