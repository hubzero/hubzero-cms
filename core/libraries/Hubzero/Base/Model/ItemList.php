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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Model;

/**
 * Extended Iterator class
 */
class ItemList extends \Hubzero\Base\ItemList
{
	/**
	 * Fetch Item with key equal to value
	 *
	 * @param   $key    Object Key
	 * @param   $value  Object Value
	 * @return  mixed
	 */
	public function fetch($key, $value)
	{
		foreach ($this->_data as $data)
		{
			if ($data->get($key) == $value)
			{
				return $data;
			}
		}
		return null;
	}

	/**
	 * Lists a specific key from the item list
	 *
	 * @param   string  $key      Key to grab from item
	 * @param   string  $default  Default value if key is empty
	 * @return  array   Array of keys
	 */
	public function lists($key, $default = null)
	{
		$results = array();
		foreach ($this->_data as $data)
		{
			array_push($results, $data->get($key));
		}
		return $results;
	}
}