#!/usr/bin/php

<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

//error_reporting(E_ALL);
//@ini_set('display_errors','1');

$thispath = dirname(__FILE__);
$bits = explode('/', $thispath);
$basepath = '/'.$bits[1].'/'.$bits[2];

/** Set flag that this is a parent file */
define( "_JEXEC", 1 );

define('JPATH_BASE', $basepath );

define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$time_start = microtime(true);

JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe =& JFactory::getApplication('site');

/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
// set the language
$mainframe->initialise();

JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');

if (0) {
/**
 * ROUTE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->route();

// authorization
$Itemid = JRequest::getInt( 'Itemid');
$mainframe->authorize($Itemid);

// trigger the onAfterRoute events
JDEBUG ? $_PROFILER->mark('afterRoute') : null;
$mainframe->triggerEvent('onAfterRoute');

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$option = JRequest::getCmd('option');
$mainframe->dispatch($option);

// trigger the onAfterDispatch events
JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
$mainframe->triggerEvent('onAfterDispatch');

/**
 * RENDER  THE APPLICATION
 *
 * NOTE :
 */
$mainframe->render();

// trigger the onAfterRender events
JDEBUG ? $_PROFILER->mark('afterRender') : null;
$mainframe->triggerEvent('onAfterRender');
} // ( 0 )
// Begin Joomla 1.5 Core Compatibility Support *njk*
/*
define('JPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );
$parts = explode( DS, JPATH_BASE );
array_pop( $parts );
array_pop( $parts );
array_pop( $parts );
define( 'JPATH_ROOT', implode( DS, $parts ) );
define( 'JPATH_SITE', JPATH_ROOT );
define( 'JPATH_LIBRARIES', JPATH_ROOT.DS.'libraries' );
define( 'JPATH_CONFIGURATION',     JPATH_ROOT );
require_once( JPATH_LIBRARIES . DS . 'loader.php' );
jimport( 'joomla.common.abstract.object' );
jimport( 'joomla.factory' );
ximport( 'xfactory' );
*/
// End Joomla 1.5 Core Compatibility Support *njk*

/*
if (!file_exists( "../../../configuration.php" )) {
	header( "Location: ../../../installation/index.php" );
	exit();
}

require_once( JPATH_ROOT.DS."globals.php" );
require_once( JPATH_ROOT.DS."configuration.php" );
include_once( $mosConfig_absolute_path . "/language/".$mosConfig_lang.".php" );

$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
$database->debug( $mosConfig_debug );
*/

ximport('xuser');
ximport('textfilter');
ximport('bankaccount');

include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_userpoints'.DS.'admin.controller.php' );

echo "_______________________________________________\n";
echo date(DATE_RFC822)."\n";
echo "\"Royalty Payments\"\n\n";

$controller = new UserpointsController();
$controller->_option = 'com_userpoints';
//$controller->setObject('database', $database);
$controller->royalty();

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "_______________________________________________\n";
echo "Computed in $time seconds\n";
?>
