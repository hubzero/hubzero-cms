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
defined('_JEXEC') or die('Restricted access');

$option = 'com_citations';

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
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'citation.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'association.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'author.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'secondary.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'tags.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'type.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'sponsor.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'format.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'citations.php');

$controllerName = JRequest::getCmd('controller', 'citations');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'citations';
}

JSubMenuHelper::addEntry(
	JText::_('Citations'),
	'index.php?option=com_citations&controller=citations',
	($controllerName == 'citations' && JRequest::getVar('task', '') != 'stats')
);
JSubMenuHelper::addEntry(
	JText::_('Stats'),
	'index.php?option=com_citations&controller=citations&task=stats',
	($controllerName == 'citations' && JRequest::getVar('task', '') == 'stats')
);
JSubMenuHelper::addEntry(
	JText::_('Types'),
	'index.php?option=com_citations&controller=types',
	$controllerName == 'types'
);
JSubMenuHelper::addEntry(
	JText::_('Sponsors'),
	'index.php?option=com_citations&controller=sponsors',
	$controllerName == 'sponsors'
);
JSubMenuHelper::addEntry(
	JText::_('Format'),
	'index.php?option=com_citations&controller=format',
	$controllerName == 'format'
);

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'CitationsController' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

