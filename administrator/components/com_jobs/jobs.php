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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$option = 'com_jobs';

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

jimport('joomla.application.component.helper');
ximport('Hubzero_User');
ximport('Hubzero_Bank');

// Include scripts
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'admin.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'application.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'category.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'employer.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'job.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'prefs.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'resume.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'seeker.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'shortlist.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'stats.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'type.php');

include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'jobs.php');
include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'html.php');

$controllerName = JRequest::getCmd('controller', 'jobs');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'jobs';
}

JSubMenuHelper::addEntry(
	JText::_('Jobs'),
	'index.php?option=com_jobs&controller=jobs',
	$controllerName == 'jobs'
);
JSubMenuHelper::addEntry(
	JText::_('Categories'),
	'index.php?option=com_jobs&controller=categories',
	$controllerName == 'categories'
);
JSubMenuHelper::addEntry(
	JText::_('Types'),
	'index.php?option=com_jobs&controller=types',
	$controllerName == 'types'
);

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'JobsController' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

