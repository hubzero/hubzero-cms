<?php //#!/usr/bin/php ?>

<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

error_reporting(E_ALL);
@ini_set('display_errors','1');

//path of this file
$thispath = dirname(__FILE__);

//get parts of path
$parts = explode('/', $thispath);

//build base path
//$basepath = '/'.$parts[1].'/'.$parts[2].'/'.$parts[3].'/'.$parts[4];
$basepath = '/'.$parts[1].'/'.$parts[2];

//echo $basepath;

//set flag that this is a parent file
define( "_JEXEC", 1 );

//define base path
define('JPATH_BASE', $basepath );

//define directory seperator
define( 'DS', DIRECTORY_SEPARATOR );

//include joomla framework
require_once ( JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE.DS.'includes'.DS.'framework.php' );

//set a time start variable
$time_start = microtime(true);

echo "right before afterload \n";

function apache_note()
{
	
}

//
JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

//CREATE THE APPLICATION
$mainframe =& JFactory::getApplication('site');

echo "right before initialise \n";

//INITIALISE THE APPLICATION
$mainframe->initialise();

//import the system plugins
JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');

if (0) {
	//ROUTE THE APPLICATION
	$mainframe->route();

	// authorization
	$Itemid = JRequest::getInt( 'Itemid');
	$mainframe->authorize($Itemid);

	// trigger the onAfterRoute events
	JDEBUG ? $_PROFILER->mark('afterRoute') : null;
	$mainframe->triggerEvent('onAfterRoute');

	//DISPATCH THE APPLICATION
	$option = JRequest::getCmd('option');
	$mainframe->dispatch($option);

	// trigger the onAfterDispatch events
	JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
	$mainframe->triggerEvent('onAfterDispatch');

	//RENDER  THE APPLICATION
	$mainframe->render();

	// trigger the onAfterRender events
	JDEBUG ? $_PROFILER->mark('afterRender') : null;
	$mainframe->triggerEvent('onAfterRender');
} // ( 0 )

//ximport('Hubzero_User_Profile');
//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_userpoints'.DS.'controller.php' );
for($i=0;$i<20;$i++) {
	echo "_______________________________________________\n";
}
?>
