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
JPlugin::loadLanguage( 'plg_whatsnew_kb' );

//-----------

class plgWhatsnewKb extends JPlugin
{
	public function plgWhatsnewKb(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'whatsnew', 'kb' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onWhatsnewAreas() 
	{
		$areas = array(
			'kb' => JText::_('PLG_WHATSNEW_KB')
		);
		return $areas;
	}

	//-----------

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
		$f_count = "SELECT COUNT(*)";
		$f_fields = "SELECT"
			. " f.id, "
			. " f.title, "
			. " 'kb' AS section, NULL AS subsection, "
			. " CONCAT( f.introtext, f.fulltext ) AS text,"
			. " CONCAT( 'index.php?option=com_kb&task=article&id=', f.id ) AS href";

		$f_from = " FROM #__faq AS f";

		$f_where = "f.created > '$period->cStartDate' AND f.created < '$period->cEndDate'";

		$order_by  = " ORDER BY created DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $f_count.$f_from ." WHERE ". $f_where );
			return $database->loadResult();
		} else {
			// Get results
			$database->setQuery( $f_fields.$f_from ." WHERE ". $f_where . $order_by );
			$rows = $database->loadObjectList();

			foreach ($rows as $key => $row) 
			{
				$rows[$key]->href = JRoute::_($row->href);
			}

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
