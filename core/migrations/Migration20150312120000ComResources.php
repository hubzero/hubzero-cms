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
 * Migration script for adding master_doi field to #__resources
 **/
class Migration20150312120000ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resources'))
		{
			if (!$this->db->tableHasField('#__resources', 'master_doi'))
			{
				$query = "ALTER TABLE `#__resources` ADD COLUMN master_doi varchar(100) DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
