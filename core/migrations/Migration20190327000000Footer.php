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
 * Migration script for incorrect link in default footer content
 **/
class Migration20190327000000Footer extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__modules'))
		{
			$query = "SELECT * FROM `#__modules` WHERE `title`='Hub Footer' AND `module`='mod_custom' AND `content` LIKE '%/about/dmcapolicy%'";
			$this->db->setQuery($query);
			$modules = $this->db->loadObjectList();

			if ($modules)
			{
				foreach ($modules as $module)
				{
					$content = $module->content;
					$content = str_replace('/about/dmcapolicy', '/aboutus/dmcapolicy', $content);

					$query = "UPDATE `#__modules` SET `content`=" . $this->db->quote($content) . " WHERE `id`=" . $this->db->quote($module->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated DMCA link in default footer module');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__modules'))
		{
			$query = "SELECT * FROM `#__modules` WHERE `title`='Hub Footer' AND `module`='mod_custom' AND `content` LIKE '%/aboutus/dmcapolicy%'";
			$this->db->setQuery($query);
			$modules = $this->db->loadObjectList();

			if ($modules)
			{
				foreach ($modules as $module)
				{
					$content = $module->content;
					$content = str_replace('/aboutus/dmcapolicy', '/about/dmcapolicy', $content);

					$query = "UPDATE `#__modules` SET `content`=" . $this->db->quote($content) . " WHERE `id`=" . $this->db->quote($module->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated DMCA link in default footer module');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}
			}
		}
	}
}
