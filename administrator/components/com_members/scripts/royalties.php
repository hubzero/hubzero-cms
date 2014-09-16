#!/usr/bin/php

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

$thispath = dirname(__FILE__);
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

require_once (JPATH_BASE  . DS . 'includes' . DS . 'defines.php');
require_once (JPATH_BASE  . DS . 'includes' . DS . 'framework.php');

$time_start = microtime(true);

JPROFILE ? $_PROFILER->mark('afterLoad') : null;

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

JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JPROFILE ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');

if (0) {
/**
 * ROUTE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->route();

// authorization
$Itemid = JRequest::getInt('Itemid');
$mainframe->authorize($Itemid);

// trigger the onAfterRoute events
JPROFILE ? $_PROFILER->mark('afterRoute') : null;
$mainframe->triggerEvent('onAfterRoute');

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$option = JRequest::getCmd('option');
$mainframe->dispatch($option);

// trigger the onAfterDispatch events
JPROFILE ? $_PROFILER->mark('afterDispatch') : null;
$mainframe->triggerEvent('onAfterDispatch');

/**
 * RENDER  THE APPLICATION
 *
 * NOTE :
 */
$mainframe->render();

// trigger the onAfterRender events
JPROFILE ? $_PROFILER->mark('afterRender') : null;
$mainframe->triggerEvent('onAfterRender');
} // (0)
// Begin Joomla 1.5 Core Compatibility Support *njk*
/*
define('JPATH_BASE', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
$parts = explode(DS, JPATH_BASE);
array_pop($parts);
array_pop($parts);
array_pop($parts);
define('JPATH_ROOT', implode(DS, $parts));
define('JPATH_SITE', JPATH_ROOT);
define('JPATH_LIBRARIES', JPATH_ROOT . DS . 'libraries');
define('JPATH_CONFIGURATION',     JPATH_ROOT);
require_once(JPATH_LIBRARIES . DS . 'loader.php');
jimport('joomla.common.abstract.object');
jimport('joomla.factory');
*/
// End Joomla 1.5 Core Compatibility Support *njk*

/*
if (!file_exists("../../../configuration.php")) {
	header("Location: ../../../installation/index.php");
	exit();
}

require_once(JPATH_ROOT . DS . "globals.php");
require_once(JPATH_ROOT . DS . "configuration.php");
include_once($mosConfig_absolute_path . "/language/".$mosConfig_lang.".php");

$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
$database->debug($mosConfig_debug);
*/

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'economy.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'economy.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'helpers' . DS . 'economy.php');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'controllers' . DS . 'points.php');

echo "_______________________________________________\n";
echo date(DATE_RFC822)."\n";
echo "\"Royalty Payments\"\n\n";

JRequest::setVar('option', 'com_members');
JRequest::setVar('controller', 'points');
JRequest::setVar('task', 'royalty');

$controller = new MembersControllerPoints();
$controller->execute();

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "_______________________________________________\n";
echo "Computed in $time seconds\n";
