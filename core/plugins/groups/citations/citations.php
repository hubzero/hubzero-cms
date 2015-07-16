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
 * @author	  Alissa Nedossekina <alisa@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
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
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'format.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'type.php');

use Hubzero\Config\Registry;
use Components\Tags\Models\Tag;
use Components\Tags\Models\Cloud;
use Components\Citations\Models\Citation;
use Components\Citations\Models\Author;
use Components\Citations\Models\Type;
use Components\Citations\Models\Format;


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
			'name'			   => $this->_name,
			'title'			   => Lang::txt('PLG_GROUPS_CITATIONS'),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'			   => '275D'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param	   object  $group	   Current group
	 * @param	   string  $option	   Name of the component
	 * @param	   string  $authorized User's authorization level
	 * @param	   integer $limit	   Number of records to pull
	 * @param	   integer $limitstart Start of records to pull
	 * @param	   string  $action	   Action to perform
	 * @param	   array   $access	   What can be accessed
	 * @param	   array   $areas	   Active area(s)
	 * @return	   array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$returnhtml = true;
		$active		= 'citations';

		// The output array we're returning
		$arr = array(
			'html'	   => '',
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
		$this->members	  = $members;
		$this->group	  = $group;
		$this->option	  = $option;
		$this->action	  = $action;
		$this->access	  = $access;

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
				case 'save':	 $arr['html'] .= $this->_save();		break;
				case 'add':		 $arr['html'] .= $this->_edit();		break;
				case 'edit':	 $arr['html'] .= $this->_edit();		break;
				case 'delete':	 $arr['html'] .= $this->_delete();		break;
				case 'browse':	 $arr['html'] .= $this->_browse();		break;
				case 'import':	 $arr['html'] .= $this->_import();		break;
				case 'settings': $arr['html'] .= $this->_settings();		break;
				default:		 $arr['html'] .= $this->_browse();
			}
		}

		//set metadata for menu
		$arr['metadata']['count'] = \Components\Citations\Models\Citation::all()
			->where('scope', '=', 'group')
			->where('scope_id', '=', $this->group->get('gidNumber'))
			->count();
		$arr['metadata']['alert'] = '';

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of all citations, with filtering&search options.
	 *
	 * @return	   string HTML
	 */
	private function _browse()
	{
		//initialize the view
		$view = $this->view('default', 'browse');
		// push objects to the view
		$view->group			 = $this->group;
		$view->option			 = $this->option;
		$view->task				 = $this->_name;
		$view->database			 = $this->database;
		$view->title			 = Lang::txt(strtoupper($this->_name));
		$view->isManager		   = ($this->authorized == 'manager') ? true : false;

		// Instantiate a new citations object
		$obj = $this->_filterHandler(Request::getVar('filters', array()), $this->group->get('gidNumber'));

		//get applied filters
		$view->filters = $obj['filters'];

		//get filtered citations
		$view->citations = $obj['citations']->rows();

		//get the earliest year we have citations for
		$view->earliest_year = 2001;

		// Affiliation filter
		$view->filters['filter'] = array(
			'all'	 => Lang::txt('PLG_GROUPS_CITATIONS_ALL'),
			'aff'	 => Lang::txt('PLG_GROUPS_CITATIONS_AFFILIATED'),
			'nonaff' => Lang::txt('PLG_GROUPS_CITATIONS_NONAFFILIATED'),
			'member' => Lang::txt('PLG_GROUPS_CITATIONS_MEMBERCONTRIB')
		);

		$view->filters['activeFilter'] = '';

		// Sort Filter
		$view->sorts = array(
			'sec_cnt DESC' => Lang::txt('PLG_GROUPS_CITATIONS_CITEDBY'),
			'year DESC'    => Lang::txt('PLG_GROUPS_CITATIONS_YEAR'),
			'created DESC' => Lang::txt('PLG_GROUPS_CITATIONS_NEWEST'),
			'title ASC'    => Lang::txt('PLG_GROUPS_CITATIONS_TITLE'),
			'author ASC'   => Lang::txt('PLG_GROUPS_CITATIONS_AUTHOR'),
			'journal ASC'  => Lang::txt('PLG_GROUPS_CITATIONS_JOURNAL')
		);

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

		$view->citationTemplate = 'apa';


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
	 * @return	   string HTML
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
			$pubAuthor	  = $this->isPubAuthor($view->assocs);
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
	 * @return	   void
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

		//var_dump($c); die;

		// Bind incoming data to object
		//$row = new \Components\Citations\Models\Citation($this->database);
		$citation = \Components\Citations\Models\Citation::oneOrNew(Request::getInt('id'))
			->set(array(
				'type' => Request::getInt('type'),
				'cite' => Request::getVar('cite'),
				'ref_type' => Request::getVar('ref_type'),
				'date_submit' => Request::getVar('date_submit'),
				'date_accept' => Request::getVar('date_accept'),
				'date_publish' => Request::getVar('date_publish'),
				'year' => Request::getVar('year'),
				'month' => Request::getVar('month'),
				'author' => Request::getVar('author'),
				'author_address' => Request::getVar('author_address'),
				'editor' => Request::getVar('editor'),
				'title' => Request::getVar('title'),
				'booktitle' => Request::getVar('booktitle'),
				'short_title' => Request::getVar('short_title'),
				'journal' => Request::getVar('journal'),
				'volume' => Request::getVar('volume'),
				'number' => Request::getVar('number'),
				'pages' => Request::getVar('pages'),
				'isbn' => Request::getVar('isbn'),
				'doi' => Request::getVar('doi'),
				'call_number' => Request::getVar('call_number'),
				'accession_number' => Request::getVar('accession_number'),
				'series' => Request::getVar('series'),
				'edition' => Request::getVar('edition'),
				'school' => Request::getVar('school'),
				'publisher' => Request::getVar('publisher'),
				'institution' => Request::getVar('institution'),
				'address' => Request::getVar('address'),
				'location' => Request::getVar('location'),
				'howpublished' => Request::getVar('howpublished'),
				'url' => Request::getVar('uri'),
				'eprint' => Request::getVar('eprint'),
				'abstract' => Request::getVar('abstract'),
				'keywords' => Request::getVar('keywords'),
				'research_notes' => Request::getVar('research_notes'),
				'language' => Request::getVar('language'),
				'label' => Request::getVar('label'),
				//'format_type' => Request::getVar('format_type'),
				'uid' => Request::getVar('uid')
			));

		// New entry so set the created date
		/*if (!$row->id)
		{
			$row->created = Date::toSql();
		}*/

		// Store new content
		if (!$citation->save())
		{
			$this->setError($citation->getError());
			$this->_edit();
			return;
		}

		$this->config = Component::params('com_citations');
		//check if we are allowing tags
		if ($this->config->get('citation_allow_tags', 'no') == 'yes')
		{
			$ct1 = new \Components\Tags\Models\Cloud($citation->id, 'citations');
			$ct1->setTags($tags, User::get('id'), 0, 1, '');
		}

		//check if we are allowing badges
		if ($this->config->get('citation_allow_badges', 'no') == 'yes')
		{
			$ct1 = new \Components\Tags\Models\Cloud($citation->id, 'citations');
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
	 * Settings for group citations
	 *
	 * @param null
	 * @return void
	 *
	 *
	 */
	private function _settings()
	{
		if ($_POST)
		{
			$display = Request::getVar('display', '');
			$format = Request::getVar('citation-format', '');
			$params = json_decode($this->group->get('params'));

			// if the setting a custom group citation type
			if ($format == "custom")
			{
				// craft a clever name
				$name =  "custom-group-" . $this->group->cn;
				$params->citationFormat = isset($params->citationFormat) ? $params->citationFormat : '';

				// create new format
				$citationFormat = \Components\Citations\Models\Format::oneOrNew($params->citationFormat)->set(array(
				'format'	  => Request::getVar('template'),
				'style'		  => $name
				));

				//save format
				$citationFormat->save();

				//update group
				$params->citationFormat = $citationFormat->id;
			}
			else
			{
				// returned value from format select box
				$params->citationFormat = $format;
			}

			// more parameters for citations
			$params->display = Request::getVar('display', '');
			$params->include_coins = Request::getVar('include_coins', '');
			$params->coins_only = Request::getVar('coins_only', '');

			// update the group parameters
			$gParams = new Registry($params);
			$gParams->merge($params);
			$this->group->set('params', $gParams->toString());
			$this->group->update();

			// redirect after save
			App::redirect(
				Route::url('index.php?option=com_groups&cn=' . $this->group->cn . '&active=citations'),
				Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_SAVED'),
				'success'
			);
			return;

		}
		else
		{
			//instansiate the view
			$view = $this->view('default', 'settings');

			// pass the group through
			$view->group = $this->group;

			//get group settings
			$params = json_decode($this->group->get('params'));

			$view->include_coins = (isset($params->include_coins) ? $params->include_coins : "false");
			$view->coins_only = (isset($params->coins_only) ? $params->coins_only : "false");
			$citationsFormat = (isset($params->citationFormat) ? $params->citationFormat : 1);

			//get formats
			$view->formats = \Components\Citations\Models\Format::all()->rows()->toObject();
			$view->templateKeys = \Components\Citations\Models\Format::all()->getTemplateKeys();
			$view->currentFormat = \Components\Citations\Models\Format::oneOrFail($citationsFormat);
			$view->customFormat = false;

			// the name of the custom format
			$name = "custom-group-" . $this->group->cn;

			// helps prevent creating more than one custom format per group
			if ($view->currentFormat->style == $name)
			{
				$view->customFormat = true;
			}

			// Output HTML
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			return $view->loadTemplate();
		}
	}

	/**
	 * [getCitationConfig description]
	 * @return [object] [description]
	 */
	private function getCitationConfig($group)
	{
		if (isset($group))
		{
			$params = json_decode($group->get('params'));

			return $params;
		}
		else
		{
			return false;
		}
	}

	private function saveConfig($params = null)
	{


	}

	/**
	 * Determine if user is part of publication project and is allowed to edit citation
	 *
	 * @param	   array $assocs
	 * @return	   void
	 */
	public function isPubAuthor()
	{	/**
		*@TODO Implement
		**/
		return false;
	}

	/**
	 * Redirect to login form
	 *
	 * @return	void
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
	 * Uses URL to determine OpenURL server
	 *
	 * @return	object $openURL
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
	 * @return	array sanitized and validated filter values
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
				// yay sanitization
				$value = \Hubzero\Utility\Sanitize::clean($value);

				if ($filter != 'search' && $filter != 'sort' && $value != "")
				{
					if ($filter == 'author')
					{
						$citations->where('author', 'LIKE', "%{$value}%", 'and', 1);
					}
					else
					{
						$citations->where($filter, '=', $value);
					}
				}

				// Take filters and apply them to the tasks
				if ($filter == "search" && $value != "")
				{
					$terms = preg_split('/\s+/', $value);
					$collection = array();
					$columns = array('author', 'title', 'isbn', 'doi', 'publisher', 'abstract');
					foreach($columns as $column)
					{
						foreach ($terms as $term)
						{
							// copy the original item
							$cite = $citations;

							// do some searching
							$cite->where($column, 'LIKE', "%{$term}%", 'and', 1);

							// put for collection later
							array_push($collection, $cite);
						}
					}
				}

			}

			return array('citations' => $citations, 'filters' => $filters);

		}
		else
		{
			return array('citations' => $citations, 'filters' => array());
		}

		//search/filtering params
		/*$view->filters['scope']			= 'group';
		$view->filters['scope_id']		  = $this->group->get('gidNumber');
		$view->filters['id']			  = Request::getInt('id', 0);
		$view->filters['tag']			  = trim(Request::getVar('tag', '', 'request', 'none', 2));
		$view->filters['search']		  = Request::getVar('search', '');
		$view->filters['type']			  = Request::getVar('type', '');
		$view->filters['author']		  = Request::getVar('author', '');
		$view->filters['publishedin']	  = Request::getVar('publishedin', '');
		$view->filters['year_start']	  = Request::getInt('year_start', $view->earliest_year);
		$view->filters['year_end']		  = Request::getInt('year_end', date("Y"));
		$view->filters['filter']		  = Request::getVar('filter', '');
		$view->filters['sort']			  = Request::getVar('sort', 'year DESC');
		$view->filters['reftype']		  = Request::getVar('reftype', array('research' => 1, 'education' => 1, 'eduresearch' => 1, 'cyberinfrastructure' => 1));
		$view->filters['geo']			  = Request::getVar('geo', array('us' => 1, 'na' => 1,'eu' => 1, 'as' => 1));
		$view->filters['aff']			  = Request::getVar('aff', array('university' => 1, 'industry' => 1, 'government' => 1));
		$view->filters['startuploaddate'] = Request::getVar('startuploaddate', '0000-00-00');
		$view->filters['enduploaddate']   = Request::getVar('enduploaddate', '0000-00-00');
		$view->filters['limit']			  = Request::getInt('limit', 10);
		$view->filters['start']			  = Request::getInt('start', 0);*/

		/*
				if (!isset($filter['published']))
		{
			$filter['published'] = array(1);
		}

		$query = " WHERE r.published IN (" . implode(',', $filter['published']) . ")";

		//search term match
		if (isset($filter['search']) && $filter['search'] != '')
		{
			$query .= " AND (MATCH(r.title, r.isbn, r.doi, r.abstract, r.author, r.publisher) AGAINST (" . $this->_db->quote($filter['search']) . " IN BOOLEAN MODE) > 0)";

			//if ($admin = true)
			//{
			//	$query .= " OR LOWER(u.username) = " . $this->_db->quote(strtolower($filter['search'])) . "
			//				OR r.uid = " . $this->_db->quote($filter['search']);
			//}
		}

		//tag search
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			//if we have multiple tags we must explode them
			if (strstr($filter['tag'], ","))
			{
				$tags = array_filter(array_map('trim',explode(',', $filter['tag'])));
			}
			else
			{
				$tags = array($filter['tag']);
			}

			//prevent SQL injection
			foreach ($tags as &$tag)
			{
				$tag = $this->_db->quote($tag);
			}

			$query .= " AND tago.tbl='citations' AND tag.tag IN (" . implode(",", $tags) . ")";
		}

		//type filter
		if (isset($filter['type']) && $filter['type'] != '')
		{
			$query .= " AND r.type=" . $this->_db->quote($filter['type']);
		}

		//author filter
		if (isset($filter['author']) && $filter['author'] != '')
		{
			$query .= " AND r.author LIKE " . $this->_db->quote('%' . $filter['author'] . '%');
		}

		//published in filter
		if (isset($filter['publishedin']) && $filter['publishedin'] != '')
		{
			$query .= " AND (r.booktitle LIKE " . $this->_db->quote('%' . $filter['publishedin'] . '%') . " OR r.journal LIKE " . $this->_db->quote('%' . $filter['publishedin'] . '%') . ")";
		}

		//year filter
		if (isset($filter['year_start']) && is_numeric($filter['year_start']) && $filter['year_start'] > 0)
		{
			$query .= " AND (r.year >=" . $this->_db->quote($filter['year_start']) . " OR r.year IS NULL OR r.year=0)";
		}
		if (isset($filter['year_end']) && is_numeric($filter['year_end']) && $filter['year_end'] > 0)
		{
			$query .= " AND (r.year <=" . $this->_db->quote($filter['year_end']) . " OR r.year IS NULL OR r.year=0)";
		}
		if (isset($filter['startuploaddate']) && isset($filter['enduploaddate']))
		{
			$query .= " AND r.created >= " . $this->_db->quote($filter['startuploaddate']) . " AND r.created <= " . $this->_db->quote($filter['enduploaddate']);
		}

		//affiated? filter
		if (isset($filter['filter']) && $filter['filter'] != '')
		{
			if ($filter['filter'] == 'aff')
			{
				$query .= " AND r.affiliated=1";
			}
			else
			{
				$query .= " AND r.affiliated=0";
			}
		}

		//reference type check
		if (isset($filter['reftype']))
		{
			// make sure its valid
			if (!is_array($filter['reftype']))
			{
				throw new Exception(Lang::txt('Citations: Invalid search param "reftype"'), 500);
			}

			if ((isset($filter['reftype']['research']) && $filter['reftype']['research'] == 1)
			 && (isset($filter['reftype']['education']) && $filter['reftype']['education'] == 1)
			 && (isset($filter['reftype']['eduresearch']) && $filter['reftype']['eduresearch'] == 1)
			 && (isset($filter['reftype']['cyberinfrastructure']) && $filter['reftype']['cyberinfrastructure'] == 1))
			{
				// Show all
			}
			else
			{
				$query .= " AND";
				$multi = 0;
				$o = 0;
				foreach ($filter['reftype'] as $g)
				{
					if ($g == 1)
					{
						$multi++;
					}
				}

				if ($multi)
				{
					$query .= " (";
				}
				if (isset($filter['reftype']['research']) && $filter['reftype']['research'] == 1)
				{
					$query .= " ((ref_type LIKE '%R%' OR ref_type LIKE '%N%' OR ref_type LIKE '%S%') AND ref_type NOT LIKE '%E%')";
					if ($multi)
					{
						$o = 1;
					}
				}
				if (isset($filter['reftype']['education']) && $filter['reftype']['education'] == 1)
				{
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ((ref_type NOT LIKE '%R%' AND ref_type NOT LIKE '%N%' AND ref_type NOT LIKE '%S%') AND ref_type LIKE '%E%')";
				}
				if (isset($filter['reftype']['eduresearch']) && $filter['reftype']['eduresearch'] == 1)
				{
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " (ref_type LIKE '%R%E%' OR ref_type LIKE '%E%R%' AND ref_type LIKE '%N%E%' OR ref_type LIKE '%E%N%' OR ref_type LIKE '%S%E%' OR ref_type LIKE '%E%S%')";
				}
				if (isset($filter['reftype']['cyberinfrastructure']) && $filter['reftype']['cyberinfrastructure'] == 1)
				{
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ((ref_type LIKE '%C%' OR ref_type LIKE '%A%' OR ref_type LIKE '%HD%' OR ref_type LIKE '%I%') AND (ref_type NOT LIKE '%R%' AND ref_type NOT LIKE '%N%' AND ref_type NOT LIKE '%S%' AND ref_type NOT LIKE '%E%'))";
				}
				if ($multi)
				{
					$query .= ")";
				}
			}
		}

		//author affiliation filter
		if (isset($filter['aff']))
		{
			if ((isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
			 && (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
			 && (isset($filter['aff']['government']) && $filter['aff']['government'] == 1))
			{
				// Show all
			}
			else
			{
				$query .= " AND ca.cid=r.id AND";
				$multi = 0;
				$o = 0;
				foreach ($filter['aff'] as $g)
				{
					if ($g == 1)
					{
						$multi++;
					}
				}
				if ($multi)
				{
					$query .= " (";
				}
				if (isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
				{
					$query .= " (ca.orgtype LIKE '%education%' OR ca.orgtype LIKE 'university%')";
					if ($multi)
					{
						$o = 1;
					}
				}
				if (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
				{
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ca.orgtype LIKE '%industry%'";
				}
				if (isset($filter['aff']['government']) && $filter['aff']['government'] == 1)
				{
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ca.orgtype LIKE '%government%'";
				}
				if ($multi)
				{
					$query .= ")";
				}
			}
		}

		//author geo filter
		if (isset($filter['geo']))
		{
			if ((isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
			 && (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
			 && (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
			 && (isset($filter['geo']['as']) && $filter['geo']['as'] == 1))
			{
				// Show all
			}
			else
			{
				$query .= " AND ca.cid=r.id AND";

				$multi = 0;
				$o = 0;
				foreach ($filter['geo'] as $g)
				{
					if ($g == 1)
					{
						$multi++;
					}
				}
				if ($multi)
				{
					$query .= " (";
				}
				if (isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
				{
					$query .= " LOWER(ca.countryresident) = 'us'";
					if ($multi)
					{
						$o = 1;
					}
				}
				if (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
				{
					$countries = Geocode::getCountriesByContinent('na');
					$c = implode("','",$countries);
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " LOWER(ca.countryresident) IN ('" . strtolower($c) . "')";
				}
				if (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
				{
					$countries = Geocode::getCountriesByContinent('eu');
					$c = implode("','", $countries);
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " LOWER(ca.countryresident) IN ('" . strtolower($c) . "')";
				}
				if (isset($filter['geo']['as']) && $filter['geo']['as'] == 1)
				{
					$countries = Geocode::getCountriesByContinent('as');
					$c = implode("','", $countries);
					if ($multi)
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " LOWER(ca.countryresident) IN ('" . strtolower($c) . "')";
				}
				if ($multi)
				{
					$query .= ")";
				}
			}
		}

		if (isset($filter['id']) && $filter['id'] > 0)
		{
			$query .= " AND r.id=" . $filter['id'];
		}

		// scope & scope Id
		if (isset($filter['scope']) && $filter['scope'] != '')
		{
			$query .= " AND r.scope=" . $this->_db->quote($filter['scope']);
		}
		if (isset($filter['scope_id']) && $filter['scope_id'] != NULL)
		{
			$query .= " AND r.scope_id=". $this->_db->quote($filter['scope_id']);
		}

		//group by
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			$query .= " GROUP BY r.id HAVING uniques=" . count($tags);
		}

		//if we had a search term lets order by search match
		if (isset($filter['search']) && $filter['search'] != '')
		{
			$query .= " ORDER BY MATCH(r.title, r.isbn, r.doi, r.abstract, r.author, r.publisher) AGAINST (" . $this->_db->quote($filter['search']) . " IN BOOLEAN MODE) DESC";
			$filter['sort'] = '';
		}

		//sort filter
		if (isset($filter['sort']) && $filter['sort'] != '')
		{
			if (isset($filter['search']) && $filter['search'] != '')
			{
				$query .= ", " . $filter['sort'];
			}
			else
			{
				$query .= " ORDER BY " . $filter['sort'];
			}
		}

		//limit
		if (isset($filter['limit']) && $filter['limit'] > 0)
		{
			$query .= " LIMIT " . intval($filter['start']) . "," . intval($filter['limit']);
		}
		 */

	}

	/**
	 * Return a list of citations for a specific user
	 *
	 * @param	object	$group	  Current group
	 * @param	object	$profile  USer profile
	 * @return	string
	 */
	public function onGroupMemberAfter($group, $profile)
	{
		$view = $this->view('default', 'member');
		$view->group	= $group;
		$view->option	= 'com_groups';
		$view->task		= $this->_name;
		$view->database = App::get('db');
		$view->title	= Lang::txt(strtoupper($this->_name));

		$view->citationTemplate = 'apa';

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

		switch ($view->label)
		{
			case 'none':
				$view->citations_label_class = 'no-label';
			break;
			case 'number':
				$view->citations_label_class = 'number-label';
			break;
			case 'type':
				$view->citations_label_class = 'type-label';
			break;
			case 'both':
			default:
				$view->citations_label_class = 'both-label';
			break;
		}

		// enable coins support
		$view->coins = 1;

		// config
		$view->config = Component::params('com_citations');

		// types
		$view->types = \Components\Citations\Models\Type::all();

		// OpenURL
		$openURL = $this->_handleOpenURL();
		$view->openurl['link'] = $openURL['link'];
		$view->openurl['text'] = $openURL['text'];
		$view->openurl['icon'] = $openURL['icon'];

		$view->citations = array();

		return $view->loadTemplate();
	}
}
