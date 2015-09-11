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

namespace Components\Tools\Models\Middleware;

use Components\Tools\Helpers\Utils;
use Hubzero\Base\Model;
use Lang;

/**
 * Abstract model class
 */
class Base extends Model
{
	/**
	 * Registry
	 *
	 * @var object
	 */
	private $_config = null;

	/**
	 * Constructor
	 *
	 * @param      mixed $oid Integer (ID), string (alias), object or array
	 * @return     void
	 */
	public function __construct($oid=null)
	{
		$this->_db = Utils::getMWDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of JTable.'));
			}

			if (is_numeric($oid) || is_string($oid))
			{
				// Make sure $oid isn't empty
				// This saves a database call
				if ($oid)
				{
					$this->_tbl->load($oid);
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Get a config value
	 *
	 * @param	   string $key     Property to return
	 * @param	   mixed  $default Default value
	 * @return     mixed
	 */
	public function config($key='', $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = \Component::params('com_tools');
		}

		if ($key)
		{
			return $this->_config->get((string) $key, $default);
		}
		return $this->_config;
	}
}

