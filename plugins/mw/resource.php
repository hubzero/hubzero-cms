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
JPlugin::loadLanguage( 'plg_mw_resource' );

//-----------

class plgMwResource extends JPlugin
{
	function plgMwResource(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'mw', 'resource' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onMwAreas( $authorized )
	{
		$areas = array(
				'about' => JText::_('ABOUT')
		);

		return $areas;
	}

	//-----------

	function onMw( $toolname, $option, $authorized, $areas )
	{
		$returnhtml = true;

		$arr = array(
				'html'=>'',
				'metadata'=>''
			);

		// Build the final HTML
		if ($returnhtml) {
			// Import needed libraries
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
			
			$database =& JFactory::getDBO();

			// Load the resource
			$resource = new ResourcesResource( $database );
			$resource->loadAlias( $toolname );
	
			// Build the HTML
			$arr['html']  = MwHtml::hed(3,'<a name="about"></a>'.JText::_('ABOUT')).n;
			$arr['html'] .= $this->about( $resource, $database, 'com_resources' );
		}
		
		// Returnt he final output
		return $arr;
	}
	
	//-----------

	function about( $resource, $database, $option ) 
	{
		// Make sure we got a result from the database
		if (!$resource) {
			return MwHtml::error( JText::_('Resource not found.') );
		}
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php');
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.assoc.php');
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.doi.php');
		//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.tool.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.html.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.extended.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.tool.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.author.php' );
		ximport('resourcestats');
		
		// Push some styles to the tmeplate
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_resources');
		
		// Push some scripts to the template
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.js')) {
			$document->addScript('components/com_resources/resources.js');
		}
		
		// Get com_resources config
		$config =& JComponentHelper::getParams( 'com_resources' );
		
		// Version checks (tools only)
		$revision = JRequest::getVar( 'rev', '' );
		$revision = ($revision) ? $revision : JRequest::getVar( 'version', '' );
		$alltools = array();
		$thistool = '';
		$curtool  = '';

		if ($resource->type == 7 && $resource->alias) {		
			$tables = $database->getTableList();
			$table = $database->_table_prefix.'tool_version';
			
			// get contribtool params
			$tparams =& JComponentHelper::getParams( 'com_contribtool' );
			$ldap = $tparams->get('ldap_read');
		
			if (in_array($table,$tables)) {
				$tv = new ToolVersion( $database );
				$tv->getToolVersions( '', $alltools, $resource->alias, $ldap); 

				if ($alltools) {
					foreach ($alltools as $tool) 
					{
						// Archive version, if requested
						if (($revision && $tool->revision == $revision && $revision != 'dev') or ($revision == 'dev' and $tool->state==3) ) {
							$thistool = $tool;
						}
						// Current version
						if ($tool->state == 1 && count($alltools) > 1 &&  $alltools[1]->version == $tool->version) {
							$curtool = $tool;
						}
						// Dev version
						if (!$revision && count($alltools)==1 && $tool->state==3) {
							$thistool = $tool;
							$revision = 'dev';
						}
					}
	
					if (!$thistool && !$curtool && count($alltools) > 1) { 
						// Tool is retired, display latest unpublished version
						$thistool = $alltools[1];
						$revision = $alltools[1]->revision;
					}
	
					if ($curtool && $thistool && $thistool == $curtool) { 
						// Display default resource page for current version
						$thistool = '';
					}			
				}
			
				// replace resource info with requested version
				$tv->compileResource($thistool, $curtool, $resource, $revision, $config);
			}
		}
		
		// Initiate a resource helper class
		$helper = new ResourcesHelper( $resource->id, $database );
		
		// Get parameters and merge with the component params
		$rparams =& new JParameter( $resource->params );
		$params = $config;
		$params->merge( $rparams );
		
		// Get attributes
		$attribs =& new JParameter( $resource->attribs );
		
		// Get the groups the user has access to
		$juser =& JFactory::getUser();
		ximport('xuserhelper');
		$xgroups = XUserHelper::getGroups($juser->get('id'), 'all');
		$usersgroups = $this->_getUsersGroups($xgroups);
		
		$resource->tagform = true;
		
		// Return HTML
		return ResourcesHtml::about( $database, 0, $usersgroups, $resource, $helper, $config, array(), $thistool, $curtool, $alltools, $revision, $params, $attribs, 'com_resources', 0 );
	}
	
	//-----------
	
	function _getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) {
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) {
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}
}