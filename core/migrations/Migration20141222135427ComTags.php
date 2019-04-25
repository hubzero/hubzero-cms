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
 * Migration script for fixing tag created and created_by info from tag logs
 **/
class Migration20141222135427ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags') && $this->db->tableExists('#__tags_log'))
		{
			$query = "UPDATE `#__tags` AS t
						INNER JOIN `#__tags_log` AS l ON t.`id`=l.`tag_id`
					SET t.`created`=l.`timestamp`, t.`created_by`=l.`user_id`
					WHERE t.`created_by`=0 AND l.`action`='tag_created'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
