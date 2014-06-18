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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'tables' . DS . 'preferences.php';

class MembersDashboardModelPreferences extends \Hubzero\Base\Model
{
	/**
	 * JTable
	 *
	 * @var string
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'MembersDashboardTablePreferences';

	/**
	 * Constructor
	 *
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct( $oid = null )
	{
		// create needed objects
		$this->_db = JFactory::getDBO();

		// load page jtable
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
	 * Get Instance this Model
	 *
	 * @param   $key   Instance Key
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($key);
		}

		return $instances[$key];
	}

	/**
	 * Load Member Dashboard Prefs by uidNumber
	 *
	 * @param  int $uidNumber  Member uidNumber
	 * @return object
	 */
	public static function loadForUser($uidNumber=null)
	{
		// get db
		$db = JFactory::getDBO();

		// load prefs by userid
		$sql = "SELECT * FROM `#__xprofiles_dashboard_preferences` WHERE `uidNumber`=" . $db->quote($uidNumber) . " LIMIT 1";
		$db->setQuery($sql);
		$data = $db->loadObject();

		// create new instance of this object
		$object = new self();

		// bind data if we have any
		if (isset($data))
		{
			$object->bind($data);
		}

		// return object
		return $object;
	}
}
