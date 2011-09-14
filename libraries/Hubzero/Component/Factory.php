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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_Component_Factory'
 * 
 * Long description (if any) ...
 */
class Hubzero_Component_Factory
{

	/**
	 * Description for '_name'
	 * 
	 * @var string
	 */
	var $_name;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($name)
	{
		$this->_name = $name;
		$this->loadConfig();
	}

	/**
	 * Short description for 'loadConfig'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $file Parameter description (if any) ...
	 * @return     void
	 */
	public function loadConfig($file = '')
	{
		if ($file == '') {
			$file = JPATH_SITE . '/administrator/components/com_' . $this->_name . '/config.php';
		}

		$configclass = 'Hubzero_' . $this->_name . '_Config';
		$namespace = 'hubzero_' . $this->_name;

		if (file_exists($file)) {
			include_once($file);
		}

		if (class_exists($configclass)) {
			$registry =& JFactory::getConfig();
			$config = new $configclass();
			$registry->loadObject($config, $namespace);
		}
	}

	/**
	 * Short description for 'getCfg'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $varname Parameter description (if any) ...
	 * @param      string $default Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getCfg( $varname, $default = '' )
	{
		$registry =& JFactory::getConfig();

		return $registry->getValue('hubzero_' . $this->_name . '.' . $varname, $default);
	}

	/**
	 * Short description for 'getDBO'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function &getDBO()
	{
		if (defined('_JEXEC')) {
			return JFactory::getDBO();
		} else {
			return $GLOBALS['database'];
		}
	}
}

