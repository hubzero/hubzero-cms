<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// include needed jtables
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'log.php';

class GroupsModelLog extends \Hubzero\Base\Model
{
	/**
	 * GroupsTablePageCategory
	 *
	 * @var object
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'GroupsTableLog';

	/**
	 * Constructor
	 *
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct($oid = null)
	{
		// create database object
		$this->_db = JFactory::getDBO();

		// create page cateogry jtable object
		$this->_tbl = new $this->_tbl_name($this->_db);

		// load object
		if (is_numeric($oid))
		{
			$this->_tbl->load( $oid );
		}
		else if(is_object($oid) || is_array($oid))
		{
			$this->bind( $oid );
		}
	}

	/**
	 * Returns array of log defaults
	 *
	 * @return    array
	 */
	protected static function logDefaults()
	{
		return array(
			'gidNumber' => null,
			'timestamp' => JFactory::getDate()->toSql(),
			'userid'    => JFactory::getUser()->get('id'),
			'action'    => '',
			'comments'  => '',
			'actorid'   => JFactory::getUser()->get('id')
		);
	}

	/**
	 * Log a Group action
	 *
	 * @param    array   $options
	 */
	private function log(array $options = null)
	{
		// merge defaults with passed in options
		$details = array_merge(self::logDefaults(), $options);

		// if we passed in a string lets normalize to array
		if (is_string($details['comments']))
		{
			$details['comments'] = array('message' => $details['comments']);
		}

		// json encode comments
		$details['comments'] = json_encode($details['comments']);

		// bind log details
		$this->bind($details);

		// store log details
		if (!$this->store(true))
		{
			return $this->getError();
		}

		return $this;
	}

	/**
	 * Overloading Static Method Call
	 *
	 * Resolves instance of log model and runs method on instance with args
	 *
	 * @param    $method    Static method name
	 * @param    $args      Method args passed
	 * @return   void
	 */
	public static function __callStatic($method, $args)
	{
		// resolve instance
		$instance = new GroupsModelLog();

		// run method on instance
		switch (count($args))
		{
			case 0:
				return $instance->$method();
			case 1:
				return $instance->$method($args[0]);
			case 2:
				return $instance->$method($args[0], $args[1]);
			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);
			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);
			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}
}
