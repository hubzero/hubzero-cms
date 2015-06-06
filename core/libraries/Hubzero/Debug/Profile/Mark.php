<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
