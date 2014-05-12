<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
 
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

//add ACL
if(version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL('com_feedaggregator', 'manage', 'users', 'super administrator');
	$jacl->addACL('com_feedaggregator', 'manage', 'users', 'administrator');
	$jacl->addACL('com_feedaggregator', 'manage', 'users', 'manager');
	
	print "Version 1.6";
}

$controllerName = JRequest::getCmd('controller', 'posts');
$taskName = JRequest::getCmd('task', '');

//import the component view?

/*JSubMenuHelper::addEntry(
		JText::_('New Posts'),
		'index.php?option=com_feedaggregator&controller=posts&task=RetrieveNewPosts',
		($controllerName == 'posts' && $taskName == 'RetrieveNewPosts')
); 
JSubMenuHelper::addEntry(
		JText::_('Stored Posts'),
		'index.php?option=com_feedaggregator&controller=posts',
		($controllerName == 'posts')
);
JSubMenuHelper::addEntry(
		JText::_('Mangage Feeds'),
		'index.php?option=com_feedaggregator&controller=feeds',
		($controllerName == 'feeds')
);
*/



if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'feeds';
}
// this adds the controller to the component's path

require_once(JPATH_COMPONENT. DS . 'controllers' . DS . $controllerName . '.php');

$controllerName = 'FeedaggregatorController' . ucfirst(strtolower($controllerName));

//$controllerName = 'FeedaggregatorControllerFeeds';

// Instantiate controller
$controller = new $controllerName();

$controller->execute();
$controller->redirect();
