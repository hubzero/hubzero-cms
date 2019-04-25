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
 * Migration script to add 'archived' column to #__publication_versions
 **/
class Migration20150116111000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions') && !$this->db->tableHasField('#__publication_versions', 'archived'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD COLUMN `archived` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `accepted`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
