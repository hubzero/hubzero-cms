<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');

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
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => 'resources',
			'title'            => Lang::txt('PLG_GROUPS_RESOURCES'),
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
		$this->group = $group;
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

		//get the group members
		$members = $group->get('members');

		if ($return == 'html')
		{
			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if (User::isGuest()
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$area = Request::getWord('area', 'resource');
				$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active . '&area=' . $area);

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array(User::get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			if (file_exists(PATH_CORE . DS . 'components' . DS . 'com_search' . DS . 'models' . DS . 'hubgraph' . DS . 'client.php'))
			{
				require_once PATH_CORE . DS . 'components' . DS . 'com_search' . DS . 'models' . DS . 'hubgraph' . DS . 'client.php';

				$hgConf = \Components\Search\Models\Hubgraph\Configuration::instance();

				if ($hgConf->isOptionEnabled('com_groups'))
				{
					$view =$this->view('default', 'results');
					// Pass the view some info
					$view->option = $option;
					$view->group  = $group;

					Lang::load('com_search', PATH_CORE . DS . 'components' . DS . 'com_search' . DS . 'site');

					ob_start();
					$_GET['group'] = $group->gidNumber;
					Request::setVar('group', $group->gidNumber);
					define('HG_INLINE', 1);
					require PATH_CORE . DS . 'components' . DS . 'com_search' . DS . 'site' . DS . 'controllers' . DS . 'hubgraph.php';
					$controller = new \Components\Search\Site\Controllers\Hubgraph();
					$controller->execute();
					$controller->redirect();

					$view->hubgraphResponse = ob_get_clean();

					return array(
						'html' => $view->loadTemplate('hubgraph')
					);
				}
			}
		}

		$database = App::get('db');

		// Incoming paging vars
		$sort = Request::getVar('sort', 'date');
		if (!in_array($sort, array('date', 'title', 'ranking', 'rating')))
		{
			$sort = 'date';
		}
		$access = Request::getVar('access', 'all');
		if (!in_array($access, array('all', 'public', 'protected', 'private')))
		{
			$access = 'date';
		}

		$config = Component::params('com_resources');
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
		$area = Request::getWord('area', 'resources');
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
				// If we have a specific ID and we're a supergroup, serve a resource page inside supergroup template
				if (Request::getVar('id', Request::getVar('alias', null)) && $this->group->type == 3)
				{
					// Load neccesities for com_resources controller
					$lang = App::get('language');
					$lang->load('com_resources', Component::path('com_resources') . DS . 'site');
					require_once Component::path('com_resources') . DS .'models' . DS . 'resource.php';
					require_once Component::path('com_resources') . DS .'site' . DS . 'controllers' . DS . 'resources.php';
					require_once Component::path('com_resources') . DS .'helpers' . DS . 'helper.php';
					require_once Component::path('com_resources') . DS .'helpers' . DS . 'html.php';
					require_once Component::path('com_resources') . DS .'helpers' . DS . 'tags.php';
					require_once Component::path('com_tools') . DS . 'tables' . DS . 'tool.php';
					require_once Component::path('com_tools') . DS . 'tables' . DS . 'version.php';
					require_once Component::path('com_tools') . DS . 'tables' . DS . 'author.php';

					// Set the request up to make it look like a user made the request to the controller
					Request::setVar('task', 'view');
					Request::setVar('option', 'com_resources');
					Request::setVar('active', Request::get('tab_active', 'about'));
					// Add some extra variables to let the tab view know we need a different base url
					Request::setVar('tab_active_key', 'tab_active');
					Request::setVar('tab_base_url', 'index.php?option=' . $this->option . '&cn=' . $this->group->cn . '&active=resources');
					// Added a noview variable to indicate to the controller that we do not want it to try to display the view, simply build it
					Request::setVar('noview', 1);

					// Instantiate the controller and have it execute
					$newtest = new \Components\Resources\Site\Controllers\Resources(array('base_path'=>Component::path('com_resources') . DS . 'site'));
					$newtest->execute();

					// Set up the return for the plugin 'view'
					$arr['html'] = $newtest->view->loadTemplate();
					$arr['metadata']['count'] = $total;
				}
				else
				{
					// Instantiate a vew
					$view = $this->view('default', 'results');

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

					foreach ($this->getErrors() as $error)
					{
						$view->setError($error);
					}

					// Return the output
					$arr['metadata']['count'] = $total;
					$arr['html'] = $view->loadTemplate();
				}
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
		$log = Lang::txt('PLG_GROUPS_RESOURCES_LOG') . ': ';
		if (count($ids) > 0)
		{
			$database = App::get('db');

			// Loop through all the IDs for resources associated with this group
			foreach ($ids as $id)
			{
				// Disassociate the resource from the group and unpublish it
				$rr = new \Components\Resources\Tables\Resource($database);
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
			$log .= Lang::txt('PLG_GROUPS_RESOURCES_NONE') . "\n";
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
		return Lang::txt('PLG_GROUPS_RESOURCES_LOG') . ': ' . count($this->getResourceIDs($group->get('cn')));
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
		$database = App::get('db');

		$rr = new \Components\Resources\Tables\Resource($database);

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
			$database = App::get('db');
			$rt = new \Components\Resources\Tables\Type($database);
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

		$database = App::get('db');

		// Instantiate some needed objects
		$rr = new \Components\Resources\Tables\Resource($database);

		// Build query
		$filters = array();
		$filters['now'] = \Date::toSql();
		$filters['sortby'] = $sort;
		$filters['group'] = $group->get('cn');
		$filters['access'] = $access;
		$filters['authorized'] = $authorized;
		$filters['state'] = array(1);

		// Get categories
		$categories = $this->_cats;
		if (!is_array($categories))
		{
			$rt = new \Components\Resources\Tables\Type($database);
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
					if ($this->group->type == '3')
					{
						if ($row->alias)
						{
							$rows[$key]->href = Route::url('index.php?option=com_groups&cn=' . $this->group->cn . '&active=resources&alias=' . $row->alias);
						}
						else
						{
							$rows[$key]->href = Route::url('index.php?option=com_groups&cn=' . $this->group->cn . '&active=resources&id=' . $row->id);
						}
					}
					else
					{
						if ($row->alias)
						{
							$rows[$key]->href = Route::url('index.php?option=com_resources&alias=' . $row->alias);
						}
						else
						{
							$rows[$key]->href = Route::url('index.php?option=com_resources&id=' . $row->id);
						}
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

