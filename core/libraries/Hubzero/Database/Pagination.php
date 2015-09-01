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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Database;

/**
 * Pagination class
 */
class Pagination
{
	/**
	 * Total rows available
	 *
	 * @var  int
	 **/
	public $total;

	/**
	 * Pagination starting point
	 *
	 * @var  int
	 **/
	public $start;

	/**
	 * Pagination page limit
	 *
	 * @var  int
	 **/
	public $limit;

	/**
	 * The HUBzero paginator
	 *
	 * @var  object
	 **/
	private $paginator;

	/**
	 * Attempts to forward calls to the paginator itself
	 *
	 * @param   string  $name       The method name being called
	 * @param   array   $arguments  The method arguments provided
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function __call($name, $arguments)
	{
		// See if we need to call a method on the HUBzero paginator
		if (in_array($name, get_class_methods($this->getPaginator())))
		{
			call_user_func_array(array($this->getPaginator(), $name), $arguments);
		}

		return $this;
	}

	/**
	 * Initializes pagination object
	 *
	 * @param   string  $namespace  The session state variable namespace
	 * @param   int     $total      Total number of records
	 * @param   string  $start      The variable name representing the pagination start number
	 * @param   string  $limit      The variable name representing the pagination limit number
	 * @return  object
	 * @since   2.0.0
	 **/
	public static function init($namespace, $total, $start = 'start', $limit = 'limit')
	{
		$instance = new self;

		$instance->total = $total;
		$instance->start = \Request::getInt($start, 0);
		$instance->limit = \Request::getInt(
			$limit,
			User::getState($namespace . '.limit', \Config::get('list_limit'))
		);

		User::setState($namespace . '.start', $instance->start);
		User::setState($namespace . '.limit', $instance->limit);

		return $instance;
	}

	/**
	 * Returns the html pagination output
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function __toString()
	{
		return $this->getPaginator()->render();
	}

	/**
	 * Gets the HUBzero paginator, or creates a new one
	 *
	 * @return  \Hubzero\Pagination\Paginator
	 * @since   2.0.0
	 **/
	protected function getPaginator()
	{
		if (!isset($this->paginator))
		{
			$this->paginator = new \Hubzero\Pagination\Paginator($this->total, $this->start, $this->limit);
		}

		return $this->paginator;
	}
}