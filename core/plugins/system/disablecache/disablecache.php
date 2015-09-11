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

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for disabling the cache for select pages
 */
class plgSystemDisablecache extends \Hubzero\Plugin\Plugin
{
	/**
	 * Caching turned on/off
	 *
	 * @var integer
	 */
	private $_caching = 0;

	/**
	 * Current URI
	 *
	 * @var string
	 */
	private $_path = '';

	/**
	 * Check if caching is disabled for this page and set the site config accordingly
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
		if ($this->_checkRules() && \App::isSite())
		{
			$this->_caching = \Config::get('caching');
			\Config::set('caching', 0);
		}
	}

	/**
	 * Check if caching should be re-enabled for this page if it was disabled and
	 * set the site config accordingly
	 *
	 * @return  void
	 */
	public function onAfterDispatch()
	{
		if ($this->_checkRules() && \App::isSite())
		{
			if ($this->params->def('reenable_afterdispatch', 0))
			{
				\Config::set('caching', $this->_caching);
			}
		}
	}

	/**
	 * Check if the current URL is one of the set rules
	 *
	 * @return  boolean  True if the current page is a rule
	 */
	private function _checkRules()
	{
		if (!$this->_path)
		{
			$this->_path = $this->_parseQueryString(str_replace(\Request::base(), '', \Request::current()));
		}

		$defs = str_replace("\r", '', $this->params->def('definitions', ''));
		$defs = explode("\n", $defs);

		foreach ($defs As $def)
		{
			$result = $this->_parseQueryString($def);
			if ($result == $this->_path)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Trim leading and trailing slashes off a URI
	 *
	 * @param   string  $str
	 * @return  string
	 */
	private function _parseQueryString($str)
	{
		$str = trim($str);
		$str = trim($str, DS);

		return $str;
	}
}