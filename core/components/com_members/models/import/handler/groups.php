<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Import\Handler;

use Hubzero\Base\Obj;
use Hubzero\User\Group;
use User;
use Lang;

/**
 * Member Groups import handler
 */
class Groups extends Obj
{
	/**
	 * Return a sample for import header and content
	 *
	 * @return  array
	 */
	public function sample()
	{
		return array(
			'header'  => 'groups',
			'content' => 'groupalias;groupalias;groupalias'
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
		if (isset($raw->groups) && $raw->groups != '')
		{
			$record->groups = (array)$this->_multiValueField($raw->groups);

			foreach ($record->groups as $i => $gid)
			{
				$gid = trim($gid, '"');
				$gid = trim($gid, "'");

				$record->groups[$i] = $gid;

				$group = Group::getInstance($gid);
				if (!$group || !$group->get('gidNumber'))
				{
					$this->setError(Lang::txt('COM_MEMBERS_IMPORT_ERROR_GROUP_NOT_FOUND', $gid));
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
		if (!isset($record->groups))
		{
			return $record;
		}

		if ($mode == 'PATCH' && !$record->groups)
		{
			return $record;
		}

		$id = $record->entry->get('id');

		// Get all the user's current groups
		$existing = \Hubzero\User\Helper::getGroups($id);
		$gids = array();
		if ($existing)
		{
			foreach ($existing as $e)
			{
				$gids[] = $e->gidNumber;
			}
		}

		// Add user to specified groups
		$added = array();
		foreach ($record->groups as $gid)
		{
			$group = Group::getInstance($gid);
			if (!$group || !$group->get('gidNumber'))
			{
				$this->setError(Lang::txt('COM_MEMBERS_IMPORT_ERROR_GROUP_NOT_FOUND', $gid));
				continue;
			}

			// Track groups added to
			$added[] = $group->get('gidNumber');

			// No need to add if already in the group
			if (in_array($group->get('gidNumber'), $gids))
			{
				continue;
			}

			$group->add('members', array($id));
			$group->update();
		}

		// Remove user from all old groups that weren't in the new list
		foreach ($gids as $gid)
		{
			if (in_array($gid, $added))
			{
				continue;
			}

			$group = Group::getInstance($gid);
			if (!$group || !$group->get('gidNumber'))
			{
				continue;
			}

			$group->remove('members', array($id));
			$group->remove('managers', array($id));
			$group->remove('invitees', array($id));
			$group->remove('applicants', array($id));
			$group->update();
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
