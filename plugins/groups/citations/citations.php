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
 * @author    Alissa Nedossekina <alisa@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
// Check to ensure this file is included in Joomla!
//defined('_JEXEC') or die('Restricted access');
require_once('/var/www/example/components/com_citations' . DS . 'helpers' . DS . 'format.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'citation.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'association.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'author.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'secondary.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'sponsor.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'tags.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'format.php');
require_once('/var/www/example/administrator/components/com_citations' . DS . 'tables' . DS . 'type.php');
/**
 * Groups Plugin class for blog entries
 */
class plgGroupsCitations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;


	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);


		// Do some extra initialization in this constructor if required
	}

	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => JText::_('PLG_GROUPS_CITATIONS'),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f095'
		);
		return $area;
	}

	/**
	 * Return content that is to be displayed before group main area
	 *
	 * @return     string
	 */
	public function onBeforeGroup( $group, $authorized )
	{

		//create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'browse',
				'layout'  => 'sticky'
			)
		);

		//vars for view
		$view->authorized = $authorized;
		$view->option     = 'plg_groups_citations';
		$view->group      = $group;
		$view->name       = $this->_name;
		$view->juser      = JFactory::getUser();
		$view->database   = JFactory::getDBO();

		// get plugin access
		//$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'citations');

		//if set to nobody make sure cant access
		//check if guest and force login if plugin access is registered or members
		//check to see if user is member and plugin access requires members
		if ($access == 'nobody'
			|| ($view->juser->get('guest') && $access == 'registered')
			|| (!in_array($view->juser->get('id'), $group->get('members')) && $access == 'members'))
		{
			return '';
		}

		//build array of filters
		$view->filters              = array();
		$view->filters['scope']     = 'group';
		$view->filters['scope_id']  = $view->group->get('gidNumber');
		$view->filters['state']     = 1;
		$view->filters['sticky']    = 1;
		$view->filters['published'] = 1;

		//create new announcement Object
		$hubzeroAnnouncement = new \Hubzero\Item\Announcement($view->database);
		$view->total = $hubzeroAnnouncement->count($view->filters);
		$view->rows  = $hubzeroAnnouncement->find($view->filters);

		//display list of announcements
		return $view->loadTemplate();
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
		$active = 'citations';

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$returnhtml = false;
			}
		}

		//Create user object
		$this->juser = JFactory::getUser();

		//creat database object
		$this->database = JFactory::getDBO();

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
					$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
					return $arr;
				}

				//check if guest and force login if plugin access is registered or members
				if ($this->juser->get('guest')
				 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
				{
					$url = JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active);

					$this->redirect(
						JRoute::_('index.php?option=com_users&view=login?return=' . base64_encode($url)),
						JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
						'warning'
					);
					return;
				}

				//check to see if user is member and plugin access requires members
				if (!in_array($this->juser->get('id'), $members) && $group_plugin_acl == 'members')
				{
					$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
					return $arr;
				}
			}

			//run task based on action
			switch ($this->action)
			{
				case 'save':     $arr['html'] .= $this->_save($group);		break;
				case 'add':      $arr['html'] .= $this->_edit($group);		break;
				case 'edit':     $arr['html'] .= $this->_edit($group);		break;
				case 'delete':   $arr['html'] .= $this->_delete();			break;
				case 'browse':	 $arr['html'] .= $this->_browse($group);	break;
				default:         $arr['html'] .= $this->_dashboard($group);
			}
		}

		//filters to get announcement count
		//get count of active
		$filters = array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber'),
			'state'     => 1,
			'published' => 1
		);

		//instantiate announcement object and get count
		$hubzeroAnnouncement = new \Hubzero\Item\Announcement($this->database);
		$total = $hubzeroAnnouncement->count($filters);

		//set metadata for menu
		$arr['metadata']['count'] = $total;
		$arr['metadata']['alert'] = '';

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of all citations, with filtering&search options.
	 *
	 * @return     string HTML
	 */
	private function _dashboard($group)
	{
		//initialize the view
		$view = new \Hubzero\Plugin\View(
				array(
						'folder'  => $this->_type,
						'element' => $this->_name,
						'name'    => 'dashboard'
				)
		);

		//push the group object to the view
		$view->group = $group;

		//appends view override if this is a supergroup
		if ($group->isSuperGroup())
		{
			$view->addTemplatePath(JPATH_ROOT.'/site/groups/'.$group->gidNumber.'/template/plugins/citations/dashboard');
		}

		$view->option = $this->option;

		//grabs the components configuration
		$this->config = JComponentHelper::getParams('com_citations');

		$view->database = $this->database;

		// Load the object
		$row = new CitationsCitation($this->database);


		// Get some stats
		$view->typestats = array();
		$ct = new CitationsType($this->database);
		$types = $ct->getType();

		foreach ($types as $t)
		{
			$view->typestats[$t['type_title']] = $row->getCount(array('type' => $t['id']), false, $group->gidNumber);
		}

		$view->yearlystats = $row->getStats();

		// are we allowing importing?
		$view->allow_import = $this->config->get('citation_import', 1);
		$view->allow_bulk_import = $this->config->get('citation_bulk_import', 1);
		$view->isAdmin = false;

		if ($this->juser->authorize($this->_name, 'import'))
		{
			$view->isAdmin = true;
		}


		return $view->loadTemplate();

	}

	/**
	 * Display a list of all citations, with filtering&search options.
	 *
	 * @return     string HTML
	 */
	private function _browse($group)
	{
		//initialize the view
		$view = new \Hubzero\Plugin\View(
				array(
						'folder'  => $this->_type,
						'element' => $this->_name,
						'name'    => 'browse'
				)
		);

		//push the group object to the view
		$view->group = $group;

		$view->option = $this->option;


		//grabs the components configuration
		$this->config = JComponentHelper::getParams('com_citations');
		$view->task = $this->_name;

		//permissions for importing or creating new citations
		$view->allow_import = $this->config->get('citation_import', 1);
		$view->allow_bulk_import = $this->config->get('citation_bulk_import', 1);


		// Instantiate a new view
		$view->title    = JText::_(strtoupper($this->_name));
		$view->database = $this->database;
		$view->config   = $this->config;

		$view->isAdmin = false;
		if ($this->juser->authorize($this->_name, 'import'))
		{
			$view->isAdmin = true;
		}

		// Instantiate a new citations object
		$obj = new CitationsCitation($this->database);

		//get the earliest year we have citations for
		$view->earliest_year = $obj->getEarliestYear();


		// Incoming
		$view->filters = array();

		//appends view override if this is a supergroup
		if ($group->isSuperGroup())
		{
			$view->addTemplatePath(JPATH_ROOT.'/site/groups/'.$group->gidNumber.'/template/plugins/citations/browse');
			//paging filters
			$view->filters['limit']   = JRequest::getInt('limit', 0, 'request');
			$view->filters['start']   = JRequest::getInt('limitstart', 0, 'get');
		}
		else
		{
			//paging filters
			$view->filters['limit']   = JRequest::getInt('limit', 50, 'request');
			$view->filters['start']   = JRequest::getInt('limitstart', 0, 'get');
		}

		//search/filtering params
		$view->filters['id']			  = JRequest::getInt('id', 0);
		$view->filters['tag']             = trim(JRequest::getVar('tag', '', 'request', 'none', 2));
		$view->filters['search']          = JRequest::getVar('search', '');
		$view->filters['type']            = JRequest::getVar('type', '');
		$view->filters['author']          = JRequest::getVar('author', '');
		$view->filters['publishedin']     = JRequest::getVar('publishedin', '');
		$view->filters['year_start']      = JRequest::getInt('year_start', $view->earliest_year);
		$view->filters['year_end']        = JRequest::getInt('year_end', date("Y"));
		$view->filters['filter']          = JRequest::getVar('filter', '');
		$view->filters['sort']            = JRequest::getVar('sort', 'created DESC');
		$view->filters['reftype']         = JRequest::getVar('reftype', array('research' => 1, 'education' => 1, 'eduresearch' => 1, 'cyberinfrastructure' => 1));
		$view->filters['geo']             = JRequest::getVar('geo', array('us' => 1, 'na' => 1,'eu' => 1, 'as' => 1));
		$view->filters['aff']             = JRequest::getVar('aff', array('university' => 1, 'industry' => 1, 'government' => 1));
		$view->filters['startuploaddate'] = JRequest::getVar('startuploaddate', '0000-00-00');
		$view->filters['enduploaddate']   = JRequest::getVar('enduploaddate', '0000-00-00');

		// Affiliation filter
		$view->filter = array(
				'all'    => JText::_('PLG_GROUPS_CITATIONS_ALL'),
				'aff'    => JText::_('PLG_GROUPS_CITATIONS_AFFILIATED'),
				'nonaff' => JText::_('PLG_GROUPS_CITATIONS_NONAFFILIATED')
		);
		if (!in_array($view->filters['filter'], array_keys($view->filter)))
		{
			$view->filters['filter'] = '';
		}

		// Sort Filter
		$view->sorts = array(
				'sec_cnt DESC' => JText::_('PLG_GROUPS_CITATIONS_CITEDBY'),
				'year DESC'    => JText::_('PLG_GROUPS_CITATIONS_YEAR'),
				'created DESC' => JText::_('PLG_GROUPS_CITATIONS_NEWEST'),
				'title ASC'    => JText::_('PLG_GROUPS_CITATIONS_TITLE'),
				'author ASC'   => JText::_('PLG_GROUPS_CITATIONS_AUTHOR'),
				'journal ASC'  => JText::_('PLG_GROUPS_CITATIONS_JOURNAL')
		);
		if (!in_array($view->filters['sort'], array_keys($view->sorts)))
		{
			$view->filters['sort'] = 'created DESC';
		}

		// Handling ids of the the boxes checked for download
		$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
		$session = JFactory::getSession();

		// If it's new search remove all user citation checkmarks
		if (isset($_POST['filter']))
		{
			$view->filters['idlist'] = "";
			$session->set('idlist', $view->filters['idlist']);
		}
		else
		{
			$view->filters['idlist'] = JRequest::getVar('idlist', $session->get('idlist'));
			$session->set('idlist', $view->filters['idlist']);
		}

		// Reset the filter if the user came from a different section
		if (strpos($referer, "/citations/browse") == false)
		{
			$view->filters['idlist'] = "";
			$session->set('idlist', $view->filters['idlist']);
		}

		//Convert upload dates to correct time format
		if ($view->filters['startuploaddate'] == '0000-00-00'
				|| $view->filters['startuploaddate'] == '0000-00-00 00:00:00'
				|| $view->filters['startuploaddate'] == '')
		{
			$view->filters['startuploaddate'] = '0000-00-00 00:00:00';
		}
		else
		{
			$view->filters['startuploaddate'] = JFactory::getDate($view->filters['startuploaddate'])->format('Y-m-d 00:00:00');
		}
		if ($view->filters['enduploaddate'] == '0000-00-00'
				|| $view->filters['enduploaddate'] == '0000-00-00 00:00:00'
				|| $view->filters['enduploaddate'] == '')
		{
			$view->filters['enduploaddate'] = JFactory::getDate()->modify('+1 DAY')->format('Y-m-d 00:00:00');
		}
		else
		{
			$view->filters['enduploaddate'] = JFactory::getDate($view->filters['enduploaddate'])->format('Y-m-d 00:00:00');
		}

		//Make sure the end date for the upload search isn't before the start date
		if ($view->filters['startuploaddate'] > $view->filters['enduploaddate'])
		{
			$this->setRedirect(
					JRoute::_('index.php?option=com_citations&task=browse'),
					JText::_('PLG_GROUPS_CITATIONS_END_DATE_MUST_BE_AFTER_START_DATE'),
					'error'
			);
			return;
		}

		$group_id = $group->get('gidNumber');

		// Get a record count
		$total = $obj->getCount($view->filters, $view->isAdmin, $group_id);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
				$total,
				$view->filters['start'],
				$view->filters['limit']
		);


		// Get records
		$view->filters['group'] =  $group_id;
		$citations = $obj->getRecords($view->filters, $view->isAdmin);
		$view->citations = $citations;


		// Add some data to our view for form filtering/sorting
		$ct = new CitationsType($this->database);
		$view->types = $ct->getType();

		//for the inemo-navigation
		//grabs the IDs of the types for navigation purposes without making additional queries.
				$view->typeName = '';
		if (isset($view->filters['type']))
		{
			foreach ($view->types as $type)
			{
				if ($view->filters['type'] == $type['id'])
				{
					$view->typeName = $type["type_title"];
				}
			}
		}


		//get the users id to make lookup
		$users_ip = $this->getIP();

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
		$view->openurl = array(
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
			$view->openurl['link'] = $resolver->baseURL;
			$view->openurl['text'] = $resolver->linkText;
			$view->openurl['icon'] = $resolver->linkIcon;
		}

		// Output HTML
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		//get any messages
		//$view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		//are we allowing importing
		//$view->allow_import = $this->config->get('citation_import', 1);
		//$view->allow_bulk_import = $this->config->get('citation_bulk_import', 1);

		return $view->loadTemplate();
	}

	/**
	 * Display the form allowing to edit a citation
	 *
	 * @return     string HTML
	 */
	private function _edit($group)
	{
		//create view object
		$view = new \Hubzero\Plugin\View(
				array(
						'folder'  => $this->_type,
						'element' => $this->_name,
						'name'    => 'edit',
						'option'  => 'edit',
						'layout'  => 'sticky'
				)
		);

		//appends view override if this is a supergroup
		if ($group->isSuperGroup())
		{
			$view->addTemplatePath(JPATH_ROOT.'/site/groups/'.$group->gidNumber.'/template/plugins/citations/edit');
		}


		//push the group object to the view
		$view->group = $group;

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->_loginTask($group);
		}

		// Check if admin
		$isAdmin = false;
		if ($this->juser->authorize($this->_name, 'manage'))
		{
			$isAdmin = true;

		}

		//are we allowing user to add citation
		$this->config = JComponentHelper::getParams('com_citations');
		$view->config = $this->config;
		$allowImport = $this->config->get('citation_import', 1);
		if ($allowImport == 0
				|| ($allowImport == 2 && $this->juser->get('usertype') != 'Super Administrator'))
		{
			// Redirect
			$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_name, false),
					JText::_('PLG_GROUPS_CITATION_EDIT_NOTALLOWED'),
					'warning'
			);
			return;
		}

		//get the citation types
		$ct = new CitationsType($this->database);
		$types = $ct->getType();

		$fields = array();
		foreach ($types as $type)
		{
			if (isset($type['fields']))
			{
				$f = $type['fields'];
				if (strpos($f, ',') !== false)
				{
					$f = str_replace(',', "\n", $f);
				}

				$f = array_map('trim', explode("\n", $f));
				$f = array_values(array_filter($f));

				$fields[strtolower(str_replace(' ', '', $type['type_title']))] = $f;
			}
		}

		//add an empty value for the first type
		array_unshift($types, array(
				'type'       => '',
				'type_title' => ' - Select a Type &mdash;'
		));

		// Incoming - expecting an array id[]=4232
		$id = JRequest::getInt('id', 0);

		// Pub author
		$pubAuthor = false;

		// Load the associations object
		$assoc = new CitationsAssociation($this->database);

		// Get associations
		if ($id)
		{
			$view->assocs = $assoc->getRecords(array('cid' => $id), $isAdmin);

			$pubAuthor = $this->isPubAuthor($view->assocs);
		}

		// Is user authorized to edit citations?
		if (!$isAdmin && !$pubAuthor)
		{
			$id = 0;
		}

		// Load the object
		$view->row = new CitationsCitation($this->database);
		$view->row->load($id);

		//make sure title isnt too long
		$maxTitleLength = 30;
		$shortenedTitle = (strlen($view->row->title) > $maxTitleLength)
		? substr($view->row->title, 0, $maxTitleLength) . '&hellip;'
		: $view->row->title;

		// Set the pathway
		$pathway = JFactory::getApplication()->getPathway();
		$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option=' . $this->_name);
		if ($id && $id != 0)
		{
			$pathway->addItem( $shortenedTitle, 'index.php?option=' . $this->_name . '&task=view&id=' . $view->row->id);
		}
		$pathway->addItem( JText::_('PLG_GROUPS_CITATIONS_EDIT'), 'index.php?option=' . $this->_name . '&task=edit&id=' . $view->row->id);

		// Set the page title
		$document = JFactory::getDocument();
		$document->setTitle( JText::_('PLG_GROUPS_CITATIONS_CITATION') . $shortenedTitle );

		//push jquery to doc
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('var fields = ' . json_encode($fields) . ';');

		// Instantiate a new view
		$view->title  = JText::_(strtoupper($this->_name)) . ': ' . JText::_(strtoupper($this->_name) . '_' . strtoupper($this->action));
		$view->config = $this->config;

		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id)
		{
			$view->row->uid = $this->juser->get('id');

			// It's new - no associations to get
			$view->assocs = array();

			//tags & badges
			$view->tags   = array();
			$view->badges = array();
		}
		else
		{
			//tags & badges
			$view->tags = CitationFormat::citationTags($view->row, $this->database, false);
			$view->badges = CitationFormat::citationBadges($view->row, $this->database, false);
		}

		//get the citation types
		$ct = new CitationsType($this->database);
		$view->types = $ct->getType();


		// Output HTML
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		return $view->loadTemplate();
	}


	private function _delete()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask();
			return;
		}

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0)
		{
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation($this->database);
			$assoc    = new CitationsAssociation($this->database);
			$author   = new CitationsAuthor($this->database);
			foreach ($ids as $id)
			{
				// Fetch and delete all the associations to this citation
				$isAdmin = ($this->juser->get("usertype") == "Super Administrator") ? true : false;
				$assocs = $assoc->getRecords(array('cid' => $id), $isAdmin);
				foreach ($assocs as $a)
				{
					$assoc->delete($a->id);
				}

				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords(array('cid' => $id), $isAdmin);
				foreach ($authors as $a)
				{
					$author->delete($a->id);
				}

				// Delete the citation
				$citation->delete($id);
			}
		}

		// Redirect
		$this->setRedirect(
				'index.php?option=' . $this->_name
		);
	}


	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	private function _save($group)
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->_loginTask($group);
		}

		//get the posted vars
		$c = $_POST;
		if (isset($c['format_type']))
		{
			$c['format'] = $c['format_type'];
		}

		//get tags
		$tags = trim(JRequest::getVar('tags', ''));
		unset($c['tags']);

		//get badges
		$badges = trim(JRequest::getVar('badges', ''));
		unset($c['badges']);

		// Bind incoming data to object
		$row = new CitationsCitation($this->database);
		if (!$row->bind($c))
		{
			$this->setError($row->getError());
			$this->_browse();
			return;
		}

		// New entry so set the created date
		if (!$row->id)
		{
			$row->created = JFactory::getDate()->toSql();
		}

		// Field named 'uri' due to conflict with existing 'url' variable
		$row->url = JRequest::getVar('uri', '', 'post');

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
		$arr = JRequest::getVar('assocs', array());

		$ignored = array();

		foreach ($arr as $a)
		{
			$a = array_map('trim', $a);

			// Initiate extended database class
			$assoc = new CitationsAssociation($this->database);

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

			$ct1 = new CitationTags($row->id);
			$ct1->setTags($tags, $this->juser->get('id'), 0, 1, '');

			$this->redirect(
			JRoute::_('index.php?option=com_groups' . DS . $this->group->cn . DS .'citations',
			JText::_('PLG_GROUPS_CITATIONS_NOT_LOGGEDIN'),
				'warning')
			);

		return;
	}
	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_name));
		if ($this->_name)
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_name));
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
					JText::_(strtoupper($this->_name)),
					'index.php?option=' . $this->_name
			);
		}
		if ($this->action)
		{
			$pathway->addItem(
					JText::_(strtoupper($this->_name) . '_' . strtoupper($this->action)),
					'index.php?option=' . $this->_name . '&task=' . $this->action
			);
		}
	}


	/**
	 * Get user IP
	 *
	 * @return     string
	 */
	private function getIP()
	{
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
		{
			if (array_key_exists($key, $_SERVER) === true)
			{
				foreach (explode(',', $_SERVER[$key]) as $ip)
				{
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false)
					{
						return $ip;
					}
				}
			}
		}
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
		if (!is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php'))
		{
			return false;
		}

		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');

		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.owner.php');

		// Get connections to publications
		foreach ($assocs as $entry)
		{
			if ($entry->tbl == 'publication')
			{
				$pubID = $entry->oid;
				$objP = new Publication($this->database);

				if ($objP->load($pubID))
				{
					$objO = new ProjectOwner($this->database);

					if ($objO->isOwner($this->juser->get('id'), $objP->project_id))
					{
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Redirect to login form
	 *
	 * @return     void
	 */
	private function _loginTask()
	{

		$this->redirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . DS . DS. $this->group->cn . DS. $this->_name .'&action=' . $this->action, false, true))),
				JText::_('PLG_GROUPS_CITATIONS_NOT_LOGGEDIN'),
				'warning'
		);
		return;
	}

} //end class
