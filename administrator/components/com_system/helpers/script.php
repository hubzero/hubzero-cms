<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Base class for scripts to extend
 */
class SystemHelperScript extends JObject
{
	/**
	 * Current user
	 *
	 * @var	object JUser
	 */
	protected $_juser = null;

	/**
	 * Database connector
	 *
	 * @var	object JDatabase
	 */
	protected $_db = null;

	/**
	 * Extra options the script can be run with
	 *
	 * @var	array
	 */
	protected $_options = array();

	/**
	 * Path to a log file
	 *
	 * @var	string
	 */
	protected $_description = '';

	/**
	 * Object constructor to set database and juser field
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->_juser = JFactory::getUser();
		$this->_db = JFactory::getDBO();
	}

	/**
	 * Returns description
	 *
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->_description;
	}

	/**
	 * Returns options
	 *
	 * @return	array
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Generic run method
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @return	void
	 */
	public function run()
	{
	}
}
