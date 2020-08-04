<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Helpers;

use Hubzero\Utility\Arr;

class QueryDropColumnStatement
{
	protected $_asString, $_name;

	/**
	 * Constructs QueryDropColumnStatement instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_name = $args['name'];
		$this->_asString = '';
	}

	/**
	 * Returns string representation of drop column statement
	 *
	 * @return   string
	 */
	public function toString()
	{
		$this->_generateBaseString();
		$this->_addName();

		return $this->_asString;
	}

	/**
	 * Generates base SQL string statement
	 *
	 * @return   void
	 */
	protected function _generateBaseString()
	{
		$this->_asString = 'DROP COLUMN';
	}

	/**
	 * Adds column name to SQL string statement
	 *
	 * @return   void
	 */
	protected function _addName()
	{
		$this->_asString .= " $this->_name";
	}
}
