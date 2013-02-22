<?php
/**
 * @package		HUBzero                                  CMS
 * @author		Christopher                               Smoak <csmoak@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
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
defined('_JEXEC') or die('Restricted access');

if (JFactory::getConfig()->getValue('config.debug')) 
{
	error_reporting(E_ALL);
	@ini_set('display_errors','1');
}

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL('com_forum', 'manage', 'users', 'super administrator');
	$jacl->addACL('com_forum', 'manage', 'users', 'administrator');
	$jacl->addACL('com_forum', 'manage', 'users', 'manager');
}

jimport('joomla.application.component.view');

require_once(JPATH_COMPONENT_SITE . DS . 'tables' . DS . 'attachment.php');
require_once(JPATH_COMPONENT_SITE . DS . 'tables' . DS . 'post.php');
require_once(JPATH_COMPONENT_SITE . DS . 'tables' . DS . 'category.php');
require_once(JPATH_COMPONENT_SITE . DS . 'tables' . DS . 'section.php');
require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'pagination.php');
require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'tags.php');

$controllerName = JRequest::getCmd('controller', JRequest::getCmd('view', 'sections'));
if (!file_exists(JPATH_COMPONENT_SITE . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'sections';
}
require_once(JPATH_COMPONENT_SITE . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'ForumController' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
