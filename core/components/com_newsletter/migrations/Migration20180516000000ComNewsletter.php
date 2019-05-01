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
 * Migration script for allowing guest users on the default list
 **/
class Migration20180516000000ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__newsletter_mailinglists') &&
		    !$this->db->tableHasField('#__newsletter_mailinglists', 'guest'))
		{
			$query = "ALTER TABLE `#__newsletter_mailinglists` ADD COLUMN
			  `guest` int(11) DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__newsletter_mailinglists`
			  (`name`, `description`, `private`, `deleted`, `guest`)
			  VALUES('Guest Default List', '', '0', '0', '1');";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__newsletter_mailinglists') &&
		    $this->db->tableHasField('#__newsletter_mailinglists', 'guest'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_mailinglists`;";

			$query = "DELETE FROM `#__newsletter_mailinglists` WHERE `guest`='1';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER TABLE `#__newsletter_mailinglists` DROP COLUMN `guest`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
