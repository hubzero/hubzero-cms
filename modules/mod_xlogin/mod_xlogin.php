<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

//----------------------------------------------------------

ximport('misc_func');

class modXLogin 
{
	var $_objects = array();
	var $debug = 0;

	//-----------

	function setObject($name, &$object)
	{
		$this->_objects[$name] =& $object;
	}

	//-----------

	function &getObject($name)
	{
		return $this->_objects[$name];
	}

	function display()
	{
		$xhub = &XFactory::getHub();

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

$modxlogin = new modXLogin();
$modxlogin->setObject('params', $params);
require( JModuleHelper::getLayoutPath('mod_xlogin') );
?>
