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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Members\Models\Member;
use Components\Members\Models\Profile\Field;
use Components\Members\Helpers\Filters;
use Component;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;
use User;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member.php');
include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'profile' . DS . 'field.php');
include_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'filters.php');

/**
 * Members API controller class
 */
class Profilesv1_1 extends ApiController
{
	/**
	 * Display a list of members
	 *
	 * @apiMethod GET
	 * @apiUri    /members/list
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
	 *      "default":       "name",
	 * 		"allowedValues": "name, id"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'sortby'     => Request::getWord('sort', 'name'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC')),
			'activation' => 1,
			'access'     => User::getAuthorisedViewLevels()
		);

		$admin = false;
		if (User::authorise('core.admin'))
		{
			$admin = true;
			$searchable = Request::getVar('searchable', false);
		}

		// Build query
		$entries = Member::all()
			->whereEquals('block', 0)
			->whereEquals('activation', 1)
			->where('approved', '>', 0);

		if ($filters['search'])
		{
			$entries->whereLike('name', strtolower((string)$filters['search']), 1)
				->orWhereLike('username', strtolower((string)$filters['search']), 1)
				->orWhereLike('email', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if (!empty($filters['access']))
		{
			$entries->whereIn('access', $filters['access']);
		}

		switch ($filters['sortby'])
		{
			case 'organization':
				$filters['sort'] = 'surname';
				$filters['sort_Dir'] = 'asc';
			break;

			case 'id':
				$filters['sort'] = 'id';
				$filters['sort_Dir'] = 'asc';
			break;

			case 'name':
			default:
				$filters['sort'] = 'surname';
				$filters['sort_Dir'] = 'asc';
			break;
		}

		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$response = new stdClass;
		$response->members = array();
		$response->total   = $rows->pagination->total;

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($rows as $entry)
			{
				$fields = $entry->profiles()->rows()->toObject();

				// 1 - public
				// 2 - register
				// 3 - special
				// 4 - protected
				// 5 - private
				if ($admin == true && isset($searchable))
				{
					$obj = new stdClass;
					$obj->hubtype = 'member';
					$obj->id = 'member-' . $entry->get('id');
					$obj->title = $entry->get('name');
					$obj->url = str_replace('/api', '', $base . '/' . ltrim(Route::url('index.php?option=' . $this->_option . '&id=' . $entry->get('id')), '/'));

					// @TODO: Add more fields to the SOLR core.
					$fields = $entry->profiles()->rows()->toObject();
					$description = '';
					foreach ($fields as $field)
					{
						if ($field->access == 1)
						{
							$description .= $field->profile_value . ' ';
						}
					}
					$obj->description = $description;

					$access = $entry->get('access');
					if ($access == 1)
					{
						$obj->access_level = 'public';
						$obj->owner = $entry->get('id');
						$obj->owner_type = 'user';
					}
					else
					{
						$obj->access_level = 'private';
						$obj->owner = $entry->get('id');
						$obj->owner_type = 'user';
					}

					$response->members[] = $obj;
				}
				else
				{
					$obj = new stdClass;
					$obj->id           = $entry->get('id');
					$obj->username     = $entry->get('username');
					$obj->name         = $entry->get('name');
					$obj->givenName    = $entry->get('givenName');
					$obj->middleName   = $entry->get('middleName');
					$obj->surname      = $entry->get('surname');
					$obj->organization = $entry->get('organization');
					$obj->uri          = str_replace('/api', '', $base . '/' . ltrim(Route::url('index.php?option=' . $this->_option . '&id=' . $entry->get('id')), '/'));

					$response->members[] = $obj;
				}
			}
		}
		$response->success = true;
		$this->send($response);
	}

	/**
	 * Create a user profile
	 *
	 * @apiMethod POST
	 * @apiUri    /members
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		// Initialize new usertype setting
		$usersConfig = Component::params('com_users');
		$newUsertype = $usersConfig->get('new_usertype');
		if (!$newUsertype)
		{
			$db = App::get('db');
			$query = $db->getQuery()
				->select('id')
				->from('#__usergroups')
				->whereEquals('title', 'Registered');
			$db->setQuery($query->toString());
			$newUsertype = $db->loadResult();
		}

		// Incoming
		$user = User::getInstance();
		$user->set('id', 0);
		$user->set('groups', array($newUsertype));
		$user->set('registerDate', Date::toSql());

		$user->set('name', Request::getVar('name', '', 'post'));
		if (!$user->get('name'))
		{
			App::abort(500, Lang::txt('No name provided.'));
		}

		$user->set('username', Request::getVar('username', '', 'post'));
		if (!$user->get('username'))
		{
			App::abort(500, Lang::txt('No username provided.'));
		}
		if (!\Hubzero\Utility\Validate::username($user->get('username')))
		{
			App::abort(500, Lang::txt('Username not valid.'));
		}

		$user->set('email', Request::getVar('email', '', 'post'));
		if (!$user->get('email'))
		{
			App::abort(500, Lang::txt('No email provided.'));
		}
		if (!\Hubzero\Utility\Validate::email($user->get('email')))
		{
			App::abort(500, Lang::txt('Email not valid.'));
		}

		$name = explode(' ', $user->get('name'));
		$surname    = $user->get('name');
		$givenName  = '';
		$middleName = '';
		if (count($name) > 1)
		{
			$surname    = array_pop($name);
			$givenName  = array_shift($name);
			$middleName = implode(' ', $name);
		}

		// Set the new info
		$user->set('givenName', $givenName);
		$user->set('middleName', $middleName);
		$user->set('surname', $surname);
		$user->set('activation', -rand(1, pow(2, 31)-1));
		$user->set('access', 1);
		$user->set('password', $password);
		//$user->set('password_clear', $password);

		$result = $user->save();

		$user->set('password_clear', '');
		$user->set('password', '');

		if ($result)
		{
			$result = \Hubzero\User\Password::changePassword($user->get('id'), $password);

			// Set password back here in case anything else down the line is looking for it
			$user->set('password', $password);
			$user->save();
		}

		// Did we successfully create/update an account?
		if (!$result)
		{
			App::abort(500, Lang::txt('Account creation failed.'));
		}

		if ($groups = Request::getVar('groups', array(), 'post'))
		{
			foreach ($groups as $id)
			{
				$group = \Hubzero\User\Group::getInstance($id);

				if ($group)
				{
					if (!in_array($user->get('id'), $group->get('members')))
					{
						$group->add('members', array($user->get('id')));
						$group->update();
					}
				}
			}
		}

		// Create a response object
		$response = new stdClass;
		$response->id       = $user->get('id');
		$response->name     = $user->get('name');
		$response->email    = $user->get('email');
		$response->username = $user->get('username');

		$this->send($response);
	}

	/**
	 * Get user profile info
	 *
	 * @apiMethod GET
	 * @apiUri    /members/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Member identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		$userid = Request::getInt('id', 0);

		$result = Member::oneOrFail($userid);

		if (!$result || !$result->get('id'))
		{
			throw new Exception(Lang::txt('COM_MEMBERS_ERROR_USER_NOT_FOUND'), 404);
		}

		// Get any request vars
		$base = rtrim(Request::base(), '/');

		$profile = array(
			'id'                => $result->get('id'),
			'username'          => $result->get('username'),
			'name'              => $result->get('name'),
			'first_name'        => $result->get('givenName'),
			'middle_name'       => $result->get('middleName'),
			'last_name'         => $result->get('surname'),
			'email'             => $result->get('email'),
			'member_since'      => $result->get('registerDate'),
			'picture'   => array(
				'thumb' => $result->picture(0, true),
				'full'  => $result->picture(0, false)
			),
			'interests' => array(),
			'url'       => str_replace('/api', '', $base . '/' . ltrim(Route::url($result->link()), '/'))
		);

		// Get custom fields
		$attribs = Field::all()
			->ordered()
			->rows();

		foreach ($attribs as $attrib)
		{
			$key = $attrib->get('name');

			if ($attrib->get('type') == 'tags')
			{
				$val = $result->tags('string');
			}
			else
			{
				$val = $result->get($key);
			}

			if (is_array($val))
			{
				$val = implode(';', $val);
			}

			$profile[$key] = $val;
		}

		require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'tags.php');
		$cloud = new \Components\Members\Models\Tags($userid);

		foreach ($cloud->tags('list') as $i => $tag)
		{
			$obj = new stdClass;
			$obj->id      = $tag->get('id');
			$obj->raw_tag = $tag->get('raw_tag');
			$obj->tag     = $tag->get('tag');
			$obj->uri     = str_replace('/api', '', $base . '/' . ltrim(Route::url($tag->link()), '/'));

			$obj->substitutes_count = $tag->get('substitutes');
			$obj->objects_count = $tag->get('total');

			$profile['interests'][] = $obj;
		}

		// Corrects image path, API application breaks Route::url() in the Helper::getMemberPhoto() method.
		$profile['picture']['thumb'] = str_replace('/api', '', $base . '/' . $profile['picture']['thumb']);
		$profile['picture']['full']  = str_replace('/api', '', $base . '/' . $profile['picture']['full']);

		// Encode and return result
		$object = new stdClass();
		$object->profile = $profile;

		$this->send($object);
	}

	/**
	 * Get a member's groups
	 *
	 * @apiMethod GET
	 * @apiUri    /members/{id}/groups
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Member identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function groupsTask()
	{
		$this->requiresAuthentication();

		$userid = Request::getInt('id', 0);
		$result = User::getInstance($userid);

		if (!$result || !$result->get('id'))
		{
			throw new Exception(Lang::txt('COM_MEMBERS_ERROR_USER_NOT_FOUND'), 404);
		}

		$groups = $result->groups('members');

		$g = array();
		foreach ($groups as $k => $group)
		{
			$g[$k]['gidNumber']   = $group->gidNumber;
			$g[$k]['cn']          = $group->cn;
			$g[$k]['description'] = $group->description;
		}

		// Encode and return result
		$obj = new stdClass();
		$obj->groups = $g;

		$this->send($obj);
	}

	/**
	 * Check password
	 *
	 * @apiMethod GET
	 * @apiUri    /members/checkpass
	 * @apiParameter {
	 * 		"name":        "password1",
	 * 		"description": "Password to validate",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function checkpassTask()
	{
		$userid = App::get('authn')['user_id'];

		if (!isset($userid) || empty($userid))
		{
			// We don't have a logged in user, but this may be a password reset
			// If so, check session for a user id
			$session  = App::get('session');
			$registry = $session->get('registry');
			$userid   = (!is_null($registry)) ? $registry->get('com_users.reset.user', null) : null;
		}

		// Get the password rules
		$password_rules = \Hubzero\Password\Rule::all()
					->whereEquals('enabled', 1)
					->rows();

		$pw_rules = array();

		// Get the password rule descriptions
		foreach ($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$pw_rules[] = $rule['description'];
			}
		}

		// Get the password
		$pw = Request::getVar('password1', null, 'post');

		// Validate the password
		if (!empty($pw))
		{
			$msg = \Hubzero\Password\Rule::verify($pw, $password_rules, $userid);
		}
		else
		{
			$msg = array();
		}

		$html = '';

		// Iterate through the rules and add the appropriate classes (passed/error)
		if (count($pw_rules) > 0)
		{
			foreach ($pw_rules as $rule)
			{
				if (!empty($rule))
				{
					if (!empty($msg) && is_array($msg))
					{
						$err = in_array($rule, $msg);
					}
					else
					{
						$err = '';
					}
					$mclass = ($err)  ? ' class="error"' : 'class="passed"';
					$html .= "<li $mclass>" . $rule . '</li>';
				}
			}

			if (!empty($msg) && is_array($msg))
			{
				foreach ($msg as $message)
				{
					if (!in_array($message, $pw_rules))
					{
						$html .= '<li class="error">' . $message . '</li>';
					}
				}
			}
		}

		// Encode sessions for return
		$object = new stdClass();
		$object->html = $html;

		$this->send($object);
	}

	/**
	 * Get a list of oranizations used throughout member profiles
	 *
	 * @apiMethod GET
	 * @apiUri    /members/organizations
	 * @return    void
	 */
	public function organizationsTask()
	{
		$organizations = array();

		$field = Field::all()
			->whereEquals('name', 'organization')
			->row();

		if ($field->get('id'))
		{
			$options = $field->options()->ordered()->rows();

			foreach ($options as $option)
			{
				$organization = new stdClass;
				$organization->id = $option->get('id');
				$organization->organization = $option->get('label');

				$organizations[] = $organization;
			}
		}

		// Encode sessions for return
		$object = new stdClass();
		$object->organizations = $organizations;

		$this->send($object);
	}

