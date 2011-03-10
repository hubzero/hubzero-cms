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
class JoomdleViewConfig extends JView {
    function display($tpl = null) {


	    $params = &JComponentHelper::getParams( 'com_joomdle' );
	    $this->comp_params = $params;

	$this->lists['auto_create_users'] = JHTML::_('select.booleanlist',   'auto_create_users', 'class="inputbox"', $params->get ('auto_create_users'));
	$this->lists['auto_delete_users'] = JHTML::_('select.booleanlist',   'auto_delete_users', 'class="inputbox"', $params->get ('auto_delete_users'));
	$this->lists['auto_login_users'] = JHTML::_('select.booleanlist',   'auto_login_users', 'class="inputbox"', $params->get ('auto_login_users'));

	$connection_method = array (
		JHTML::_('select.option', 'fgc', JText::_('file_get_contents')),
		JHTML::_('select.option', 'curl', JText::_('cURL')));
	$this->lists['connection_method'] = JHTML::_('select.genericlist',  $connection_method, 'connection_method', 'class="inputbox" size="1"', 'value', 'text', $params->get ('connection_method'));

	$moodle_version = array (
		JHTML::_('select.option', '19', JText::_('Moodle 1.9.x')),
		JHTML::_('select.option', '20', JText::_('Moodle 2.0.x')));
	$this->lists['moodle_version'] = JHTML::_('select.genericlist',  $moodle_version, 'moodle_version', 'class="inputbox" size="1"', 'value', 'text', $params->get ('moodle_version'));

	$linkstarget = array (
		JHTML::_('select.option', 'self', JText::_('SAME WINDOW')),
		JHTML::_('select.option', 'new', JText::_('NEW WINDOW')),
		JHTML::_('select.option', 'wrapper', JText::_('WRAPPER')));
	$this->lists['linkstarget'] = JHTML::_('select.genericlist',  $linkstarget, 'linkstarget', 'class="inputbox" size="1"', 'value', 'text', $params->get ('linkstarget'));

	$scrolling = array (
		JHTML::_('select.option', 'auto', JText::_('Auto')),
		JHTML::_('select.option', 'yes', JText::_('Yes')),
		JHTML::_('select.option', 'no', JText::_('No')));
	$this->lists['scrolling'] = JHTML::_('select.genericlist',  $scrolling, 'scrolling', 'class="inputbox" size="1"', 'value', 'text', $params->get ('scrolling'));
	$this->lists['autoheight'] = JHTML::_('select.booleanlist',   'autoheight', 'class="inputbox"', $params->get ('autoheight'));
	$this->lists['transparency'] = JHTML::_('select.booleanlist',   'transparency', 'class="inputbox"', $params->get ('transparency'));


	$this->lists['show_topìcs_link'] = JHTML::_('select.booleanlist',   'show_topìcs_link', 'class="inputbox"', $params->get ('show_topìcs_link'));
	$this->lists['show_grading_system_link'] = JHTML::_('select.booleanlist',   'show_grading_system_link', 'class="inputbox"', $params->get ('show_grading_system_link'));
	$this->lists['show_teachers_link'] = JHTML::_('select.booleanlist',   'show_teachers_link', 'class="inputbox"', $params->get ('show_teachers_link'));
	$this->lists['show_enrol_link'] = JHTML::_('select.booleanlist',   'show_enrol_link', 'class="inputbox"', $params->get ('show_enrol_link'));
	$this->lists['show_paypal_button'] = JHTML::_('select.booleanlist',   'show_paypal_button', 'class="inputbox"', $params->get ('show_paypal_button'));

	//$this->lists['shop_integration'] = JHTML::_('select.booleanlist',   'shop_integration', 'class="inputbox"', $params->get ('shop_integration'));
	$shop_integration = array (
		JHTML::_('select.option', '0', JText::_('None')),
		JHTML::_('select.option', 'tienda', JText::_('Tienda')),
		JHTML::_('select.option', 'virtuemart', JText::_('Virtuemart')));
	$this->lists['shop_integration'] = JHTML::_('select.genericlist',  $shop_integration, 'shop_integration', 'class="inputbox" size="1"', 'value', 'text', $params->get ('shop_integration'));
	$this->lists['buy_for_children'] = JHTML::_('select.booleanlist',   'buy_for_children', 'class="inputbox"', $params->get ('buy_for_children'));

	$additional_data_source = array (
		JHTML::_('select.option', 'none', JText::_('None')),
		JHTML::_('select.option', 'jomsocial', JText::_('Jomsocial')),
		JHTML::_('select.option', 'cb', JText::_('Community Builder')),
		JHTML::_('select.option', 'tienda', JText::_('Tienda')),
		JHTML::_('select.option', 'virtuemart', JText::_('Virtuemart')));
	$this->lists['additional_data_source'] = JHTML::_('select.genericlist',  $additional_data_source, 'additional_data_source', 'class="inputbox" size="1"', 'value',
			'text', $params->get ('additional_data_source'));

	$this->lists['use_xipt_integration'] = JHTML::_('select.booleanlist',   'use_xipt_integration', 'class="inputbox"', $params->get ('use_xipt_integration'));

        parent::display($tpl);
    }
}
?>
