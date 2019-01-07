<?php
/*
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
