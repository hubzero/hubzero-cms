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
 * Migration script for adding faq state field
 **/
class Migration20140314131113ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__faq_comments', 'state'))
		{
			$query = "ALTER TABLE `#__faq_comments` ADD `state` TINYINT(2)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__faq_comments` SET `state`=1 WHERE `state`='0'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__faq_comments', 'state'))
		{
			$query = "ALTER TABLE `#__faq_comments` DROP COLUMN `state`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
