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
 * Migration script for replacing odd characters in resource license text
 **/
class Migration20131113193815ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_licenses'))
		{
			$query = "UPDATE `#__resource_licenses` SET `text` = REPLACE(`text`, 'â€”', '—')";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
