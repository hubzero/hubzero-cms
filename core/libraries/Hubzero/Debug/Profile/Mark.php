<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @param  string   $label   The label or identifier for a mark
	 * @param  integer  $start   The relative time of the start of the period (in milliseconds)
	 * @param  integer  $end     The relative time of the end of the period (in milliseconds)
	 * @param  integer  $memory  The memory usage (in bytes)
	 */
	public function __construct($label, $start = 0.0, $end = 0.0, $memory = 0)
	{
		$this->label  = (string) $label;
		$this->start  = (float) $start;
		$this->end    = (float) $end;
		$this->memory = (int) $memory;
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
	 * Get string output
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Get string output
	 *
	 * @return  string
	 */
	public function toString()
	{
		return sprintf('%s: %.2F MiB - %d ms', $this->label(), $this->memory() / 1024 / 1024, $this->duration());
	}

	/**
	 * Get array output
	 *
	 * @return  array
	 */
	public function toArray()
	{
		return array(
			'label'  => $this->label(),
			'start'  => $this->started(),
			'end'    => $this->ended(),
			'memory' => $this->memory()
		);
	}
}
