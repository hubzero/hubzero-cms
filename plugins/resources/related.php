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
JPlugin::loadLanguage( 'plg_resources_related' );
	
//-----------

class plgResourcesRelated extends JPlugin
{
	public function plgResourcesRelated(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'related' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesSubAreas( $resource )
	{
		$areas = array(
			'related' => JText::_('PLG_RESOURCES_RELATED')
		);
		return $areas;
	}

	//-----------

	public function onResourcesSub( $resource, $option, $miniview=0 )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
			
		$database =& JFactory::getDBO();
		
		// Build the query that checks topic pages
		$sql1 = "SELECT v.id, v.pageid, MAX(v.version) AS version, w.title, w.pagename AS alias, v.pagetext AS introtext, NULL AS type, NULL AS published, NULL AS publish_up, w.scope, w.rating, w.times_rated, w.ranking, 'Topic' AS section, w.`group`  
				FROM #__wiki_page AS w, #__wiki_version AS v
				WHERE w.id=v.pageid AND v.approved=1 AND (v.pagetext LIKE '%[[Resource(".$resource->id.")]]%' OR v.pagetext LIKE '%[[Resource(".$resource->id.",%' OR v.pagetext LIKE '%[/resources/".$resource->id." %'";
		$sql1 .= ($resource->alias) ? " OR v.pagetext LIKE '%[[Resource(".$resource->alias."%') " : ") ";
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			if ($juser->authorize('com_resources', 'manage') || $juser->authorize('com_groups', 'manage')) {
				$sql1 .= '';
			} else {
				ximport('xuserhelper');

				$ugs = XUserHelper::getGroups( $juser->get('id'), 'members' );
				$groups = array();
				if ($ugs && count($ugs) > 0) {
					foreach ($ugs as $ug) 
					{
						$groups[] = $ug->cn;
					}
				}
				$g = "'".implode("','",$groups)."'";

				$sql1 .= "AND (w.access!=1 OR (w.access=1 AND (w.group IN ($g) OR w.created_by='".$juser->get('id')."'))) ";
			}
		} else {
			$sql1 .= "AND w.access!=1 ";
		}
		$sql1 .= "GROUP BY pageid ORDER BY ranking DESC, title LIMIT 10";

		// Build the query that checks resource parents
		$sql2 = "SELECT DISTINCT r.id, NULL AS pageid, NULL AS version, r.title, r.alias, r.introtext, r.type, r.published, r.publish_up, NULL AS scope, r.rating, r.times_rated, r.ranking, rt.type AS section, NULL AS `group` "
			 . "\n FROM #__resource_types AS rt, #__resources AS r"
			 . "\n JOIN #__resource_assoc AS a ON r.id=a.parent_id"
			 . "\n LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . "\n WHERE r.published=1 AND a.child_id=".$resource->id." AND r.type=rt.id AND r.type!=8"
			 . "\n ORDER BY r.ranking LIMIT 10";

		// Build the final query
		$query = "SELECT k.* FROM (($sql1) UNION ($sql2)) AS k ORDER BY ranking DESC LIMIT 10";

		// Execute the query
		$database->setQuery( $sql1 );
		$topics = $database->loadObjectList();
		
		$database->setQuery( $sql2 );
		$resources = $database->loadObjectList();
		
		$rel = array();
		if ($topics) {
			foreach ($topics as $t) 
			{
				$rel[$t->ranking] = $t;
			}
		}
		if ($resources) {
			foreach ($resources as $r) 
			{
				$rel[$r->ranking] = $r;
			}
		}
		
		krsort($rel);
		$i = 0;
		$related = array();
		foreach ($rel as $k=>$r) 
		{
			$i++;
			if ($i == 11) {
				break;
			}
			$related[] = $r;
		}
		
		ximport('Hubzero_View_Helper_Html');
		
		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		if ($miniview) {
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'related',
					'name'=>'browse',
					'layout'=>'mini'
				)
			);
		} else {
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'related',
					'name'=>'browse'
				)
			);
		}

		// Pass the view some info
		$view->option = $option;
		$view->resource = $resource;
		$view->related = $related;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		// Return the an array of content
		return $arr;
	}
}
