<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

// include needed libs
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'citation.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'author.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'sponsor.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'format.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'type.php');

use Components\Tags\Models\Tag;
use Components\Tags\Models\Cloud;
use Components\Citations\Models\Citation;
use Components\Citations\Models\Author;
use Components\Citations\Models\Type;




/**
 * Groups plugin class for citations
 */
class plgGroupsCitations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Get Tab
	 *
	 * @return array plugin tab details
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => Lang::txt('PLG_GROUPS_CITATIONS'),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => '275D'
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
		$returnhtml = true;
		$active     = 'citations';

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$returnhtml = false;
			}
		}

		//creat database object
		$this->database = App::get('db');

		//get the group members
		$members = $group->get('members');

		// Set some variables so other functions have access
		$this->authorized = $authorized;
		$this->members    = $members;
		$this->group      = $group;
		$this->option     = $option;
		$this->action     = $action;
		$this->access     = $access;

		//if we want to return content
		if ($returnhtml)
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//if were not trying to subscribe
			if ($this->action != 'subscribe')
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
					$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active);

					App::redirect(
						Route::url('index.php?option=com_users&view=login?return=' . base64_encode($url)),
						Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
						'warning'
					);
					return;
				}

				//check to see if user is member and plugin access requires members
				if (!in_array(User::get('id'), $members) && $group_plugin_acl == 'members')
				{
					$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
					return $arr;
				}
			}

			//run task based on action
			switch ($this->action)
			{
				case 'save':     $arr['html'] .= $this->_save();		break;
				case 'add':      $arr['html'] .= $this->_edit();		break;
				case 'edit':     $arr['html'] .= $this->_edit();		break;
				case 'delete':   $arr['html'] .= $this->_delete();			break;
				case 'browse':	 $arr['html'] .= $this->_browse();	break;
				case 'import': 	 $arr['html'] .= $this->_import(); 	break;
				default:         $arr['html'] .= $this->_browse();
			}
		}

		// instantiate citations object and get count
		//$obj = new \Components\Citations\Tables\Citation($this->database);
		/*$total = $obj->getCount(array(
			'scope'    => 'group',
			'scope_id' => $group->gidNumber
		), true);

		//set metadata for menu
		$arr['metadata']['count'] = $total;
		$arr['metadata']['alert'] = '';

		*/

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of all citations, with filtering&search options.
	 *
	 * @return     string HTML
	 */
	private function _browse()
	{

		//initialize the view
		$view = $this->view('default', 'browse');
		// push objects to the view
		$view->group             = $this->group;
		$view->option            = $this->option;
		$view->task              = $this->_name;
		$view->database          = $this->database;
		$view->title             = Lang::txt(strtoupper($this->_name));
		$view->isManager           = ($this->authorized == 'manager') ? true : false;

		// Instantiate a new citations object

		$obj = $this->_filterHandler(Request::getVar('filters', array()), $this->group->get('gidNumber'));

		//get applied filters
		$view->filters = $obj['filters'];

		//get filtered citations
		$view->citations = $obj['citations']->rows();

		//get the earliest year we have citations for
		$view->earliest_year = 2001;
		//$view->earliest_year = $citations->getEarliestYear();

		// Affiliation filter
		$view->filters['filter'] = array(
			'all'    => Lang::txt('PLG_GROUPS_CITATIONS_ALL'),
			'aff'    => Lang::txt('PLG_GROUPS_CITATIONS_AFFILIATED'),
			'nonaff' => Lang::txt('PLG_GROUPS_CITATIONS_NONAFFILIATED'),
			'member' => Lang::txt('PLG_GROUPS_CITATIONS_MEMBERCONTRIB')
		);

		/*if (!in_array($view->filters['filter'], array_keys($view->filter)))
		{
			$view->filters['filter'] = '';
		} */

		// Sort Filter
		$view->sorts = array(
			'sec_cnt DESC' => Lang::txt('PLG_GROUPS_CITATIONS_CITEDBY'),
			'year DESC'    => Lang::txt('PLG_GROUPS_CITATIONS_YEAR'),
			'created DESC' => Lang::txt('PLG_GROUPS_CITATIONS_NEWEST'),
			'title ASC'    => Lang::txt('PLG_GROUPS_CITATIONS_TITLE'),
			'author ASC'   => Lang::txt('PLG_GROUPS_CITATIONS_AUTHOR'),
			'journal ASC'  => Lang::txt('PLG_GROUPS_CITATIONS_JOURNAL')
		);
		/*if (!in_array($view->filters['sort'], array_keys($view->sorts)))
		{
			$view->filters['sort'] = 'created DESC';
		} */

		// Handling ids of the the boxes checked for download
		$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
		$session = App::get('session');

		// If it's new search remove all user citation checkmarks
		if (isset($_POST['filter']))
		{
			$view->filters['idlist'] = "";
			$session->set('idlist', $view->filters['idlist']);
		}
		else
		{
			$view->filters['idlist'] = Request::getVar('idlist', $session->get('idlist'));
			$session->set('idlist', $view->filters['idlist']);
		}

		// Reset the filter if the user came from a different section
		if (strpos($referer, "/citations/browse") == false)
		{
			$view->filters['idlist'] = "";
			$session->set('idlist', $view->filters['idlist']);
		}

		//Convert upload dates to correct time format
		/*if ($view->filters['startuploaddate'] == '0000-00-00'
			|| $view->filters['startuploaddate'] == '0000-00-00 00:00:00'
			|| $view->filters['startuploaddate'] == '')
		{
			$view->filters['startuploaddate'] = '0000-00-00 00:00:00';
		}
		else
		{
			$view->filters['startuploaddate'] = Date::of($view->filters['startuploaddate'])->format('Y-m-d 00:00:00');
		}
		if ($view->filters['enduploaddate'] == '0000-00-00'
			|| $view->filters['enduploaddate'] == '0000-00-00 00:00:00'
			|| $view->filters['enduploaddate'] == '')
		{
			$view->filters['enduploaddate'] = Date::modify('+1 DAY')->format('Y-m-d 00:00:00');
		}
		else
		{
			$view->filters['enduploaddate'] = Date::of($view->filters['enduploaddate'])->format('Y-m-d 00:00:00');
		}

		//Make sure the end date for the upload search isn't before the start date
		if ($view->filters['startuploaddate'] > $view->filters['enduploaddate'])
		{
			App::redirect(
				Route::url('index.php?option=com_citations&task=browse'),
				Lang::txt('PLG_GROUPS_CITATIONS_END_DATE_MUST_BE_AFTER_START_DATE'),
				'error'
			);
			return;
		}
		*/

		// Get record count
		//$view->total = $view->citations->count();
		//$view->total = 10;

		$view->citationTemplate = 'apa';

		// check to see if super group has any additional filters
		/*if ($this->group->isSuperGroup() && file_exists($this->_superGroupHelper()))
		{
			// load helper
			require_once($this->_superGroupHelper());

			// build helper name
			$helperClass =  $this->_name . 'SuperGroupHelper';

			// instantiate the helper class
			$helper = new $helperClass($this->database);

			//override sortings
			$view->filters['sort'] = $helper->getCustomSort();

		} */

		// get the citations
		//$view->citations = $citations->getRecords($view->filters, $view->isManager);

		// get the default citation format
		/*$groupParams = json_decode($this->group->get('params'));
		if (array_keys( (array) $groupParams , 'citation_format') && $groupParams['citations_format'] != "")
		{
			//use the group setting
			$view->citationTemplate = $groupParams['citations_format'];
		}
		else
		{
			//use the hub default
			$citationsFormat = new \Components\Citations\Tables\Format($this->database);
			$view->citationTemplate = $citationsFormat->getDefaultFormat()->format;
		} */

		$view->filters['search'] = "";
		$view->filters['type'] = '';
		$view->filters['tag'] = '';
		$view->filters['author'] = '';
		$view->filters['publishedin'] = '';
		$view->filters['year_start'] = '';
		$view->filters['year_end'] = '';
		$view->filters['startuploaddate'] = '';
		$view->filters['enduploaddate'] = '';
		$view->filters['sort'] = '';

		// get the preferred labeling scheme
		$view->label = null;

		if ($view->label == "none")
		{
			$view->citations_label_class = "no-label";
		}
		elseif ($view->label == "number")
		{
			$view->citations_label_class = "number-label";
		}
		elseif ($view->label == "type")
		{
			$view->citations_label_class = "type-label";
		}
		elseif ($view->label == "both")
		{
			$view->citations_label_class = "both-label";
		}
		else
		{
			$view->citations_label_class = "both-label";
		}

		// enable coins support
		$view->coins = 1;

		// config
		$view->config = Component::params('com_citations');

		// types
		$ct = \Components\Citations\Models\Type::all();
		$view->types = $ct;

		// OpenURL
		$openURL = $this->_handleOpenURL();
		$view->openurl['link'] = $openURL['link'];
		$view->openurl['text'] = $openURL['text'];
		$view->openurl['icon'] = $openURL['icon'];

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display the form allowing to edit a citation
	 *
	 * @return     string HTML
	 */
	private function _edit()
	{
		//create view object
		$view = $this->view('default', 'edit');

		//appends view override if this is a supergroup
		if ($this->group->isSuperGroup())
		{
			$view->addTemplatePath($this->_superGroupViewOverride('edit'));
		}

		// Check if they're logged in
		if (User::isGuest())
		{
			$this->_loginTask();
		}

		// push objects to view
		$view->group   = $this->group;
		$view->isManager = ($this->authorized == 'manager') ? true : false;
		$view->config  = Component::params('com_citations');

		// are we allowing user to add citation
		$allowImport = $view->config->get('citation_import', 1);
		if ($allowImport == 0
			|| ($allowImport == 2 && User::get('usertype') != 'Super Administrator'))
		{
			// Redirect
			App::redirect(
				Route::url('index.php?option=com_groups&cn=' . $this->group->get('gidNumber') . '&active=' . $this->_name . '&action=browse', false),
				Lang::txt('PLG_GROUPS_CITATION_EDIT_NOTALLOWED'),
				'warning'
			);
			return;
		}

		//get the citation types
		$citationsType = \Components\Citations\Models\Type::all();
		$view->types = $citationsType->rows()->toObject();

		$fields = array();
		foreach ($view->types as $type)
		{
			if (isset($type->fields))
			{
				$f = $type->fields;
				if (strpos($f, ',') !== false)
				{
					$f = str_replace(',', "\n", $f);
				}

				$f = array_map('trim', explode("\n", $f));
				$f = array_values(array_filter($f));

				$fields[strtolower(str_replace(' ', '', $type->type_title))] = $f;
			}
		}

		// add an empty value for the first type
		array_unshift($view->types, array(
			'id'         => 0,
			'type'       => '',
			'type_title' => ' - Select a Type &mdash;'
		));

		$view->types[0] = (object) $view->types[0];

		// Incoming - expecting an array id[]=4232
		$id = Request::getInt('id', 0);

		// Pub author
		$pubAuthor = false;

		// Load the associations object
		$assoc = new \Components\Citations\Tables\Association($this->database);

		// Get associations
		if ($id)
		{
			$view->assocs = $assoc->getRecords(array('cid' => $id), $view->isManager);
			$pubAuthor    = $this->isPubAuthor($view->assocs);
		}

		// Is user authorized to edit citations?
		if (!$view->isManager && !$pubAuthor)
		{
			$id = 0;
		}

		// Load the object
		$view->row = \Components\Citations\Models\Citation::oneorNew($id);

		//make sure title isnt too long
		$maxTitleLength = 30;
		$shortenedTitle = (strlen($view->row->title) > $maxTitleLength)
			? substr($view->row->title, 0, $maxTitleLength) . '&hellip;'
			: $view->row->title;

		// Set the pathway
		if ($id && $id != 0)
		{
			Pathway::append($shortenedTitle, 'index.php?option=com_citations&task=view&id=' . $view->row->id);
			Pathway::append(Lang::txt('PLG_GROUPS_CITATIONS_EDIT'));
		}
		else
		{
			Pathway::append(Lang::txt('PLG_GROUPS_CITATIONS_ADD'));
		}

		// Set the page title
		Document::setTitle( Lang::txt('PLG_GROUPS_CITATIONS_CITATION') . $shortenedTitle );

		//push jquery to doc
		Document::addScriptDeclaration('var fields = ' . json_encode($fields) . ';');

		// Instantiate a new view
		$view->title  = Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt(strtoupper($this->_name) . '_' . strtoupper($this->action));

		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id)
		{
			$view->row->uid = User::get('id');

			// It's new - no associations to get
			$view->assocs = array();

			//tags & badges
			$view->tags   = array();
			$view->badges = array();
		}
		else
		{
			//tags & badges
			$view->tags   = \Components\Citations\Helpers\Format::citationTags($view->row, $this->database, false);
			$view->badges = \Components\Citations\Helpers\Format::citationBadges($view->row, $this->database, false);
		}

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	private function _save()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->_loginTask();
		}

		//get the posted vars
		$c = $_POST;
		if (isset($c['format_type']))
		{
			$c['format'] = $c['format_type'];
		}

		// set scope & scope id in save so no one can mess with hidden form inputs
		$c['scope']    = 'group';
		$c['scope_id'] = $this->group->get('gidNumber');

		//get tags
		$tags = trim(Request::getVar('tags', ''));
		unset($c['tags']);

		//get badges
		$badges = trim(Request::getVar('badges', ''));
		unset($c['badges']);

		// Bind incoming data to object
		$row = new \Components\Citations\Tables\Citation($this->database);
		if (!$row->bind($c))
		{
			$this->setError($row->getError());
			$this->_browse();
			return;
		}

		// New entry so set the created date
		if (!$row->id)
		{
			$row->created = Date::toSql();
		}

		// Field named 'uri' due to conflict with existing 'url' variable
		$row->url = Request::getVar('uri', '', 'post');

		// Check content for missing required data
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->_edit($group);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->_edit($group);
			return;
		}

		// Incoming associations
		$arr     = Request::getVar('assocs', array());
		$ignored = array();

		foreach ($arr as $a)
		{
			$a = array_map('trim', $a);

			// Initiate extended database class
			$assoc = new \Components\Citations\Tables\Association($this->database);

			//check to see if we should delete
			if (isset($a['id']) && $a['tbl'] == '' && $a['oid'] == '')
			{
				// Delete the row
				if (!$assoc->delete($a['id']))
				{
					$this->setError($assoc->getError());
					$this->_browse();
					return;
				}
			}
			else if ($a['tbl'] != '' || $a['oid'] != '')
			{
				$a['cid'] = $row->id;

				// bind the data
				if (!$assoc->bind($a))
				{
					$this->setError($assoc->getError());
					$this->_browse();
					return;
				}

				// Check content
				if (!$assoc->check())
				{
					$this->setError($assoc->getError());
					$this->_browse();
					return;
				}

				// Store new content
				if (!$assoc->store())
				{
					$this->setError($assoc->getError());
					$this->_browse();
					return;
				}
			}
		}

		$this->config = Component::params('com_citations');
		//check if we are allowing tags
		if ($this->config->get('citation_allow_tags', 'no') == 'yes')
		{
			$ct1 = new \Components\Tags\Models\Cloud($row->id, 'citations');
			$ct1->setTags($tags, User::get('id'), 0, 1, '');
		}

		//check if we are allowing badges
		if ($this->config->get('citation_allow_badges', 'no') == 'yes')
		{
			$ct1 = new \Components\Tags\Models\Cloud($row->id, 'citations');
			$ct2->setTags($badges, User::get('id'), 0, 1, 'badge');
		}

		// redirect after save
		App::redirect(
			Route::url('index.php?option=com_groups&cn=' . $this->group->cn . '&active=citations'),
			Lang::txt('PLG_GROUPS_CITATIONS_CITATION_SAVED'),
			'success'
		);
		return;
	}

	/**
	 * Determine if user is part of publication project and is allowed to edit citation
	 *
	 * @param      array $assocs
	 * @return     void
	 */
	public function isPubAuthor($assocs)
	{
		if (!$assocs)
		{
			return false;
		}
		if (!is_file(PATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php'))
		{
			return false;
		}

		// include libs
		require_once(PATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
		require_once(PATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'owner.php');

		// Get connections to publications
		foreach ($assocs as $entry)
		{
			if ($entry->tbl == 'publication')
			{
				$pubID = $entry->oid;
				$objP = new \Components\Publications\Tables\Publication($this->database);

				if ($objP->load($pubID))
				{
					$objO = new \Components\Projects\Tables\Owner($this->database);

					if ($objO->isOwner(User::get('id'), $objP->project_id))
					{
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Return a path to super group override
	 *
	 * @param   string  $name  Plugin name
	 * @return  string
	 */
	public function _superGroupViewOverride($name)
	{
		// get groups config
		$groupsConfig = Component::params('com_groups');

		// build base path
		$base = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);

		// return path
		return $base . DS . $this->group->get('gidNumber') . DS . 'template' . DS . 'plugins' . DS . $this->_name . DS . $name;
	}


	/**
	 * Return a path to super group helper
	 *
	 * @return  string
	 */
	public function _superGroupHelper()
	{
		// get groups config
		$groupsConfig = Component::params('com_groups');

		// build base path
		$base = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);

		// build helper path
		$helperPath =  $base . DS . $this->group->get('gidNumber') . DS . 'libraries' . DS . $this->_name . DS . 'helper.php';

		return $helperPath;
	}

	/**
	 * Redirect to login form
	 *
	 * @return  void
	 */
	private function _loginTask()
	{
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->option . DS . $this->group->get('cn') . DS. $this->_name .'&action=' . $this->action, false, true))),
			Lang::txt('PLG_GROUPS_CITATIONS_NOT_LOGGEDIN'),
			'warning'
		);
		return;
	}

	/**
	 * Redirect to citation importer
	 *
	 * @return  void
	 */
	private function _import()
	{
		App::redirect(Route::url('index.php?option=com_citations&controller=import&group=' . $group->get('gidNumber')));
		return;
	}

	/**
	 * Uses URL to determine OpenURL server
	 *
	 * @return  object $openURL
	 */
	private function _handleOpenURL()
	{
		//get the users id to make lookup
		$users_ip = Request::ip();

		//get the param for ip regex to use machine ip
		$ip_regex = array('10.\d{2,5}.\d{2,5}.\d{2,5}');

		$use_machine_ip = false;
		foreach ($ip_regex as $ipr)
		{
			$match = preg_match('/' . $ipr . '/i', $users_ip);
			if ($match)
			{
				$use_machine_ip = true;
			}
		}

		//make url based on if were using machine ip or users
		if ($use_machine_ip)
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $_SERVER['SERVER_ADDR'];
		}
		else
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $users_ip;
		}

		//get the resolver
		$r = null;
		if (function_exists('curl_init'))
		{
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, $url );
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($cURL, CURLOPT_TIMEOUT, 10);
			$r = curl_exec($cURL);
			curl_close($cURL);
		}

		//parse the returned xml
		$openurl = array(
			'link' => '',
			'text' => '',
			'icon' => ''
		);

		//parse the return from resolver lookup
		$resolver = null;
		$xml = simplexml_load_string($r);
		if (isset($xml->resolverRegistryEntry))
		{
			$resolver = $xml->resolverRegistryEntry->resolver;
		}

		//if we have resolver set vars for creating open urls
		if ($resolver != null)
		{
			$openURL['link'] = $resolver->baseURL;
			$openURL['text'] = $resolver->linkText;
			$openURL['icon'] = $resolver->linkIcon;

			return $openURL;
		}

		return false;
	}

	/**
	 * Applies filters to Citations model and returns applied filters
	 * @param array  $filters array of POST values
	 * @return  array sanitized and validated filter values
	 */
	private function _filterHandler($filters = array(),  $scope_id = 0)
	{
		$citations = \Components\Citations\Models\Citation::all();
		// require citations
		if (!$citations)
		{
			return false;
		}

		// get the ones for this group
		$scope = 'group';

		$citations->where('scope', '=', $scope);
		$citations->where('scope_id', '=', $scope_id);

		// for search: $query .= " AND (MATCH(r.title, r.isbn, r.doi, r.abstract, r.author, r.publisher) AGAINST (" . $this->_db->quote($filter['search']) . " IN BOOLEAN MODE) > 0)";
		if (count($filters) > 0)
		{
			foreach ($filters as $filter => $value)
			{
				if ($filter != 'search' && $value != "")
				{
					$citations->where($filter, '=', $value);
				}
			}

			return array('citations' => $citations, 'filters' => $filters);

		}
		else
		{
			return array('citations' => $citations, 'filters' => array());
		}

		//search/filtering params
		/*$view->filters['scope']           = 'group';
		$view->filters['scope_id']        = $this->group->get('gidNumber');
		$view->filters['id']			  = Request::getInt('id', 0);
		$view->filters['tag']             = trim(Request::getVar('tag', '', 'request', 'none', 2));
		$view->filters['search']          = Request::getVar('search', '');
		$view->filters['type']            = Request::getVar('type', '');
		$view->filters['author']          = Request::getVar('author', '');
		$view->filters['publishedin']     = Request::getVar('publishedin', '');
		$view->filters['year_start']      = Request::getInt('year_start', $view->earliest_year);
		$view->filters['year_end']        = Request::getInt('year_end', date("Y"));
		$view->filters['filter']          = Request::getVar('filter', '');
		$view->filters['sort']            = Request::getVar('sort', 'year DESC');
		$view->filters['reftype']         = Request::getVar('reftype', array('research' => 1, 'education' => 1, 'eduresearch' => 1, 'cyberinfrastructure' => 1));
		$view->filters['geo']             = Request::getVar('geo', array('us' => 1, 'na' => 1,'eu' => 1, 'as' => 1));
		$view->filters['aff']             = Request::getVar('aff', array('university' => 1, 'industry' => 1, 'government' => 1));
		$view->filters['startuploaddate'] = Request::getVar('startuploaddate', '0000-00-00');
		$view->filters['enduploaddate']   = Request::getVar('enduploaddate', '0000-00-00');
		$view->filters['limit']			  = Request::getInt('limit', 10);
		$view->filters['start']			  = Request::getInt('start', 0);*/

	}

}
