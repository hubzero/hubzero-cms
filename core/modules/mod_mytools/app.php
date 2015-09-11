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

namespace Modules\MyTools;

/**
 * This class holds information about one application.
 * It may be either a running session or an app that can be invoked.
 */
class App
{
	/**
	 * Tool name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Tool caption
	 *
	 * @var string
	 */
	public $caption;

	/**
	 * Tool description
	 *
	 * @var string
	 */
	public $desc;

	/**
	 * which environment to run in
	 *
	 * @var string
	 */
	public $middleware;

	/**
	 * sessionid of application
	 *
	 * @var integer
	 */
	public $session;

	/**
	 * owner of a running session
	 *
	 * @var integer
	 */
	public $owner;

	/**
	 * Nth occurrence of this application in a list
	 *
	 * @var integer
	 */
	public $num;

	/**
	 * is this tool public?
	 *
	 * @var integer
	 */
	public $public;

	/**
	 * what license is in use?
	 *
	 * @var string
	 */
	public $revision;

	/**
	 * Tool name
	 *
	 * @var string
	 */
	public $toolname;

	/**
	 * Constructor
	 *
	 * @param   string   $n    Name
	 * @param   string   $c    Caption
	 * @param   string   $d    Description
	 * @param   string   $m    sessionid of application
	 * @param   integer  $s    sessionid of application
	 * @param   integer  $o    Parameter description (if any) ...
	 * @param   integer  $num  Nth occurrence of this application in a list
	 * @param   integer  $p    is this tool public?
	 * @param   string   $r    what license is in use?
	 * @param   string   $tn   Tool name
	 * @return  void
	 */
	public function __construct($n, $c, $d, $m, $s, $o, $num, $p, $r, $tn)
	{
		$this->name       = $n;
		$this->caption    = $c;
		$this->desc       = $d;
		$this->middleware = $m;
		$this->session    = $s;
		$this->owner      = $o;
		$this->num        = $num;
		$this->public     = $p;
		$this->revision   = $r;
		$this->toolname   = $tn;
	}
}
