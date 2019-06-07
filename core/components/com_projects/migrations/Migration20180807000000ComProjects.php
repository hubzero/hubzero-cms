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
 * Migration script for setting proper scope on project To-dos
 **/
class Migration20180807000000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__activity_logs'))
		{
			$query = "UPDATE `#__activity_logs` SET `scope`='project.todo' WHERE `scope`='project' AND `description` IN ('posted a to do item', 'checked off a to do item')";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__activity_logs` SET `scope`='project.todo.comment' WHERE `scope`='project' AND `description`='commented on a to do item'";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__activity_logs` SET `action`='updated' WHERE `scope`='project.todo' AND `description`='checked off a to do item'";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableExists('#__project_todo'))
			{
				$query = "SELECT id, scope_id, details FROM `#__activity_logs` WHERE `scope`='project.todo' LIMIT 10000";
				$this->db->setQuery($query);
				$rows = $this->db->loadObjectList();
				if ($rows)
				{
					foreach ($rows as $row)
					{
						$query = "SELECT `projectid` FROM `#__project_todo` WHERE `id`=" . $row->scope_id;
						$this->db->setQuery($query);
						$projectid = $this->db->loadResult();

						$details = json_decode($row->details);
						$details->referenceid = $row->scope_id;
						$details->projectid = $projectid;

						$row->details = json_encode($details);

						$query = "UPDATE `#__activity_logs` SET `details`=" . $this->db->quote($row->details) . " WHERE `id`=" . $row->id;
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}

			if ($this->db->tableExists('#__project_comments'))
			{
				$query = "SELECT id, scope_id, details FROM `#__activity_logs` WHERE `scope`='project.todo.comment' LIMIT 10000";
				$this->db->setQuery($query);
				$rows = $this->db->loadObjectList();
				if ($rows)
				{
					foreach ($rows as $row)
					{
						$query = "SELECT t.`projectid` FROM `#__project_todo` AS t INNER JOIN `#__project_comments` AS c ON c.`itemid`=t.`id` WHERE c.`id`=" . $row->scope_id;
						$this->db->setQuery($query);
						$projectid = $this->db->loadResult();

						$details = json_decode($row->details);
						$details->referenceid = $row->scope_id;
						$details->projectid = $projectid;

						$row->details = json_encode($details);

						$query = "UPDATE `#__activity_logs` SET `details`=" . $this->db->quote($row->details) . " WHERE `id`=" . $row->id;
						$this->db->setQuery($query);
						$this->db->query();
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
		if ($this->db->tableExists('#__activity_logs'))
		{
			$query = "UPDATE `#__activity_logs` SET `scope`='project' WHERE `scope`='project.todo' AND `description`='posted a to do item'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
