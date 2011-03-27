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
JPlugin::loadLanguage( 'plg_tags_answers' );

//-----------

class plgTagsAnswers extends JPlugin
{
	private $_total = null;
	
	//-----------
	
	public function plgTagsAnswers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tags', 'answers' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------
	
	public function onTagAreas()
	{
		$areas = array(
			'answers' => JText::_('PLG_TAGS_ANSWERS')
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
		$f_count = "SELECT COUNT(f.id) FROM (SELECT a.id, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.id, a.subject AS title, a.question AS text, a.state, a.created, a.created_by, a.anonymous, 'answers' AS section, COUNT(DISTINCT t.tagid) AS uniques, (SELECT COUNT(*) FROM #__answers_responses AS r WHERE r.qid=a.id) AS rcount";
		/*$f_count = "SELECT COUNT(f.id) FROM (SELECT a.id, COUNT(DISTINCT tg.tag) AS uniques ";

		$f_fields = "SELECT a.id, a.subject AS title, a.question AS text, a.state, a.created, a.created_by, a.anonymous, 'answers' AS section, COUNT(DISTINCT tg.tag) AS uniques, (SELECT COUNT(*) FROM #__answers_responses AS r WHERE r.qid=a.id) AS rcount";

		$f_from = " FROM #__answers_questions AS a, #__tags_object AS t, #__tags AS tg 
					WHERE a.id=t.objectid AND t.tbl='answers' AND tg.id=t.tagid AND (tg.tag='".$tag."' OR tg.raw_tag='".$tag."' OR tg.alias='".$tag."')";

		$f_from = " FROM #__answers_questions AS a, #__tags_object AS t, #__tags AS tg 
					WHERE a.id=t.objectid AND t.tbl='answers' AND tg.id=t.tagid AND tg.id IN ($ids)";
		
		$f_from = " FROM #__answers_questions AS a, #__tags_object AS t INNER JOIN #__tags AS tg ON (t.tagid = tg.id)
					WHERE a.id=t.objectid AND t.tbl='answers' AND tg.id=t.tagid AND tg.id IN ($ids)";*/
		$f_from  = " FROM #__answers_questions AS a, #__tags_object AS t WHERE a.id=t.objectid AND t.tbl='answers' AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.id HAVING uniques=".count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort) 
		{
			case 'title': $order_by .= 'title ASC, created';    break;
			case 'id':    $order_by .= "id DESC";               break;
			case 'date':  
			default:      $order_by .= 'a.created DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

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
					$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id='.$row->id);
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
	}*/
	
	//-----------

	public function out( $row ) 
	{
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
		
		$html  = "\t".'<li class="resource">'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		$html .= "\t\t".'<p class="details">';
		if ($row->state == 1) {
			$html .= JText::_('PLG_TAGS_ANSWERS_OPEN');
		} else {
			$html .= JText::_('PLG_TAGS_ANSWERS_CLOSED');
		}
		$html .= ' <span>|</span> '.JText::_('PLG_TAGS_ANSWERS_RESPONSES').' '.$row->rcount .'</p>'."\n";
		if ($row->text) {
			//$row->text = strip_tags($row->text);
			$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->text)), 200)."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		return $html;
	}
	
	//-----------
	
	/*public function after()
	{
		// ...
	}*/
}
