<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';

use Components\Publications\Tables\Publication as Publicationtable;

/**
 * Groups Plugin class for publications
 */
class plgGroupsPublications extends \Hubzero\Plugin\Plugin
{
	
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically. Standard HUBzero plugin approach
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;
	
	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_groups';
	
	/**
	 * Store internal message
	 *
	 * @var	 array
	 */
	protected $_msg = null;
	
	/**
	 * Categories
	 *
	 * @var array
	 */
	private $_cats  = null;
	
	/**
	 * Count for record
	 *
	 * @var array
	 */
	protected $_total = null;
	
	/**
	 * Publications areas
	 *
	 * @var array
	 */
	private $_areas = null;
	
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
	 * Return the alias and name for this category of content name. changed from on ProjectAreas to onGroupAreas
	 *
	 * @return     array
	 */
	
	public function &onGroupAreas()
	{
		$area = array(
			'name'    => 'publications',
			'title'   => Lang::txt('PLG_GROUPS_PUBLICATIONS'),
			'default_access'   => $this->params->get('plugin_access', 'members'), //changed, line from resources.php, fixes default access error
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'    => 'f053'
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
		$active = 'publications';
		
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
			//if set to nobody make sure they cant access TODO
			// if ($group_plugin_acl == 'nobody')
			// {
			// 	$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
			// 	return $arr;
			// }
			
			//check if guest and force login if plugin access is registered or members
			// if (User::isGuest()
			//  && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			// {
			// 	$area = Request::getWord('area', 'publications');//changed from 'resources' to 'publications'
			// 	$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active . '&area=' . $area);
			
			// 	App::redirect(
			// 		Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
			// 		Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
			// 		'warning'
			// 	);
			// 	return;
			// }
			
			//check to see if user is member and plugin access requires members
			// if (!in_array(User::get('id'), $members)
			//  && $group_plugin_acl == 'members'
			//  && $authorized != 'admin')
			// {
			// 	$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
			// 	return $arr;
			// }
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
		
		$config = Component::params('com_publications');//changed from com_resources to com_publications
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
		$pareas = $this->getPublicationsAreas(); //changed from rareas to pareas and method called
		
		// Get the active category
		$area = Request::getWord('area', 'publications');//changed from resources to publications
		if ($area)
		{
			$activeareas = array($area);
		}
		else
		{
			$limit = 5;
			$activeareas = $pareas;
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
		$ts = $this->getPublications(
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
		foreach ($pareas as $c => $t)
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
				foreach ($t as $s => $st)
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
		
		// Get the search results THIS is where search the database r
		$r = $this->getPublications(//changed from getResources to getPublications
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
			// If we have a specific ID and we're a supergroup, serve a publication page inside supergroup template
			if (Request::getVar('id', Request::getVar('alias', null)) && $this->group->type == 3)
			{
				//Uncomment this section below for the not working yet-trying to just replicate the component method
				// //Load neccesities for com_publications controller
				$lang = App::get('language');
				$lang->load('com_publications', \Component::path('com_publications') . DS . 'site');
				require_once \Component::path('com_publications') . DS .'models' . DS . 'publication.php'; // Mirrors com_resources/models/entry.php
				require_once \Component::path('com_publications') . DS .'site' . DS . 'controllers' . DS . 'publications.php'; // Mirrors com_resources/site/controllers/resources.php
				require_once \Component::path('com_publications') . DS .'helpers' . DS . 'html.php'; // Mirrors com_resources/helpers/html.php
				// //require_once Component::path('com_publications') . DS .'helpers' . DS . 'utilities.php';//replaced helper.php with utilities.php
				// require_once Component::path('com_publications') . DS .'helpers' . DS . 'tags.php';
				
				// Set the request up to make it look like a user made the request to the controller
				Request::setVar('task', 'page');
				Request::setVar('option', 'com_publications');
				Request::setVar('active', Request::get('tab_active', 'about'));
				// Add some extra variables to let the tab view know we need a different base url
				Request::setVar('tab_active_key', 'tab_active');
				Request::setVar('tab_base_url', 'index.php?option=' . $this->option . '&cn=' . $this->group->cn . '&active=publications');
				// Added a noview variable to indicate to the controller that we do not want it to try to display the view, simply build it
				Request::setVar('noview', 1);
				
				// Instantiate the controller and have it execute
				$newtest = new \Components\Publications\Site\Controllers\Publications(array('base_path'=>\Component::path('com_publications') . DS . 'site'));
				$newtest->execute(); // This is leaving the group
				
				$arr['html'] = $newtest->view->loadTemplate();
				$arr['metadata']['count'] = $total;
				
				// $view = $this->view('result','results');
				// $view->option = $option;
				// $view->group = $group;
				// foreach ($results as $category){
				// 	$amt = count($category);
				// 	if ($amt > 0)
				// 	{
				// 		foreach ($category as $row)
				// 		{
				// 			if ($row->id ==Request::getVar('id', Request::getVar('alias', null))) {
				// 				$view->row=$row;
				// 			}
				// 		}
				// 	}
				// }
				// $view->selectedpub= Request::getVar('id', Request::getVar('alias', null)) ;
				// $view->sort = $sort;
				// $view->authorized = $authorized;
				// $view->access = $access;
				//
				// foreach ($this->getErrors() as $error)
				// {
				// 	$view->setError($error);
				// }
				//
				// $arr['metadata']['count'] = count($results[0]); // We need to clean this up - was $total, which should work
				// $arr['html'] = $view->loadTemplate();
				
			}
			else
			{
				// Instantiate a vew
				$view = $this->view('cards', 'results');
				
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
				$arr['metadata']['count'] = count($results[0]); // We need to clean this up - was $total, which should work
				$arr['html'] = $view->loadTemplate();
			}
			break;
			
			case 'metadata':
			$arr['metadata']['count'] = count($results[0]); // We need to clean this up - was $total, which should work
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
		$log = Lang::txt('PLG_GROUPS_PUBLICATIONS_LOG') . ': '; //changed from resources to publications
		if (count($ids) > 0)
		{
			$database = App::get('db');
			
			// Loop through all the IDs for resources associated with this group
			foreach ($ids as $id)
			{
				// Disassociate the resource from the group and unpublish it
				$rr = new \Components\Publications\Tables\Publication($database); //changed from \Components\Resources\Tables\Resource($database);
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
			$log .= Lang::txt('PLG_GROUPS_PUBLICATIONS_NONE') . "\n"; //changed from PLG_GROUPS_RESOURCES_NONE to PLG_GROUPS_RESOURCES_NONE
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
		return Lang::txt('PLG_GROUPS_PUBLICATIONS_LOG') . ': ' . count($this->getPublicationIDs($group->get('cn')));
	}
	
	/**
	 * Get a list of publication IDs associated with this group
	 *
	 * @param      string $gid Group alias
	 * @return     array
	 */
	private function getPublicationIDs($gid=null)
	{
		if (!$gid)
		{
			return array();
		}
		$database = App::get('db');
		
		$rr = new \Components\Publications\Tables\Publication($database);
		
		$database->setQuery("SELECT id FROM ".$rr->getTableName()." AS p WHERE p.group_owner=".$database->quote($gid));
		return $database->loadObjectList();
	}
	
	/**
	 * Get a list of Publications Areas
	 */
	public function getPublicationsAreas()
	{
		$areas = $this->_areas;
		if (is_array($areas))
		{
			return $areas;
		}
		//MAKING AN ARRAY TO PASS IN TO GET CATEGORIES, different way of doing things than resources
		$filters = array();
		$filters['limit']         = Request::getInt('limit', Config::get('list_limit'));
		$filters['start']         = Request::getInt('limitstart', 0);
		$filters['sortby']        = Request::getVar('sortby', 'title');
		$filters['sortdir']       = Request::getVar('sortdir', 'ASC');
		//$filters['project']       = $this->model->get('id');
		$filters['ignore_access'] = 1;
		$filters['dev']           = 1; // get dev versions
		$categories = $this->_cats;
		if (!is_array($categories))
		{
			// 	// Get categories
			// 	$database = App::get('db');
			// 	$rt = new \Components\Publications\Tables\Category($database);//changed from components\resources\tables\type
			// 	$categories = $rt->getCategories($filters);
			// 	$this->_cats = $categories;
		}
		
		// // Normalize the category names
		// // e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			// 	$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $categories[$i]->getCategory());
			// 	$normalized = strtolower($normalized);
			
			// 	//$categories[$i]->title = $normalized;
			$cats[$normalized] = $categories[$i]/*->type*/;
		}
		
		$areas = array(
			'publications' => $cats //changed from resources to publications
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
	public function getPublications($group, $authorized, $limit=0, $limitstart=0, $sort='date', $access='all', $areas=null)
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			$ars = $this->getPublicationsAreas(); //changed from getResourcesAreas to getPublicationAreas
			if (!isset($areas[$this->_name])
			&& !in_array($this->_name, $areas)
			&& !array_intersect($areas, array_keys($ars['publications'])))//changed from 'resources' to 'publications'
			{
				return array();
			}
		}
		// Do we have a member ID?
		if (!$group->get('cn'))
		{
			return array();
		}
		//access the database
		$database = App::get('db');
		//instantiate a table object
		$pubtable = new plgGroupsPublicationsTablePublication($database);
		//building a query to get the publications deemed by our search terms passed into this function
		$filters = array();//array that will contain our filters
		$filters['now'] = \Date::toSQL();
		$filters['sortby'] = $sort;
		$filters['group'] = $group->get('cn');
		$filters['access'] = $access;
		$filters['authorized'] = $authorized;
		$filters['state'] = array(1);
		//get categories of project
		
		$filters = array();
		$filters['limit']         = Request::getInt('limit', Config::get('list_limit'));
		$filters['start']         = Request::getInt('limitstart', 0);
		$filters['sortby']        = Request::getVar('sortby', 'title');
		$filters['sortdir']       = Request::getVar('sortdir', 'ASC');
		//$filters['project']       = $this->model->get('id');
		$filters['ignore_access'] = 1;
		$filters['dev']           = 1; // get dev versions
		$categories = $this->_cats;
		if (!is_array($categories))
		{
			// Get categories
			$database = App::get('db');
			$rt = new \Components\Publications\Tables\Category($database);//changed from components\resources\tables\type
			$categories = $rt->getCategories($filters);
			$this->_cats = $categories;
		}
		$cats = array();
		// for ($i = 0; $i < count($categories); $i++)
		// {
		// 	$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $categories[$i]->type);
		// 	$normalized = strtolower($normalized);
		// 	$cats[$normalized] = array();
		// 	$cats[$normalized]['id'] = $categories[$i]->id;
		// }
		
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
				// } CHANGED made below if statement included in above if statement
				if ($total == 0)
				{
					return array();
				}
			}
			
			
			$filters['group_owner'] = $group->get('gidNumber');
			$filters['sortby'] = 'title';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;
			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (count($areas) == 1 && !isset($areas['publications']) && $areas[0] != 'publications')
			{
				$filters['type'] = $cats[$areas[0]]['id'];
			}
			// Get results
			
			$rows = $pubtable->getRecords($filters);
			// Did we get any results?
			// print_r($rows);
	
			if ($rows)
			{
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					//if we were a supergroup, this would be different
					if ($this->group->type == '3')
					{
						if ($row->alias)
						{
							$rows[$key]->href = Route::url('index.php?option=com_groups&cn=' . $this->group->cn . '&active=publications&alias=' . $row->alias);
						}
						else
						{
							$rows[$key]->href = Route::url('index.php?option=com_groups&cn=' . $this->group->cn . '&active=publications&id=' . $row->id);
						}
					}
					else
					{
						if ($row->alias)
						{
							$rows[$key]->href = Route::url('index.php?option=com_publications&alias=' . $row->alias);
						}
						else //most common case
						{
							$rows[$key]->href = Route::url('index.php?option=com_publications&id=' . $row->id);
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
			$ares = $this->getPublicationsAreas();
			foreach ($ares as $area => $val)
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

class plgGroupsPublicationsTablePublication extends Publicationtable
{

public function getRecords($filters = array(), $admin = false)
	{
		$sql  = "SELECT V.*, C.id as id, C.category, C.project_id, C.access as master_access,
				C.checked_out, C.checked_out_time, C.rating as master_rating,
				C.group_owner, C.master_type, C.master_doi,
				C.ranking as master_ranking, C.times_rated as master_times_rated,
				C.alias, V.id as version_id, t.name AS cat_name, t.alias as cat_alias,
				t.url_alias as cat_url, PP.alias as project_alias, PP.title as project_title,
				PP.state as project_status, PP.private as project_private,
				PP.provisioned as project_provisioned, MT.alias as base, MT.params as type_params";
		$sql .= ", (SELECT vv.version_label FROM `#__publication_versions` as vv WHERE vv.publication_id=C.id AND vv.state=3 ORDER BY ID DESC LIMIT 1) AS dev_version_label ";

		$sql .= ", (SELECT COUNT(*) FROM `#__publication_versions` WHERE publication_id=C.id AND state!=3) AS versions ";

		// $sortby  = isset($filters['sortby']) ? $filters['sortby'] : 'title';

		// if ($sortby == 'popularity')
		// {
		// 	$sql .= ", (SELECT S.users FROM `#__publication_stats` AS S WHERE S.publication_id=C.id AND S.period=14 ORDER BY S.datetime DESC LIMIT 1) as stat ";
		// }

		// $sql .= (isset($filters['tag']) && $filters['tag'] != '') ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " : " ";
		$sql .= $this->buildQuery($filters, $admin);
		$start = isset($filters['start']) ? $filters['start'] : 0;
		$sql .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $start . ", " . $filters['limit'] : "";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build query
	 *
	 * @param      array 		$filters
	 * @return     query string
	 */
	public function buildQuery($filters = array(), $admin = false)
	{
		$now 		= Date::toSql();
		$groupby 	= ' GROUP BY C.id ';

		$project 		= isset($filters['project']) && intval($filters['project']) ? $filters['project'] : "";
		$dev 			= isset($filters['dev']) && $filters['dev'] == 1 ? 1 : 0;
		$projects 		= isset($filters['projects']) && !empty($filters['projects']) ? $filters['projects'] : array();
		$mine 			= isset($filters['mine']) && $filters['mine'] ? $filters['mine'] : 0;
		$sortby  		= isset($filters['sortby']) ? $filters['sortby'] : 'title';

		$query  = "FROM ";
		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			$query .= "`#__tags_object` AS RTA ";
			$query .= "INNER JOIN `#__tags` AS TA ON RTA.tagid = TA.id AND RTA.tbl='publications', ";
		}

		$query .= " `#__publication_versions` as V, `#__projects` as PP,
				  `#__publication_master_types` AS MT";
		if (isset($filters['author']) && intval($filters['author']))
		{
			$query .= ", `#__publication_authors` as A ";
		}
		$query .= ", `$this->_tbl` AS C ";

		$query .= "LEFT JOIN `#__publication_categories` AS t ON t.id=C.category ";
		$query .= " WHERE V.publication_id=C.id AND MT.id=C.master_type AND PP.id = C.project_id ";

		if ($dev)
		{
			// $query .= " AND V.main=1 ";
			// Not sure why this was included?.
			// It prevents publications with a draft from being listed to owners inside a project.

			if (isset($filters['status']) && $filters['status'] != 'all')
			{
				if (is_array($filters['status']))
				{
					$squery = '';
					foreach ($filters['status'] as $s)
					{
						$squery .= "'" . $s . "',";
					}
					$squery = substr($squery, 0, strlen($squery) - 1);
					$query .= " AND (V.state IN (" . $squery . ")) ";
				}
				else
				{
					$query .= " AND V.state=" . intval($filters['status']);
				}
			}
			if ($mine)
			{
				if (count($projects) > 0)
				{
					$p_query = '';
					foreach ($projects as $p)
					{
						$p_query .= "'" . $p . "',";
					}
					$p_query = substr($p_query, 0, strlen($p_query) - 1);
					$query .= " AND (C.project_id IN (" . $p_query . ")) ";
				}
				else
				{
					$query .= "AND C.created_by=" . intval($filters['mine']);
				}
			}
			// Individual assigned curator?
			if (isset($filters['curator']))
			{
				if ($filters['curator'] == 'owner')
				{
					$query .=" AND V.curator = " . User::get('id');
				}
				if ($filters['curator'] == 'other')
				{
					$query .=" AND V.curator != " . User::get('id');
				}
			}
			// Make sure we get the max version
			$query .= " AND V.id = (SELECT MAX(wv2.id) FROM `#__publication_versions` AS wv2 WHERE wv2.publication_id = C.id)";
		}
		else
		{
			$query .= " AND V.version_number = (SELECT MAX(version_number) FROM `#__publication_versions`
						WHERE publication_id=C.id AND state=1 ) AND (V.state=1";
			if (count($projects) > 0)
			{
				$p_query = '';
				foreach ($projects as $p)
				{
					$p_query .= "'" . $p . "',";
				}
				$p_query = substr($p_query, 0, strlen($p_query) - 1);
				$query .= " OR (C.project_id IN (" . $p_query . ") AND V.state != 3 AND V.state != 2) ";
			}
			$query .= ") ";
		}

		$query .= $project ? " AND C.project_id=".$project : "";

		// Category
		if (isset($filters['category']) && $filters['category'] != '')
		{
			if (is_numeric($filters['category']))
			{
				$query .= " AND C.category=" . $filters['category']." ";
			}
			else
			{
				$query .= " AND t.url_alias='" . $filters['category']."' ";
			}
		}
		// group owner - either owned directly by group, or within project owned by group
		if (isset($filters['group_owner']) && $filters['group_owner'] != '')
		{
			$query .= " AND (C.group_owner=" . $filters['group_owner']." OR PP.owned_by_group=" . $filters['group_owner'].") ";

		}
		if (isset($filters['author']) && intval($filters['author']))
		{
			$query .= " AND A.publication_version_id=V.id AND A.user_id=" . $filters['author'];
			$query .= " AND A.status=1 AND (A.role IS NULL OR A.role!='submitter') ";
		}



		// Master type
		if (isset($filters['master_type']) && $filters['master_type'] != '')
		{
			if (is_array($filters['master_type']) && !empty($filters['master_type']))
			{
				$tquery = '';
				foreach ($filters['master_type'] as $type)
				{
					$tquery .= "'" . $type . "',";
				}
				$tquery = substr($tquery, 0, strlen($tquery) - 1);
				$query .= " AND ((C.master_type IN (" . $tquery . ")) ";
			}
			elseif (is_numeric($filters['master_type']))
			{
				$query .= " AND (C.master_type=" . $filters['master_type']." ";
			}
			elseif (is_string($filters['master_type']))
			{
				$query .= " AND (MT.alias='" . $filters['master_type']."' ";
			}
			else
			{
				$query .= " AND (1=1";
			}
			$query .= " OR V.curator = " . User::get('id') . ") ";
		}

		if (isset($filters['minranking']) && $filters['minranking'] != '' && $filters['minranking'] > 0)
		{
			$query .= " AND C.ranking > " . $filters['minranking']." ";
		}
		if (!$dev)
		{
			$query .= " AND (V.published_up = '0000-00-00 00:00:00' OR V.published_up <= '" . $now . "') ";
			$query .= " AND (V.published_down IS NULL OR V.published_down = '0000-00-00 00:00:00' OR V.published_down >= '".$now."') ";
		}
		if (isset($filters['startdate']))
		{
			$query .= "AND V.published_up > " . $this->_db->quote($filters['startdate']) . " ";
		}
		if (isset($filters['enddate']))
		{
			$query .= "AND V.published_up < " . $this->_db->quote($filters['enddate']) . " ";
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
				$words = array();
				$ws = explode(' ', $filters['search']);
				foreach ($ws as $w)
				{
					$w = trim($w);
					if (strlen($w) > 2)
					{
						$words[] = $w;
					}
				}
				$text = implode(' +', $words);
				$text = addslashes($text);
				$text2 = str_replace('+', '', $text);

				$query .= " AND ((MATCH(V.title) AGAINST ('+$text -\"$text2\"') > 0) OR"
						 . " (MATCH(V.abstract,V.description) AGAINST ('+$text -\"$text2\"') > 0)) ";
		}

		// Do not show deleted
		if ($admin == false || (isset($filters['status']) && $filters['status'] != 2))
		{
			$query .= " AND V.state != 2 ";
		}

		if (!isset($filters['ignore_access']) || $filters['ignore_access'] == 0)
		{
			$query .= " AND (V.access != 2)  ";
		}
		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'tags.php');
			$tagging = new \Components\Publications\Helpers\Tags($this->_db);
			$tags = $tagging->_parse_tags($filters['tag']);

			$query .= "AND RTA.objectid=C.id AND TA.tag IN ('" . implode("','", $tags) . "')";
			$groupby = " GROUP BY C.id HAVING uniques=".count($tags);
		}

		$query .= $groupby;
		if (!isset($filters['count']) or $filters['count'] == 0)
		{
			$query  .= " ORDER BY ";
			$sortdir = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'DESC'  ? 'DESC' : 'ASC';

			switch ($sortby)
			{
				case 'date':
				case 'date_published':
					$query .= 'V.published_up DESC';

					break;

				case 'date_oldest':
					$query .= 'V.published_up ASC';

					break;

				case 'date_accepted':
					$query .= 'V.accepted DESC';
					break;

				case 'date_created':
					$query .= 'C.created DESC';
					break;

				case 'date_version_created':
					$query .= 'V.created DESC';
					break;

				case 'date_modified':
					$query .= 'V.modified DESC';
					break;

				case 'title':
				default:
					$query .= 'V.title ' . $sortdir . ', V.version_number DESC';
					break;

				case 'id':
					$query .= 'C.id '.$sortdir;
					break;

				case 'mine':
					$query .= 'PP.provisioned DESC, V.title ' . $sortdir . ', V.version_number DESC';
					break;

				case 'rating':
					$query .= "C.rating DESC, C.times_rated DESC";
					break;

				case 'ranking':
					$query .= "C.ranking DESC";
					break;

				case 'project':
					$query .= "PP.title " . $sortdir;
					break;

				case 'version_ranking':
					$query .= "V.ranking DESC";
					break;

				case 'popularity':
					$query .= "stat DESC, V.published_up ASC";
					break;

				case 'category':
					$query .= "C.category " . $sortdir;
					break;

				case 'type':
					$query .= "C.master_type " . $sortdir;
					break;

				case 'status':
					$query .= "V.state " . $sortdir;
					break;

				case 'random':
					$query .= "RAND()";
					break;

				case 'submitted':
					$query .= "V.submitted " . $sortdir;
					break;
			}
		}

		return $query;
	}
}
