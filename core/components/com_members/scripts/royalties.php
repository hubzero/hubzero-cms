#!/usr/bin/php

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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$thispath = __DIR__;
$bits = explode('/', $thispath);
$basepath = '/'.$bits[1].'/'.$bits[2];

/** Set flag that this is a parent file */
define("_JEXEC", 1);

/**
 * Description for ''JPATH_BASE''
 */
define('JPATH_BASE', $basepath);

/**
 * Description for ''DS''
 */
define('DS', DIRECTORY_SEPARATOR);

require_once (PATH_CORE  . DS . 'bootstrap' . DS . 'site' . DS . 'defines.php');
require_once (PATH_CORE  . DS . 'bootstrap' . DS . 'site' . DS . 'framework.php');

$time_start = microtime(true);

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe = JFactory::getApplication('site');

/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
// set the language
$mainframe->initialise();

Plugin::import('system');

// trigger the onAfterInitialise events
Event::trigger('onAfterInitialise');

if (0)
{
/**
 * ROUTE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->route();

// authorization
$Itemid = Request::getInt('Itemid');
$mainframe->authorize($Itemid);

// trigger the onAfterRoute events
Event::trigger('onAfterRoute');

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$option = Request::getCmd('option');
$mainframe->dispatch($option);

// trigger the onAfterDispatch events
Event::trigger('onAfterDispatch');

/**
 * RENDER  THE APPLICATION
 *
 * NOTE :
 */
$mainframe->render();

// trigger the onAfterRender events
Event::trigger('onAfterRender');
} // (0)

include_once(PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'economy.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'economy.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'helpers' . DS . 'economy.php');

include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'controllers' . DS . 'points.php');

echo "_______________________________________________\n";
echo date(DATE_RFC822)."\n";
echo "\"Royalty Payments\"\n\n";

Request::setVar('option', 'com_members');
Request::setVar('controller', 'points');
Request::setVar('task', 'royalty');

$controller = new MembersControllerPoints();
$controller->execute();

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "_______________________________________________\n";
echo "Computed in $time seconds\n";
