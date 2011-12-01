<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPluginHelper::loadLanguage( 'plg_whatsnew_events' );

/**
 * Short description for 'plgWhatsnewEvents'
 * 
 * Long description (if any) ...
 */
class plgWhatsnewEvents extends JPlugin
{

	/**
	 * Short description for 'plgWhatsnewEvents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgWhatsnewEvents(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'whatsnew', 'events' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onWhatsnewAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function &onWhatsnewAreas()
	{
		$areas = array(
			'events' => JText::_('PLG_WHATSNEW_EVENTS')
		);
		return $areas;
	}

	/**
	 * Short description for 'onWhatsnew'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $period Parameter description (if any) ...
	 * @param      mixed $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @param      array $tagids Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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
		$e_count = "SELECT count(DISTINCT e.id)";
		/*$e_fields = "SELECT "
				. " e.id,"
				. " e.title, "
				. " e.content AS text,"
				. " CONCAT( 'index.php?option=com_events&task=details&id=', e.id ) AS href,"
				. " e.publish_up AS publish_up,"
				. " 'events' AS section, NULL AS subsection";*/
		$e_fields = "SELECT e.id, e.title, NULL AS alias, e.content AS itext, NULL AS ftext, e.state, e.created, e.modified, e.publish_up, NULL AS params, 
					CONCAT( 'index.php?option=com_events&task=details&id=', e.id ) AS href, 'events' AS section, NULL AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, e.access ";
		$e_from = " FROM #__events AS e";

		$e_where = "e.created > '$period->cStartDate' AND e.created < '$period->cEndDate'";

		$order_by  = " ORDER BY publish_up DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $e_count.$e_from ." WHERE ". $e_where );
			return $database->loadResult();
		} else {
			// Get results
			$query = $e_fields.$e_from ." WHERE ". $e_where . $order_by;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = JRoute::_($row->href);
					$rows[$key]->text = $rows[$key]->itext;
				}
			}

			return $rows;
		}
	}

	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	/**
	 * Short description for 'documents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function documents()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_events');
	}

	/*public function before()
	{
		// ...
	}*/

	/**
	 * Short description for 'out'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $row Parameter description (if any) ...
	 * @param      unknown $period Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function out( $row, $period )
	{
		$month = JHTML::_('date', $row->publish_up, '%b');
		$day = JHTML::_('date', $row->publish_up, '%d');
		$year = JHTML::_('date', $row->publish_up, '%Y');

		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}

		// Start building the HTML
		$html  = "\t".'<li class="event">'."\n";
		$html .= "\t\t".'<p class="event-date"><span class="month">'.$month.'</span> <span class="day">'.$day.'</span> <span class="year">'.$year.'</span></p>'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->itext) {
			$row->itext = str_replace('[[BR]]', '', $row->itext);
			$html .= "\t\t".'<p>'.Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->itext)),200,0).'</p>'."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";

		// Return output
		return $html;
	}

	/*public function after()
	{
		// ...
	}*/
}

