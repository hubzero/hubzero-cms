<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: view.html.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewWrapper extends JView {
	function display($tpl = null) {
	global $mainframe;

	$params = &JComponentHelper::getParams( 'com_joomdle' );
	$this->assignRef('params',              $params);
	$this->wrapper->load = '';

	$mtype = $params->get( 'moodle_page_type' );
	if (!$mtype)
		$mtype =  JRequest::getVar( 'moodle_page_type' );
	$id = $params->get( 'course_id' );
	if (!$id)
		$id =  JRequest::getVar( 'id' );
	$day =  JRequest::getVar( 'day' );
	$mon =  JRequest::getVar( 'mon' );
	$year =  JRequest::getVar( 'year' );

	switch ($mtype)
	{
		case "course" :
			$path = '/course/view.php?id=';
			$this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
			break;
		case "news" :
			$path = '/mod/forum/discuss.php?d=';
			$this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
			break;
		case "event" :
		//	$path = "/calendar/view.php?view=day&course=$id&cal_d=$day&cal_m=$mon&cal_y=$year";
			$path = "/calendar/view.php?view=day&cal_d=$day&cal_m=$mon&cal_y=$year";
			$this->wrapper->url = $params->get( 'MOODLE_URL' ).$path;
			break;
		case "user" :
			$path = '/user/view.php?id=';
			$this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
			break;
		default:
			$path = '';
			$this->wrapper->url = $params->get( 'MOODLE_URL' ).$path;
	}
        parent::display($tpl);
    }
}
?>
