<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$cname = \JRequest::getVar('cn', \JRequest::getVar('gid', ''));
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
		return \JRequest::getCmd('option', '') == 'com_groups';
	}
}