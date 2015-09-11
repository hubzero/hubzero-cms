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

namespace Components\Cron\Models;

use Components\Cron\Models\Expression;
use Components\Cron\Tables\Job as Table;
use Hubzero\Base\ItemList;
use Hubzero\Base\Model;
use Hubzero\User\Profile;
use Hubzero\Debug\Profiler;
use Hubzero\Config\Registry;
use Date;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'job.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'Cron' . DS . 'CronExpression.php');

/**
 * Table class for a cron job model
 */
class Job extends Model
{
	/**
	 * Table class name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Cron\\Tables\\Job';

	/**
	 * Cron expression
	 *
	 * @var  object
	 */
	private $_expression = NULL;

	/**
	 * Profiler
	 *
	 * @var  object
	 */
	private $_profiler = NULL;

	/**
	 * Constructor
	 *
	 * @param   integer  $id  Record ID, array, or object
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		parent::__construct($oid);

		$this->set('params', new Registry($this->get('params')));

		$this->_profiler = new Profiler('cron_job_' . $this->get('id'));
	}

	/**
	 * Returns a reference to this object
	 *
	 * @param   integer  $oid  Record ID
	 * @return  object
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
			$instances[$oid] = new self($oid);
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
	 * @param   string  $property Property to retrieve
	 * @param   mixed   $default   Default value if property not set
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof Profile))
		{
			$this->_creator = Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new Profile();
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
	 * @param   boolean  $check  Perform data validation?
	 * @return  boolean  True on success, False on error
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
	 * @return  object
	 */
	public function expression()
	{
		if (!($this->_expression instanceof \Cron\CronExpression))
		{
			$this->_expression = \Cron\CronExpression::factory($this->get('recurrence'));
		}
		return $this->_expression;
	}

	/**
	 * Check if the job is available
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
	 * Has the job started?
	 *
	 * @return  boolean
	 */
	public function started()
	{
		if (!$this->exists() || !$this->isPublished())
		{
			return false;
		}

		$now = Date::of('now')->toLocal('Y-m-d H:i:s');

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
	 * @return  boolean
	 */
	public function ended()
	{
		if (!$this->exists() || !$this->isPublished())
		{
			return true;
		}

		$now = Date::of('now')->toLocal('Y-m-d H:i:s');

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
	 * @return  void
	 */
	public function lastRun($format='Y-m-d H:i:s')
	{
		return $this->expression()->getPreviousRunDate()->format($format);
	}

	/**
	 * Get the next run timestamp
	 *
	 * @return  void
	 */
	public function nextRun($format='Y-m-d H:i:s')
	{
		return $this->expression()->getNextRunDate()->format($format);
	}

	/**
	 * Mark a time
	 *
	 * @param   string   $label
	 * @return  boolean
	 */
	public function mark($label)
	{
		return $this->_profiler->mark($label);
	}

	/**
	 * Get all profiler marks.
	 *
	 * Returns an array of all marks created since the Profiler object
	 * was instantiated.
	 *
	 * @return  array  Array of profiler marks
	 */
	public function profile()
	{
		return $this->_profiler->marks();
	}

	/**
	 * Return data about this job, icluding profile info as an array
	 *
	 * @param   boolean  $vebose
	 * @return  array
	 */
	public function toArray($verbose = false)
	{
		$buffer = $this->profile();

		$start = $buffer[0];
		$end   = end($buffer);

		return array(
			'id'         => $this->get('id'),
			'title'      => $this->get('title'),
			'plugin'     => $this->get('plugin'),
			'event'      => $this->get('event'),
			'last_run'   => $this->get('last_run'),
			'next_run'   => $this->get('next_run'),
			'active'     => $this->get('active'),
			'start_time' => round($start->started(), 3),
			'start_mem'  => round($start->memory(), 3),
			'end_time'   => round($end->ended(), 3),
			'end_mem'    => round($end->memory(), 3),
			'delta_time' => round($end->ended() - $start->started(), 3),
			'delta_mem'  => round($end->memory() - $start->memory(), 3)
		);
	}
}
