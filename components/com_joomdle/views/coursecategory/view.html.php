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
class JoomdleViewCoursecategory extends JView {
	function display($tpl = null) {
	global $mainframe;

	$app                = JFactory::getApplication();
	$pathway = &$app->getPathWay();
	$menus = &JSite::getMenu();
	$menu  = $menus->getActive();

	$params = &JComponentHelper::getParams( 'com_joomdle' );
	$this->assignRef('params',              $params);

	$id =  JRequest::getVar( 'cat_id' );
	if (!$id)
		$id = $params->get( 'cat_id' );
	
	$id = (int) $id;

	$this->cat_id = $id;

	$this->cat_name = JoomdleHelperContent::call_method ('get_cat_name', $id);

	$this->cursos = JoomdleHelperContent::getCourseCategory ($id);
	$this->categories = JoomdleHelperContent::getCourseCategories ($id);


	if(is_object($menu) && $menu->query['view'] != 'coursecategory') {
                        $pathway->addItem($this->cat_name, '');
                }


        parent::display($tpl);
    }
}
?>
