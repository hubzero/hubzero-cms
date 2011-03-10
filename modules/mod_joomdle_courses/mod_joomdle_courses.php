<?php
/**
* @package		Joomdle
* @copyright	Copyright (C) 2009 - 2010 Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');


$comp_params = &JComponentHelper::getParams( 'com_joomdle' );

$moodle_xmlrpc_server_url = $comp_params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';
$moodle_auth_land_url = $comp_params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';
$moodle_url = $comp_params->get( 'MOODLE_URL' );
$linkstarget = $comp_params->get( 'linkstarget' );
$default_itemid = $comp_params->get( 'default_itemid' );

$linkto = $params->get( 'linkto' );

$user = & JFactory::getUser();
$username = $user->get('username');

$session                =& JFactory::getSession();
$token = md5 ($session->getId());

$guest_courses_only = $params->get( 'guest courses only', 0 );

if  ( $params->get( 'latest courses only' ))
{
	$cursos = JoomdleHelperContent::getCourseList ( 0, 'created DESC', $guest_courses_only);
	$limit = $params->get( 'latest courses only' );
}
else
{
	$cursos = JoomdleHelperContent::getCourseList ( 0, 'fullname ASC', $guest_courses_only);
	$limit = PHP_INT_MAX; //no limit
}

//print_r ($cursos);

if  ( $params->get( 'courses_shown' ))
{
	if (is_array($params->get( 'courses_shown' )))
		$courses_shown = $params->get( 'courses_shown' );
	else
		$courses_shown = array ( $params->get( 'courses_shown' ));

	$cursos = modJoomdleCoursesHelper::filter_by_value ($cursos, 'remoteid', $courses_shown );
}
if  ( $params->get( 'categories_shown' ))
{
	if (is_array($params->get( 'categories_shown' )))
		$cats_shown = $params->get( 'categories_shown' );
	else
		$cats_shown = array ( $params->get( 'categories_shown' ));

	$cursos = modJoomdleCoursesHelper::filter_by_value ($cursos, 'cat_id', $cats_shown );
}
if  ( $params->get( 'free courses only' ))
{
	$cursos = modJoomdleCoursesHelper::filter_by_value ($cursos, 'cost', array (0) );
}

require(JModuleHelper::getLayoutPath('mod_joomdle_courses'));
