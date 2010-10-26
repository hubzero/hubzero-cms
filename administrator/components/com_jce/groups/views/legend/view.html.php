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
class GroupsViewLegend extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;		
		$db	=& JFactory::getDBO();

		$query = 'SELECT title, name, type, layout, icon'
		. ' FROM #__jce_plugins'
		. ' WHERE published = 1'
		. ' AND row > 0'
		. ' ORDER BY title'
		;
		$db->setQuery( $query );
		$icons = $db->loadObjectList();

		$this->assignRef('icons', $icons);

		parent::display($tpl);
	}
}