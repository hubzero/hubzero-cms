<?php //#!/usr/bin/php ?>

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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//path of this file
$thispath = dirname(__FILE__);

//get parts of path
$parts = explode('/', $thispath);

//build base path
//$basepath = '/'.$parts[1].'/'.$parts[2].'/'.$parts[3].'/'.$parts[4];
$basepath = '/'.$parts[1].'/'.$parts[2];

//echo $basepath;

//set flag that this is a parent file

/**
 * Description for '"_JEXEC"'
 */
define( "_JEXEC", 1 );

//define base path

/**
 * Description for ''JPATH_BASE''
 */
define('JPATH_BASE', $basepath );

//define directory seperator

/**
 * Description for ''DS''
 */
define( 'DS', DIRECTORY_SEPARATOR );

//include joomla framework
require_once ( JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE.DS.'includes'.DS.'framework.php' );

//set a time start variable
$time_start = microtime(true);

echo "right before afterload \n";

/**
 * Short description for 'apache_note'
 *
 * Long description (if any) ...
 *
 * @return void
 */
function apache_note()
{

}

//
JPROFILE ? $_PROFILER->mark( 'afterLoad' ) : null;

//CREATE THE APPLICATION
$mainframe = JFactory::getApplication('site');

echo "right before initialise \n";

//INITIALISE THE APPLICATION
$mainframe->initialise();

//import the system plugins
JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JPROFILE ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');

if (0) {
	//ROUTE THE APPLICATION
	$mainframe->route();

	// authorization
	$Itemid = JRequest::getInt( 'Itemid');
	$mainframe->authorize($Itemid);

	// trigger the onAfterRoute events
	JPROFILE ? $_PROFILER->mark('afterRoute') : null;
	$mainframe->triggerEvent('onAfterRoute');

	//DISPATCH THE APPLICATION
	$option = JRequest::getCmd('option');
	$mainframe->dispatch($option);

	// trigger the onAfterDispatch events
	JPROFILE ? $_PROFILER->mark('afterDispatch') : null;
	$mainframe->triggerEvent('onAfterDispatch');

	//RENDER  THE APPLICATION
	$mainframe->render();

	// trigger the onAfterRender events
	JPROFILE ? $_PROFILER->mark('afterRender') : null;
	$mainframe->triggerEvent('onAfterRender');
} // ( 0 )

for($i=0;$i<20;$i++) {
	echo "_______________________________________________\n";
}
?>
