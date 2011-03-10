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

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class JoomdleViewCheck extends JView {
    function display($tpl = null) {

	$params = &JComponentHelper::getParams( 'com_joomdle' );
        if ($params->get( 'MOODLE_URL' ) != "")
        {
                $this->system_info = JoomdleHelperContent::check_joomdle_system ();
                parent::display($tpl);
        }
        else echo "Joomdle is not configured yet. Please fill Moodle URL setting in Configuration";

    }
}
?>
