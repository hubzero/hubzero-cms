<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_recommendations' );
	
//-----------

class plgResourcesRecommendations extends JPlugin
{
	public function plgResourcesRecommendations(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'recommendations' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesSubAreas( $resource )
	{
		$areas = array(
			'recommendations' => JText::_('PLG_RESOURCES_RECOMMENDATIONS')
		);
		return $areas;
	}

	//-----------

	public function onResourcesSub( $resource, $option )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
			
		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'resources',
				'element'=>'recommendations',
				'name'=>'browse'
			)
		);

		// Pass the view some info
		$view->option = $option;
		$view->resource = $resource;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
	
	//-----------
	
	public function onResourcesRecoms( $option )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$rid = JRequest::getInt( 'rid', 0 );

		// Is the user logged-in or were we *not* passed a UID?
		if (!$juser->get('guest')) {
			// Yes, get their ID
			$uid = $juser->get('id');
		} else {
			// No, check if we were passed an ID
			// If not, then use the default
			$uid = 21000;
		}

		// Build the keyword list from the resource's tags
		$resourceEx = new ResourceExtended($rid, $database);
		$resourceEx->getTagsForEditing();
		$keywords = $resourceEx->tagsForEditing;

		// Get the recommendations
		$recoms = $this->_getRecommendations( $uid, $keywords );

		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'resources',
				'element'=>'recommendations',
				'name'=>'browse',
				'layout'=>'ajax'
			)
		);

		// Pass the view some info
		$view->option = $option;
		$view->lines = $recoms;
		$view->rid = $rid;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
	
	//-----------

	private function _getRecommendations($uid, $keywords) 
	{
		// Import the NuSOAP library - needed for talkign to web services
		ximport('nusoap.lib.nusoap');

		// Retrieve some plugin parameters
		$proxyhost     = $this->_params->get('host', '');
		$proxyport     = $this->_params->get('port', '');
		$proxyusername = $this->_params->get('username', '');
		$proxypassword = $this->_params->get('password', '');
		$proxyclient   = $this->_params->get('webservice', '');
		$display_limit = $this->_params->get('display_limit', '');

		// Instantiate the NuSOAP Client
		$client = new nusoap_client($proxyclient, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);
		$err = $client->getError();
		if ($err) {
			$this->setError( JText::_('PLG_RESOURCES_RECOMMENDATIONS_CONSTRUCTOR_ERROR').' '. $err );
			return false;
		}
		
		// Take a string of comma-separted heywords and turn it into an array
		$kywrds = array();
		$keywords = explode(',',$keywords);
		foreach ($keywords as $keyword) 
		{
			$kywrds[] = trim($keyword);
		}

		// Set the array of parameters we'll be passing to the web service
		$param = array('arg0'=>$uid,'arg1'=>$kywrds,'arg2'=>$display_limit);

		$result = $client->call('getRecommendationDTOs', $param, '', '', false, true);

		// Check for a fault
		if ($client->fault) {
			$this->setError( JText::_('PLG_RESOURCES_RECOMMENDATIONS_WEBSERVICE_FAULT').' '.$result['faultString'] );
			return false;
		} else {
			// Check for errors
			$err = $client->getError();
			if ($err) {
				// Return the error
				$this->setError( JText::_('PLG_RESOURCES_RECOMMENDATIONS_WEBSERVICE_ERROR').' '. $err );
				return false;
			} else {
				// Return the result
				return $result;
			}
		}
	}
}
