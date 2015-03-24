<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Config;

// Access checks are done internally because of different requirements for the two controllers.

// Tell the browser not to cache this page.
\JResponse::setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);

if (strstr(\JRequest::getCmd('task'), '.'))
{
	@list($ctrl, $task) = explode('.', \JRequest::getCmd('task'));
	\JRequest::setVar('controller', $ctrl);
	\JRequest::setVar('task', $task);
}

$controllerName = \JRequest::getCmd('controller', \JRequest::getCmd('view', 'application'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	\JError::raiseError(404, \JText::_('JERROR_ALERTNOAUTHOR'));
}
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Execute the controller.
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
