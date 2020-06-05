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
 * Migration script for adding Tools (Windows) resource type
 **/
class Migration20160321145900ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			$query = "SELECT id FROM `#__resource_types` WHERE alias=" . $this->db->quote('windowstools');
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$query = "INSERT INTO `#__resource_types` (`id`, `alias`, `type`, `category`, `description`, `customFields`, `contributable`, `params`)
					VALUES (NULL, " . $this->db->quote('windowstools') . ",
						" . $this->db->quote('Tools (Windows)') . ",
						" . $this->db->quote(27) . ",
						" . $this->db->quote('<p>A simulation tool is software that allows users to run a specific type of calculation. These are (MS) Windows-based.</p>') . ",
						" . $this->db->quote('{"fields":[{"default":"","name":"credits","label":"Credits","type":"textarea","required":"0"},{"default":"","name":"sponsoredby","label":"Sponsors","type":"textarea","required":"0"},{"default":"","name":"references","label":"References","type":"textarea","required":"0"}]}') . ",
						" . $this->db->quote(0) . ",
						" . $this->db->quote('{"plg_about":"1","plg_citations":"0","plg_findthistext":"0","plg_groups":"1","plg_questions":"1","plg_related":"0","plg_reviews":"1","plg_share":"1","plg_sponsors":"1","plg_supportingdocs":"0","plg_usage":"0","plg_versions":"0","plg_wishlist":"1"}') . "
					)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			$query = "SELECT id FROM `#__resource_types` WHERE alias=" . $this->db->quote('windowstools');
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if ($id)
			{
				$query = "DELETE FROM `#__resource_types` WHERE `id`=" . $this->db->quote($id);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
