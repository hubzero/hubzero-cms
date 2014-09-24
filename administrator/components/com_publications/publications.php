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
defined('_JEXEC') or die( 'Restricted access' );

$option = JRequest::getCmd('option', 'com_publications');
$task = JRequest::getWord('task', '');

if (!JFactory::getUser()->authorise('core.manage', $option))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'publication.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'author.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'attachment.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'category.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'access.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'review.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'audience.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'audience.level.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'license.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'master.type.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'screenshot.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'version.php');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'utilities.php');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'tags.php');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'helper.php');
include_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'publication.php');
include_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'curation.php');

// Projects
require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_projects' . DS . 'tables' . DS . 'project.php');
require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_projects' . DS . 'tables' . DS . 'project.owner.php');
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'. DS
	. 'helpers' . DS . 'html.php');

$controllerName = JRequest::getCmd('controller', 'items');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

JSubMenuHelper::addEntry(
	JText::_('COM_PUBLICATIONS_PUBLICATIONS'),
	'index.php?option=' .  $option . '&controller=items',
	$controllerName == 'items'
);

JSubMenuHelper::addEntry(
	JText::_('COM_PUBLICATIONS_LICENSES'),
	'index.php?option=' .  $option . '&controller=licenses',
	$controllerName == 'licenses'
);
JSubMenuHelper::addEntry(
	JText::_('COM_PUBLICATIONS_CATEGORIES'),
	'index.php?option=' .  $option . '&controller=categories',
	$controllerName == 'categories'
);
JSubMenuHelper::addEntry(
	JText::_('COM_PUBLICATIONS_MASTER_TYPES'),
	'index.php?option=' .  $option . '&controller=types',
	$controllerName == 'types'
);
JSubMenuHelper::addEntry(
	JText::_('COM_PUBLICATIONS_BATCH_CREATE'),
	'index.php?option=' .  $option . '&controller=batchcreate',
	$controllerName == 'batchcreate'
);

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'PublicationsController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();