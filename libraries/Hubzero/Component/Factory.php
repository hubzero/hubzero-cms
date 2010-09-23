<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class Hubzero_Component_Factory 
{
	var $_name;

	//-----------

	public function __construct($name) 
	{
		$this->_name = $name;
		$this->loadConfig();
	}
	
	//-----------

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
			$config =& new $configclass();
			$registry->loadObject($config, $namespace);
		}
	}
	
	//-----------

	public function getCfg( $varname, $default = '' )
	{
		$registry =& JFactory::getConfig();

		return $registry->getValue('hubzero_' . $this->_name . '.' . $varname, $default);
	}
	
	//-----------

	public function &getDBO()
	{
		if (defined('_JEXEC')) {
			return JFactory::getDBO();
		} else {
			return $GLOBALS['database'];
		}
	}
}
