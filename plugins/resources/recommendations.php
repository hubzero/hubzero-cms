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
	function plgResourcesRecommendations(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'recommendations' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onResourcesSubAreas( $resource )
	{
		$areas = array(
			'recommendations' => JText::_('RECOMMENDATIONS')
		);
		return $areas;
	}

	//-----------

	function onResourcesSub( $resource, $option )
	{
		$sbjt  = '<p>'.JText::_('RECOMMENDATIONS_PLACEHOLDER').'</p>';
		$sbjt .= '<ul>'.n;
		$sbjt .= t.'<li>'.JText::_('RECOMMENDATIONS_REASON_ONE').'</li>'.n;
		$sbjt .= t.'<li>'.JText::_('RECOMMENDATIONS_REASON_TWO').'</li>'.n;
		$sbjt .= t.'<li>'.JText::_('RECOMMENDATIONS_REASON_THEREE').'</li>'.n;
		$sbjt .= '</ul>'.n;

		$html  = ResourcesHtml::hed(3,'<a name="recommendations"></a>'.JText::_('RECOMMENDATIONS_HEADER')).n;
		$html .= ResourcesHtml::aside('<p>'.JText::_('RECOMMENDATIONS_EXPLANATION').'</p>');
		$html .= ResourcesHtml::subject($sbjt, 'recommendations-subject');
		//$html .= '<input type="hidden" name="rid" id="rid" value="'.$resource->id.'" />'.n;

		$arr = array(
				'html'=>$html,
				'metadata'=>''
			);

		return $arr;
	}
	
	//-----------
	
	function onResourcesRecoms( $option )
	{
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
		$recoms = $this->getRecommendations( $uid, $keywords );

		if (!$this->getError()) {
			// Build the HTML
			$html = $this->giveRecommendations( $recoms, $rid );
		} else {
			$html = '<p class="warning">'.JText::_('UNABLE_TO_GET_RECOMMENDATIONS').'</p><!-- '.ResourcesHtml::error( $this->getError() ).' -->';
		}

		$arr = array(
				'html'=>$html,
				'metadata'=>''
			);

		return $arr;
	}

	//-----------
	
	function giveRecommendations( $lines, $rid )
	{
		$juser =& JFactory::getUser();

		$sbjt = '';
		if (is_array($lines) && count($lines['return']) > 0) {
			$sbjt .= t.'<table class="recommendations" summary="'.JText::_('RECOMMENDATIONS_TBL_SUMMARY').'">'.n;
			$sbjt .= t.t.'<thead>'.n;
			$sbjt .= t.t.t.'<tr>'.n;
			$sbjt .= t.t.t.t.'<th>'.JText::_('RECOMMENDATION').'</th>'.n;
			$sbjt .= t.t.t.t.'<th>'.JText::_('RESULT_TYPE').'</th>'.n;
			$sbjt .= t.t.t.t.'<th>'.JText::_('RESULT_RELEVANCE').'</th>'.n;
			//$sbjt .= t.t.t.t.'<th>'.JText::_('RESULT_REASON').'</th>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$sbjt .= t.t.'</thead>'.n;
			$sbjt .= t.t.'<tbody>'.n;
			foreach ($lines['return'] as $r)
			{
				$r['url'] = str_replace( '&amp;', '&', $r['url'] );
				$r['url'] = str_replace( '&', '&amp;', $r['url'] );

				$sbjt .= t.t.t.'<tr>'.n;
				$sbjt .= t.t.t.t.'<td><a href="'.$r['nanoUrl'].'?rec='.$rid.'">'. $r['label'] . '</a></td>'.n;
				$sbjt .= t.t.t.t.'<td class="type">'.ucfirst($r['type']).'</td>'.n;
				$sbjt .= t.t.t.t.'<td>'.($r['score'] * 10).'%</td>'.n;
				//$sbjt .= t.t.t.t.'<td><a href="'.$r['url'].'">'.JText::_('WHY').</a></td>'.n;
				$sbjt .= t.t.t.'</tr>'.n;
			}
			$sbjt .= t.t.'</tbody>'.n;
			$sbjt .= t.'</table>'.n;
			$sbjt .= '<div class="clear"></div><!-- Used only to fix some strange IE 6 display problems -->'.n;
			if ($juser->get('guest')) {
				$sbjt .= t.ResourcesHtml::warning( JText::_('LOGIN_TO_GET_BETTER_RESULTS') ).n;
			}
		} else {
			$sbjt .= t.'<p>'.JText::_('NO_RECOMMENDATIONS_FOUND').'</p>'.n;
		}

		return $sbjt;
	}

	//-----------

	/*function getRecommendations($uid, $keywords) 
	{
		// Retrieve some plugin parameters
		$provy = array();
		$proxy['host']     = $this->_params->get('host', '');
		$proxy['port']     = $this->_params->get('port', '');
		$proxy['username'] = $this->_params->get('username', '');
		$proxy['password'] = $this->_params->get('password', '');
		$proxyclient   = $this->_params->get('webservice', '');
		$display_limit = $this->_params->get('display_limit', '');
		
		if (!$proxyclient) {
			$this->setError( JText::_('NO_WEBSERVICE') );
			return false;
		}
		
		// Try to connect to the web service
		try {
			$client = new SoapClient($proxyclient, $proxy);
		} catch (Exception $e) {
			$config = JFactory::getConfig();

			if ($config->getValue('config.debug')) {
				$this->setError( $e->getMessage() );
			}
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

		// Try to call the web service
		try {
			$result = $client->__soapCall('getRecommendationDTOs', $param, '', '', false, true);
		} catch (SoapFault $e) {
			$this->setError( JText::_('WEBSERVICE_FAULT').' '.$e );
			return false;
		}
		
		// Return the result
		return $result;
	}*/
	
	//-----------

	function getRecommendations($uid, $keywords) 
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
			$this->setError( JText::_('CONSTRUCTOR_ERROR').' '. $err );
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
			$this->setError( JText::_('WEBSERVICE_FAULT').' '.$result['faultString'] );
			return false;
		} else {
			// Check for errors
			$err = $client->getError();
			if ($err) {
				// Return the error
				$this->setError( JText::_('WEBSERVICE_ERROR').' '. $err );
				return false;
			} else {
				// Return the result
				return $result;
			}
		}
	}
}
