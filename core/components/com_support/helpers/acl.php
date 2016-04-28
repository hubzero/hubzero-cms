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

namespace Components\Support\Helpers;

use Hubzero\Base\Object;
use Hubzero\User\Helper as UserHelper;
use User;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'acos.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'aros_acos.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'aros.php');

/**
 * Helper class for support ACL
 */
class ACL extends Object
{
	/**
	 * Current user
	 *
	 * @var object
	 */
	private $_user;

	/**
	 * Database
	 *
	 * @var object
	 */
	private $_db;

	/**
	 * Raw data from database
	 *
	 * @var array
	 */
	private $_raw_data;

	/**
	 * User's groups
	 *
	 * @var array
	 */
	private $_user_groups;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_user = User::getInstance();
		$this->_db = \App::get('db');

		$sql = "SELECT m.*, r.model AS aro_model, r.foreign_key AS aro_foreign_key, r.alias AS aro_alias, c.model AS aco_model, c.foreign_key AS aco_foreign_key
				FROM `#__support_acl_aros_acos` AS m
				LEFT JOIN `#__support_acl_aros` AS r ON m.aro_id=r.id
				LEFT JOIN `#__support_acl_acos` AS c ON m.aco_id=c.id";

		$this->_db->setQuery($sql);
		$this->_raw_data = $this->_db->loadAssocList();

		if (!$this->_user->isGuest())
		{
			$this->_user_groups = UserHelper::getGroups($this->_user->get('id'));
		}
	}

	/**
	 * Get the support ACL, creating if not already exists
	 *
	 * @return  object
	 */
	public static function &getACL()
	{
		static $instance;

		if (!is_object($instance))
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Check permissions
	 *
	 * @param   string   $action           Action to check permissions for
	 * @param   string   $aco              ACO model (comment, ticket, etc)
	 * @param   integer  $aco_foreign_key  Parameter description (if any) ...
	 * @param   integer  $aro_foreign_key  User ID
	 * @return  integer  1 = allowed, 0 = not allowed
	 */
	public function check($action=null, $aco=null, $aco_foreign_key=null, $aro_foreign_key=null)
	{
		$permission = 0;

		// Check if they are logged in
		if (!$aro_foreign_key && $this->_user->isGuest())
		{
			return $permission;
		}

		if ($this->_user->authorise('core.admin'))
		{
			return 1;
		}

		if ($aro_foreign_key)
		{
			$this->setUser($aro_foreign_key);
		}

		// Check user's groups
		if ($this->_user_groups && count($this->_user_groups) > 0)
		{
			foreach ($this->_user_groups as $ug)
			{
				foreach ($this->_raw_data as $line)
				{
					// Get the aco permission
					if ($line['aro_model'] == 'group'
					 && $line['aro_foreign_key'] == $ug->gidNumber
					 && $line['aco_model'] == $aco)
					{
						$permission = ($line['action_' . $action] > $permission || ($line['action_' . $action] < 0 && $permission == 0)) ? $line['action_' . $action] : $permission;
					}
					// Get the specific aco model permission if specified (overrides aco permission)
					if ($aco_foreign_key)
					{
						if ($line['aro_model'] == 'group'
						 && $line['aro_foreign_key'] == $ug->gidNumber
						 && $line['aco_model'] == $aco
						 && $line['aco_foreign_key'] == $aco_foreign_key)
						{
							$permission = ($line['action_' . $action] > $permission || ($line['action_' . $action] < 0 && $permission == 0)) ? $line['action_' . $action] : $permission;
						}
					}
				}
			}
		}
		$grouppermission = $permission;
		$userspecific = false;
		// Check individual
		$permission = 0;
		foreach ($this->_raw_data as $line)
		{
			// Get the aco permission
			if ($line['aro_model'] == 'user'
			 && $line['aro_foreign_key'] == $this->_user->get('id')
			 && $line['aco_model'] == $aco)
			{
				if (isset($line['action_' . $action]))
				{
					$permission = ($line['action_' . $action] > $permission || ($line['action_' . $action] < 0 && $permission == 0)) ? $line['action_' . $action] : $permission;
					$userspecific = true;
				}
			}
			// Get the specific aco model permission if specified (overrides aco permission)
			if ($aco_foreign_key)
			{
				if ($line['aro_model'] == 'user'
				 && $line['aro_foreign_key'] == $this->_user->get('id')
				 && $line['aco_model'] == $aco
				 && $line['aco_foreign_key'] == $aco_foreign_key)
				{
					if (isset($line['action_' . $action]))
					{
						$permission = ($line['action_' . $action] > $permission || ($line['action_' . $action] < 0 && $permission == 0)) ? $line['action_' . $action] : $permission;
						$userspecific = true;
					}
				}
			}
		}

		if ($userspecific)
		{
			return $permission;
		}

		return $grouppermission;
	}

	/**
	 * Set a specific user to check permissions for
	 *
	 * @param   integer  $aro_foreign_key  User ID
	 * @return  void
	 */
	public function setUser($aro_foreign_key=null)
	{
		if ($aro_foreign_key)
		{
			if ($this->_user->get('id') != $aro_foreign_key)
			{
				$this->_user = User::getInstance($aro_foreign_key);
				$this->_user_groups = UserHelper::getGroups($this->_user->get('id'));
			}
		}
	}

	/**
	 * Set the permissions for an action
	 *
	 * @param   string   $action           Action to check permissions for
	 * @param   string   $aco              ACO model (comment, ticket, etc)
	 * @param   integer  $permission       Permission to set
	 * @param   integer  $aco_foreign_key  Parameter description (if any) ...
	 * @param   integer  $aro_foreign_key  User ID
	 * @return  void
	 */
	public function setAccess($action=null, $aco=null, $permission=null, $aco_foreign_key=null, $aro_foreign_key=null)
	{
		if ($aro_foreign_key)
		{
			$this->setUser($aro_foreign_key);
		}
		$set = false;
		for ($i=0, $n=count($this->_raw_data); $i < $n; $i++)
		{
			$line =& $this->_raw_data[$i];

			// Get the aco permission
			if ($line['aro_model'] == 'user'
			 && $line['aro_foreign_key'] == $this->_user->get('id')
			 && $line['aco_model'] == $aco)
			{
				$line['action_' . $action] = $permission;
				$set = true;
			}
			// Get the specific aco model permission if specified (overrides aco permission)
			if ($aco_foreign_key)
			{
				if ($line['aro_model'] == 'user'
				 && $line['aro_foreign_key'] == $this->_user->get('id')
				 && $line['aco_model'] == $aco
				 && $line['aco_foreign_key'] == $aco_foreign_key)
				{
					$line['action_' . $action] = $permission;
					$set = true;
				}
			}
		}
		if (!$set)
		{
			$l = array(
				'aro_model'         => 'user',
				'aro_foreign_key'   => $this->_user->get('id'),
				'aco_model'         => $aco,
				'aco_foreign_key'   => $aco_foreign_key,
				'action_' . $action => $permission
			);
			array_push($this->_raw_data, $l);
		}
	}

	/**
	 * Check if a user is in a group
	 *
	 * @param   string   $group  Group to check
	 * @return  boolean  True if in group
	 */
	public function authorize($group=null)
	{
		if ($group && $this->_user_groups && count($this->_user_groups) > 0)
		{
			foreach ($this->_user_groups as $ug)
			{
				if ($ug->cn == $group)
				{
					return true;
				}
			}
		}
		return false;
	}
}

