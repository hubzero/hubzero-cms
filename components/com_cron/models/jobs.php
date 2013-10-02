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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_cron' . DS . 'models' . DS . 'job.php');

/**
 * Table class for forum posts
 */
class CronModelJobs extends JObject
{
	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * CronTableJob
	 * 
	 * @var object
	 */
	private $_tbl = NULL;

	/**
	 * CronModelJob
	 * 
	 * @var array
	 */
	private $_job = null;

	/**
	 * \Hubzero\ItemList
	 * 
	 * @var array
	 */
	private $_jobs = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDBO();
		$this->_tbl = new CronTableJob($this->_db);
	}

	/**
	 * Returns a reference to a cron Jobs model
	 *
	 * @return     object CronModelJobs
	 */
	static function &getInstance()
	{
		static $instance;

		$instance = new CronModelJobs();

		return $instance;
	}

	/**
	 * Set and get a specific job
	 * 
	 * @return     void
	 */
	public function job($id=null)
	{
		// If the current job isn't set
		//    OR the ID passed doesn't equal the current job's ID or alias
		if (!isset($this->_job) 
		 || ($id !== null && (int) $this->_job->get('id') != $id && (string) $this->_job->get('alias') != $id))
		{
			// Reset current job
			$this->_job = null;

			// If the list of all jobs is available ...
			if ($this->_jobs instanceof \Hubzero\ItemList)
			{
				// Find a job in the list that matches the ID passed
				foreach ($this->jobs() as $job)
				{
					if ((int) $job->get('id') == $id || (string) $job->get('alias') == $id)
					{
						// Set current job
						$this->_job = $job;
						break;
					}
				}
			}

			if (!$this->_job)
			{
				$this->_job = CronModelJob::getInstance($id);
			}
		}
		// Return current job
		return $this->_job;
	}

	/**
	 * Get a list of jobs
	 * 
	 * @param      array $filters Filters to apply to data fetch
	 * @return     object \Hubzero\ItemList
	 */
	public function jobs($filters=array())
	{
		if (isset($filters['count']) && $filters['count'])
		{
			return $this->_tbl->count($filters);
		}

		if (!($this->_jobs instanceof \Hubzero\ItemList))
		{
			if (($results = $this->_tbl->find($filters)))
			{
				// Loop through all the items and turn into models
				foreach ($results as $key => $result)
				{
					$results[$key] = new CronModelJob($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_jobs = new \Hubzero\ItemList($results);
		}

		return $this->_jobs;
	}
}
