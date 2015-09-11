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

use Components\Cron\Models\Job;
use Components\Cron\Tables\Job as Table;
use Hubzero\Base\ItemList;
use Hubzero\Base\Model;

require_once(__DIR__ . DS . 'job.php');

/**
 * Table class for cron jobs
 */
class Manager extends Model
{
	/**
	 * Job
	 *
	 * @var  object
	 */
	private $_job = null;

	/**
	 * Record count for total number of jobs
	 *
	 * @var  integer
	 */
	private $_jobs_count = null;

	/**
	 * ItemList
	 *
	 * @var  object
	 */
	private $_jobs = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_db  = \App::get('db');
		$this->_tbl = new Table($this->_db);
	}

	/**
	 * Returns a reference to a cron Jobs model
	 *
	 * @return  object
	 */
	static function &getInstance()
	{
		static $instance;

		if (!isset($instance))
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Set and get a specific job
	 *
	 * @param   integer  $id  Record ID
	 * @return  object   Job
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
			if ($this->_jobs instanceof ItemList)
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
				$this->_job = Job::getInstance($id);
			}
		}
		// Return current job
		return $this->_job;
	}

	/**
	 * Get a list of jobs
	 *
	 * @param   string   $rtrn     What data to fetch
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
	 */
	public function jobs($rtrn='list', $filters=array(), $clear=false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_jobs_count) || $clear)
				{
					$this->_jobs_count = $this->_tbl->count($filters);
				}
				return $this->_jobs_count;
			break;

			case 'list':
			case 'all':
			default:
				if (!($this->_jobs instanceof ItemList) || $clear)
				{
					if ($results = $this->_tbl->find($filters))
					{
						// Loop through all the items and turn into models
						foreach ($results as $key => $result)
						{
							$results[$key] = new Job($result);
						}
					}
					else
					{
						$results = array();
					}

					$this->_jobs = new ItemList($results);
				}

				return $this->_jobs;
			break;
		}
	}
}
