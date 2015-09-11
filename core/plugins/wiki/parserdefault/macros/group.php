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

namespace Plugins\Wiki\Parserdefault\Macros;

use WikiMacro;
use Hubzero\User\Group;

/**
 * Group Macro Base Class
 * Extends basic macro class
 */
class GroupMacro extends WikiMacro
{
	/**
	 * Group
	 * @var Hubzero\User\Group Object
	 */
	public $group;

	/**
	 * Load group
	 */
	public function __construct()
	{
		$cname = \Request::getVar('cn', \Request::getVar('gid', ''));
		$this->group = Group::getInstance($cname);
	}

	/**
	 * Get macro args
	 * @return array of arguments
	 */
	protected function getArgs()
	{
		//get the args passed in
		return explode(',', $this->args);
	}

	/**
	 * Returns description of macro, use, and accepted arguments
	 * this should be overriden by extended classes
	 *
	 * @return     string
	 */
	public function description()
	{
		return;
	}

	/**
	 * Can render macro method
	 * @return bool
	 */
	protected function canRender()
	{
		return \Request::getCmd('option', '') == 'com_groups';
	}
}