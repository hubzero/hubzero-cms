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
	 * Container for properties
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
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance()
	{
		static $instance;

		$instance = new CronModelJobs();

		return $instance;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function job($id=null)
	{
		// If the current offering isn't set
		//    OR the ID passed doesn't equal the current offering's ID or alias
		if (!isset($this->_job) 
		 || ($id !== null && (int) $this->_collection->get('id') != $id && (string) $this->_collection->get('alias') != $id))
		{
			// Reset current offering
			$this->_collection = null;

			// If the list of all offerings is available ...
			if (isset($this->_collections) && is_a($this->_collections, 'CollectionsModelIterator'))
			{
				// Find an offering in the list that matches the ID passed
				foreach ($this->collections() as $key => $collection)
				{
					if ((int) $collection->get('id') == $id || (string) $collection->get('alias') == $id)
					{
						// Set current offering
						$this->_collection = $collection;
						break;
					}
				}
			}
			else
			{
				$this->_collection = CollectionsModelCollection::getInstance($id, $this->_object_id, $this->_object_type);
			}
		}
		// Return current offering
		return $this->_job;
	}

	/**
	 * Get a list of resource types
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function jobs($filters=array())
	{
		/*if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}*/

		if (isset($filters['count']) && $filters['count'])
		{
			return $this->_tbl->count($filters);
		}

		if (!isset($this->_collections) || !is_a($this->_collections, 'CollectionsModelIterator'))
		{
			if (($results = $this->_tbl->find($filters)))
			{
				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new CronModelJob($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_jobs = $results; //new CollectionsModelIterator($results);
		}

		return $this->_jobs;
	}
}
