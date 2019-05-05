<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Import\Handler;

use Hubzero\Base\Obj;
use Components\Projects\Models\Project;
use Components\Projects\Tables;
use Component;
use User;
use Lang;

include_once Component::path('com_projects') . '/models/project.php';

/**
 * Member Projects import handler
 */
class Projects extends Obj
{
	/**
	 * Return a sample for import header and content
	 *
	 * @return  array
	 */
	public function sample()
	{
		return array(
			'header'  => 'projects',
			'content' => 'projectalias;projectalias;projectalias'
		);
	}

	/**
	 * Bind all raw data
	 *
	 * @param   object  $raw
	 * @param   object  $record
	 * @param   string  $mode
	 * @return  object
	 */
	public function bind($raw, $record, $mode = 'UPDATE')
	{
		if (!Component::isEnabled('com_projects'))
		{
			$this->setError(Lang::txt('COM_MEMBERS_IMPORT_ERROR_PROJECTS_DISABLED'));

			return $record;
		}

		if (isset($raw->projects) && $raw->projects != '')
		{
			$record->projects = (array)$this->_multiValueField($raw->projects);

			foreach ($record->projects as $i => $pid)
			{
				$pid = trim($pid, '"');
				$pid = trim($pid, "'");

				$record->projects[$i] = $pid;

				$project = new Project($pid);

				if (!$project || !$project->get('id'))
				{
					$this->setError(Lang::txt('COM_MEMBERS_IMPORT_ERROR_PROJECT_NOT_FOUND', $pid));
					continue;
				}
			}
		}

		return $record;
	}

	/**
	 * Check Data integrity
	 *
	 * @param   object  $raw
	 * @param   object  $record
	 * @param   string  $mode
	 * @return  object
	 */
	public function check($raw, $record, $mode = 'UPDATE')
	{
		return $record;
	}

	/**
	 * Store data
	 *
	 * @param   object  $raw
	 * @param   object  $record
	 * @param   string  $mode
	 * @return  object
	 */
	public function store($raw, $record, $mode = 'UPDATE')
	{
		if (!isset($record->projects))
		{
			return $record;
		}

		if ($mode == 'PATCH' && !$record->projects)
		{
			return $record;
		}

		$id = $record->entry->get('id');

		// Get all the user's current groups
		$db = App::get('db');
		$tbl = new Tables\Project($db);

		$pids = $tbl->getUserProjectIds($id);

		// Add user to specified groups
		$added = array();
		foreach ($record->projects as $pid)
		{
			$project = new Project($pid);

			if (!$project || !$project->get('id'))
			{
				$this->setError(Lang::txt('COM_MEMBERS_IMPORT_ERROR_PROJECT_NOT_FOUND', $pid));
				continue;
			}

			// No need to add if already in the group
			if (in_array($project->get('id'), $added))
			{
				continue;
			}

			$objO = $project->table('Owner');

			$native = ($project->access('owner')) ? 1 : 0;
			if ($objO->saveOwners($project->get('id'), User::get('id'), $id, 0, 0, 1, $native))
			{
				// Track projects added to
				$added[] = $project->get('id');
			}
		}

		$config = Component::params('com_projects');

		// Remove user from all old projects that weren't in the new list
		foreach ($pids as $pid)
		{
			if (in_array($pid, $added))
			{
				continue;
			}

			$project = new Project($pid);

			if (!$project || !$project->get('id'))
			{
				continue;
			}

			$objO = $project->table('Owner');

			if (!$objO->removeOwners($project->get('id'), $id, 1))
			{
				$this->setError(Lang::txt('COM_MEMBERS_IMPORT_ERROR_UNABLE_TO_REMOVE_FROM_PROJECT', $pid));
				continue;
			}

			$objO->sysGroup($project->get('alias'), $config->get('group_prefix', 'pr-'));
		}

		return $record;
	}

	/**
	 * Split a string into multiple values based on delimiter(s)
	 *
	 * @param   mixed   $data   String or array of field values
	 * @param   string  $delim  List of delimiters, separated by a pipe "|"
	 * @return  array
	 */
	private function _multiValueField($data, $delim=',|;')
	{
		if (is_string($data))
		{
			$data = array_map('trim', preg_split("/($delim)/", $data));
			$data = array_values(array_filter($data));
		}

		return $data;
	}
}
