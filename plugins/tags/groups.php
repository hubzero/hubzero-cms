<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_tags_groups' );

//-----------

class plgTagsGroups extends JPlugin
{
	private $_total = null;
	
	//-----------
	
	public function plgTagsGroups(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tags', 'groups' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onTagAreas()
	{
		$areas = array(
			'groups' => JText::_('PLG_TAGS_GROUPS')
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
		$f_count = "SELECT COUNT(f.gidNumber) FROM (SELECT a.gidNumber, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.gidNumber AS id, a.description AS title, a.public_desc AS text, a.cn, 'groups' AS section, COUNT(DISTINCT t.tagid) AS uniques";
		$f_from = " FROM #__xgroups AS a, #__tags_object AS t
					WHERE a.type=1 AND a.privacy<=1
					AND a.gidNumber=t.objectid 
					AND t.tbl='groups' 
					AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.gidNumber HAVING uniques=".count($tags);
		$order_by = " ORDER BY a.description DESC, title LIMIT $limitstart,$limit";

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
					$rows[$key]->href = JRoute::_('index.php?option=com_groups&gid='.$row->cn);
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

	/*public function documents() 
	{
		// ...
	}
	
	//-----------
	
	public function before()
	{
		// ...
	}
	
	//-----------
	
	public function out()
	{
		// ...
	}
	
	//-----------
	
	public function after()
	{
		// ...
	}*/
}
