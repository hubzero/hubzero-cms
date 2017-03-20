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

namespace Components\Whatsnew\Helpers;

use Date;

/**
 * Whats New helper class for time periods
 */
class Period
{
	/**
	 * The original search text - should NEVER BE CHANGED
	 *
	 * @var  string
	 */
	private $_period = null;

	/**
	 * Container for storing overloaded data
	 *
	 * @var  array
	 */
	private $_data = array();

	/**
	 * Constructor
	 *
	 * @param   string  $period  Time period (month, week, etc)
	 * @return  void
	 */
	public function __construct($period=null)
	{
		$this->setPeriod($period);

		$this->process();
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param   string  $property  Name of overloaded variable to add
	 * @param   mixed   $value     Value of the overloaded variable
	 * @return  void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Method to get an overloaded variable of the component
	 *
	 * @param   string  $property  Name of overloaded variable to retrieve
	 * @return  mixed   Value of the overloaded variable
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Processes the _period text into actual dates
	 *
	 * @return  void
	 */
	public function setPeriod($period)
	{
		$this->_period = trim((string) $period);

		return $this;
	}

	/**
	 * Processes the _period text into actual dates
	 *
	 * @return  void
	 */
	public function process()
	{
		if (!$this->_period)
		{
			return;
		}

		$this->period = $this->_period;

		// Determine last week and last month date
		switch ($this->period)
		{
			case 'week':
				$this->endTime   = Date::of('now')->toSql();
				$this->startTime = Date::of('-1 week')->toSql();
			break;

			case 'month':
				$this->endTime   = Date::of('now')->toSql();
				$this->startTime = Date::of('-1 month')->toSql();
			break;

			case 'quarter':
				$this->endTime   = Date::of('now')->toSql();
				$this->startTime = Date::of('-3 months')->toSql();
			break;

			case 'year':
				$this->endTime   = Date::of('now')->toSql();
				$this->startTime = Date::of('-1 year')->toSql();
			break;

			default:
				if (substr($this->period, 0, 2) == 'c_')
				{
					$tokens = preg_split('/_/', $this->period);
					$this->period = $tokens[1];

					$this->endTime   = strtotime('12/31/' . $this->period);
					$this->startTime = strtotime('01/01/' . $this->period);
				}
				else
				{
					$this->endTime   = strtotime('08/31/' . $this->period);
					$this->startTime = strtotime('09/01/' . ($this->period-1));
				}
				$this->endTime   = gmdate("Y-m-d H:i:s", $this->endTime);
				$this->startTime = gmdate("Y-m-d H:i:s", $this->startTime);
			break;
		}

		$this->cStartDate = $this->startTime;
		$this->dStartDate = substr($this->startTime, 0, strlen('0000-00-00'));
		$this->cEndDate   = $this->endTime;
		$this->dEndDate   = substr($this->endTime, 0, strlen('0000-00-00'));
	}
}
