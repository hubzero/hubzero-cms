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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'tool.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'tool.instance.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'tool.status.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'tool.log.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'tool.view.php');
require_once(__DIR__ . DS . 'tool' . DS . 'instance.php');
require_once(__DIR__ . DS . 'tool' . DS . 'log.php');
require_once(__DIR__ . DS . 'tool' . DS . 'status.php');
require_once(__DIR__ . DS . 'tool' . DS . 'view.php');

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Base\ItemList;

/**
 * Project Tool model
 */
class Tool extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\Tool';

	/**
	 * Entry
	 *
	 * @var object
	 */
	private $_entry = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_entries = null;

	/**
	 * Registry
	 *
	 * @var object
	 */
	public $config = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct($oid = NULL, $projectid = NULL, $instance = NULL)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Tool($this->_db);

		if ($oid)
		{
			if (is_numeric($oid) || is_string($oid))
			{
				$this->_tbl->loadTool($oid, $projectid);
			}

			// Load instance
			if ($this->exists())
			{
				$this->version($instance, $oid);
			}
		}
	}

	/**
	 * Returns a reference to a tool model
	 *
	 * @param      mixed $oid TODO ID
	 * @return     object Todo
	 */
	static function &getInstance($oid=null, $projectid = NULL, $instance = NULL)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new static($oid, $projectid, $instance);
		}

		return $instances[$oid];
	}

	/**
	 * Returns a reference to a tool model
	 *
	 * @param      mixed $oid TODO ID
	 * @return     object Todo
	 */
	static function mapInstance($result)
	{
		if (!is_object($result))
		{
			return false;
		}

		$oid = $result->id;
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new static();
			foreach ($result as $key => $value)
			{
				$instances[$oid]->set($key, $value);
			}
		}

		return $instances[$oid];
	}

	/**
	 * Load a specific instance
	 *
	 * @return     void
	 */
	public function version($id=null, $parent = NULL)
	{
		if (!isset($this->_version)
		 || ($id !== null && (int) $this->_version->get('id') != $id))
		{
			$this->_version = Tool\Instance::getInstance($id, $parent);
		}

		return $this->_version;
	}

	/**
	 * Get specific instance property
	 *
	 * @return     void
	 */
	public function instance($property)
	{
		if ($property)
		{
			return $this->_version->get($property);
		}
		return $this->_version;
	}

	/**
	 * Get a log model
	 *
	 * @return     void
	 */
	public function log($parent_id = NULL, $parent_name = NULL)
	{
		if (!isset($this->_log))
		{
			$this->_log = new Tool\Log();
			if ($parent_id)
			{
				$this->_log->set('parent_id', $parent_id);
			}
			if ($parent_name)
			{
				$this->_log->set('parent_name', $parent_name);
			}
		}
		return $this->_log;
	}

	/**
	 * Get last status change
	 *
	 * @return     boolean
	 */
	public function lastStatusChange($property = NULL, $as = NULL)
	{
		return $this->lastUpdate($property, $as, true);

	}

	/**
	 * Get history
	 *
	 * @return  array
	 */
	public function history($filters = array())
	{
		// Get log model
		$log = $this->log($this->get('id'), $this->get('name'));

		return $log->getHistory($filters);
	}

	/**
	 * Get last update
	 *
	 * @return     boolean
	 */
	public function lastUpdate($property = NULL, $as = NULL, $statusChange = false)
	{
		// Get log model
		$log = $this->log($this->get('id'), $this->get('name'));

		if (!isset($this->_lastUpdate))
		{
			$this->_lastUpdate = $log->getLastUpdate($statusChange);
		}
		if ($property)
		{
			if ($property == 'recorded')
			{
				$this->set('recorded', $this->_lastUpdate->recorded);
				return $this->_date('recorded', $as);
			}
			if ($property == 'actor')
			{
				if (!isset($this->_actor) || !($this->_actor instanceof \Hubzero\User\User))
				{
					$this->_actor = \User::getInstance($this->_lastUpdate->actor);
				}
				if ($as)
				{
					return $this->_actor->get($as);
				}
			}
			return isset($this->_lastUpdate->$property) ? $this->_lastUpdate->$property : NULL;
		}
		return $this->_lastUpdate;
	}

	/**
	 * Get a status model
	 *
	 * @return     void
	 */
	public function status($property = NULL, $statusModel = NULL)
	{
		if (!empty($statusModel) && ($statusModel instanceof Tool\Status))
		{
			$statusModel->getStatus($this->get('status'));
			$this->_status = $statusModel;
		}
		if (!isset($this->_status))
		{
			$this->_status = new Tool\Status($this->get('status'));
			$options = new \Hubzero\Config\Registry($this->_status->get('options'));
			$this->_status->set('options', $options);
		}
		if ($property)
		{
			return $this->_status->get($property);
		}
		return $this->_status;
	}

	/**
	 * Get status css
	 *
	 * @return     void
	 */
	public function getStatusCss($admin = false)
	{
		$next_actor = $this->status('next_actor');
		if (!$admin)
		{
			$class = $next_actor == 1 ? 'wait' : 'donext';
			$class = $next_actor == 2 ? 'followup' : $class;
		}
		else
		{
			$class = $next_actor == 1 ? 'donext' : 'wait';
			$class = $next_actor == 2 ? 'followup' : $class;
		}
		return $class;
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		return $this->_date('created', $as);
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function statusChanged($as='')
	{
		return $this->_date('status_changed', $as);
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function lastView($as='')
	{
		if (!isset($this->_lastView))
		{
			if (!isset($this->_viewLog))
			{
				$this->_viewLog = new Tool\View();
			}
			$this->_lastView = $this->_viewLog->lastView($this->get('id'), User::get('id'));
		}
		return $this->_lastView;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $key Field to return
	 * @param      string $as  What data to return
	 * @return     string
	 */
	protected function _date($key, $as='')
	{
		if ($this->get($key) == $this->_db->getNullDate())
		{
			return NULL;
		}
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'datetime':
				return $this->_date($key, 'date') . ' &#64; ' . $this->_date($key, 'time');
			break;

			case 'timeago':
				return \Components\Projects\Helpers\Html::timeAgo($this->get($key));
			break;

			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Get a list of tools
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function entries($rtrn='list', $filters=array(), $admin = false)
	{
		$results = array();

		switch (strtolower($rtrn))
		{
			case 'count':
				$filters['count'] = 1;
				return (int) $this->_tbl->getRecordCount($filters, $admin);
			break;

			case 'results':
				return $this->_tbl->getRecords($filters, $admin);
			break;

			case 'list':
			default:
				if ($results = $this->_tbl->getRecords($filters, $admin))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = self::mapInstance($result);
					}
				}
				else
				{
					$results = array();
				}

				return new ItemList($results);
			break;
		}
		return null;
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param      string $key Config property to retrieve
	 * @return     mixed
	 */
	public function config($key=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = Component::params('com_tools');
		}
		if ($key)
		{
			return $this->_config->get($key);
		}
		return $this->_config;
	}

	/**
	 * Verify data before saving
	 *
	 * @return    boolean False if error, True on success
	 */
	public function verify()
	{
		if (!$this->check())
		{
			// Name check
			return false;
		}
		if (trim($this->get('title')) == '')
		{
			// Title check
			$this->setError( Lang::txt('PLG_PROJECTS_TOOLS_ERROR_MISSING_TITLE') );
			return false;
		}

		// Clean title
	}

	/**
	 * Check tool name
	 *
	 * @param     string $name Alias name
	 * @return    boolean False if error, True on success
	 */
	public function check($name = '', $ajax = 0)
	{
		$name = $name ? $name : $this->get('name');

		// Load config
		$this->config();

		// Set name length
		$minLength = $this->_config->get('min_name_length', 3);
		$maxLength = $this->_config->get('max_name_length', 30);

		// Array of reserved names (task names and default dirs)
		$reserved = explode(',', $this->_config->get('reserved_names'));
		$tasks    = array('temp', 'toolname', 'register');

		if ($name)
		{
			$name = preg_replace('/ /', '', $name);
			$name = strtolower($name);
			$this->set('name', $name);
		}

		// Perform checks
		if (!$name)
		{
			// Cannot be empty
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_EMPTY'));
		}
		elseif (strlen($name) < intval($minLength))
		{
			// Check for length
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_SHORT'));
		}
		elseif (strlen($name) > intval($maxLength))
		{
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_LONG'));
		}
		elseif (preg_match('/[^a-z0-9]/', $name))
		{
			// Check for illegal characters
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID'));
		}
		elseif (is_numeric($name))
		{
			// Check for all numeric (not allowed)
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC'));
		}
		else
		{
			// Verify name uniqueness
			if ($this->_tbl->checkUniqueName( $name, $this->get('id') )
				|| ($reserved && in_array( $name, $reserved)) ||
				in_array( $name, $tasks ))
			{
				$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_NOT_UNIQUE'));
			}
		}
		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !($this->_creator instanceof \Hubzero\User\User))
		{
			$this->_creator = \User::getInstance($this->get('created_by'));
		}
		if ($property)
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get the use who changed status of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function statusChanger($property=null)
	{
		if (!isset($this->_statusChanger) || !($this->_statusChanger instanceof \Hubzero\User\User))
		{
			$this->_statusChanger = \User::getInstance($this->get('status_changed_by'));
		}
		if ($property)
		{
			return is_object($this->_statusChanger) ? $this->_statusChanger->get($property) : NULL;
		}
		return $this->_statusChanger;
	}

	/**
	 * Is sandbox open?
	 *
	 * @return     boolean
	 */
	public function isOpenDev()
	{
		if ($this->get('opendev') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is source open?
	 *
	 * @return     boolean
	 */
	public function isOpenSource()
	{
		if ($this->get('opensource') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is source open?
	 *
	 * @return     boolean
	 */
	public function isPublished()
	{
		if ($this->get('published') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Tool repo exists
	 *
	 * @return     boolean
	 */
	public function repoExists()
	{
		if ($this->get('status') > 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Tool publication
	 *
	 * @return     boolean
	 */
	public function publication()
	{
		return false;
	}

	/**
	 * Is tool in particular status
	 *
	 * @return     boolean
	 */
	public function inStatus($status = NULL)
	{
		switch ($status)
		{
			case 'created':
				return $this->status('status') == 'created' ? true : false;
			break;

			case 'uploaded':
				return $this->status('status') == 'uploaded' ? true : false;
			break;

			case 'installed':
				return $this->status('status') == 'installed' ? true : false;
			break;

			case 'draft':
				return $this->get('status') < 5 ? true : false;
			break;
		}
	}

	/**
	 * Is action required?
	 *
	 * @return     boolean
	 */
	public function actionRequired($type = 'user')
	{
		$next_actor = $this->status('next_actor');
		if ($type == 'user')
		{
			$required = $next_actor == 1 ? false : true;
		}
		else
		{
			$required = $next_actor == 1 ? true : false;
		}

		return $required;
	}
}
