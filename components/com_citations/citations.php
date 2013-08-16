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

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');

	//add acl for importing citations - for admins and super admins
	$jacl->addACL($option, 'import', 'users', 'super administrator');
	$jacl->addACL($option, 'import', 'users', 'administrator');
}

jimport('joomla.application.component.helper');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'citation.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'association.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'author.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'secondary.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'tags.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'type.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'sponsor.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'format.php');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'format.php');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'download.php');

$controllerName = JRequest::getCmd('controller', JRequest::getCmd('view', 'citations'));
if (!file_exists(JPATH_COMPONENT_SITE . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'citations';
}
require_once(JPATH_COMPONENT_SITE . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'CitationsController' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

