<?php
/**
 * @package  hubzero-cms
 * @copyright   Copyright 2005-2012 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @author	    Brandon Beatty
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
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
 */

namespace Components\Geosearch\Site;


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$componentBase = dirname(__DIR__) . DS;

// require controllers
require_once($componentBase . 'site' . DS . 'controllers' . DS .  'map.php' );

// require models
require_once($componentBase . 'tables' . DS . 'markers.php');
require_once($componentBase . 'tables' . DS . 'tags.php');

$controllerName = \Request::getCmd('controller', 'map');
if (!file_exists($componentBase . DS . 'site' . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'map';
}
require_once($componentBase . DS . 'site' . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'GeosearchController' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
