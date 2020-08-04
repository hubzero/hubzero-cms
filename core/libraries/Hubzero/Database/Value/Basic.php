<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Value;

/**
 * Database basic value class
 */
class Basic
{
	/**
	 * The content of the value item
	 *
	 * @var  string
	 **/
	protected $content = null;

	/**
	 * Constructs the content of the value item
	 *
	 * @param   string  $content  The content of the given value
	 * @return  void
	 * @since   2.1.0
	 **/
	public function __construct($content)
	{
		$this->content = $content;
	}

	/**
	 * Builds the given string representation of the value object
	 *
	 * @param   object  $syntax  The syntax object with which the query is being built
	 * @return  string
	 * @since   2.1.0
	 **/
	public function build($syntax)
	{
		$syntax->bind(is_string($this->content) ? trim($this->content) : $this->content);

		return '?';
	}
}
