<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

/**
 * Short description for 'modXLogin'
 * 
 * Long description (if any) ...
 */
class modXLogin
{

	/**
	 * Description for '_objects'
	 * 
	 * @var array
	 */
	var $_objects = array();

	/**
	 * Description for 'debug'
	 * 
	 * @var integer
	 */
	var $debug = 0;

	/**
	 * Short description for 'setObject'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown &$object Parameter description (if any) ...
	 * @return     void
	 */
	function setObject($name, &$object)
	{
		$this->_objects[$name] =& $object;
	}

	/**
	 * Short description for 'getObject'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function &getObject($name)
	{
		return $this->_objects[$name];
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	function display()
	{
		$xhub = &Hubzero_Factory::getHub();

		if ( !isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off' )
		{
			$xhub->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			die('insecure connection and redirection failed');
		}

		$plugins = JPluginHelper::getPlugin('xauthentication');

		$realms = array();

		foreach ($plugins as $plugin)
		{
			$params = new JParameter($plugin->params);

			$realm = $params->get('domain');

			if (empty($realm))
				$realm = $plugin->name;

			if (!in_array($realm, $realms))
				$realms[$plugin->name] = $realm;
		}

		if (count($realms) == 0)
			return JError::raiseError( '500', 'xHUB Configuration Error: No XAuthentication Plugins Enabled.');

		$hubShortName = $xhub->getCfg('hubShortName');

		$return = base64_decode( JRequest::getVar('return', '', 'method', 'base64') );

		if(empty($return))
			$return = JRequest::getVar( 'REQUEST_URI', null, 'server' );

		if ($return == '/login')
			$return = '/';

		if (count($realms) == 1)
		{
			$realmName = current($realms);
			$realm = key($realms);
			include $xhub->getComponentViewFilename('com_hub', 'login');
		}
		else
			include $xhub->getComponentViewFilename('com_hub', 'realm');
	}
}
