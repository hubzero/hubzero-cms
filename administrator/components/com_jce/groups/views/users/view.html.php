<?php
/**
* @version		$Id: view.html.php 47 2009-05-26 18:06:30Z happynoodleboy $
* @package		Joomla
* @subpackage	Users
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Users component
 *
 * @static
 * @package		Joomla
 * @subpackage	Users
 * @since 1.0
 */
class GroupsViewUsers extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'groups' .DS. 'helper.php' );
		
		$client = 'admin';
		$type 	= JRequest::getWord( 'type' );
		$task 	= JRequest::getWord( 'task' );
		
		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.filter_type",			'filter_type', 		0,			'string' );
		$search				= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.search",				'search', 			'',			'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'a.username LIKE '.$searchEscaped.' OR a.email LIKE '.$searchEscaped.' OR a.name LIKE '.$searchEscaped;
		}
		if ( $filter_type )
		{
			$where[] = 'a.usertype = LOWER( '.$db->Quote($filter_type).' ) ';
		}

		// exclude any child group id's for this user
		$pgids = $acl->get_group_children( $currentUser->get('gid'), 'ARO', 'RECURSE' );

		if (is_array( $pgids ) && count( $pgids ) > 0)
		{
			JArrayHelper::toInteger($pgids);
			$where[] = 'a.gid NOT IN (' . implode( ',', $pgids ) . ')';
		}
		// Exclude ROOT, USERS, Super Administrator, Public Frontend, Public Backend
		$where[] = 'a.gid NOT IN (17,28,29,30)';
		// Only unblocked users
		$where[] = 'a.block = 0';
		
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(a.id)'
		. ' FROM #__users AS a'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );
				
		$query = 'SELECT a.*, g.name AS groupname'
			. ' FROM #__users AS a'
			. ' INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id'
			. ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
			. ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
			. $where
			. ' GROUP BY a.id'
			. $orderby
		;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();

		// get list of Groups for dropdown filter
		$query = 'SELECT name AS value, name AS text'
		. ' FROM #__core_acl_aro_groups'
		// Exclude ROOT, USERS, Super Administrator, Public Frontend, Public Backend
		. ' WHERE id NOT IN (17,28,29,30)'
		;
		$db->setQuery( $query );
		$types[] = JHTML::_('select.option',  '0', '- '. JText::_( 'Select Group' ) .' -' );
		foreach( $db->loadObjectList() as $obj )
		{
			$types[] = JHTML::_('select.option',  $obj->value, JText::_( $obj->text ) );
		}
		$lists['group'] 	= JHTML::_('select.genericlist', $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}