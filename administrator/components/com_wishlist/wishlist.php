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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$option = JRequest::getCmd('option', 'com_wishlist');

// Authorization check
if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');

	$user = JFactory::getUser();
	if (!$user->authorize($option, 'manage'))
	{
		$app = JFactory::getApplication();
		$app->redirect('index.php', JText::_('ALERTNOTAUTH'));
	}
}
else 
{
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

// Include scripts
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'wishlist.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'wishlist.plan.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'wishlist.owner.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'wishlist.owner.group.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'wish.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'wish.rank.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'wish.attachment.php');
include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'wishlist.php');

$controllerName = JRequest::getCmd('controller', 'lists');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'lists';
}
switch ($controllerName)
{
	case 'wishes':
		JSubMenuHelper::addEntry(JText::_('Lists'), 'index.php?option=' .  $option . '&controller=lists');
		JSubMenuHelper::addEntry(JText::_('Wishes'), 'index.php?option=' .  $option . '&controller=wishes&wishlist=-1', true);
		JSubMenuHelper::addEntry(JText::_('Comments'), 'index.php?option=' .  $option . '&controller=comments&wish=-1');
	break;
	
	case 'comments':
		JSubMenuHelper::addEntry(JText::_('Lists'), 'index.php?option=' .  $option . '&controller=lists');
		JSubMenuHelper::addEntry(JText::_('Wishes'), 'index.php?option=' .  $option . '&controller=wishes&wishlist=-1');
		JSubMenuHelper::addEntry(JText::_('Comments'), 'index.php?option=' .  $option . '&controller=comments&wish=-1', true);
	break;

	default:
	case 'lists':
		JSubMenuHelper::addEntry(JText::_('Lists'), 'index.php?option=' .  $option . '&controller=lists', true);
		JSubMenuHelper::addEntry(JText::_('Wishes'), 'index.php?option=' .  $option . '&controller=wishes&wishlist=-1');
		JSubMenuHelper::addEntry(JText::_('Comments'), 'index.php?option=' .  $option . '&controller=comments&wish=-1');
	break;
}
require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'WishlistController' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

