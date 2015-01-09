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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Database;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Pagination class
 */
class Pagination
{
	/**
	 * Total rows available
	 *
	 * @var int
	 **/
	public $total;

	/**
	 * Pagination starting point
	 *
	 * @var int
	 **/
	public $start;

	/**
	 * Pagination page limit
	 *
	 * @var int
	 **/
	public $limit;

	/**
	 * Initializes pagination object
	 *
	 * @return object
	 * @since  1.3.2
	 **/
	public static function init($namespace, $total, $start='start', $limit='limit')
	{
		$instance = new self;

		$instance->total = $total;
		$instance->start = \JRequest::getInt($start, 0);
		$instance->limit = \JRequest::getInt(
			$limit,
			\JFactory::getApplication()->getUserState($namespace . '.limit', \JFactory::getConfig()->get('list_limit'))
		);

		\JFactory::getApplication()->setUserState($namespace . '.start', $instance->start);
		\JFactory::getApplication()->setUserState($namespace . '.limit', $instance->limit);

		return $instance;
	}

	/**
	 * Returns the html pagination output
	 *
	 * @return string
	 * @since  1.3.2
	 **/
	public function __toString()
	{
		return with(new \JPagination($this->total, $this->start, $this->limit))->getListFooter();
	}
}