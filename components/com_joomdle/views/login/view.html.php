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
class JoomdleViewLogin extends JView {
	function display($tpl = null) {
	global $mainframe;

	$params = &JComponentHelper::getParams( 'com_joomdle' );
	$this->assignRef('params',              $params);
	$moodle_url = $params->get( 'MOODLE_URL' );

	$login_data =  JRequest::getVar( 'data' );

	if (!$login_data)
	{
		echo "Login error";
		exit ();
	}

	$data = base64_decode ($login_data);

	$fields = explode (':', $data);

	$credentials['username'] = $fields[0];
	$credentials['password'] = $fields[1];

	$options = array ('skip_joomdlehooks' => '1');

	$mainframe->login($credentials, $options);

	$mainframe->redirect( $moodle_url );

    }
}
?>
