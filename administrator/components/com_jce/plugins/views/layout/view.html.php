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
class PluginsViewLayout extends JView
{
	function display( $tpl = null )
	{
		global $mainframe;

		$db =& JFactory::getDBO();
		
		$client = JRequest::getWord( 'client', 'site' );
			
		$plugin =& JPluginHelper::getPlugin('editors', 'jce');
 		$params = new JParameter( $plugin->params );
		
		$num 	= intval( $params->get( 'layout_rows', 5 ) );	
		$rows 	= array();
		
		for($i=1; $i<=$num; $i++){
			$query = "SELECT id, title, name, type, layout, icon"
			. "\n FROM #__jce_plugins"
			. "\n WHERE row = ". $i .""
			. "\n AND published = 1"
			. "\n AND icon != ''"
			. "\n ORDER BY ordering ASC"
			;
			$db->setQuery( $query );
			$rows[] = $db->loadObjectList();
		}

		$dimensions['width'] 	= $params->get( 'width', '600' );
		$dimensions['height'] 	= $params->get( 'height', '600' );
		
		if ($client == 'admin') {
			$client_id = 1;
		} else {
			$client_id = 0;
		}

		$this->assignRef('dimensions', $dimensions);
		$this->assignRef('client', $client_id);
		$this->assignRef('items', $rows);

		parent::display($tpl);
	}
}