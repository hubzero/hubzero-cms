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
 * Members Plugin class for resources
 */
class plgMembersResources extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

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
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
	}

	/**
	 * Return a list of categories
	 *
	 * @return     array
	 */
	public function onMembersContributionsAreas()
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
			$rt = new ResourcesType($database);
			$categories = $rt->getMajorTypes();
			$this->_cats = $categories;
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $categories[$i]->type);
			$normalized = strtolower($normalized);

			$cats[$normalized] = $categories[$i]->type;
		}

		$areas = array(
			'resources' => $cats
		);
		$this->_areas = $areas;
		return $areas;
	}

	/**
	 * Build SQL for returning the count of the number of contributions
	 *
	 * @param      string $user_id  Field to join on user ID
	 * @param      string $username Field to join on username
	 * @return     string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		$query = "SELECT COUNT(R.id) FROM #__resources AS R, #__author_assoc AS AA WHERE AA.authorid=" . $user_id . " AND R.id = AA.subid AND AA.subtable = 'resources' AND R.published=1 AND R.standalone=1";
		return $query;
	}

	/**
	 * Return either a count or an array of the member's contributions
	 *
	 * @param      object  $member     Current member
	 * @param      string  $option     Component name
	 * @param      string  $authorized Authorization level
	 * @param      integer $limit      Number of record to return
	 * @param      integer $limitstart Record return start
	 * @param      string  $sort       Field to sort records on
	 * @param      array   $areas      Areas to return data for
	 * @return     array
	 */
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
		$database = JFactory::getDBO();

		if (is_array($areas) && $limit)
		{
			$ars = $this->onMembersContributionsAreas();
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas)
			 && !array_intersect($areas, array_keys($ars['resources'])))

			{
				return array();
			}
		}

		// Do we have a member ID?
		if ($member instanceof \Hubzero\User\Profile)
		{
			if (!$member->get('uidNumber'))
			{
				return array();
			}
			else
			{
				$uidNumber = $member->get('uidNumber');
			}
		}
		else
		{
			if (!$member->uidNumber)
			{
				return array();
			}
			else
			{
				$uidNumber = $member->uidNumber;
			}
		}

		// Instantiate some needed objects
		$rr = new ResourcesResource($database);

		// Build query
		$filters = array();
		$filters['author'] = $uidNumber;
		$filters['sortby'] = $sort;
		//$filters['authorized'] = $authorized;

		//$filters['usergroups'] = \Hubzero\User\Helper::getGroups($uidNumber, 'all');
		$filters['usergroups'] = $member->getGroups('all');

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
		$normalized_valid_chars = 'a-zA-Z0-9';
		for ($i = 0; $i < count($categories); $i++)
		{
			$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $categories[$i]->type);
			$normalized = strtolower($normalized);

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
			}
			if ($total == 0)
			{
				return array();
			}

			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			//if (count($areas) == 1 && key($areas[0]) != 'resources') {
			if (count($areas) == 1 && !isset($areas['resources']))
			{
				$filters['type'] = (isset($cats[$areas[0]])) ? $cats[$areas[0]]['id'] : 0;
			}

			// Get results
			$database->setQuery($rr->buildPluginQuery($filters));
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
			$ares = $this->onMembersContributionsAreas();
			foreach ($ares as $area => $val)
			{
				if (is_array($val))
				{
					$i = 0;
					foreach ($val as $a => $t)
					{
						if ($limitstart == -1)
						{
							if ($i == 0)
							{
								$database->setQuery($rr->buildPluginQuery($filters));
								$counts[] = $database->loadResult();
							}
							else
							{
								$counts[] = 0;
							}
						}
						else
						{
							$filters['type'] = $cats[$a]['id'];

							// Execute a count query for each area/category
							$database->setQuery($rr->buildPluginQuery($filters));
							$counts[] = $database->loadResult();
						}
						$i++;
					}
				}
			}

			// Return the counts
			$this->_total = $counts;
			return $counts;
		}
	}

	/**
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$authorized = false;
		$juser = JFactory::getUser();
		if ($juser->authorize('com_resources', 'manage'))
		{
			$authorized = true;
		}
		$database = JFactory::getDBO();

		// Instantiate a helper object
		$helper = new ResourcesHelper($row->id, $database);
		$helper->getContributors();

		// Get the component params and merge with resource params
		$config = JComponentHelper::getParams('com_resources');

		$rparams = new JRegistry($row->params);
		$params = $config;
		$params->merge($rparams);

		// Set the display date
		switch ($params->get('show_date'))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = JHTML::_('date', $row->created, 'd M Y');    break;
			case 2: $thedate = JHTML::_('date', $row->modified, 'd M Y');   break;
			case 3: $thedate = JHTML::_('date', $row->publish_up, 'd M Y'); break;
		}

		$html  = "\t" . '<li class="';
		switch ($row->access)
		{
			case 1: $html .= 'registered'; break;
			case 2: $html .= 'special';    break;
			case 3: $html .= 'protected';  break;
			case 4: $html .= 'private';    break;
			case 0:
			default: $html .= 'public'; break;
		}
		$html .= ' resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a>';
		if ($authorized || $row->created_by == $juser->get('id'))
		{
			switch ($row->state)
			{
				case 5: $html .= ' <span class="resource-status internal">' . JText::_('PLG_MEMBERS_RESOURCES_STATUS_PENDING_INTERNAL') . '</span>'; break;
				case 4: $html .= ' <span class="resource-status deleted">' . JText::_('PLG_MEMBERS_RESOURCES_STATUS_DELETED') . '</span>'; break;
				case 3: $html .= ' <span class="resource-status pending">' . JText::_('PLG_MEMBERS_RESOURCES_STATUS_PENDING') . '</span>'; break;
				case 2: $html .= ' <span class="resource-status draft">' . JText::_('PLG_MEMBERS_RESOURCES_STATUS_DRAFT') . '</span>'; break;
				case 1: $html .= ' <span class="resource-status published">' . JText::_('PLG_MEMBERS_RESOURCES_STATUS_PUBLISHED') . '</span>'; break;
				case 0:
				default: $html .= ' <span class="resource-status unpublished">' . JText::_('PLG_MEMBERS_RESOURCES_STATUS_UNPUBLISHED') . '</span>'; break;
			}
		}
		$html .= '</p>' . "\n";
		if ($params->get('show_ranking'))
		{
			$helper->getCitationsCount();
			$helper->getLastCitationDate();

			if ($row->category == 7)
			{
				$stats = new ToolStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			}
			else
			{
				$stats = new AndmoreStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			}
			$statshtml = $stats->display();

			$row->ranking = round($row->ranking, 1);

			$html .= "\t\t".'<div class="metadata">'."\n";

			$r = (10*$row->ranking);
			if (intval($r) < 10)
			{
				$r = '0' . $r;
			}

			$html .= "\t\t\t" . '<dl class="rankinfo">' . "\n";
			$html .= "\t\t\t\t" . '<dt class="ranking"><span class="rank-' . $r . '">' . JText::_('PLG_MEMBERS_RESOURCES_THIS_HAS') . '</span> ' . number_format($row->ranking, 1) . ' ' . JText::_('PLG_MEMBERS_RESOURCES_RANKING') . '</dt>' . "\n";
			$html .= "\t\t\t\t" . '<dd>' . "\n";
			$html .= "\t\t\t\t\t" . '<p>' . JText::_('PLG_MEMBERS_RESOURCES_RANKING_EXPLANATION') . '</p>' . "\n";
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
			$html .= "\t\t\t" . '<p class="rating"><span class="avgrating' . $class . '"><span>' . JText::sprintf('PLG_MEMBERS_RESOURCES_OUT_OF_5_STARS', $row->rating) . '</span>&nbsp;</span></p>' . "\n";
			$html .= "\t\t" . '</div>' . "\n";
		}
		$html .= "\t\t".'<p class="details">' . $thedate . ' <span>|</span> ' . stripslashes($row->area);
		if ($helper->contributors)
		{
			$html .= ' <span>|</span> ' . JText::_('PLG_MEMBERS_RESOURCES_CONTRIBUTORS') . ': ' . $helper->contributors;
		}
		$html .= '</p>' . "\n";
		if ($row->itext)
		{
			$html .= \Hubzero\Utility\String::truncate(stripslashes($row->itext))."\n";
		}
		else if ($row->ftext)
		{
			$html .= \Hubzero\Utility\String::truncate(stripslashes($row->ftext))."\n";
		}
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @return     void
	 */
	public static function documents()
	{
		// Push some CSS and JS to the tmeplate that may be needed
		\Hubzero\Document\Assets::addComponentStylesheet('com_resources');

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'usage.php');
	}
}
