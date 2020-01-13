<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

use Hubzero\Utility\Arr;

class Criterion
{

	protected $_name, $_operator, $_value;

	/**
	 * Constructs Criterion instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_name = Arr::getValue($args, 'name', null);
		$this->_operator = Arr::getValue($args, 'operator', null);
		$this->_value = Arr::getValue($args, 'value', null);
	}

	/**
	 * Returns array representation of criterion
	 *
	 * @return   array
	 */
	public function toArray()
	{
		$thisAsArray = [
			'name' => $this->_name,
			'operator' => $this->_operator,
			'value' => $this->_value
		];

		return $thisAsArray;
	}

	/**
	 * Indiciates if criterion should be used for filtering
	 *
	 * @return   bool
	 */
	public function isValid()
	{
		$isValid = $this->_name !== null;
		$isValid = $isValid && !empty($this->_operator);
		$isValid = $isValid && $this->_value !== null;

		return $isValid;
	}

	/**
	 * Returns instances name value
	 *
	 * @return   string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Returns instances operator value
	 *
	 * @return   string
	 */
	public function getOperator()
	{
		return $this->_operator;
	}

	/**
	 * Returns instances value value
	 *
	 * @return   string
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * Returns instances value to use when creating SQL statement
	 *
	 * @return   string
	 */
	public function getSqlValue()
	{
		return $this->getValue();
	}

}
