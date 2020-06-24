<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Import\Handler;

use Hubzero\Base\Obj;
use User;
use Lang;

/**
 * Member Groups import handler
 */
class Members extends Obj
{
	/**
	 * Return a sample for import header and content
	 *
	 * @return  array
	 */
	public function sample()
	{
		return array(
			'header'  => 'members',
			'content' => 'username or id;username or id;username or id'
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
		if (isset($raw->members) && $raw->members != '')
		{
			$record->members = (array)$this->_multiValueField($raw->members);

			foreach ($record->members as $i => $gid)
			{
				$gid = trim($gid, '"');
				$gid = trim($gid, "'");

				$record->members[$i] = $gid;

				$member = User::getInstance($gid);

				if (!$member || !$member->get('id'))
				{
					$this->setError(Lang::txt('COM_GROUPS_IMPORT_ERROR_MEMBER_NOT_FOUND', $gid));
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
		if (!isset($record->members))
		{
			return $record;
		}

		if ($mode == 'PATCH' && !$record->members)
		{
			return $record;
		}

		// Add user to specified groups
		$added = array();
		foreach ($record->members as $gid)
		{
			$member = User::getInstance($gid);

			if (!$member || !$member->get('id'))
			{
				$this->setError(Lang::txt('COM_GROUPS_IMPORT_ERROR_MEMBER_NOT_FOUND', $gid));
				continue;
			}

			// No need to add if already in the group
			if (in_array($member->get('id'), $added))
			{
				continue;
			}

			// Track members added
			$added[] = $member->get('id');
		}

		$record->entry->add('members', $added);
		//$record->entry->update();

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
