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
 * Migration script for extending filed from 100 to 255 chars
 **/
class Migration20160715100931PlgHubzeroComments extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__item_comment_files') && $this->db->tableHasField('#__item_comment_files', 'filename'))
		{
			$query = "ALTER TABLE `#__item_comment_files` CHANGE `filename` `filename` VARCHAR(255) DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__item_comment_files') && $this->db->tableHasField('#__item_comment_files', 'filename'))
		{
			$query = "ALTER TABLE `#__item_comment_files` CHANGE `filename` `filename` VARCHAR(100) DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
