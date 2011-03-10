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
class JoomdleViewMycourses extends JView {
	function display($tpl = null) {
	global $mainframe;

	$params = &JComponentHelper::getParams( 'com_joomdle' );
	$this->assignRef('params',              $params);

	$this->my_courses = JoomdleHelperContent::getMyCourses();

	$this->jump_url =  JoomdleHelperContent::getJumpURL ();


        parent::display($tpl);
    }
}
?>
