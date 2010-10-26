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
class PluginsViewPlugin extends JView
{
	function display( $tpl = null )
	{
		global $option, $mainframe;

		$db		=& JFactory::getDBO();
		$user 	=& JFactory::getUser();

		$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$lists 	= array();		
		$row 	=& JTable::getInstance('plugin', 'JCETable');

		// load the row from the db table
		$row->load( $cid[0] );

		// fail if checked out not by 'me'

		if ($row->isCheckedOut( $user->get('id') ))
		{
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The plugin' ), $row->title );
			$this->setRedirect( 'index.php?option='. $option .'&type=plugin', $msg, 'error' );
			return false;
		}
		$path		= JPATH_PLUGINS .DS. 'editors' .DS. 'jce' .DS. 'tiny_mce' .DS. 'plugins' .DS. $row->name;
		$url		= $mainframe->getSiteURL(). 'plugins/editors/jce/tiny_mce/plugins/' .$row->name;
		$xmlPath 	= $path .DS. $row->name .'.xml';
		
		if ($cid[0])
		{
			$row->checkout( $user->get('id') );

			if ( $row->ordering > 0 && $row->ordering < 100 )
			{
				// build the html select list for ordering
				$query = 'SELECT ordering AS value, title AS text'
					. ' FROM #__jce_plugins'
					. ' WHERE name = "'. $row->name. '"'
					. ' AND published > 0'
					. ' AND ordering > 0'
					. ' AND ordering < 100'
					. ' ORDER BY ordering'
				;
				$order = JHTML::_('list.genericordering',  $query );
				$lists['ordering'] = JHTML::_('select.genericlist',   $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
			} else {
				$lists['ordering'] = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. JText::_( 'This plugin cannot be reordered' );
			}

			$lang =& JFactory::getLanguage();
			$lang->load( 'com_jce_' . trim( $row->name ), JPATH_SITE );
			
			$data = JApplicationHelper::parseXMLInstallFile( $xmlPath );
			$row->description = $data['description'];		
		} else {
			$row->type 			= 'plugin';
			$row->row	 		= 4;
			$row->ordering 		= 1;
			$row->published 	= 1;
			$row->details 		= 'From XML file';
			$row->icon 			= '';
			$row->layout	 	= '';
			$row->params		= '';
			$row->description	= '';
			
			$ordering = array();
			for($i=1; $i<31; $i++){
				$ordering[] = JHTML::_( 'select.option', $i, $i );
			}		
			$lists['ordering'] 	= JHTML::_('select.genericlist', $ordering, 'ordering', 'class="inputbox" size="1"', 'value', 'text', '' );		
		}
		
		$row_list = array(
			JHTML::_( 'select.option', '1','1' ),
			JHTML::_( 'select.option', '2','2' ),
			JHTML::_( 'select.option', '3','3' ),
			JHTML::_( 'select.option', '4','4' )
		);
		if ( $row->row > 0 ){
			$lists['row'] 		= JHTML::_('select.genericlist', $row_list, 'row', 'class="inputbox" size="1"', 'value', 'text', intval( $row->row ) );
		}else{
			$lists['row'] = '<input type="hidden" name="row" value="'. $row->row .'" />'. JText::_( 'This plugin\'s row cannot be altered' );
		}
		$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

		$row->preview = file_exists( $path .DS. 'img' .DS. 'preview.jpg' ) ? '<img src="' .$url. '/img/preview.jpg" alt="Preview" width="400" />' : JText::_( 'No Preview Available' );
		
		// get params definitions
		$params 	= new JParameter( $row->params, $xmlPath );
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('plugin',		$row);
		$this->assignRef('params',		$params);

		parent::display($tpl);
	}
}