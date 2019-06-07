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
 * Migration script for adding a state field to resource reviews
 **/
class Migration20140627140011PlgResourcesReviews extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__resource_ratings', 'state'))
		{
			$query = "ALTER TABLE `#__resource_ratings` ADD `state` TINYINT(2)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__resource_ratings` SET state=1 WHERE resource_id!=0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__resource_ratings', 'state'))
		{
			$query = "ALTER TABLE `#__resource_ratings` DROP `state`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
