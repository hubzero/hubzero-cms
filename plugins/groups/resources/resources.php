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

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');

/**
 * Groups Plugin class for resources
 */
class plgGroupsResources extends \Hubzero\Plugin\Plugin
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
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => 'resources',
			'title'            => JText::_('PLG_GROUPS_RESOURCES'),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f02d'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = 'resources';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		//set group members plugin access level
		$group_plugin_acl = $access[$active];

		//Create user object
		$juser = JFactory::getUser();

		//get the group members
		$members = $group->get('members');

		if ($return == 'html')
		{
			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest')
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$area = JRequest::getWord('area', 'resource');
				$url = JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active . '&area=' . $area);

				$this->redirect(
					JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($juser->get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			require_once JPATH_BASE . '/components/com_hubgraph/client.php';

			$hgConf = HubgraphConfiguration::instance();
			if ($hgConf->isOptionEnabled('com_groups'))
			{
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'   => $this->_type,
						'element'  => $this->_name,
						'name'     => 'results'
					)
				);
				// Pass the view some info
				$view->option = $option;
				$view->group  = $group;

				ob_start();
				$_GET['group'] = $group->gidNumber;
				define('HG_INLINE', 1);
				require JPATH_BASE . '/components/com_hubgraph/hubgraph.php';
				$view->hubgraphResponse = ob_get_clean();
				return array(
					'html' => $view->loadTemplate('hubgraph')
				);
			}
		}

		$database = JFactory::getDBO();
		$dispatcher = JDispatcher::getInstance();

		// Incoming paging vars
		$sort = JRequest::getVar('sort', 'date');
		$access = JRequest::getVar('access', 'all');

		$config = JComponentHelper::getParams('com_resources');
		if ($return == 'metadata')
		{
			if ($config->get('show_ranking'))
			{
				$sort = 'ranking';
			}
			elseif ($config->get('show_rating'))
			{
				$sort = 'rating';
			}
		}

		// Trigger the functions that return the areas we'll be using
		$rareas = $this->getResourcesAreas();

		// Get the active category
		$area = JRequest::getWord('area', 'resources');
		if ($area)
		{
			$activeareas = array($area);
		}
		else
		{
			$limit = 5;
			$activeareas = $rareas;
		}

		if ($return == 'metadata')
		{
			$ls = -1;
		}
		else
		{
			$ls = $limitstart;
		}

		// Get the search result totals
		$ts = $this->getResources(
			$group,
			$authorized,
			0,
			$ls,
			$sort,
			$access,
			$activeareas
		);
		$totals = array($ts);

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;
		$cats = array();
		foreach ($rareas as $c=>$t)
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t))
			{
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub']  = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s=>$st)
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i]) && !empty($totals[$i]) && isset($totals[$i][$z]))
					{
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title']    = $st;
						$cats[$i]['_sub'][$z]['total']    = $totals[$i][$z];
					}
					$z++;
				}
			}
			else
			{
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$total = $total + intval($cats[$i]['total']);
			$i++;
		}

		// Do we have an active area?
		if (count($activeareas) == 1 && !is_array(current($activeareas)))
		{
			$active = $activeareas[0];
		}
		else
		{
			$active = '';
		}

		// Get the search results
		$r = $this->getResources(
			$group,
			$authorized,
			$limit,
			$limitstart,
			$sort,
			$access,
			$activeareas
		);
		$results = array($r);

		// Build the output
		switch ($return)
		{
			case 'html':
				// Instantiate a vew
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  => $this->_type,
						'element' => $this->_name,
						'name'    => 'results'
					)
				);

				// Pass the view some info
				$view->option = $option;
				$view->group = $group;
				$view->authorized = $authorized;
				$view->totals = $totals;
				$view->results = $results;
				$view->cats = $cats;
				$view->active = $active;
				$view->limitstart = $limitstart;
				$view->limit = $limit;
				$view->total = $total;
				$view->sort = $sort;
				$view->access = $access;
				if ($this->getError())
				{
					foreach ($this->getErrors() as $error)
					{
						$view->setError($error);
					}
				}

				// Return the output
				$arr['metadata']['count'] = $total;
				$arr['html'] = $view->loadTemplate();
			break;

			case 'metadata':
				$arr['metadata']['count'] = $total;
			break;
		}

		// Return the output
		return $arr;
	}

	/**
	 * Remove any associated resources when group is deleted
	 *
	 * @param      object $group Group being deleted
	 * @return     string Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// Get all the IDs for resources associated with this group
		$ids = $this->getResourceIDs($group->get('cn'));

		// Start the log text
		$log = JText::_('PLG_GROUPS_RESOURCES_LOG') . ': ';
		if (count($ids) > 0)
		{
			$database = JFactory::getDBO();

			// Loop through all the IDs for resources associated with this group
			foreach ($ids as $id)
			{
				// Disassociate the resource from the group and unpublish it
				$rr = new ResourcesResource($database);
				$rr->load($id->id);
				$rr->group_owner = '';
				$rr->published = 0;
				$rr->store();

				// Add the page ID to the log
				$log .= $id->id . ' ' . "\n";
			}
		}
		else
		{
			$log .= JText::_('PLG_GROUPS_RESOURCES_NONE') . "\n";
		}

		// Return the log
		return $log;
	}

	/**
	 * Return a count of items that will be removed when group is deleted
	 *
	 * @param      object $group Group to delete
	 * @return     string
	 */
	public function onGroupDeleteCount($group)
	{
		return JText::_('PLG_GROUPS_RESOURCES_LOG') . ': ' . count($this->getResourceIDs($group->get('cn')));
	}

	/**
	 * Get a list of resource IDs associated with this group
	 *
	 * @param      string $gid Group alias
	 * @return     array
	 */
	private function getResourceIDs($gid=NULL)
	{
		if (!$gid)
		{
			return array();
		}
		$database = JFactory::getDBO();

		$rr = new ResourcesResource($database);

		$database->setQuery("SELECT id FROM ".$rr->getTableName()." AS r WHERE r.group_owner=".$database->quote($gid));
		return $database->loadObjectList();
	}

	/**
	 * Get a list of resource categories (types)
	 *
	 * @return     array
	 */
	public function getResourcesAreas()
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
	 * Retrieve records for items associated with this group
	 *
	 * @param      object  $group      Group that owns the records
	 * @param      unknown $authorized Authorization level
	 * @param      mixed   $limit      SQL record limit
	 * @param      integer $limitstart SQL record limit start
	 * @param      string  $sort       The field to sort records by
	 * @param      string  $access     Access level
	 * @param      mixed   $areas      An array or string of areas that should retrieve records
	 * @return     mixed Returns integer when counting records, array when retrieving records
	 */
	public function getResources($group, $authorized, $limit=0, $limitstart=0, $sort='date', $access='all', $areas=null)
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			$ars = $this->getResourcesAreas();
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas)
			 && !array_intersect($areas, array_keys($ars['resources'])))
			{
				return array();
			}
		}

		// Do we have a member ID?
		if (!$group->get('cn'))
		{
			return array();
		}

		$database = JFactory::getDBO();

		// Instantiate some needed objects
		$rr = new ResourcesResource($database);

		// Build query
		$filters = array();
		$filters['now'] = \JFactory::getDate()->toSql();
		$filters['sortby'] = $sort;
		$filters['group'] = $group->get('cn');
		$filters['access'] = $access;
		$filters['authorized'] = $authorized;
		$filters['state'] = array(1);

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
			$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $categories[$i]->type);
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
			if (count($areas) == 1 && !isset($areas['resources']) && $areas[0] != 'resources')
			{
				$filters['type'] = $cats[$areas[0]]['id'];
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
			$ares = $this->getResourcesAreas();
			foreach ($ares as $area=>$val)
			{
				if (is_array($val))
				{
					$i = 0;
					foreach ($val as $a=>$t)
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
}

