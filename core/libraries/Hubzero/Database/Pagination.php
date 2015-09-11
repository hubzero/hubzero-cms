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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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