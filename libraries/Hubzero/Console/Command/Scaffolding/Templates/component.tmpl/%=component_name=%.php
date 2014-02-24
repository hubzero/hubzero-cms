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
 * @author    %=author_name=% <%=author_email=%>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$option = JRequest::getCmd('option', 'com_%=component_name=%');

// Include base model
require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . '%=component_name=%.php');

// Get controller name
$controllerName = JRequest::getCmd('controller', JRequest::getCmd('view', '%=component_name+p=%'));
require_once(JPATH_COMPONENT_SITE . DS . 'controllers' . DS . $controllerName . '.php');
$className = '%=component_name+ucf=%Controller' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $className();
$controller->execute();
$controller->redirect();