<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron' . DS . 'tables' . DS . 'job.php');

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
 * Table class for a cron job model
 */
class CronModelJob extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CronTableJob';

	/**
	 * Cron expression
	 *
	 * @var object
	 */
	private $_expression = NULL;

	/**
	 * JProfiler
	 *
	 * @var object
	 */
	private $_profiler = NULL;

	/**
	 * Constructor
	 *
	 * @param      integer $id Record ID, array, or object
	 * @return     void
	 */
	public function __construct($oid=null)
	{
		parent::__construct($oid);

		$this->set('params', new JRegistry($this->get('params')));

		jimport('joomla.error.profiler');
		$this->_profiler = new JProfiler('cron_job_' . $this->get('id'));
	}

	/**
	 * Returns a reference to a CronModelJob
	 *
	 * @param      integer $oid Record ID
	 * @return     object CronModelJob
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
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param      string $property Property to retrieve
	 * @param      mixed  $default  Default value if property not set
	 * @return     mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_creator->get((string) $property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Store the record in the database
	 *
	 * @param     boolean $check Perform data validation?
	 * @return    boolean True on success, False on error
	 */
	public function store($check=true)
	{
		$params = $this->get('params');
		if (is_object($params))
		{
			$this->set('params', $params->toString());
		}

		if (!parent::store($check))
		{
			return false;
		}

		$this->set('params', $params);

		return true;
	}

	/**
	 * Get a cron expression
	 *
	 * @return     object
	 */
	public function expression()
	{
		if (!($this->_expression instanceof Cron\CronExpression))
		{
			$this->_expression = Cron\CronExpression::factory($this->get('recurrence'));
		}
		return $this->_expression;
	}

	/**
	 * Check if the job is available
	 *
	 * @return     boolean
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
	 * Has the job started?
	 *
	 * @return     boolean
	 */
	public function started()
	{
		if (!$this->exists() || !$this->isPublished())
		{
			return false;
		}

		$now = \JHTML::_('date', \JFactory::getDate(), 'Y-m-d H:i:s'); //\JFactory::getDate()->toSql();

		if ($this->get('publish_up')
		 && $this->get('publish_up') != $this->_db->getNullDate()
		 && $this->get('publish_up') > $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the job ended?
	 *
	 * @return     boolean
	 */
	public function ended()
	{
		if (!$this->exists() || !$this->isPublished())
		{
			return true;
		}

		$now = \JHTML::_('date', \JFactory::getDate(), 'Y-m-d H:i:s'); //\JFactory::getDate()->toSql();

		if ($this->get('publish_down')
		 && $this->get('publish_down') != $this->_db->getNullDate()
		 && $this->get('publish_down') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the last run timestamp
	 *
	 * @return     void
	 */
	public function lastRun($format='Y-m-d H:i:s')
	{
		return $this->expression()->getPreviousRunDate()->format($format);
	}

	/**
	 * Get the next run timestamp
	 *
	 * @return     void
	 */
	public function nextRun($format='Y-m-d H:i:s')
	{
		return $this->expression()->getNextRunDate()->format($format);
	}

	/**
	 * Mark a time
	 *
	 * @param      string $label
	 * @return     boolean
	 */
	public function mark($label)
	{
		return $this->_profiler->mark($label);
	}

	/**
	 * Get all profiler marks.
	 *
	 * Returns an array of all marks created since the Profiler object
	 * was instantiated.  Marks are strings as per {@link JProfiler::mark()}.
	 *
	 * @return  array  Array of profiler marks
	 */
	public function profile()
	{
		return $this->_profiler->getBuffer();
	}

	/**
	 * Return data about this job, icluding profile info as an array
	 *
	 * @return     array
	 */
	public function toArray()
	{
		$buffer = $this->profile();

		if (!is_array($buffer) || !isset($buffer[0]))
		{
			$buffer = array(
				'0 0 0 0 0',
				'0 0 0 0 0'
			);
		}
		$start_run = explode(' ', $buffer[0]);
		$end_run   = explode(' ', $buffer[1]);

		return array(
			'id'         => $this->get('id'),
			'title'      => $this->get('title'),
			'plugin'     => $this->get('plugin'),
			'event'      => $this->get('event'),
			'last_run'   => $this->get('last_run'),
			'next_run'   => $this->get('next_run'),
			'active'     => $this->get('active'),
			'start_time' => round($start_run[2], 3),
			'start_mem'  => round($start_run[4], 3),
			'end_time'   => round($end_run[2], 3),
			'end_mem'    => round($end_run[4], 3),
			'delta_time' => round($end_run[2] - $start_run[2], 3),
			'delta_mem'  => round($end_run[4] - $start_run[4], 3)
		);
	}
}
