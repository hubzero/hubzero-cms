<?php
/**
 * @version		
 * @package		Joomdle
 * @copyright	Copyright (C)  2008 - 2010 Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');

$mainframe->registerEvent( 'onSearch', 'plgSearchCoursetopics' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchCoursetopicsAreas' );

JPlugin::loadLanguage( 'plg_search_coursetopics' );

/**
 * @return array An array of search areas
 */
function &plgSearchCoursetopicsAreas()
{
	static $areas = array(
		'coursetopics' => 'Course Topics'
	);
	return $areas;
}

/**
* Joomdle Course Topics Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
*/
function plgSearchCoursetopics( $text, $phrase='', $ordering='', $areas=null )
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();
	$username = $user->get('username');

	$session                =& JFactory::getSession();
	$token = md5 ($session->getId());

	if (is_array( $areas )) {
		if (!array_intersect( $areas, array_keys( plgSearchCoursetopicsAreas() ) )) {
			return array();
		}
	}

	// load plugin params info
 	$plugin =& JPluginHelper::getPlugin('search', 'coursetopics');
 	$pluginParams = new JParameter( $plugin->params );

	$params = &JComponentHelper::getParams( 'com_joomdle' );

	$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';
	//$moodle_url = $params->get( 'MOODLE_URL' );
	$moodle_auth_land_url = $params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

	if ($params->get( 'linkstarget' ) == 'wrapper')
		$open_in_wrapper = 1;
	else
		$open_in_wrapper = 0;

	$limit = $pluginParams->def( 'search_limit', 50 );

	$text = trim( $text );
	if ($text == '') {
		return array();
	}

	$section = JText::_( 'Course Topics' );

	$rows = JoomdleHelperContent::call_method ("search_topics", $text,$phrase,$ordering, $limit);

	$i = 0;
	if (!is_array ($rows))
		return array();

	$rows_result = array ();
	foreach($rows as $key => $row) {
		$cat_slug = $row['cat_id']."-".$row['cat_name']; 
		$course_slug = $row['remoteid']."-".$row['fullname'];
		$rows_result[$i]->href = 'index.php?option=com_joomdle&view=topics&cat_id='.$cat_slug.'&course_id='.$course_slug;
	//	$rows_result[$i]->href = $moodle_url.'/course/view.php?id='.$row['remoteid'];
		$id = $row['remoteid'];
		/// XXX esto es para apuntar directo al curso en moodle
	//	$rows_result[$i]->href = $moodle_auth_land_url."?username=$username&token=$token&mtype=course&id=$id&use_wrapper=$open_in_wrapper"; // $moodle_url.'/course/view.php?id='.$row['remoteid'];
		$rows_result[$i]->title= substr (strip_tags ($row['summary']), 0, 50);
		$rows_result[$i]->section = JText::_('Courses').' / '.$row['fullname'];
		$rows_result[$i]->created = "";
		$rows_result[$i]->browsernav = '2'; // 1 = new window, 2 = same window
		$rows_result[$i]->text = $row['summary'];
		$i++;
	}

	return $rows_result;
}
