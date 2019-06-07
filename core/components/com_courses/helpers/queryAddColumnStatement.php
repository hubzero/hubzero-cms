<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Helpers;

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

	protected function _generateBaseString()
	{
		$this->_asString = 'ADD COLUMN';
	}

	protected function _addName()
	{
		$this->_asString .= " $this->_name";
	}

	protected function _addType()
	{
		$this->_asString .= " $this->_type";
	}

	protected function _addRestriction()
	{
		$restriction = rtrim(" $this->_restriction");

		$this->_asString .= $restriction;
	}

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
