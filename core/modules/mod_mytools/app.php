<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
