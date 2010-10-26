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
class PluginsViewAccess extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;

		$db =& JFactory::getDBO();

		$db		=& JFactory::getDBO();
		$lists 	= JCEHelper::accessList( 'access', '', 1, 'parent.document.adminForm.accessall.value=this.value;' );
		
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}