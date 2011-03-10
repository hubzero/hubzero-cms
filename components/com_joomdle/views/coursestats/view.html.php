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
class JoomdleViewCoursestats extends JView {
	function display($tpl = null) {
	global $mainframe;

	$app                = JFactory::getApplication();
	$pathway = &$app->getPathWay();
	$menus = &JSite::getMenu();
	$menu  = $menus->getActive();

	$params = &JComponentHelper::getParams( 'com_joomdle' );
	$this->assignRef('params',              $params);

	$id = $params->get( 'course_id' );
        if (!$id)
                $id =  JRequest::getVar( 'course_id' );

	$id = (int) $id;


	$this->course_info = JoomdleHelperContent::getCourseInfo($id);
	$this->student_no = JoomdleHelperContent::getCourseStudentsNo($id);
	$this->assignments = JoomdleHelperContent::getAssignmentSubmissions($id);
    $this->grades = JoomdleHelperContent::getAssignmentGrades($id);
    $this->daily_stats = JoomdleHelperContent::getCourseDailyStats($id);

	if(is_object($menu) && $menu->query['view'] != 'coursestats') {
                        $pathway->addItem($this->course_info['fullname'], '');
                }

        parent::display($tpl);
    }
}
?>
