<?php
/**
* @version		$Id: view.html.php 100 2009-06-21 19:19:46Z happynoodleboy $
* @package		JCE
* @copyright	Copyright (C) 2009 Ryan Demmer. All rights reserved.
* @license		GNU/GPL
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
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
class GroupsViewGroup extends JView
{
	function display( $tpl = null )
	{
		global $option;

		require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'groups' .DS. 'helper.php' );
		
		$db		=& JFactory::getDBO();
		$user 	=& JFactory::getUser();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger( $cid, array(0) );

		$lists 	= array();		
		$row 	=& JTable::getInstance('groups', 'JCETable');
		
		// load the row from the db table
		$row->load( $cid[0] );
			
		// fail if checked out not by 'me'

		if( $row->isCheckedOut( $user->get('id') ) ){
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The Group' ), $row->name );
			$this->setRedirect( 'index.php?option='. $option .'&type=group', $msg, 'error' );
			return false;
		}
		// Load editor
		$editor =& JPluginHelper::getPlugin('editors', 'jce');
		
		// Load Language
		$lang =& JFactory::getLanguage();
        $lang->load( 'com_jce', JPATH_SITE );
		
		// Get all plugins/commands
		$query = 'SELECT *'
		. ' FROM #__jce_plugins'
		. ' WHERE published = 1'
		;
		
		$db->setQuery( $query );
		$plugins = $db->loadObjectList();

		// load the row from the db table
		if( $cid[0] ){
			$row->checkout( $user->get('id') );			
		}else{
			$query = 'SELECT COUNT(id)'
			. ' FROM #__jce_groups'
			;
			$db->setQuery( $query );
			$total = $db->loadResult();			
			
			$row->name 			= '';
			$row->description 	= '';
			$row->types			= '';
			$row->components	= '';
			$row->types			= '';
			$row->rows			= '';
			$row->plugins		= '';
			$row->published 	= 1;
			$row->ordering		= 0;
			$row->params 		= '';
			
			$row->params .= $editor->params;
			
			/*foreach( $plugins as $plugin ){
				if( $plugin->type == 'plugin' ){
					$row->params .= $plugin->params;
				}
			}*/
		}	
		
		// build the html select list for ordering
		$query = 'SELECT ordering AS value, name AS text'
			. ' FROM #__jce_groups'
			. ' WHERE published = 1'
			. ' AND ordering > -10000'
			. ' AND ordering < 10000'
			. ' ORDER BY ordering'
		;
		$order = JHTML::_('list.genericordering',  $query );
		$lists['ordering'] 	= JHTML::_('select.genericlist',   $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );		
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published );
		
		// Get components list
		$query = "SELECT *"
		. " FROM #__components"
		. " WHERE link <> ''"
		. " AND parent = 0"
		. " AND enabled = 1"
		//. " AND option NOT IN ('com_jce','com_wrapper','com_search','com_polls','com_newsfeeds')"
		. " ORDER BY name";
		$db->setQuery( $query );
		$components = $db->loadObjectList();
		
		$options 	= array();
		foreach( $components as $component ){
			if (!in_array($component->option, array('com_jce','com_wrapper','com_search','com_polls','com_newsfeeds'))) {
				$options[] = JHTML::_('select.option', $component->option, JText::_( $component->name ), 'value', 'text', $row->components == '');
			}
		}
		
		$disabled 	= $row->components == '' ? ' disabled="disabled"' : '';
		
		$lists['components'] = JHTML::_('select.genericlist', $options, 'components[]', 'class="inputbox levels" size="10" multiple="multiple"'.$disabled, 'value', 'text', $row->components == '' ? '' : explode( ',', $row->components ) );	
		
		$options = array(
			JHTML::_('select.option', 'all', JText::_( 'All Components' ), 'value', 'text'),
			JHTML::_('select.option', 'select', JText::_( 'Select From List' ), 'value', 'text')
		);
		$lists['components_radio'] = JHTML::_('select.radiolist', $options, 'components-select', 'class="inputbox"', 'value', 'text', $row->components ? 'select' : 'all', 'components-');	
		
			
		$query = 'SELECT types'
		. ' FROM #__jce_groups'
		// Exclude ROOT, USERS, Super Administrator, Public Frontend, Public Backend
		. ' WHERE id NOT IN (17,28,29,30)'
		;
		$db->setQuery( $query );
		$types = $db->loadResultArray();
		
		// get list of Groups for dropdown filter
		$query = 'SELECT id AS value, name AS text'
		. ' FROM #__core_acl_aro_groups'
		// Exclude ROOT, USERS, Super Administrator, Public Frontend, Public Backend
		. ' WHERE id NOT IN (17,28,29,30)'
		;
		$db->setQuery( $query );
		$types = $db->loadObjectList();
		
		$i = '-';
		$options = array( JHTML::_('select.option', '0', JText::_( 'Guest' ) ) );
		foreach( $types as $type ){
			$options[] = JHTML::_('select.option', $type->value, $i . JText::_( $type->text ) );
			$i .= '-';
		}
		$lists['types'] = JHTML::_('select.genericlist', $options, 'types[]', 'class="inputbox levels" size="8" multiple="multiple"', 'value', 'text', $row->types == '' ? '' : explode( ',', $row->types ) );
		
		$options = array();
		if( $row->id && $row->users ){
			$query = 'SELECT id as value, username as text'
			. ' FROM #__users'
			. ' WHERE id IN ('.$row->users.')'
			;
			
			$db->setQuery( $query );
			$gusers = $db->loadObjectList();
			if( $gusers ){
				foreach( $gusers as $guser ){
					$options[] = JHTML::_('select.option', $guser->value, $guser->text );
				}
			}	
		}
		$lists['users'] = JHTML::_('select.genericlist', $options, 'users[]', 'class="inputbox users" size="10" multiple="multiple"', 'value', 'text', '' );
				
		// get params definitions
		$xml = JCE_LIBRARIES.DS.'xml'.DS.'config'.DS.'config.xml';
        if (!file_exists($xml)) {
        	$xml = JPATH_PLUGINS.DS.'editors'.DS.'jce.xml';
        }

		$params = new JParameter( $row->params, $xml );
		
		$params->addElementPath( JPATH_COMPONENT . DS . 'elements' );
		
		$rows = str_replace( ';', ',', $row->rows );
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('group',		$row);
		$this->assignRef('params',		$params);
		$this->assignRef('plugins',		$plugins);

		parent::display($tpl);
	}
}