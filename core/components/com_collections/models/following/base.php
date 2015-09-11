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

namespace Components\Collections\Models\Following;

use Hubzero\Base\Object;

/**
 * Abstract model class for following
 */
abstract class Base extends Object
{
	/**
	 * Varies
	 *
	 * @var object
	 */
	private $_obj = NULL;

	/**
	 * File path
	 *
	 * @var string
	 */
	private $_image = NULL;

	/**
	 * URL
	 *
	 * @var string
	 */
	private $_baselink = 'index.php';

	/**
	 * Constructor
	 *
	 * @param   integer  $id  ID
	 * @return  void
	 */
	public function __construct($oid=null)
	{
	}

	/**
	 * Returns a reference to this object
	 *
	 * @param   integer  $oid  User ID
	 * @return  object
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new static($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return  mixed
	 */
	public function creator()
	{
		return null;
	}

	/**
	 * Get this item's image
	 *
	 * @return  string
	 */
	public function image()
	{
		return $this->_image;
	}

	/**
	 * Get this item's alias
	 *
	 * @return  string
	 */
	public function alias()
	{
		return '';
	}

	/**
	 * Get this item's title
	 *
	 * @return  string
	 */
	public function title()
	{
		return '';
	}

	/**
	 * Get the URL for this item
	 *
	 * @return  string
	 */
	public function link($what='base')
	{
		return $this->_baselink;
	}
}
