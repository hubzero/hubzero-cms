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
 * Migration script for fixing up hubgraph queue engine and character set
 **/
class Migration20141114195247ComHubgraph extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('hg_update_queue'))
		{
			if (strtolower($this->db->getEngine('hg_update_queue')) != 'myisam')
			{
				$query = "ALTER TABLE `hg_update_queue` ENGINE = MyISAM";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (strtolower($this->db->getCharacterSet('hg_update_queue')) != 'utf8')
			{
				$query = "ALTER TABLE `hg_update_queue` CHARACTER SET = utf8";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
