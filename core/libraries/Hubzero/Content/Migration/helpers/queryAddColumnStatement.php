<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Helpers;

use Hubzero\Utility\Arr;

class QueryAddColumnStatement
{
	protected $_asString, $_default, $_name, $_restriction, $_type;

	/**
	 * Constructs QueryAddColumnStatement instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_name = $args['name'];
		$this->_type = $args['type'];
		$this->_restriction = Arr::getValue($args, 'restriction', null);
		$this->_default = Arr::getValue($args, 'default', null);
		$this->_asString = '';
	}

	/**
	 * Returns string representation of add column statement
	 *
	 * @return   string
	 */
	public function toString()
	{
		$this->_generateBaseString();
		$this->_addName();
		$this->_addType();
		$this->_addRestriction();
		$this->_addDefault();

		return $this->_asString;
	}

	/**
	 * Generates base SQL string statement
	 *
	 * @return   void
	 */
	protected function _generateBaseString()
	{
		$this->_asString = 'ADD COLUMN';
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

	/**
	 * Adds column type to SQL string statement
	 *
	 * @return   void
	 */
	protected function _addType()
	{
		$this->_asString .= " $this->_type";
	}

	/**
	 * Adds column restriction to SQL string statement
	 *
	 * @return   void
	 */
	protected function _addRestriction()
	{
		$restriction = rtrim(" $this->_restriction");

		$this->_asString .= $restriction;
	}

	/**
	 * Adds column default to SQL string statement
	 *
	 * @return   void
	 */
	protected function _addDefault()
	{
		$default = '';

		if ($this->_default === 0 || $this->_default)
		{
			$default = " DEFAULT $this->_default";
		}

		$this->_asString .= $default;
	}
}