	/**
	 * Get a resource based on tool name
	 *
	 * @param   string  $appname
	 * @param   object  $database
	 * @return  object
	 */
	private function getResourceFromAppname($appname, $database)
	{
		$sql = "SELECT r.*, tv.id as revisionid FROM `#__resources` as r, `#__tool_version` as tv WHERE tv.toolname=r.alias and tv.instance=" . $database->quote($appname);
		$database->setQuery($sql);
		return $database->loadObject();
	}

	/**
	 * Retrieves option values for a profile field
	 *
	 * @apiMethod GET
	 * @apiUri    /members/fieldValues
	 * @apiParameter {
	 * 		"name":        "field",
	 * 		"description": "Profile field of interest",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     ""
	 * }
	 * @return  void
	 */
	public function fieldValuesTask()
	{
		$name = Request::getVar('field', '');

		$field = Field::all()
			->whereEquals('name', $name)
			->row();

		if (!$field->get('id'))
		{
			App::abort(404, 'Field not found');
		}

		// Create object with values
		$response = new stdClass();
		$response->type = $field->get('type');

		$values = array();

		if ($field->get('type') == 'country')
		{
			$countries = \Hubzero\Geocode\Geocode::countries();

			foreach ($countries as $option)
			{
				// Create a new option object based on the <option /> element.
				$tmp = new stdClass;
				$tmp->value = (string) $option->code;
				$tmp->label = trim((string) $option->name);

				// Add the option object to the result set.
				$values[] = $tmp;
			}
		}
		else
		{
			foreach ($field->options()->ordered()->rows() as $option)
			{
				$values[] = $option->toObject();
			}
		}

		$response->values = $values;

		// Return object
		$this->send($response);
	}
}
