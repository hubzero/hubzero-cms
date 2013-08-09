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

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');
	
	// Authorization check
	$user = JFactory::getUser();
	if (!$user->authorize($option, 'manage'))
	{
		$app = JFactory::getApplication();
		$app->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
	}
}
else 
{
	$option = JRequest::getCmd('option');

	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}
// Include scripts
//require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'models'.DS.'mw.class.php' );
require_once(JPATH_ROOT . DS . 'components' . DS . $option . DS . 'models' . DS . 'mw.utils.php');
require_once(JPATH_ROOT . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'helper.php');

$controllerName = JRequest::getCmd('controller', 'pipeline');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'pipeline';
}

JSubMenuHelper::addEntry(
	JText::_('Pipeline'),
	'index.php?option=com_tools&controller=pipeline',
	$controllerName == 'pipeline'
);
JSubMenuHelper::addEntry(
	JText::_('Hosts'),
	'index.php?option=com_tools&controller=hosts',
	$controllerName == 'hosts'
);
JSubMenuHelper::addEntry(
	JText::_('Host Types'),
	'index.php?option=com_tools&controller=hosttypes',
	$controllerName == 'hosttypes'
);
JSubMenuHelper::addEntry(
	JText::_('Venues'),
	'index.php?option=com_tools&controller=venues',
	$controllerName == 'venues'
);
JSubMenuHelper::addEntry(
	JText::_('Sessions'),
	'index.php?option=com_tools&controller=sessions',
	$controllerName == 'sessions'
);

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'ToolsController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
