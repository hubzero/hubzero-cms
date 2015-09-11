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

namespace Plugins\Content\Formathtml;

use ReflectionClass;
use RuntimeException;

/**
 * Base class for wiki macros
 * Should be extended
 */
class Macro
{
	/**
	 * Name of the macro
	 *
	 * @var string
	 */
	protected $_name  = NULL;

	/**
	 * Container for internal data
	 *
	 * @var array
	 */
	protected $_data  = array();

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	protected $_db    = NULL;

	/**
	 * Container for errors
	 *
	 * @var unknown
	 */
	protected $_error = NULL;

	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = false;

	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $linkLog = array();

	/**
	 * Instance of a macro
	 *
	 * @var object
	 */
	static protected $thisInstance = NULL;

	/**
	 * Constructor
	 *
	 * @param      array $config Configuration options
	 * @return     void
	 */
	public function __construct($config=array())
	{
		$this->_db = \App::get('db');

		$this->args = '';

		// Set the controller name
		if (empty($this->_name))
		{
			if (isset($config['name']))
			{
				$this->_name = $config['name'];
			}
			else
			{
				// Get the reflection info
				$r = new ReflectionClass($this);

				// Is it namespaced?
				if ($r->inNamespace())
				{
					// It is! This makes things easy.
					$this->_name = strtolower($r->getShortName());
				}
				else if (preg_match('/(.*)Macro/i', get_class($this), $r))
				{
					$this->_name = strtolower($r[1]);
				}
				else
				{
					throw new RuntimeException(__CLASS__ . '::__construct(); Can\'t get or parse class name.');
				}
			}
		}
	}

	/**
	 * Set a property
	 *
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Get an instance of this macro, creating it if not found
	 *
	 * @param      array $config Configuration parameters
	 * @return     object
	 */
	static public function getInstance($config=array())
	{
		if (self::$thisInstance == null)
		{
			if (isset($config['name']))
			{
				$name = $config['name'];
			}
			else
			{
				$name = get_class();
			}
			self::$thisInstance = new $name();
		}
		return self::$thisInstance;
	}

	/**
	 * Render macro output
	 * this should be overriden by extended classes
	 *
	 * @return     void
	 */
	public function render()
	{
		// Abstract function for overloading
	}

	/**
	 * Returnt he macro's name
	 *
	 * @return  string
	 */
	public function name()
	{
		return $this->_name;
	}

	/**
	 * Returns description of macro, use, and accepted arguments
	 * this should be overriden by extended classes
	 *
	 * @return     string
	 */
	public function description()
	{
		return \Lang::txt('Not implemented.');
	}
}

