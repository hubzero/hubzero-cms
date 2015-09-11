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

namespace Hubzero\Debug\Profile;

/**
 * Represents a time mark for the profiler.
 */
class Mark
{
	/**
	 * The label.
	 *
	 * @var  string
	 */
	private $label;

	/**
	 * The time of the start of the period (in milliseconds)
	 *
	 * @var  float
	 */
	private $start;

	/**
	 * The time of the end of the period (in milliseconds)
	 *
	 * @var  float
	 */
	private $end;

	/**
	 * The memory usage at the time of the mark
	 *
	 * @var  integer
	 */
	private $memory;

	/**
	 * Constructor.
	 *
	 * @param  string   $label  The label or identifier for a mark
	 * @param  integer  $start  The relative time of the start of the period (in milliseconds)
	 * @param  integer  $end    The relative time of the end of the period (in milliseconds)
	 */
	public function __construct($label, $start, $end)
	{
		$this->label  = (string) $label;
		$this->start  = (float) $start;
		$this->end    = (float) $end;
		$this->memory = memory_get_usage(true);
	}

	/**
	 * Gets the label.
	 *
	 * @return  string  The label
	 */
	public function label()
	{
		return $this->label;
	}

	/**
	 * Gets the relative time of the start of the period.
	 *
	 * @return  integer  The time (in milliseconds)
	 */
	public function started()
	{
		return $this->start;
	}

	/**
	 * Gets the relative time of the end of the period.
	 *
	 * @return  integer  The time (in milliseconds)
	 */
	public function ended()
	{
		return $this->end;
	}

	/**
	 * Gets the time spent in this period.
	 *
	 * @return  integer  The period duration (in milliseconds)
	 */
	public function duration()
	{
		return $this->end - $this->start;
	}

	/**
	 * Gets the memory usage.
	 *
	 * @return  integer  The memory usage (in bytes)
	 */
	public function memory()
	{
		return $this->memory;
	}

	/**
	 * Geta string output
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return sprintf('%s: %.2F MiB - %d ms', $this->label(), $this->memory() / 1024 / 1024, $this->duration());
	}
}
