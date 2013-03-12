<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'FieldInterface.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'AbstractField.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'DayOfMonthField.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'DayOfWeekField.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'FieldFactory.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'HoursField.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'MinutesField.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'MonthField.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'YearField.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'helpers' . DS . 'Cron' . DS . 'CronExpression.php');

/**
 * Table class for forum posts
 */
class CronModelJob extends JObject
{
	/**
	 * CollectionsTableCollection
	 * 
	 * @var object
	 */
	private $_tbl = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CronTableJob($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
		}
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new CronModelJob($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param     string $property The name of the property
	 * @param     mixed  $default  The default value
	 * @return    mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param     string $property The name of the property
	 * @param     mixed  $value    The value of the property to set
	 * @return    mixed Previous value of the property
	 */
	public function set($property, $value = null)
	{
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function exists()
	{
		if ($this->get('id') && (int) $this->get('id') > 0) 
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
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator()
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		return $this->_creator;
	}

	/**
	 * Check if the course exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function bind($data=null)
	{
		return $this->_tbl->bind($data);
	}

	/**
	 * Short title for 'update'
	 * Long title (if any) ...
	 *
	 * @param unknown $course_id Parameter title (if any) ...
	 * @param array $data Parameter title (if any) ...
	 * @return boolean Return title (if any) ...
	 */
	public function store($check=true)
	{
		if (empty($this->_db))
		{
			return false;
		}

		if ($check)
		{
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Get the item entry for a collection
	 * 
	 * @return     void
	 */
	public function expression()
	{
		if (!isset($this->_expression) || !is_a($this->_expression, 'CronExpression'))
		{
			$this->_expression = Cron\CronExpression::factory($this->get('recurrence'));
		}
		return $this->_expression;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function lastRun($format='Y-m-d H:i:s')
	{
		return $this->expression()->getPreviousRunDate()->format($format);
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function nextRun($format='Y-m-d H:i:s')
	{
		return $this->expression()->getNextRunDate()->format($format);
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function toArray()
	{
		return array(
			'id'       => $this->get('id'),
			'title'    => $this->get('title'),
			'plugin'   => $this->get('plugin'),
			'event'    => $this->get('event'),
			'last_run' => $this->get('last_run'),
			'next_run' => $this->get('next_run'),
			'active'   => $this->get('active')
		);
	}
}
