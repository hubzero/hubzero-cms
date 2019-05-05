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
 * Migration script for adding default Projects content
 **/
class Migration20170902000000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__project_types'))
		{
			$query = "SELECT COUNT(*) FROM `#__project_types`";

			$this->db->setQuery($query);
			$total = $this->db->loadResult();

			if (!$total)
			{
				$types = array(
					array('General','Individual or collaborative projects of general nature','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0'),
					array('Content publication','Projects created with the purpose to publish data as a resource or a collection of related resources','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0'),
					array('Application development','Projects created with the purpose to develop and publish a simulation tool or a code library','apps_dev=1\npublications_public=1\nteam_public=1\nallow_invite=0')
				);
				foreach ($types as $type)
				{
					$query = "INSERT INTO `#__project_types` (`type`,`description`,`params`) VALUES (" . $this->db->quote($type[0]) . "," . $this->db->quote($type[1]) . "," . $this->db->quote($type[2]) . ")";

					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log(sprintf('Created project type "%s"', $this->db->quote($type[0])));
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
		if ($this->db->tableExists('#__project_types'))
		{
			$query = "DELETE FROM `#__project_types` WHERE `type` IN ('General', 'Content publication', 'Application development')";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log('Deleted project types "General, Content publication, Application development"');
			}
		}
	}
}
