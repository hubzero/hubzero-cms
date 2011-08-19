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
JPluginHelper::loadLanguage( 'plg_whatsnew_kb' );

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
			. " f.fulltext AS text,"
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

