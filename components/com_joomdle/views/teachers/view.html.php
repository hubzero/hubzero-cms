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
class JoomdleViewTeachers extends JView {
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
	$this->teachers = JoomdleHelperContent::getCourseTeachers($id);

	/* pathway */
	$cat_slug = $this->course_info['cat_id'].":".$this->course_info['cat_name'];
	$course_slug = $this->course_info['remoteid'].":".$this->course_info['fullname'];

	if(is_object($menu) && $menu->query['view'] != 'teachers') {
                        $pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
                        $pathway->addItem($this->course_info['fullname'], 'index.php?view=detail&cat_id='.$cat_slug.'&course_id='.$course_slug);
                        $pathway->addItem(JText::_('CJ COURSE TEACHERS'), '');
                }

        parent::display($tpl);
    }
}
?>
