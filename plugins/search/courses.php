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


$mainframe->registerEvent( 'onSearch', 'plgSearchCourses' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchCoursesAreas' );

JPlugin::loadLanguage( 'plg_search_courses' );

/**
 * @return array An array of search areas
 */
function &plgSearchCoursesAreas()
{
	static $areas = array(
		'courses' => 'Courses'
	);
	return $areas;
}

/**
* Joomdle Courses Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
*/
function plgSearchCourses( $text, $phrase='', $ordering='', $areas=null )
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();

	if (is_array( $areas )) {
		if (!array_intersect( $areas, array_keys( plgSearchCoursesAreas() ) )) {
			return array();
		}
	}

	// load plugin params info
 	$plugin =& JPluginHelper::getPlugin('search', 'courses');
 	$pluginParams = new JParameter( $plugin->params );

	$params = &JComponentHelper::getParams( 'com_joomdle' );
	$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';


	$limit = $pluginParams->def( 'search_limit', 50 );

	$text = trim( $text );
	if ($text == '') {
		return array();
	}

	$section = JText::_( 'Courses' );

	$rows = JoomdleHelperContent::call_method ("search_courses", $text,$phrase,$ordering, $limit);


	$i = 0;
	if (!is_array ($rows))
		return array();

	$rows_result = array ();
	foreach($rows as $key => $row) {
		$cat_slug = $row['cat_id']."-".$row['cat_name'];
		$course_slug = $row['remoteid']."-".$row['fullname'];
		$rows_result[$i]->href = 'index.php?option=com_joomdle&view=detail&cat_id='.$cat_slug.'&course_id='.$course_slug;
		$rows_result[$i]->title= $row['fullname'];
		$rows_result[$i]->section = JText::_('Courses').' / '.$row['cat_name'];
		$rows_result[$i]->created = "";
		$rows_result[$i]->browsernav = '2'; // 1 = new window, 2 = same window
		$rows_result[$i]->text = $row['summary'];
		$i++;
	}

	return $rows_result;
}
