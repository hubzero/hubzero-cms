<?php
/**
* @version		$Id: view.html.php 47 2009-05-26 18:06:30Z happynoodleboy $
* @package		Joomla
* @subpackage	Config
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Plugins component
 *
 * @static
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.0
 */
class GroupsViewGroups extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;

		$db =& JFactory::getDBO();
		
		$client = 'admin';
		$type 	= JRequest::getWord( 'type' );
		$task	= JRequest::getWord( 'task' );

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.filter_order",		'filter_order',		'g.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_state		= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.filter_state",		'filter_state',		'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.$type.$task.$client.search",				'search',		'',			'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( '$option.$type.$task.$client.limitstart', 'limitstart', 0, 'int' );
		
		$limitstart = isset( $limitstart->group ) ? $limitstart->group : 0;

		$where = array();
		
		if ( $search ) {
			$where[] = 'LOWER( g.name ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'g.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'g.published = 0';
			}
		}
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$orderby 	= ' ORDER BY g.ordering ASC';

		// get the total number of records
		$query = 'SELECT COUNT(g.id)'
		. ' FROM #__jce_groups AS g'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT g.*, u.name AS editor'
			. ' FROM #__jce_groups AS g'
			. ' LEFT JOIN #__users AS u ON u.id = g.checked_out'
			. $where
			. ' GROUP BY g.id'
			. $orderby
		;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
	
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
	
		// search filter
		$lists['search']	= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}