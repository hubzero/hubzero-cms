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
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'shop.php' );


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewquizselector extends JView {
	function display($tpl = null) {
	global $mainframe;

	$app = JFactory::getApplication();
    $pathway = &$app->getPathWay();

	$menus = &JSite::getMenu();
	$menu  = $menus->getActive();

	//http://neesreu.org/moodle/mod/quiz/view.php?id=83
	$this->quiz_info = JoomdleHelperContent::getQuizList(0);
	$this->course_info = JoomdleHelperContent::getCourseList(0);
	/* pathway */
	//$cat_slug = $this->course_info['cat_id'].":".JFilterOutput::stringURLSafe($this->course_info['cat_name']);

	//if(is_object($menu) && $menu->query['view'] != 'detail') {
    //                    $pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
    //                    $pathway->addItem($this->course_info['fullname'], '');
    //            }

	$document = & JFactory::getDocument();
	$document->setTitle('quiz selector');

    parent::display($tpl);
    }
}
?>
