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

/**
 * Tags plugin class for resources
 */
class plgTagsResources extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

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

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
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
		$response = array(
			'name'     => $this->_name,
			'title'    => JText::_('PLG_TAGS_RESOURCES'),
			'total'    => 0,
			'results'  => null,
			'sql'      => '',
			'children' => array()
		);

		$database = JFactory::getDBO();
		$rt = new ResourcesType($database);
		foreach ($rt->getMajorTypes() as $category)
		{
			$response['children'][$category->alias] = array(
				'name'     => $category->alias,
				'title'    => $category->type,
				'total'    => 0,
				'results'  => null,
				'sql'      => '',
				'id'       => $category->id
			);
		}

		if (empty($tags))
		{
			return $response;
		}

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
		$filters['now'] = JFactory::getDate()->toSql();
		$filters['sortby'] = ($sort) ? $sort : 'ranking';
		$filters['authorized'] = false;

		$juser = JFactory::getUser();
		$filters['usergroups'] = \Hubzero\User\Helper::getGroups($juser->get('id'), 'all');

		$filters['select'] = 'count';

		foreach ($response['children'] as $k => $t)
		{
			$filters['type'] = $t['id'];

			// Execute a count query for each area/category
			$database->setQuery($this->_buildPluginQuery($filters));
			$response['children'][$k]['total'] = $database->loadResult();
			$response['total'] += $response['children'][$k]['total'];
		}

		if ($areas && ($areas == $response['name']) || isset($response['children'][$areas]))
		{
			// Push some CSS and JS to the tmeplate that may be needed
			\Hubzero\Document\Assets::addComponentStylesheet('com_resources');

			$filters['select']     = 'records';
			$filters['limit']      = $limit;
			$filters['limitstart'] = $limitstart;
			$filters['sortby']     = ($sort) ? $sort : 'date';

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (isset($response['children'][$areas]))
			{
				$filters['type'] = $response['children'][$areas]['id'];

				$database->setQuery($this->_buildPluginQuery($filters));
				$response['children'][$areas]['results'] = $database->loadObjectList();
			}
			else
			{
				unset($filters['type']);

				$database->setQuery($this->_buildPluginQuery($filters));
				$response['results'] = $database->loadObjectList();
			}
		}
		else
		{
			$filters['select']     = 'records';
			$filters['limit']      = 'all';
			$filters['limitstart'] = $limitstart;
			$filters['sortby']     = ($sort) ? $sort : 'date';

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (isset($response['children'][$areas]))
			{
				$filters['type'] = $response['children'][$areas]['id'];

				$response['children'][$key]['sql'] = $this->_buildPluginQuery($filters);
			}
			else
			{
				unset($filters['type']);
				$response['sql'] = $this->_buildPluginQuery($filters);
			}
		}

		return $response;
	}

	/**
	 * Build a database query
	 *
	 * @param      array $filters Options for building the query
	 * @return     string SQL
	 */
	private function _buildPluginQuery($filters=array())
	{
		$database = JFactory::getDBO();
		$juser = JFactory::getUser();

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
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'usage.php');

		if ($row->alias)
		{
			$row->href = JRoute::_('index.php?option=com_resources&alias=' . $row->alias);
		}
		else
		{
			$row->href = JRoute::_('index.php?option=com_resources&id=' . $row->id);
		}

		$database = JFactory::getDBO();

		// Instantiate a helper object
		$helper = new ResourcesHelper($row->id, $database);
		$helper->getContributors();

		// Get the component params and merge with resource params
		$config = JComponentHelper::getParams('com_resources');

		$rparams = new JRegistry($row->params);
		//$params = $config;
		//$params->merge($rparams);

		$row->rating   = $row->rcount;
		$row->category = $row->data1;
		$row->area     = $row->data2;
		$row->ranking  = $row->data3;

		// Set the display date
		switch ($rparams->get('show_date', $config->get('show_date', 3)))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = JHTML::_('date', $row->created, JText::_('DATE_FORMAT_HZ1'));    break;
			case 2: $thedate = JHTML::_('date', $row->modified, JText::_('DATE_FORMAT_HZ1'));   break;
			case 3: $thedate = JHTML::_('date', $row->publish_up, JText::_('DATE_FORMAT_HZ1')); break;
		}

		if (strstr($row->href, 'index.php'))
		{
			$row->href = JRoute::_($row->href);
		}
		$juri = JURI::getInstance();

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
		if ($rparams->get('show_ranking', $config->get('show_ranking')))
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
		elseif ($rparams->get('show_rating', $config->get('show_rating')))
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
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->itext)), 200) . '</p>' . "\n";
		}
		else if ($row->ftext)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->ftext)), 200) . '</p>' . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . $juri->base() . trim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>'."\n";

		// Return output
		return $html;
	}
}
