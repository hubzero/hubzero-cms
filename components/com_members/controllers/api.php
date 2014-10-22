<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

JLoader::import('Hubzero.Component.ApiController');
JLoader::import('Hubzero.Utility.Validate');

/**
 * Members API controller class
 */
class MembersControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch ($this->segments[0])
		{
			case 'myprofile':   $this->myprofile();   break;
			case 'mygroups':    $this->mygroups();    break;
			case 'mysessions':  $this->mysessions();  break;
			case 'recenttools': $this->recenttools(); break;
			case 'checkpass':   $this->checkpass();   break;
			case 'diskusage':   $this->diskusage();   break;
			case 'create':      $this->create();      break;
			default:            $this->not_found();
		}
	}

	/**
	 * Throw a 404 error
	 *
	 * @return  void
	 */
	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404, 'Not Found');
	}

	/**
	 * Throw a 401 error
	 *
	 * @return  void
	 */
	private function not_authorized()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(401, 'Not Authorized');
	}

	/**
	 * Throw an error
	 *
	 * @return  void
	 */
	private function error($code, $message)
	{
		if ($code != '' && $message != '')
		{
			$response = $this->getResponse();
			$response->setErrorMessage($code, $message);
		}
	}

	/**
	 * Get user profile info
	 *
	 * @return  void
	 */
	private function myprofile()
	{
		// Get the userid from authentication token
		// Load user profile from userid
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		// Check to make sure we have a profile
		if ($result === false) return $this->not_found();

		// Get any request vars
		$format = JRequest::getVar('format', 'json');

		$profile = array(
			'id'                => $result->get('uidNumber'),
			'username'          => $result->get('username'),
			'name'              => $result->get('name'),
			'first_name'        => $result->get('givenName'),
			'middle_name'       => $result->get('middleName'),
			'last_name'         => $result->get('surname'),
			'bio'               => $result->getBio('clean'),
			'email'             => $result->get('email'),
			'phone'             => $result->get('phone'),
			'url'               => $result->get('url'),
			'gender'            => $result->get('gender'),
			'organization'      => $result->get('organization'),
			'organization_type' => $result->get('orgtype'),
			'country_resident'  => $result->get('countryresident'),
			'country_origin'    => $result->get('countryorigin'),
			'member_since'      => $result->get('registerDate'),
			'orcid'             => $result->get('orcid'),
			'picture' => array(
				'thumb' => \Hubzero\User\Profile\Helper::getMemberPhoto($result, 0, true),
				'full'  => \Hubzero\User\Profile\Helper::getMemberPhoto($result, 0, false)
			)
		);

		// Encode and return result
		$object = new stdClass();
		$object->profile = $profile;

		$this->setMessageType($format);
		$this->setMessage($object);
	}

	/**
	 * Get a user's groups
	 *
	 * @return  void
	 */
	private function mygroups()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false)	return $this->not_found();

		$groups = \Hubzero\User\Helper::getGroups( $result->get('uidNumber'), 'members', 0);

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

		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}

	/**
	 * Get a user's tool sessions
	 *
	 * @return  void
	 */
	private function mysessions()
	{
		// Get user from authentication and load their profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		// Make sure we have a user
		if ($result === false) return $this->not_authorized();

		// Include middleware utilities
		JLoader::import("joomla.database.table");
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.class.php');

		// Get db connection
		$db = JFactory::getDBO();

		// Get Middleware DB connection
		$mwdb = ToolsHelperUtils::getMWDBO();

		// Get com_tools params
		$mconfig = JComponentHelper::getParams( 'com_tools' );

		// Check to make sure we have a connection to the middleware and its on
		if (!$mwdb || !$mconfig->get('mw_on') || $mconfig->get('mw_on') > 1)
		{
			return $this->error( 503, 'Middleware Service Unavailable' );
		}

		// Get request vars
		$format = JRequest::getVar('format', 'json');
		$order = JRequest::getVar('order', 'id_asc' );

		// Get my sessions
		$ms = new MwSession( $mwdb );
		$sessions = $ms->getRecords( $result->get("username"), '', false );

		// Run middleware command to create screenshots
		$cmd = "/bin/sh ". JPATH_SITE . "/components/com_tools/scripts/mw screenshot " . $result->get('username') . " 2>&1 </dev/null";
		exec($cmd, $results, $status);

		$results = array();
		foreach ($sessions as $session)
		{
			$r = array(
				'id' => $session->sessnum,
				'app' => $session->appname,
				'name' => $session->sessname,
				'started' => $session->start,
				'accessed' => $session->accesstime,
				'owner' => ($result->get('username') == $session->username) ? 1 : 0,
				'ready-only' => ($session->readonly == 'No') ? 0 : 1
			);
			$results[] = $r;
		}

		// Make sure we have an acceptable ordering
		$accepted_ordering = array('id_asc', 'id_desc', 'started_asc', 'started_desc', 'accessed_asc', 'accessed_desc');
		if (in_array($order, $accepted_ordering))
		{
			switch ($order)
			{
				case 'id_asc':
					break;
				case 'id_desc':
					usort($results, array("MembersControllerApi", "id_sort_desc"));
					break;
				case 'started_asc':
					break;
				case 'started_desc':
					usort($results, array("MembersControllerApi", "started_date_sort_desc"));
					break;
				case 'accessed_asc':
					usort($results, array("MembersControllerApi", "accessed_date_sort_asc"));
					break;
				case 'accessed_desc':
					usort($results, array("MembersControllerApi", "accessed_date_sort_desc"));
					break;
			}
		}

		// Encode sessions for return
		$object = new stdClass();
		$object->sessions = $results;

		// Set format and content
		$this->setMessageType($format);
		$this->setMessage($object);
	}

	/**
	 * Sort by ID DESC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function id_sort_desc($a, $b)
	{
		return $a['id'] < $b['id'] ? 1 : -1;
	}

	/**
	 * Sort by started date DESC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function started_date_sort_desc($a, $b)
	{
		return (strtotime($a['started']) < strtotime($b['started'])) ? 1 : -1;
	}

	/**
	 * Sort by accessed date ASC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function accessed_date_sort_asc($a, $b)
	{
		return (strtotime($a['accessed']) < strtotime($b['accessed'])) ? -1 : 1;
	}

	/**
	 * Sort by accessed date DESC
	 *
	 * @param   array $a
	 * @param   array $b
	 * @return  array
	 */
	private function accessed_date_sort_desc($a, $b)
	{
		return (strtotime($a['accessed']) < strtotime($b['accessed'])) ? 1 : -1;
	}

	/**
	 * Get recent tools for a user
	 *
	 * @return  void
	 */
	private function recenttools()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false) return $this->not_found();

		// Load database object
		$database = JFactory::getDBO();

		// Get the supported tag
		$rconfig = JComponentHelper::getParams('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');

		// Get supportedtag usage
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$this->rt = new ResourcesTags(0);
		$supportedtagusage = $this->rt->getTagUsage($supportedtag, 'alias');

		// Load users recent tools
		$sql = "SELECT r.alias, tv.toolname, tv.title, tv.description, tv.toolaccess as access, tv.mw, tv.instance, tv.revision
				FROM `#__resources` as r, `#__recent_tools` as rt, `#__tool_version` as tv
				WHERE r.published=1
				AND r.type=7
				AND r.standalone=1
				AND r.access!=4
				AND r.alias=tv.toolname
				AND tv.state=1
				AND rt.uid={$result->get("uidNumber")}
				AND rt.tool=r.alias
				GROUP BY r.alias
				ORDER BY rt.created DESC";

		$database->setQuery($sql);
		$recent_tools = $database->loadObjectList();

		$r = array();
		foreach ($recent_tools as $k => $recent)
		{
			$r[$k]['alias'] = $recent->alias;
			$r[$k]['title'] = $recent->title;
			$r[$k]['description'] = $recent->description;
			$r[$k]['version'] = $recent->revision;
			$r[$k]['supported'] = (in_array($recent->alias, $supportedtagusage)) ? 1 : 0;
		}

		// Encode sessions for return
		$object = new stdClass();
		$object->recenttools = $r;

		$this->setMessageType('json');
		$this->setMessage($object);
	}

	/**
	 * Check password
	 *
	 * @return  void
	 */
	private function checkpass()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false) return $this->not_found();

		// Get the password rules
		$password_rules = \Hubzero\Password\Rule::getRules();

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
		$pw = JRequest::getCmd('password1', null, 'post');

		// Validate the password
		if (!empty($pw))
		{
			$msg = \Hubzero\Password\Rule::validate($pw, $password_rules, $result);
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

		$this->setMessageType('json');
		$this->setMessage($object);
	}

	/**
	 * Get a resource based on tool name
	 *
	 * @return  void
	 */
	private function diskusage()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false) return $this->not_found();

		require_once JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php';
		$du = ToolsHelperUtils::getDiskUsage($result->get('username'));
		if (count($du) <=1)
		{
			// Error
			$percent = 0;
		}
		else
		{
			bcscale(6);
			$val = (isset($du['softspace']) && $du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			$percent = round($val * 100);
		}

		$amt = ($percent > 100) ? '100' : $percent;
		$total = (isset($du['softspace'])) ? $du['softspace'] / 1024000000 : 0;

		// Encode sessions for return
		$object = new stdClass();
		$object->amount = $amt;
		$object->total  = $total;

		$this->setMessageType('json');
		$this->setMessage($object);
	}

	/**
	 * Get a resource based on tool name
	 *
	 * @return  object
	 */
	private function getResourceFromAppname($appname, $database)
	{
		$sql = "SELECT r.*, tv.id as revisionid FROM `#__resources` as r, `#__tool_version` as tv WHERE tv.toolname=r.alias and tv.instance=" . $database->quote($appname);
		$database->setQuery($sql);
		return $database->loadObject();
	}

	/**
	 * Create a user profile
	 *
	 * @return  void
	 */
	private function create()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false) return $this->not_found();

		// Initialize new usertype setting
		$usersConfig = JComponentHelper::getParams('com_users');
		$newUsertype = $usersConfig->get('new_usertype');
		if (!$newUsertype)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('id')
				->from('#__usergroups')
				->where('title = "Registered"');
			$db->setQuery($query);
			$newUsertype = $db->loadResult();
		}

		// Incoming
		$user = JUser::getInstance();
		$user->set('id', 0);
		$user->set('groups', array($newUsertype));
		$user->set('registerDate', JFactory::getDate()->toMySQL());

		/*$user->set('name', JRequest::getVar('name', '', 'post'));
		if (!$user->get('name'))
		{
			return $this->error(500, JText::_('No name provided.'));
		}

		$user->set('username', JRequest::getVar('username', '', 'post'));
		if (!$user->get('username'))
		{
			return $this->error(500, JText::_('No username provided.'));
		}
		if (!\Hubzero\Utility\Validate::username($user->get('username')))
		{
			return $this->error(500, JText::_('Username not valid.'));
		}

		$user->set('email', JRequest::getVar('email', '', 'post'));
		if (!$user->get('email'))
		{
			return $this->error(500, JText::_('No email provided.'));
		}
		if (!\Hubzero\Utility\Validate::email($user->get('email')))
		{
			return $this->error(500, JText::_('Email not valid.'));
		}

		$user->set('password', $password);
		$user->set('password_clear', $password);
		$user->save();
		$user->set('password_clear', '');

		// Attempt to get the new user
		$profile = \Hubzero\User\Profile::getInstance($user->get('id'));
		$result  = is_object($profile);

		// Did we successfully create an account?
		if ($result)
		{
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
			$profile->set('givenName', $givenName);
			$profile->set('middleName', $middleName);
			$profile->set('surname', $surname);
			$profile->set('name', $user->get('name'));
			$profile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
			$profile->set('public', 0);
			$profile->set('password', '');

			$result = $profile->store();
		}

		if ($result)
		{
			$result = \Hubzero\User\Password::changePassword($profile->get('uidNumber'), $password);

			// Set password back here in case anything else down the line is looking for it
			$profile->set('password', $password);
			$profile->store();
		}

		// Did we successfully create/update an account?
		if (!$result)
		{
			return $this->error(500, JText::_('Account creation failed.'));
		}

		if ($groups = JRequest::getVar('groups', array(), 'post'))
		{
			foreach ($groups as $id)
			{
				$group = \Hubzero\User\Group::getInstance($id);
				if ($group)
				{
					if (!in_array($user->get('id'), $group->get('members'))
					{
						$group->add('members', array($user->get('id')));
						$group->update();
					}
				}
			}
		}*/

		// Create a response object
		$response = new stdClass;
		$response->id       = $user->get('id');
		$response->name     = $user->get('name');
		$response->email    = $user->get('email');
		$response->username = $user->get('username');

		$this->setMessageType(JRequest::getVar('format', 'json'));
		$this->setMessage($response);
	}
}