<?php
/**
* @version		
* @package		Joomdle
* @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');

// Include the whosonline functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$comp_params = &JComponentHelper::getParams( 'com_joomdle' );

$moodle_xmlrpc_server_url = $comp_params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';
$moodle_auth_land_url = $comp_params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';
$linkstarget = $comp_params->get( 'linkstarget' );

$show_averages = $params->get( 'show_averages' );

$itemid = JoomdleHelperContent::getMenuItem();


$user = & JFactory::getUser();
$id = $user->get('id');
$username = $user->get('username');

$limit = $params->get('last_grade_limit', 10);

if ($username)
	$tareas = JoomdleHelperContent::call_method ("get_last_user_grades", $username, (int) $limit);
else $tareas = array ();


require(JModuleHelper::getLayoutPath('mod_joomdle_my_grades'));
