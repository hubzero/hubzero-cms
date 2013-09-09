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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Tags plugin class for resources
 */
class plgTagsResources extends JPlugin
{
	/**
	 * Resource areas
	 * 
	 * @var array
	 */
	private $_areas = null;

	/**
	 * Resource categories
	 * 
	 * @var array
	 */
	private $_cats  = null;

	/**
	 * Record count
	 * 
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Constructor
	 * 
	 * @param      object &$subject The object to observe
	 * @param      array  $config   An optional associative array of configuration settings.
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
	}

	/**
	 * Return the name of the area this plugin retrieves records for
	 * 
	 * @return     array
	 */
	public function onTagAreas()
	{
		$areas = $this->_areas;
		if (is_array($areas)) 
		{
			return $areas;
		}

		$categories = $this->_cats;
		if (!is_array($categories)) 
		{
			// Get categories
			$database =& JFactory::getDBO();
			$rt = new ResourcesType($database);
			$categories = $rt->getMajorTypes();
			$this->_cats = $categories;
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($categories[$i]->type));

			//$categories[$i]->title = $normalized;
			$cats[$normalized] = $categories[$i]->type;
		}

		$areas = array(
			'resources' => $cats
		);
		$this->_areas = $areas;
		return $areas;
	}

	/**
	 * Retrieve records for items tagged with specific tags
	 * 
	 * @param      array   $tags       Tags to match records against
	 * @param      mixed   $limit      SQL record limit
	 * @param      integer $limitstart SQL record limit start
	 * @param      string  $sort       The field to sort records by
	 * @param      mixed   $areas      An array or string of areas that should retrieve records
	 * @return     mixed Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit) 
		{
			$ars = $this->onTagAreas();
			if (!array_intersect($areas, $ars)
			 && !array_intersect($areas, array_keys($ars))
			 && !array_intersect($areas, array_keys($ars['resources']))) 
			{
				return array();
			}
		}

		// Do we have any tags?
		if (empty($tags)) 
		{
			return array();
		}

		$database =& JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}

		// Instantiate some needed objects
		$rr = new ResourcesResource($database);

		// Build query
		$filters = array();
		$filters['tags'] = $ids;
		$filters['now'] = date('Y-m-d H:i:s', time() + 0 * 60 * 60);
		$filters['sortby'] = ($sort) ? $sort : 'ranking';
		$filters['authorized'] = false;

		ximport('Hubzero_User_Helper');
		$juser =& JFactory::getUser();
		$filters['usergroups'] = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');

		// Get categories
		$categories = $this->_cats;
		if (!is_array($categories)) 
		{
			$rt = new ResourcesType($database);
			$categories = $rt->getMajorTypes();
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($categories[$i]->type));

			$cats[$normalized] = array();
			$cats[$normalized]['id'] = $categories[$i]->id;
		}

		if ($limit) 
		{
			if ($this->_total != null) 
			{
				$total = 0;
				$t = $this->_total;
				foreach ($t as $l)
				{
					$total += $l;
				}

				if (count($areas) <= 1 && $total == 0) 
				{
					return array();
				}
			}

			$filters['select'] = 'records';
			$filters['limit'] = (count($areas) > 1) ? 'all' : $limit;
			$filters['limitstart'] = $limitstart;
			$filters['sortby'] = ($sort) ? $sort : 'date';

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (count($areas) == 1 && !isset($areas['resources']) && $areas[0] != 'resources') 
			{
				$filters['type'] = $cats[$areas[0]]['id'];
			}

			// Get results
			$query = $this->_buildPluginQuery($filters);
			if (count($areas) > 1) 
			{
				plgTagsResources::documents();
				return $query;
			}

			// Get results
			$database->setQuery($query);
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows) 
			{
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					if ($row->alias) 
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_resources&alias=' . $row->alias);
					} 
					else 
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_resources&id=' . $row->id);
					}
				}
			}

			// Return the results
			return $rows;
		} 
		else 
		{
			$filters['select'] = 'count';

			// Get a count
			$counts = array();
			$ares = $this->onTagAreas();
			foreach ($ares as $area=>$val)
			{
				if (is_array($val)) 
				{
					foreach ($val as $a => $t)
					{
						$filters['type'] = $cats[$a]['id'];

						// Execute a count query for each area/category
						$database->setQuery($this->_buildPluginQuery($filters));
						$counts[] = $database->loadResult();
					}
				}
			}

			// Return the counts
			$this->_total = $counts;
			return $counts;
		}
	}

	/**
	 * Build a database query
	 * 
	 * @param      array $filters Options for building the query
	 * @return     string SQL
	 */
	private function _buildPluginQuery($filters=array())
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
		$rt = new ResourcesType($database);

		if (isset($filters['select']) && $filters['select'] == 'count') 
		{
			if (isset($filters['tags'])) 
			{
				$query = "SELECT count(f.id) FROM (SELECT r.id, COUNT(DISTINCT t.tagid) AS uniques ";
			} 
			else 
			{
				$query = "SELECT count(DISTINCT r.id) ";
			}
		} 
		else 
		{
			$query = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext AS itext, r.fulltxt AS ftext, r.published AS state, r.created, r.created_by, r.modified, r.publish_up, r.publish_down,  
					CONCAT('index.php?option=com_resources&id=', r.id) AS href, 'resources' AS section ";
			if (isset($filters['tags'])) 
			{
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			$query .= ", r.params, r.rating AS rcount, r.type AS data1, rt.type AS data2, r.ranking data3 ";
		}
		$query .= "FROM #__resources AS r ";
		$query .= "LEFT JOIN " . $rt->getTableName() . " AS rt ON r.type=rt.id ";
		if (isset($filters['tag'])) 
		{
			$query .= ", #__tags_object AS t, #__tags AS tg ";
		}
		if (isset($filters['tags'])) 
		{
			$query .= ", #__tags_object AS t ";
		}
		$query .= "WHERE r.standalone=1 ";
		if ($juser->get('guest') || (isset($filters['authorized']) && !$filters['authorized'])) 
		{
			$query .= "AND r.published=1 AND r.access<4 ";
		}
		if (isset($filters['tag'])) 
		{
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid=tg.id AND (tg.tag='" . $filters['tag'] . "' OR tg.alias='" . $filters['tag'] . "') ";
		}
		if (isset($filters['tags'])) 
		{
			$ids = implode(',', $filters['tags']);
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid IN (" . $ids . ") ";
		}
		if (isset($filters['type']) && $filters['type'] != '') {
			$query .= "AND r.type=" . $filters['type'] . " ";
		}

		if (isset($filters['tags'])) 
		{
			$query .= " GROUP BY r.id HAVING uniques=" . count($filters['tags']) . " ";
		}
		if (isset($filters['select']) && $filters['select'] != 'count') 
		{
			if (isset($filters['sortby'])) 
			{
				if (isset($filters['groupby'])) 
				{
					$query .= "GROUP BY r.id ";
				}
				$query .= "ORDER BY ";
				switch ($filters['sortby'])
				{
					case 'date':    $query .= 'publish_up DESC';               break;
					case 'title':   $query .= 'title ASC, publish_up DESC';    break;
					case 'rating':  $query .= "rating DESC, times_rated DESC"; break;
					case 'ranking': $query .= "ranking DESC";                  break;
					case 'relevance': $query .= "relevance DESC";              break;
					case 'users':
					case 'usage':   $query .= "users DESC";                    break;
					case 'jobs':    $query .= "jobs DESC";                     break;
				}
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all') 
			{
				$query .= " LIMIT " . $filters['limitstart'] . "," . $filters['limit'];
			}
		}
		if (isset($filters['select']) && $filters['select'] == 'count') 
		{
			if (isset($filters['tags'])) 
			{
				$query .= ") AS f";
			}
		}

		return $query;
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 * 
	 * @return     void
	 */
	public function documents()
	{
		// Push some CSS and JS to the tmeplate that may be needed
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_resources');

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'usage.php');
	}

	/**
	 * Static method for formatting results
	 * 
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public function out($row)
	{
		$database =& JFactory::getDBO();

		// Instantiate a helper object
		$helper = new ResourcesHelper($row->id, $database);
		$helper->getContributors();

		// Get the component params and merge with resource params
		$config =& JComponentHelper::getParams('com_resources');
		$paramClass = 'JParameter';
		$dformat = '%d %b %Y';
		$tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramClass = 'JRegistry';
			$dformat = 'd M Y';
			$tz = true;
		}
		$rparams = new $paramClass($row->params);
		$params = $config;
		$params->merge($rparams);

		$row->rating = $row->rcount;
		$row->category = $row->data1;
		$row->area = $row->data2;
		$row->ranking = $row->data3;

		// Set the display date
		switch ($params->get('show_date'))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = JHTML::_('date', $row->created, $dformat, $tz);    break;
			case 2: $thedate = JHTML::_('date', $row->modified, $dformat, $tz);   break;
			case 3: $thedate = JHTML::_('date', $row->publish_up, $dformat, $tz); break;
		}

		if (strstr($row->href, 'index.php')) 
		{
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();

		// Start building the HTML
		$html  = "\t".'<li class="';
		/*switch ($row->access)
		{
			case 1: $html .= 'registered '; break;
			case 2: $html .= 'special ';    break;
			case 3: $html .= 'protected ';  break;
			case 4: $html .= 'private ';    break;
			case 0:
			default: $html .= 'public '; break;
		}*/
		$html .= 'resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		if ($params->get('show_ranking')) 
		{
			$helper->getCitationsCount();
			$helper->getLastCitationDate();

			if ($row->category == 'Tools') 
			{
				$stats = new ToolStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			} 
			else 
			{
				$stats = new AndmoreStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			}
			$statshtml = $stats->display();

			$row->ranking = round($row->ranking, 1);

			$html .= "\t\t" . '<div class="metadata">' . "\n";
			$r = (10*$row->ranking);
			if (intval($r) < 10) 
			{
				$r = '0' . $r;
			}
			$html .= "\t\t\t" . '<dl class="rankinfo">' . "\n";
			$html .= "\t\t\t\t" . '<dt class="ranking"><span class="rank-' . $r . '">' . JText::_('PLG_TAGS_RESOURCES_THIS_HAS') . '</span> ' . number_format($row->ranking, 1) . ' ' . JText::_('PLG_TAGS_RESOURCES_RANKING') . '</dt>' . "\n";
			$html .= "\t\t\t\t" . '<dd>' . "\n";
			$html .= "\t\t\t\t\t" . '<p>' . JText::_('PLG_TAGS_RESOURCES_RANKING_EXPLANATION') . '</p>' . "\n";
			$html .= "\t\t\t\t\t" . '<div>' . "\n";
			$html .= $statshtml;
			$html .= "\t\t\t\t\t" . '</div>' . "\n";
			$html .= "\t\t\t\t" . '</dd>' . "\n";
			$html .= "\t\t\t" . '</dl>' . "\n";
			$html .= "\t\t" . '</div>' . "\n";
		} 
		elseif ($params->get('show_rating')) 
		{
			switch ($row->rating)
			{
				case 0.5: $class = ' half-stars';      break;
				case 1:   $class = ' one-stars';       break;
				case 1.5: $class = ' onehalf-stars';   break;
				case 2:   $class = ' two-stars';       break;
				case 2.5: $class = ' twohalf-stars';   break;
				case 3:   $class = ' three-stars';     break;
				case 3.5: $class = ' threehalf-stars'; break;
				case 4:   $class = ' four-stars';      break;
				case 4.5: $class = ' fourhalf-stars';  break;
				case 5:   $class = ' five-stars';      break;
				case 0:
				default:  $class = ' no-stars';      break;
			}

			$html .= "\t\t" . '<div class="metadata">' . "\n";
			$html .= "\t\t\t" . '<p class="rating"><span class="avgrating' . $class . '"><span>' . JText::sprintf('PLG_TAGS_RESOURCES_OUT_OF_5_STARS', $row->rating) . '</span>&nbsp;</span></p>' . "\n";
			$html .= "\t\t" . '</div>'."\n";
		}
		$html .= "\t\t" . '<p class="details">' . $thedate . ' <span>|</span> ' . $row->area;
		if ($helper->contributors) 
		{
			$html .= ' <span>|</span> ' . JText::_('PLG_TAGS_RESOURCES_CONTRIBUTORS') . ' ' . stripslashes($helper->contributors);
		}
		$html .= '</p>' . "\n";
		if ($row->itext) 
		{
			$html .= "\t\t" . Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->itext)), 200) . "\n";
		} 
		else if ($row->ftext) 
		{
			$html .= "\t\t" . Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . $juri->base() . trim($row->href, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>'."\n";

		// Return output
		return $html;
	}
}
