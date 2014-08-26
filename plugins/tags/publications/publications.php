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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Tags plugin class for publications
 */
class plgTagsPublications extends JPlugin
{
	/**
	 * Publication areas
	 *
	 * @var array
	 */
	private $_areas = null;

	/**
	 * Publication categories
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

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_publications' . DS . 'tables' . DS . 'category.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
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
			$database = JFactory::getDBO();
			$rt = new PublicationCategory($database);
			$categories = $rt->getCategories();
			$this->_cats = $categories;
		}

		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$cats[$categories[$i]->name] = $categories[$i]->name;
		}

		$areas = array(
			'publications' => $cats
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
			if (!isset($areas['publications']) && !in_array('publications', $areas))
			{
				return array();
			}
		}

		// Do we have any tags?
		if (empty($tags))
		{
			return NULL;
		}

		$database = JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}

		// Instantiate some needed objects
		$rr = new Publication($database);

		// Build query
		$filters = array();
		$filters['tags'] = $ids;
		$filters['now'] = date('Y-m-d H:i:s', time() + 0 * 60 * 60);
		$filters['sortby'] = ($sort) ? $sort : 'ranking';
		$filters['authorized'] = false;

		$juser = JFactory::getUser();
		$filters['usergroups'] = \Hubzero\User\Helper::getGroups($juser->get('id'), 'all');

		// Get categories
		$categories = $this->_cats;
		if (!is_array($categories))
		{
			$rt = new PublicationCategory($database);
			$categories = $rt->getCategories();
		}

		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$cats[$categories[$i]->name] = array();
			$cats[$categories[$i]->name]['id'] = $categories[$i]->id;
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
			if (count($areas) == 1 && !isset($areas['publications']) && $areas[0] != 'publications')
			{
				$filters['category'] = $cats[$areas[0]]['id'];
			}

			// Get results
			$query = $this->_buildPluginQuery($filters);

			if (count($areas) > 1)
			{
				plgTagsPublications::documents();
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
					$rows[$key]->href = JRoute::_('index.php?option=com_publications&id=' . $row->id);
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
						$filters['category'] = $cats[$a]['id'];

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
		$database = JFactory::getDBO();
		$juser = JFactory::getUser();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'category.php');
		$rt = new PublicationCategory($database);

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
			$query = "SELECT DISTINCT r.id, V.title, V.version_number as alias,
					V.abstract as itext, V.id as ftext, V.state AS state, V.created, V.created_by,
					V.modified, V.published_up as publish_up, V.published_down as publish_down,
					CONCAT('index.php?option=com_publications&id=', r.id) AS href, 'publications' AS section ";
			if (isset($filters['tags']))
			{
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			$query .= ", V.params, r.rating AS rcount, r.category AS data1, rt.name AS data2, r.ranking data3 ";
		}
		$query .= "FROM #__publication_versions as V, #__publications AS r ";
		$query .= "LEFT JOIN " . $rt->getTableName() . " AS rt ON r.category=rt.id";
		if (isset($filters['tag']))
		{
			$query .= ", #__tags_object AS t, #__tags AS tg ";
		}
		if (isset($filters['tags']))
		{
			$query .= ", #__tags_object AS t ";
		}
		$query .= "WHERE V.publication_id=r.id AND V.state=1 AND V.main = 1 ";
		if (isset($filters['tag']))
		{
			$query .= "AND t.objectid=r.id AND t.tbl='publications' AND t.tagid=tg.id AND (tg.tag='" . $filters['tag'] . "' OR tg.alias='" . $filters['tag'] . "') ";
		}
		if (isset($filters['tags']))
		{
			$ids = implode(',', $filters['tags']);
			$query .= "AND t.objectid=r.id AND t.tbl='publications' AND t.tagid IN (" . $ids . ") ";
		}
		if (isset($filters['category']) && $filters['category'] != '') {
			$query .= "AND r.category=" . $filters['category'] . " ";
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
	public static function documents()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'helper.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_publications' . DS . 'tables' . DS . 'author.php');
	}

	/**
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$database = JFactory::getDBO();

		// Instantiate a helper object
		$helper = new PublicationHelper($database, $row->ftext, $row->id);

		// Get version authors
		$pa = new PublicationAuthor( $database );
		$authors = $pa->getAuthors($row->ftext);

		// Get the component params
		$config = JComponentHelper::getParams('com_publications');

		$row->rating   = $row->rcount;
		$row->category = $row->data1;
		$row->area     = $row->data2;
		$row->ranking  = $row->data3;

		// Set the display date
		switch ($config->get('show_date'))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = JHTML::_('date', $row->created, JText::_('DATE_FORMAT_HZ1'));    break;
			case 2: $thedate = JHTML::_('date', $row->modified, JText::_('DATE_FORMAT_HZ1'));    break;
			case 3: $thedate = JHTML::_('date', $row->publish_up, JText::_('DATE_FORMAT_HZ1'));    break;
		}

		if (strstr($row->href, 'index.php'))
		{
			$row->href = JRoute::_($row->href);
		}
		$juri = JURI::getInstance();

		// Start building the HTML
		$html  = "\t".'<li class="';
		$html .= 'publication">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '/?v=' . $row->alias . '">'
			. stripslashes($row->title) . '</a></p>' . "\n";

		$html .= "\t\t" . '<p class="details">' . $thedate . ' <span>|</span> ' . $row->area;
		if ($helper->contributors)
		{
			$html .= ' <span>|</span> ' . JText::_('PLG_TAGS_PUBLICATIONS_CONTRIBUTORS')
				. ' ' . stripslashes($helper->showContributors( $authors, true, false ));
		}
		$html .= '</p>' . "\n";
		if ($row->itext)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->itext)), 200) . '</p>' . "\n";
		}

		$html .= "\t\t" . '<p class="href">' . $juri->base() . trim($row->href . '/?v=' . $row->alias, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>'."\n";

		// Return output
		return $html;

	}
}
