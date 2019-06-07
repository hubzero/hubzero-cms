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
 * Migration script for making sure collection created_by is filled in
 **/
class Migration20140829142600ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections'))
		{
			$query = "UPDATE `#__collections` SET `created_by`=`object_id` WHERE `object_type`='member' AND `created_by`=0";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__collections_posts` AS p LEFT JOIN `#__collections` AS c ON p.`collection_id`=c.id SET p.`created_by`=c.`created_by` WHERE p.`created_by`=0 AND c.`is_default`=1";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
