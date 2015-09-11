<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

namespace Components\Publications\Models;

use Hubzero\Base\Object;

include_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
	. DS . 'helpers' . DS . 'html.php');

/**
 * Publication attachment model class
 */
class Attachment extends Object
{
	/**
	* Element name
	*
	* This has to be set in the final
	* renderer classes.
	*
	* @var string
	*/
	protected $_name = null;

	/**
	* Reference to the object that instantiated the element
	*
	* @var object
	*/
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @access protected
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	* Get the element name
	*
	* @access public
	* @return string type of the parameter
	*/
	public function getName()
	{
		return $this->_name;
	}

	/**
	* Get the element connector property
	*
	* @access public
	* @return string type of the parameter
	*/
	public function getConnector()
	{
		return $this->_connector;
	}
}