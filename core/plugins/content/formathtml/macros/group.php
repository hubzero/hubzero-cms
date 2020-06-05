<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use \Plugins\Content\Formathtml\Macro;
use Hubzero\User\Group;

/**
 * Group Macro Base Class
 * Extends basic macro class
 */
class GroupMacro extends Macro
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
		$cname = \Request::getString('cn', \Request::getString('gid', ''));
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
