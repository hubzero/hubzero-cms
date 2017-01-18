<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Import\Handler;

use Hubzero\Base\Object;
use Hubzero\User\Group;
use User;
use Lang;

/**
 * Member Groups import handler
 */
class Groups extends Object
{
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
