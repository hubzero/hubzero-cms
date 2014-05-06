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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.view.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.job.php');

/**
 * Middleware model for a tool session
 */
class MiddlewareModelSession extends MiddlewareModelBase
{
	/**
	 * Table class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'MwSession';

	/**
	 * \Hubzero\ItemList
	 * 
	 * @var object
	 */
	private $_cache = array(
		'shared.count' => null,
		'shared.list'  => null
	);

	/**
	 * Constructor
	 * 
	 * @param      mixed  $oid        Integer (ID), string (alias), object or array
	 * @param      string $authorized Authorization level
	 * @return     void
	 */
	public function __construct($oid=null, $authorized=null)
	{
		$this->_db = MwUtils::getMWDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . \JText::_('Table class must be an instance of JTable.')
				);
				throw new \LogicException(\JText::_('Table class must be an instance of JTable.'));
			}

			if (is_numeric($oid) || is_string($oid))
			{
				// Make sure $oid isn't empty
				// This saves a database call
				if ($oid)
				{
					$obj = $this->_tbl->loadSession($oid, $authorized);
					if ($obj)
					{
						$this->bind($obj);
					}
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to an session model
	 *
	 * @param      mixed  $oid        Session ID or object
	 * @param      string $authorized Authorization level
	 * @return     object
	 */
	static function &getInstance($oid=null, $authorized=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->sessnum;
		}
		else if (is_array($oid))
		{
			$key = $oid['sessnum'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new self($key, $authorized);
		}

		return $instances[$key];
	}

	/**
	 * Check if the entry exists (i.e., has a database record)
	 * 
	 * @return     boolean True if record exists, False if not
	 */
	public function exists()
	{
		if ($this->get('sessnum') && (int) $this->get('sessnum') > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Get a list of shared views
	 * 
	 * @param      string  $rtrn    Data type to return [count, list]
	 * @param      array   $filters Filters to apply to query
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed Returns an integer or array depending upon format chosen
	 */
	public function shared($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new MwViewperm($this->_db);

		if (!isset($filters['sessnum']))
		{
			$filters['sessnum'] = $this->get('sessnum');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['shared.count']) || $clear)
				{
					$this->_cache['shared.count'] = count($this->shared('list', $filters));
				}
				return $this->_cache['shared.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['shared.list'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($results = $tbl->loadViewperm($filters['sessnum']))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new MiddlewareModelLocation($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['shared.list'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['shared.list'];
			break;
		}
	}

	/**
	 * Share a session
	 * 
	 * @param      string $with     List of users
	 * @param      string $group    Group to share with
	 * @param      string $readonly More to share with
	 * @return     boolean
	 */
	public function share($with=null, $group=null, $readonly='Yes')
	{
		$users = array();

		if (strstr($with, ',')) 
		{
			$users = explode(',', $with);
			$users = array_map('trim', $users);
		} 
		elseif (strstr($with, ' ')) 
		{
			$users = explode(' ', $with);
			$users = array_map('trim', $users);
		} 
		else 
		{
			$users[] = $with;
		}

		if ($group)
		{
			$hg = \Hubzero\User\Group::getInstance($group);
			$members = $hg->get('members');

			// merge group members with any passed in username field
			$users = array_values(array_unique(array_merge($users, $members)));

			// remove this user
			$isUserInArray = array_search($juser->get('id'), $users);
			if (isset($isUserInArray))
			{
				unset($users[$isUserInArray]);
			}

			// fix array keys
			$users = array_values(array_filter($users));
		}

		if ($readonly != 'Yes') 
		{
			$readonly = 'No';
		}

		foreach ($users as $user)
		{
			// Check for invalid characters
			if (!preg_match("/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i", $user)) 
			{
				$this->setError(JText::_('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}

			// Check that the user exist
			$zuser = JUser::getInstance($user);
			if (!$zuser || !is_object($zuser) || !$zuser->get('id')) 
			{
				$this->setError(JText::_('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}

			//load current view perm
			$mwViewperm = new MwViewperm($this->_db);
			$currentViewPerm = $mwViewperm->loadViewperm($sess, $zuser->get('username'));

			// If there are no matching entries in viewperm, add a new entry,
			// Otherwise, update the existing entry (e.g. readonly).
			if (count($currentViewPerm) == 0) 
			{
				$mwViewperm->sessnum   = $this->get('sessnum');
				$mwViewperm->viewuser  = $zuser->get('username');
				$mwViewperm->viewtoken = md5(rand());
				$mwViewperm->geometry  = $rows[0]->geometry;
				$mwViewperm->fwhost    = $rows[0]->fwhost;
				$mwViewperm->fwport    = $rows[0]->fwport;
				$mwViewperm->vncpass   = $rows[0]->vncpass;
				$mwViewperm->readonly  = $readonly;
				$mwViewperm->insert();
			}
			else
			{
				$mwViewperm->sessnum   = $currentViewPerm[0]->sessnum;
				$mwViewperm->viewuser  = $currentViewPerm[0]->viewuser;
				$mwViewperm->viewtoken = $currentViewPerm[0]->viewtoken;
				$mwViewperm->geometry  = $currentViewPerm[0]->geometry;
				$mwViewperm->fwhost    = $currentViewPerm[0]->fwhost;
				$mwViewperm->fwport    = $currentViewPerm[0]->fwport;
				$mwViewperm->vncpass   = $currentViewPerm[0]->vncpass;
				$mwViewperm->readonly  = $readonly;
				$mwViewperm->updateViewPerm();
			}

			if ($mwViewperm->getError())
			{
				$this->setError($mwViewperm->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Stop sharing a session with a specified user
	 * 
	 * @param      string $with Username
	 * @return     boolean
	 */
	public function unshare($with=null)
	{
		$mv = new MwViewperm($this->_db);
		if (!$mv->deleteViewperm($this->get('sessnum'), $with))
		{
			$this->setError($mv->getError());
			return false;
		}
		return true;
	}

	/**
	 * Get associated tool information
	 * 
	 * @param      string $what What data to return
	 * @return     boolean
	 */
	public function app($what=null)
	{
		$app = $this->get('appname');

		switch (strtolower($what))
		{
			case 'tool':
			case 'name':
				return strstr($app, '_', true);
			break;

			case 'version':
				$r = substr(strrchr($app, '_'), 1);
				if (substr($r, 0, 1) == 'r') 
				{
					return substr($r, 1);
				}
				else if (substr($r, 0, 3) == 'dev')
				{
					return 'dev';
				}
				else
				{
					return '';
				}
			break;

			default:
				return $app;
			break;
		}
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Can't delete what doesn't exist
		if (!$this->exists()) 
		{
			return true;
		}

		// Remove comments
		foreach ($this->shared('list') as $shared)
		{
			if (!$shared->delete())
			{
				$this->setError($shared->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::delete();
	}
}

