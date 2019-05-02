<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Middleware;

use Components\Tools\Helpers\Utils;
use Hubzero\Base\ItemList;
use Hubzero\User\Group;
use User;
use Lang;

$base = dirname(dirname(__DIR__));
require_once($base . DS . 'models' . DS . 'middleware' . DS . 'base.php');
require_once($base . DS . 'tables' . DS . 'session.php');
require_once($base . DS . 'tables' . DS . 'view.php');
require_once($base . DS . 'tables' . DS . 'viewperm.php');
require_once($base . DS . 'tables' . DS . 'job.php');

/**
 * Middleware model for a tool session
 */
class Session extends Base
{
	/**
	 * Table class name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Tools\\Tables\\Session';

	/**
	 * \Hubzero\ItemList
	 *
	 * @var  object
	 */
	private $_cache = array(
		'shared.count' => null,
		'shared.list'  => null
	);

	/**
	 * Constructor
	 *
	 * @param   mixed   $oid         Integer (ID), string (alias), object or array
	 * @param   string  $authorized  Authorization level
	 * @return  void
	 */
	public function __construct($oid=null, $authorized=null)
	{
		$this->_db = Utils::getMWDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \Hubzero\Database\Table))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of \\Hubzero\\Database\\Table.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of \\Hubzero\\Database\\Table.'));
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
	 * @param   mixed   $oid         Session ID or object
	 * @param   string  $authorized  Authorization level
	 * @return  object
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
	 * @return  boolean  True if record exists, False if not
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
	 * @param   string   $rtrn     Data type to return [count, list]
	 * @param   array    $filters  Filters to apply to query
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed    Returns an integer or array depending upon format chosen
	 */
	public function shared($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new \Components\Tools\Models\Middleware\Viewperm($this->_db);

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
				if (!($this->_cache['shared.list'] instanceof ItemList) || $clear)
				{
					if ($results = $tbl->loadViewperm($filters['sessnum']))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Location($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['shared.list'] = new ItemList($results);
				}
				return $this->_cache['shared.list'];
			break;
		}
	}

	/**
	 * Share a session
	 *
	 * @param   string  $with      List of users
	 * @param   string  $group     Group to share with
	 * @param   string  $readonly  More to share with
	 * @return  boolean
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
			$hg = Group::getInstance($group);
			$members = $hg->get('members');

			// merge group members with any passed in username field
			$users = array_values(array_unique(array_merge($users, $members)));

			// remove this user
			$isUserInArray = array_search(User::get('id'), $users);
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
				$this->setError(Lang::txt('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}

			// Check that the user exist
			$zuser = User::getInstance($user);
			if (!$zuser || !is_object($zuser) || !$zuser->get('id'))
			{
				$this->setError(Lang::txt('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}

			//load current view perm
			$mwViewperm = new \Components\Tools\Models\Middleware\Viewperm($this->_db);
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
	 * @param   string   $with  Username
	 * @return  boolean
	 */
	public function unshare($with=null)
	{
		$mv = new \Components\Tools\Models\Middleware\Viewperm($this->_db);
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
	 * @param   string   $what  What data to return
	 * @return  boolean
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
	 * @return  boolean  False if error, True on success
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
