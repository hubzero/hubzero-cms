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

namespace Components\Groups\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Event;
use Route;
use Lang;
use User;

/**
 * API controller class for Groups
 */
class Groupsv1_0 extends ApiController
{
	/**
	 * Display a list of groups
	 *
	 * @apiMethod GET
	 * @apiUri    /groups/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "created, title, alias, id, publish_up, publish_down, state"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @apiParameter {
	 * 		"name":          "fields",
	 * 		"description":   "Comma-separated list of fields to return",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "gidNumber,cn,description,created,created_by",
	 * 		"allowedValues": "gidNumber, cn, description, published, approved, type, public_desc, join_policy, discoverability, logo, params, created, created_by"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'sortby'     => Request::getWord('sort', 'description'),
			'sort_Dir'   => strtoupper(Request::getWord('sort_Dir', 'DESC')),
			'fields'     => Request::getVar('fields', 'gidNumber,cn,description,created,created_by'),
			'policy'     => strtolower(Request::getWord('policy', '')),
			'type'       => array(1, 3),
			'published'  => 1
		);

		$filters['fields'] = explode(',', $filters['fields']);
		$filters['fields'] = array_map('trim', $filters['fields']);

		$response = \Hubzero\User\Group::find($filters);

		$base = rtrim(Request::base(), '/');

		foreach ($response as $i => $r)
		{
			$group = \Hubzero\User\Group::getInstance($r->gidNumber);

			$response[$i]->url  = str_replace('/api', '', $base . '/' . ltrim(Route::url('index.php?option=com_groups&cn=' . $group->get('cn')), '/'));
			$response[$i]->logo = str_replace('/api', '', $base . '/' . ltrim($group->getLogo(), '/'));
		}

		$this->send($response);
	}

	/**
	 * Create a group
	 *
	 * @apiMethod POST
	 * @apiUri    /groups
	 * @apiParameter {
	 * 		"name":          "cn",
	 * 		"description":   "Group alias that appears in the url for group. Only lowercase alphanumeric chars allowed.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "title",
	 * 		"description":   "Group title",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "tags",
	 * 		"description":   "Group tags",
	 * 		"type":          "string (comma separated)",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "public_description",
	 * 		"description":   "Group public description",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "private_description",
	 * 		"description":   "Group private description",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "join_policy",
	 * 		"description":   "Membership join policy",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "open",
	 * 		"allowedValues": "open, restricted, invite_only, closed"
	 * }
	 * @apiParameter {
	 * 		"name":          "discoverability",
	 * 		"description":   "Is the group shown in hub searches/listings.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "visible",
	 * 		"allowedValues": "visible, hidden"
	 * }
	 * @return  void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$cn              = Request::getWord('cn', '');
		$title           = Request::getVar('title','');
		$tags            = Request::getVar('tags', '');
		$publicDesc      = Request::getVar('public_description', '');
		$privateDesc     = Request::getVar('private_description', '');
		$joinPolicy      = strtolower(Request::getWord('join_policy', 'open'));
		$discoverability = Request::getWord('discoverability', 'visible');

		// var to hold errors
		$errors = array();

		// check for required fields (cn & title)
		if ($cn == '')
		{
			$errors[] = array(
				'field'   => 'cn',
				'message' => Lang::txt('Group cn cannot be empty.')
			);
		}
		if ($title == '')
		{
			$errors[] = array(
				'field'   => 'title',
				'message' => Lang::txt('Group title cannot be empty.')
			);
		}

		// check to make sure cn is valid & isnt taken
		if (!\Hubzero\Utility\Validate::group($cn, false))
		{
			$errors[] = array(
				'field'   => 'cn',
				'message' => Lang::txt('COM_GROUPS_SAVE_ERROR_INVALID_ID')
			);
		}
		if (\Hubzero\User\Group::exists($cn, false))
		{
			$errors[] = array(
				'field'   => 'cn',
				'message' => Lang::txt('COM_GROUPS_SAVE_ERROR_ID_TAKEN')
			);
		}

		// valid join policy
		$policies = array(
			0 => 'open',
			1 => 'restricted',
			2 => 'invite_only',
			3 => 'closed'
		);

		// make sure we have a valid policy
		if (!in_array($joinPolicy, $policies))
		{
			$errors[] = array(
				'field'   => 'join_policy',
				'message' => Lang::txt('Group "join_policy" value must be one of the following: %s', implode(', ', $policies))
			);
		}

		// valid discoverabilities
		$discoverabilities = array(
			0 => 'visible',
			1 => 'hidden'
		);

		// make sure we have a valid discoverability
		if (!in_array($discoverability, $discoverabilities))
		{
			$errors[] = array(
				'field'   => 'discoverability',
				'message' => Lang::txt('Group "discoverability" value must be one of the following: %s', implode(', ', $discoverabilities))
			);
		}

		// check for errors at this point
		if (!empty($errors))
		{
			throw new Exception(Lang::txt('Validation Failed') . ': ' . implode("\n", $errors), 422);
		}

		// make sure we have a public desc of none was entered
		if ($publicDesc == '')
		{
			$publicDesc = $title;
		}

		// map the join policy & discoverability values to their int value
		$joinPolicy      = array_search($joinPolicy, $policies);
		$discoverability = array_search($discoverability, $discoverabilities);

		// bind all our fields to the group object
		$group = new \Hubzero\User\Group();
		$group->set('cn', $cn);
		$group->set('type', 1);
		$group->set('published', 1);
		$group->set('approved', \App::get('component')->params('com_groups')->get('auto_approve', 1));
		$group->set('description', $title);
		$group->set('public_desc', $publicDesc);
		$group->set('private_desc', $privateDesc);
		$group->set('join_policy', $joinPolicy);
		$group->set('discoverability', $discoverability);
		$group->set('created', with(new Date('now'))->toSql());
		$group->set('created_by', User::get('id'));
		$group->add('managers', array(User::get('id')));
		$group->add('members', array(User::get('id')));

		if (!$group->create() || !$group->update())
		{
			throw new Exception(Lang::txt('Failed to create group.'), 500);
		}

		$this->send($group);
	}

	/**
	 * Retrieve a group record
	 *
	 * @apiMethod GET
	 * @apiUri    /groups/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group unique identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "fields",
	 * 		"description":   "Comma-separated list of fields to return",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "gidNumber,cn,description,created,created_by",
	 * 		"allowedValues": "gidNumber, cn, description, published, approved, type, public_desc, join_policy, discoverability, logo, params, created, created_by"
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$id     = Request::getInt('id', 0);
		$fields = Request::getVar('fields', 'gidNumber,description,public_desc,logo,created,created_by');

		$record = \Hubzero\User\Group::getInstance($id);

		if (!$record)
		{
			throw new Exception(Lang::txt('COM_GROUPS_ERROR_MISSING_RECORD'), 404);
		}

		$group = array();

		$fields = explode(',', $fields);
		$fields = array_map('trim', $fields);

		foreach ($fields as $field)
		{
			if (property_exists($record, $field))
			{
				switch ($field)
				{
					case 'logo':
						$group[$field] = str_replace('/api', '', rtrim(Request::base(), '/') . ltrim($record->getLogo(), '/'));
						break;
					case 'link':
						$group[$field] = $record->getLink();
						break;
					case 'public_desc':
					case 'private_desc':
						$group[$field] = $record->getDescription('parsed', 0, str_replace('_desc', '', $field));
						break;
					default:
						$group[$field] = $record->get($field);
				}
			}
		}

		$base = rtrim(Request::base(), '/');
		$group['url'] = str_replace('/api', '', $base . '/' . ltrim(Route::url('index.php?option=com_groups&cn=' . $record->get('cn')), '/'));

		$sections = array();

		$pluginAccess = \Hubzero\User\Group\Helper::getPluginAccess($group);
		$plugins = Event::trigger('groups.onGroupAreas', array());

		if ($plugins)
		{
			$isMember = (in_array(User::get('id'), $record->get('members')));

			foreach ($plugins as $section)
			{
				if (!$section['display_menu_tab'])
				{
					continue;
				}

				if (User::isGuest() && $pluginAccess[$section['name']] != 'anyone')
				{
					continue;
				}

				if ($pluginAccess[$section['name']] == 'nobody')
				{
					continue;
				}

				if (!$isMember && $pluginAccess[$section['name']] == 'members')
				{
					continue;
				}

				$section['url'] = str_replace('/api', '', $base . '/' . ltrim(Route::url('index.php?option=com_groups&cn=' . $record->get('cn') . '&active=' . $section['name']), '/'));

				$sections[] = $section;
			}
		}

		array_unshift($sections, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone',
			'display_menu_tab' => true,
			'url'              => str_replace('/api', '', $base . '/' . ltrim(Route::url('index.php?option=com_groups&cn=' . $record->get('cn') . '&active=overview'), '/'))
		));

		$group['sections'] = $sections;

		$this->send($group);
	}

	/**
	 * Update a group
	 *
	 * @apiMethod PUT
	 * @apiUri    /groups/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Group identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "title",
	 * 		"description":   "Group title",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "tags",
	 * 		"description":   "Group tags",
	 * 		"type":          "string (comma separated)",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "public_description",
	 * 		"description":   "Group public description",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "private_description",
	 * 		"description":   "Group private description",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "join_policy",
	 * 		"description":   "Membership join policy",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "open",
	 * 		"allowedValues": "open, restricted, invite_only, closed"
	 * }
	 * @apiParameter {
	 * 		"name":          "discoverability",
	 * 		"description":   "Is the group shown in hub searches/listings.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "visible",
	 * 		"allowedValues": "visible, hidden"
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$id    = Request::getInt('id', 0);

		$group = \Hubzero\User\Group::getInstance($id);

		if (!$group)
		{
			throw new Exception(Lang::txt('COM_GROUPS_ERROR_MISSING_RECORD'), 404);
		}

		// make sure we are a manager
		if (!in_array(User::get('id'), $group->get('managers')))
		{
			throw new Exception(Lang::txt('Unauthorized, must be a group manager.'), 403);
		}

		// get request params
		$title           = Request::getVar('title', $group->get('description'));
		$tags            = Request::getVar('tags', '');
		$publicDesc      = Request::getVar('public_description', $group->get('public_desc'));
		$privateDesc     = Request::getVar('private_description', $group->get('private_desc'));
		$joinPolicy      = strtolower(Request::getVar('join_policy', $group->get('join_policy')));
		$discoverability = strtolower(Request::getVar('discoverability',  $group->get('discoverability')));

		// var to hold errors
		$errors = array();

		// check for required fields (cn & title)
		if ($title == '')
		{
			$errors[] = array(
				'field'   => 'title',
				'message' => Lang::txt('Group title cannot be empty.')
			);
		}

		// valid join policy
		$policies = array(
			0 => 'open',
			1 => 'restricted',
			2 => 'invite_only',
			3 => 'closed'
		);

		// make sure we have a valid policy
		if (!in_array($joinPolicy, $policies)
			&& !in_array($joinPolicy, array_keys($policies)))
		{
			$errors[] = array(
				'field'   => 'join_policy',
				'message' => Lang::txt('Group "join_policy" value must be one of the following: %s', implode(', ', $policies))
			);
		}

		// valid discoverabilities
		$discoverabilities = array(
			0 => 'visible',
			1 => 'hidden'
		);

		// make sure we have a valid discoverability
		if (!in_array($discoverability, $discoverabilities)
			&& !in_array($discoverability, array_keys($discoverabilities)))
		{
			$errors[] = array(
				'field'   => 'discoverability',
				'message' => Lang::txt('Group "discoverability" value must be one of the following: %s', implode(', ', $discoverabilities))
			);
		}

		// check for errors at this point
		if (!empty($errors))
		{
			throw new Exception(Lang::txt('Validation Failed') . ': ' . implode("\n", $errors), 422);
		}

		// map the join policy & discoverability values to their int value
		if (!is_numeric($joinPolicy))
		{
			$joinPolicy = array_search($joinPolicy, $policies);
		}
		if (!is_numeric($discoverability))
		{
			$discoverability = array_search($discoverability, $discoverabilities);
		}

		// bind all our fields to the group object
		$group->set('description', $title);
		$group->set('public_desc', $publicDesc);
		$group->set('private_desc', $privateDesc);
		$group->set('join_policy', $joinPolicy);
		$group->set('discoverability', $discoverability);

		if (!$group->update())
		{
			throw new Exception(Lang::txt('COM_GROUPS_ERROR_SAVING_DATA'), 500);
		}

		$this->send($group);
	}

	/**
	 * Delete a group
	 *
	 * @apiMethod DELETE
	 * @apiUri    /groups/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Group identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$id    = Request::getInt('id', 0);
		$group = \Hubzero\User\Group::getInstance($id);

		// make sure we have a group
		if (!$group)
		{
			throw new Exception(Lang::txt('Unable to load group with gidNumber: ', $id), 404);
		}

		// make sure we are a manager
		if (!in_array(User::get('id'), $group->get('managers')))
		{
			throw new Exception(Lang::txt('Unauthorized. Must be a group manager to delete a group.'), 403);
		}

		// attempt to delete
		if (!$group->delete())
		{
			throw new Exception($group->getError(), 500);
		}

		$this->send(null, 204);
	}
}
