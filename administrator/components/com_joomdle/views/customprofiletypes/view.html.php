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
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'content.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'profiletypes.php' );

class JoomdleViewCustomprofiletypes extends JView {
    function display($tpl = null) {
	    global $mainframe, $option;

	$id = JRequest::getVar( 'profiletype_id' );
	$task = JRequest::getVar( 'task' );
	$params = &JComponentHelper::getParams( 'com_joomdle' );

	if (!$params->get( 'use_xipt_integration' ))
	{
		echo JText::_('CJ XIPT INTEGRATION NOT ENABLED');
		return;
	}

	/* List of profiletypes */


	$filter_order           = $mainframe->getUserStateFromRequest( "$option.filter_order",          'filter_order',         'joomla_app',       'cmd' );
	$filter_order_Dir       = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",      'filter_order_Dir',     'asc',                     'word' );

	/* kludge while I learn how to do it FIXME */
	if (($filter_order != 'name') && ($filter_order != 'create_on_moodle'))
		$filter_order = 'name';
	/* kludge while I learn how to do it FIXME */

	$filter_type            = $mainframe->getUserStateFromRequest( "$option.filter_type",           'filter_type',          0,                      'string' );
	$search                         = $mainframe->getUserStateFromRequest( "$option.search",                        'search',                       '',                     'string' );
	$search                         = JString::strtolower( $search );

	$limit          = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );


	$types[] = JHTML::_('select.option',  0, '- '. JText::_( 'CJ SELECT STATE' ) .' -');
	$types[] = JHTML::_('select.option',  '1',  JText::_('CJ PROFILES TO CREATE'));
	$types[] = JHTML::_('select.option',  '-1',  JText::_('CJ PROFILES NOT TO CREATE'));
	$lists['type']  = JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

	$lists['search'] = $search;

	// table ordering
	$lists['order_Dir']     = $filter_order_Dir;
	$lists['order']         = $filter_order;


	$this->assignRef('lists',               $lists);


	jimport('joomla.html.pagination');
	$db           =& JFactory::getDBO();
	if ($search)
		$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
	else $searchEscaped = "";

	$this->profiletypes = JoomdleHelperProfiletypes::getProfiletypes ($filter_type, $limitstart, $limit,$filter_order, $filter_order_Dir, $searchEscaped);


	if (!$this->profiletypes)
		$this->profiletypes = array ();

	$total  = count (JoomdleHelperProfiletypes::getProfiletypes ('', 0, 100000, '0', '', ''));
	$pagination = new JPagination( $total, $limitstart, $limit );

	$this->assignRef('pagination',  $pagination);

        parent::display($tpl);
    }
}
?>
