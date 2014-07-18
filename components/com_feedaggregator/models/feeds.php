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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_feedaggregator' . DS . 'tables' . DS . 'feeds.php');

/**
 * Courses model class for a course
 */
class FeedAggregatorModelFeeds extends \Hubzero\Base\Model
{
	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_tbl_name = 'FeedAggregatorTableFeeds';


	/**
	 *  Returns all source feeds
	 * @return     object list of source feeds
	 */
	public function loadAll()
	{
		return $this->_tbl->getRecords();
	}

	/**
	 *  Returns feed as selected by ID
	 * @param      integer $id
	 * @return     object list of feed
	 */
	public function loadbyId($id)
	{
		return $this->_tbl->getById($id);
	}

	/**
	 *  Enables or disables a feed
	 * @param 	   integer $id of feed
	 * @param      integer $status of category
	 * @return     void
	 */
	public function updateActive($id, $status)
	{
		return $this->_tbl->updateActive($id, $status);
	}
}


