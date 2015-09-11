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
 * @package    hubzero-cms
 * @author     Shawn Rice <zooley@purdue.edu>
 * @copyright  Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Traits;

/**
 * Adds entity escaping for string output
 *
 * Methods based on those found in Joomla's JView class.
 */
trait Escapable
{
	/**
	 * Callback for escaping.
	 *
	 * @var  string
	 */
	protected $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to utf8 (UTF-8)
	 *
	 * @var  string
	 */
	protected $_charset = 'UTF-8';

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is either htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param   mixed  $var  The output to escape.
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities')))
		{
			return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		}

		return call_user_func($this->_escape, $var);
	}

	/**
	 * Sets the _escape() callback.
	 *
	 * @param   mixed   $spec  The callback for _escape() to use.
	 * @return  object  Chainable
	 */
	public function setEscape($spec)
	{
		$this->_escape = $spec;
		return $this;
	}
}
