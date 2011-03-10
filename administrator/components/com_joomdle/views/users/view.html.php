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

class JoomdleViewUsers extends JView {
    function display($tpl = null) {
	    global $mainframe, $option;


	$mainframe = JFactory::getApplication();

	$params = &JComponentHelper::getParams( 'com_joomdle' );

	$filter_order           = $mainframe->getUserStateFromRequest( "$option.filter_order",          'filter_order',         'username',       'cmd' );
	$filter_order_Dir       = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",      'filter_order_Dir',     '',                     'word' );

	/* kludge while I learn how to do it FIXME */
        if (($filter_order != 'username') && ($filter_order != 'name') && ($filter_order != 'email') )
                $filter_order = 'username';
        /* kludge while I learn how to do it FIXME */


	$filter_type            = $mainframe->getUserStateFromRequest( "$option.filter_type",           'filter_type',          0,                      'string' );
	/* kludge while I learn how to do it FIXME */
	if ( ($filter_type) && ($filter_type != 'joomla') &&  ($filter_type != 'moodle') &&  ($filter_type != 'joomdle') &&  ($filter_type != 'not_joomdle'))
                $filter_type = 0;
	/* kludge while I learn how to do it FIXME */
	$search                         = $mainframe->getUserStateFromRequest( "$option.search",                        'search',                       '',                     'string' );
	$search                         = JString::strtolower( $search );

	$limit          = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );


	$types[] = JHTML::_('select.option',  0, '- '. JText::_( 'CJ SELECT FILTER' ) .' -');
	$types[] = JHTML::_('select.option',  'joomla', JText::_( 'CJ JOOMLA USERS' ) );
	$types[] = JHTML::_('select.option',  'moodle', JText::_( 'CJ MOODLE USERS' ) );
	$types[] = JHTML::_('select.option',  'joomdle', JText::_( 'CJ JOOMLDE USERS' ) );
	$types[] = JHTML::_('select.option',  'not_joomdle', JText::_( 'CJ NOT JOOMDLE USERS' ) );
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
	if ($filter_type)
	{

		if ($filter_type == 'moodle')
		{
			$total = JoomdleHelperContent::getMoodleUsersNumber ($search);
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperContent::getMoodleUsers ($limitstart, $limit,$filter_order, $filter_order_Dir, $search);
		}
		else if ($filter_type == 'joomla')
		{
			$total = JoomdleHelperContent::getJoomlaUsersNumber ($searchEscaped);
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperContent::getJoomlaUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $searchEscaped);
		}
		else if ($filter_type == 'joomdle')
		{
			$total = count (JoomdleHelperContent::getJoomdleUsers (0, 0, $filter_order, $filter_order_Dir, $searchEscaped));
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperContent::getJoomdleUsers ($limitstart, $limit,  $filter_order, $filter_order_Dir, $searchEscaped);
		}
		else if ($filter_type == 'not_joomdle')
		{
			$total = count (JoomdleHelperContent::getNotJoomdleUsers (0, 0, $filter_order, $filter_order_Dir, $search));
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperContent::getNotJoomdleUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $search);
		}
	}
	else {
		/* Show all users */
		$total = JoomdleHelperContent::getAllUsersNumber ($search);
	//	$pagination = new JPagination( $total, $limitstart, $limit );
		$this->users = JoomdleHelperContent::getAllUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $search); //XXX merge lists
		//$pagination = new JPagination( count ($this->users), $limitstart, $limit );
		$pagination = new JPagination( $total, $limitstart, $limit );
		/*
		$this->users = JoomdleHelperContent::getJoomlaUsers ();
		print_r ($this->users);*/
	}

	$this->assignRef('pagination',  $pagination);


        parent::display($tpl);
    }
}
?>
