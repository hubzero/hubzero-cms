<?php

/*?>
#!/usr/bin/php

<?php*/
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
$basepath = '/' . $bits[1] . '/' . $bits[2];

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

require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');

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

if (!defined('JPATH_COMPONENT'))
{
	define('JPATH_COMPONENT', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_cron');
}

require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'job.php');

require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'cron.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'FieldInterface.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'AbstractField.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'DayOfMonthField.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'DayOfWeekField.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'FieldFactory.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'HoursField.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'MinutesField.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'MonthField.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'YearField.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'Cron' . DS . 'CronExpression.php');

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'scripts.php');

// initiate controller
$controller = new CronControllerScripts();
$controller->execute();
