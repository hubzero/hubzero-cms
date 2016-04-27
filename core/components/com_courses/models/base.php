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

namespace Components\Courses\Models;

use Hubzero\Base\Model;
use Components\Courses\Tables;
use Component;
use Date;
use User;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'log.php');

/**
 * Abstract class for course models
 */
abstract class Base extends Model
{
	/**
	 * Draft state
	 *
	 * @var integer
	 */
	const APP_STATE_DRAFT = 3;

	/**
	 * Entry scope
	 *
	 * @var string
	 */
	protected $_scope = NULL;

	/**
	 * Entry creator
	 *
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * Date keys coming from
	 * #__courses_offering_section_dates
	 *
	 * @var array
	 */
	static $_section_keys = array(
		//'section_id',
		'publish_up',
		'publish_down'
	);

	/**
	 * Registry
	 *
	 * @var object
	 */
	protected $_config = NULL;

	/**
	 * Is the entyr in draft state?
	 *
	 * @return  boolean
	 */
	public function isDraft()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == self::APP_STATE_DRAFT)
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the entry started?
	 *
	 * @return  boolean
	 */
	public function started()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished())
		{
			return false;
		}

		$now = Date::toSql();

		if ($this->get('publish_up')
		 && $this->get('publish_up') != $this->_db->getNullDate()
		 && $this->get('publish_up') > $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the entry ended?
	 *
	 * @return  boolean
	 */
	public function ended()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished())
		{
			return true;
		}

		$now = Date::toSql();

		if ($this->get('publish_down')
		 && $this->get('publish_down') != $this->_db->getNullDate()
		 && $this->get('publish_down') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the entry is available
	 *
	 * @return  boolean
	 */
	public function isAvailable()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished())
		{
			return false;
		}

		// Make sure the item is published and within the available time range
		if ($this->started() && !$this->ended())
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire user object
	 *
	 * @param   string $property Param to return
	 * @param   mixed  $default  Value to return if property not found
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!$this->_creator)
		{
			$this->_creator = User::getInstance($this->get('created_by'));
		}
		if ($property)
		{
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Delete a record
	 *
	 * @return  boolean True on success, false on error
	 */
	public function delete()
	{
		// Get some data for the log
		$log = new \stdClass;
		foreach ($this->_tbl->getProperties() as $key => $value)
		{
			$log->$key = $value;
		}
		$log = json_encode($log);

		// Get the scope ID
		$scope_id = $this->get('id');

		if ($res = parent::delete())
		{
			// Log the event
			$this->log($scope_id, $this->_scope, 'delete', $log);
		}

		return $res;
	}

	/**
	 * Log an action
	 *
	 * @param     integer $scope_id Scope ID
	 * @param     string  $scope    Scope
	 * @param     string  $action   Action performed
	 * @param     string  $log      Data
	 * @return    void
	 */
	public function log($scope_id, $scope, $action, $log=null)
	{
		$log = new Tables\Log($this->_db);
		$log->scope_id  = $scope_id;
		$log->scope     = $scope;
		$log->user_id   = User::get('id');
		$log->timestamp = Date::toSql();
		$log->action    = $action;
		$log->comments  = $log;
		$log->actor_id  = User::get('id');
		if (!$log->store())
		{
			$this->setError($log->getError());
		}
	}

	/**
	 * Get a parameter from the component config
	 *
	 * @param   string $property Param to return
	 * @param   mixed  $default  Value to return if property not found
	 * @return  mixed
	 */
	public function config($property=null, $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = Component::params('com_courses');
		}
		if ($property)
		{
			return $this->_config->get($property, $default);
		}
		return $this->_config;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string  $action Action to check
	 * @param   string  $item   Item type to check action against
	 * @return  boolean True if authorized, false if not
	 */
	public function access($action='view', $item='course')
	{
		return $this->config()->access($action, $item);
	}
}

