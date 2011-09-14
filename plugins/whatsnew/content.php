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

jimport( 'joomla.plugin.plugin' );
JPluginHelper::loadLanguage( 'plg_whatsnew_content' );

class plgWhatsnewContent extends JPlugin
{
	public function plgWhatsnewContent(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'whatsnew', 'content' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	public function &onWhatsnewAreas()
	{
		$areas = array(
			'content' => JText::_('PLG_WHATSNEW_CONTENT')
		);
		return $areas;
	}

	public function onWhatsnew( $period, $limit=0, $limitstart=0, $areas=null, $tagids=array() )
	{
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onWhatsnewAreas() ) && !array_intersect( $areas, array_keys( $this->onWhatsnewAreas() ) )) {
				return array();
			}
		}

		// Do we have a search term?
		if (!is_object($period)) {
			return array();
		}

		$database =& JFactory::getDBO();

		// Build the query
		$c_count = " SELECT count(DISTINCT c.id)";
		$c_fields = " SELECT "
				. " c.id,"
				. " c.title, c.alias, "
				. " CONCAT( c.introtext, c.fulltext ) AS text,"
				. " CONCAT( 'index.php?option=com_content&task=view&id=', c.id ) AS href, u.alias AS fsection, b.alias AS category,"
				//. " CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(':', c.id, c.alias) ELSE c.id END as slug,"
				//. " CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(':', b.id, b.alias) ELSE b.id END as catslug,"
				//. " u.id AS sectionid,"
				. " 'content' AS section, NULL AS subsection";
		$c_from = " FROM #__content AS c"
				. " INNER JOIN #__categories AS b ON b.id=c.catid"
				. " INNER JOIN #__sections AS u ON u.id=c.sectionid";

		$c_where = "c.publish_up > '$period->cStartDate' AND c.publish_up < '$period->cEndDate' AND c.state='1'";

		$order_by  = " ORDER BY publish_up DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if ($limit) {
			// Get results
			$database->setQuery( $c_fields.$c_from ." WHERE ". $c_where . $order_by );
			$rows = $database->loadObjectList();

			if ($rows) {
				//require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

				foreach ($rows as $key => $row)
				{
					//$database->setQuery( "SELECT parent FROM #__menu WHERE alias='$row->alias' LIMIT 1" );
					$database->setQuery( "SELECT alias, parent FROM #__menu WHERE link='index.php?option=com_content&view=article&id=".$row->id."' AND published=1 LIMIT 1" );
					$menuitem = $database->loadRow();
					if ($menuitem[1]) {
						$p = $this->_recursiveMenuLookup($menuitem[1]);
						$path = implode(DS,$p);
						if ($menuitem[0]) {
							$path .= DS.$menuitem[0];
						} else if ($row->alias) {
							$path .= DS.$row->alias;
						}
					} else if ($menuitem[0]) {
						$path = DS.$menuitem[0];
					} else {
						//$rows[$key]->href = ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid);
						$path = '';
						if ($row->fsection) {
							$path .= DS.$row->fsection;
						}
						if ($row->category && $row->category != $row->fsection) {
							$path .= DS.$row->category;
						}
						if ($row->alias) {
							$path .= DS.$row->alias;
						}
						if (!$path) {
							//$path = JRoute::_($row->href);
							$path = '/content/article/'.$row->id;
						}
					}
					$rows[$key]->href = $path;
				}
			}

			return $rows;
		} else {
			// Get a count
			$database->setQuery( $c_count.$c_from ." WHERE ". $c_where );
			return $database->loadResult();
		}
	}

	private function _recursiveMenuLookup($id, $startnew=true)
	{
	    static $aliases = array();

		if ($startnew) {
			unset($aliases);
		}

		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT alias, parent FROM #__menu WHERE id='$id' LIMIT 1" );
		$level = $database->loadRow();

		$aliases[] = $level[0];
		if ($level[1]) {
			$a = $this->_recursiveMenuLookup($level[1], false);
		}

	    return $aliases;
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

