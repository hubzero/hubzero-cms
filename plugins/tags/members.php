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
JPlugin::loadLanguage( 'plg_tags_members' );

//-----------

class plgTagsMembers extends JPlugin
{
	private $_total = null;
	
	//-----------
	
	public function plgTagsMembers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tags', 'members' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onTagAreas()
	{
		$areas = array(
			'members' => JText::_('PLG_TAGS_MEMBERS')
		);
		return $areas;
	}
	
	//-----------

	public function onTagView( $tags, $limit=0, $limitstart=0, $sort='', $areas=null )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onTagAreas() ) && !array_intersect( $areas, array_keys( $this->onTagAreas() ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (empty($tags)) {
			return array();
		}

		$database =& JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag) 
		{
			$ids[] = $tag->id;
		}
		$ids = implode(',',$ids);

		// Build the query
		$f_count = "SELECT COUNT(f.uidNumber) FROM (SELECT a.uidNumber, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.uidNumber AS id, a.name AS title, b.bio AS text, a.organization, 'members' AS section, a.picture, COUNT(DISTINCT t.tagid) AS uniques";

		/*$f_from = " FROM #__xprofiles AS a, #__tags_object AS t, #__tags AS tg 
					WHERE a.public=1 AND a.uidNumber=t.objectid AND t.tbl='xprofiles' AND tg.id=t.tagid AND (tg.tag='".$tag."' OR tg.raw_tag='".$tag."' OR tg.alias='".$tag."')";*/
		$f_from = " FROM #__xprofiles AS a LEFT JOIN #__xprofiles_bio AS b ON a.uidNumber=b.uidNumber, #__tags_object AS t
					WHERE a.public=1 
					AND a.uidNumber=t.objectid 
					AND t.tbl='xprofiles' 
					AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.uidNumber HAVING uniques=".count($tags);
		$order_by = " ORDER BY a.name DESC, title LIMIT $limitstart,$limit";

		// Execute the query
		if (!$limit) {
			$database->setQuery( $f_count . $f_from .") AS f" );
			$this->_total = $database->loadResult();
			return $this->_total;
		} else {
			if ($this->_total != null) {
				if ($this->_total == 0) {
					return array();
				}
			}
			
			$database->setQuery( $f_fields . $f_from .  $order_by );
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows) {
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row) 
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_members&id='.$row->id);
				}
			}

			// Return the results
			return $rows;
		}
	}
	
	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	public function documents() 
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_members');
	}
	
	//-----------
	
	/*public function before()
	{
		// ...
	}*/
	
	//-----------
	
	public function out( $row )
	{
		$config =& JComponentHelper::getParams( 'com_members' );
		
		if ($row->picture) {
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			if ($row->id < 0) {
				$id = abs($row->id);
				$thumb .= DS.'n'.plgXSearchMembers::niceidformat($id).DS.$row->picture;
			} else {
				$thumb .= DS.plgTagsMembers::niceidformat($row->id).DS.$row->picture;
			}
		} else {
			$thumb = $config->get('defaultpic');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
		}
		
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);
		
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
		
		$html  = "\t".'<li class="member">'."\n";
		if (is_file(JPATH_ROOT.$thumb)) {
			$html .= "\t\t".'<p class="photo"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'."\n";
		}
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->text) {
			$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->text)), 200)."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		return $html;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
	
	//-----------
	
	/*public function after()
	{
		// ...
	}*/
}