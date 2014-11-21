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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Primary component controller (extends \Hubzero\Component\SiteController)
 */
class PublicationsControllerPublications extends \Hubzero\Component\SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Is component enabled?
		if ($this->config->get('enabled', 0) == 0)
		{
			$this->_redirect = JRoute::_('index.php?option=com_resources');
			return;
		}

		// Logging
		$this->_logging = false;
		if ( is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_publications' . DS . 'tables' . DS . 'logs.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_publications' . DS . 'tables' . DS . 'logs.php');
			$this->_logging = true;
		}

		// Are we allowing contributions
		$this->_contributable = JPluginHelper::isEnabled('projects', 'publications') ? 1 : 0;

		$this->_task  = JRequest::getVar( 'task', '' );
		$this->_id    = JRequest::getInt( 'id', 0 );
		$this->_alias = JRequest::getVar( 'alias', '' );
		$this->_resid = JRequest::getInt( 'resid', 0 );

		if (strrpos(strtolower($this->_alias), '.rdf') > 0)
		{
			$this->_resourceMap();
			return;
		}

		// Set the default task
		$this->registerTask('__default', 'intro');

		// Register tasks
		$this->registerTask('view', 'page');
		$this->registerTask('download', 'serve');
		$this->registerTask('video', 'serve');
		$this->registerTask('play', 'serve');
		$this->registerTask('watch', 'serve');

		$this->registerTask('wiki', 'wikipage');
		$this->registerTask('submit', 'contribute');
		$this->registerTask('edit', 'contribute');
		$this->registerTask('start', 'contribute');
		$this->registerTask('publication', 'contribute');

		if (($this->_id || $this->_alias) && !$this->_task)
		{
			$this->_task = 'page';
		}
		elseif (!$this->_task)
		{
			$this->_task = 'intro';
		}

		parent::execute();
	}

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway()
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}

		if ($this->publication && ($this->_task == 'view'
			|| $this->_task == 'serve' || $this->_task == 'wiki'))
		{
			$url = 'index.php?option='.$this->_option.'&id='.$this->publication->id;

			// Link to category
			$pathway->addItem(
				$this->publication->cat_name,
				'index.php?option='.$this->_option.'&category='.$this->publication->cat_url
			);

			// Link to publication
			if ($this->version && $this->version != 'default')
			{
				$url .= '&v='.$this->version;
			}
			$pathway->addItem(
				$this->publication->title,
				$url
			);

			if ($this->_task == 'serve' || $this->_task == 'wiki')
			{
				$pathway->addItem(
					JText::_('COM_PUBLICATIONS_SERVING_CONTENT'),
					'index.php?option='.$this->_option.'&task='.$this->_task
				);
			}
		}
		elseif (count($pathway->getPathWay()) <= 1 && $this->_task)
		{
			switch ($this->_task)
			{
				case 'browse':
				case 'submit':
					if ($this->_task_title)
					{
						$pathway->addItem(
							$this->_task_title,
							'index.php?option='.$this->_option.'&task='.$this->_task
						);
					}
					break;

				case 'start':
					if ($this->_task_title)
					{
						$pathway->addItem(
							$this->_task_title,
							'index.php?option='.$this->_option.'&task=submit'
						);
					}
					break;

				case 'block':
				case 'intro':
					// Nothing
					break;

				default:
					$pathway->addItem(
						JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						'index.php?option='.$this->_option.'&task='.$this->_task
					);
					break;
			}
		}
	}

	/**
	 * Build the title for this component
	 *
	 * @return void
	 */
	protected function _buildTitle()
	{
		if (!$this->_title)
		{
			$this->_title = JText::_(strtoupper($this->_option));
			if ($this->_task)
			{
				switch ($this->_task)
				{
					case 'browse':
					case 'submit':
					case 'start':
					case 'intro':
						if ($this->_task_title)
						{
							$this->_title .= ': '.$this->_task_title;
						}
						break;

					case 'serve':
					case 'wiki':
						$this->_title .= ': '. JText::_('COM_PUBLICATIONS_SERVING_CONTENT');
						break;

					default:
						$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
						break;
				}
			}
		}
		$document = JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	/**
	 * Set notifications
	 *
	 * @param  string $message
	 * @param  string $type
	 * @return void
	 */
	public function setNotification( $message, $type = 'success' )
	{
		// If message is set push to notifications
		if ($message != '')
		{
			$this->addComponentMessage($message, $type);
		}
	}

	/**
	 * Get notifications
	 * @param  string $type
	 * @return $messages if they exist
	 */

	public function getNotifications($type = 'success')
	{
		// Get messages in quene
		$messages = $this->getComponentMessage();

		// Return first message of type
		if ($messages && count($messages) > 0)
		{
			foreach ($messages as $message)
			{
				if ($message['type'] == $type)
				{
					return $message['message'];
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Login view
	 *
	 * @return     void
	 */
	protected function _login()
	{
		$rtrn = JRequest::getVar('REQUEST_URI',
			JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
			$this->_msg,
			'warning'
		);
	}

	/**
	 * Intro to publications (main view)
	 *
	 * @return     void
	 */
	public function introTask()
	{
		$this->view->setLayout('intro');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$this->view->title 			= $this->_title;
		$this->view->option 		= $this->_option;
		$this->view->database 		= $this->database;
		$this->view->config 		= $this->config;
		$this->view->contributable 	= $this->_contributable && $this->config->get('contribute') == 1 ? true : false;

		$this->view->filters 		   = array();
		$this->view->filters['sortby'] = 'date_published';
		$this->view->filters['limit']  = $this->config->get('listlimit', 10);
		$this->view->filters['start']  = JRequest::getInt( 'limitstart', 0 );

		// Instantiate a publication object
		$rr = new Publication( $this->database );

		// Get most recent pubs
		$this->view->results = $rr->getRecords( $this->view->filters );

		// Get most popular/oldest pubs
		$this->view->filters['sortby'] = 'popularity';
		$this->view->best = $rr->getRecords( $this->view->filters );

		// Get publications helper
		$helper = new PublicationHelper($this->database);
		$this->view->helper = $helper;

		// Get major types
		$t = new PublicationCategory( $this->database );
		$this->view->categories = $t->getCategories(array('itemCount' => 1));

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
		return;
	}

	/**
	 * Browse publications
	 *
	 * @return     void
	 */
	public function browseTask()
	{
		// Set the default sort
		$default_sort = 'date';
		if ($this->config->get('show_ranking'))
		{
			$default_sort = 'ranking';
		}

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$this->view->filters = array(
			'category'   	=> JRequest::getVar('category', ''),
			'sortby' 		=> JRequest::getCmd('sortby', $default_sort),
			'limit'  		=> JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'  		=> JRequest::getInt('limitstart', 0),
			'search' 		=> JRequest::getVar('search', ''),
			'tag'    		=> trim(JRequest::getVar('tag', '', 'request', 'none', 2)),
			'tag_ignored' 	=> array()
		);

		// Get projects user has access to
		if (!$this->juser->get('guest'))
		{
			$obj = new Project( $this->database );
			$this->view->filters['projects']  = $obj->getUserProjectIds($this->juser->get('id'));
		}

		// Get major types
		$t = new PublicationCategory( $this->database );
		$this->view->categories = $t->getCategories();

		if (!is_int($this->view->filters['category']))
		{
			foreach ($this->view->categories as $cat)
			{
				if (trim($this->view->filters['category']) == $cat->url_alias)
				{
					$this->view->filters['category'] = $cat->id;
					break;
				}
			}
		}

		// Instantiate a publication object
		$rr = new Publication( $this->database );

		// Execute count query
		$results = $rr->getCount( $this->view->filters );
		$this->view->total = ($results && is_array($results)) ? count($results) : 0;

		// Run query with limit
		$this->view->results = $rr->getRecords( $this->view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination( $this->view->total, $this->view->filters['start'], $this->view->filters['limit'] );

		// Get type if not given
		$this->_title = JText::_(strtoupper($this->_option)) . ': ';
		if ($this->view->filters['category'] != '')
		{
			$t->load( $this->view->filters['category'] );
			$this->_title .= $t->name;
			$this->_task_title = $t->name;
		}
		else
		{
			$this->_title .= JText::_('COM_PUBLICATIONS_ALL');
			$this->_task_title = JText::_('COM_PUBLICATIONS_ALL');
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->title = $this->_title;
		$this->view->config = $this->config;
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->setName('browse')
					->setLayout('default')
					->display();
		return;
	}

	/**
     * Retrieves the data from database and compose the RDF file for download.
     */
	protected function _resourceMap()
	{
		$resourceMap = new ResourceMapGenerator();
		$id = "";

		// Retrieves the ID from alias
		if (substr(strtolower($this->_alias), -4) == ".rdf")
		{
			$lastSlash = strrpos($this->_alias, "/");
			$lastDot = strrpos($this->_alias, ".rdf");
			$id = substr($this->_alias, $lastSlash, $lastDot);
		}

		// Create download headers
		$resourceMap->pushDownload($this->config->get('webpath'));
		exit;
	}

	/**
	 * View publication
	 *
	 * @return     void
	 */
	public function pageTask()
	{
		$this->view->setName('view');

		// Incoming
		$fsize    = JRequest::getVar( 'fsize', '' );    // A parameter to see file size without formatting
		$version  = JRequest::getVar( 'v', '' );        // Get version number of a publication
		$tab      = JRequest::getVar( 'active', '' );   // The active tab (section)
		$no_html  = JRequest::getInt( 'no_html', 0 );   // No-html display?

		$id 	= $this->_id;
		$alias 	= $this->_alias;

		$objP   = new Publication( $this->database );

		// Ensure we have an ID or alias to work with
		if (!$id && !$alias)
		{
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// Curation?
		$useBlocks = $this->config->get('curation', 0);
		if ($useBlocks)
		{
			// We need our curation model to parse elements
			if (JPATH_ROOT . DS . 'components' . DS . 'com_publications'
				. DS . 'models' . DS . 'curation.php')
			{
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications'
					. DS . 'models' . DS . 'curation.php');
			}
			else
			{
				JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_LOADING_REQUIRED_LIBRARY') );
				return;
			}
		}

		// Check that version number exists
		$objV 	 = new PublicationVersion( $this->database );
		$version = in_array($version, array('dev', 'default')) ? $version : intval($version);
		$version = $version && $objV->checkVersion($id, $version) ? $version : 'default';

		// Get publication
		$publication = $objP->getPublication($id, $version, NULL, $alias);

		// Make sure we got a result from the database
		if (!$publication)
		{
			if ($alias)
			{
				$this->_redirect = JRoute::_('index.php?option='.$this->_option);
				return;
			}
			else
			{
				$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
				$this->introTask();
				return;
			}
		}
		else
		{
			$id = $publication->id;
			$alias = $publication->alias;

			// Default version?
			$version = $publication->main == 1 && $publication->state != 0 ? 'default' : $version;

			// No published version yet? - Draft
			$version = $publication->state == 3 ? 'dev' : $version;
		}

		// Get last public release info
		$objV = new PublicationVersion( $this->database );
		$lastPubRelease = $objV->getLastPubRelease($id);

		// Check authorization
		$authorized = $this->_authorize($publication->project_id);

		// Dev version/pending/posted/dark archive resource? Must be project owner
		if ($publication->state != 1)
		{
			// Check if project owner
			if (!$authorized)
			{
				$block =  $publication->state == 0 ? 0 : 1;

				// Do we by any chance have a public version to display?
				if ($version == 'default' && $publication->versions > 1)
				{
					if ($lastPubRelease && $lastPubRelease->id)
					{
						$publication = $objP->getPublication($id, $lastPubRelease->version_number, NULL, $alias);
						if ($publication)
						{
							$block = 0;
						}
					}
				}
				if ($block)
				{
					$this->_blockAccess($publication);
					return;
				}
			}
		}

		// Get groups user has access to
		$xgroups = \Hubzero\User\Helper::getGroups($this->juser->get('id'), 'all');
		$usersgroups = $this->getGroupProperty($xgroups);

		// Extra authorization for restricted publications
		$restricted = false;

		if ($publication->access == 3 || $publication->access == 2)
		{
			$restricted = $this->_checkGroupAccess($publication, $version, $usersgroups);
		}
		if ($publication->access == 1 && $this->juser->get('guest'))
		{
			$restricted = true;
		}
		if ($publication->access == 3 && !$authorized)
		{
			if ($restricted)
			{
				$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NO_ACCESS') );
				$this->introTask();
				return;
			}
		}

		// Check for embargo
		$now = JFactory::getDate()->toSql();

		if (!$authorized && $publication->published_up > $now)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NO_ACCESS') );
			$this->introTask();
			return;
		}

		// Deleted resource?
		if ($publication->state == 2)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_DELETED') );
			$this->introTask();
			return;
		}

		// Load publication project
		$publication->_project = new Project($this->database);
		$publication->_project->load($publication->project_id);

		// Whew! Finally passed all the checks
		// Let's get down to business...
		$this->publication = $publication;
		$this->version     = $version;

		// Initiate a helper class
		$helper = new PublicationHelper($this->database, $publication->version_id, $publication->id);

		// Get version authors
		$pa = new PublicationAuthor( $this->database );
		$authors = $pa->getAuthors($publication->version_id);
		$publication->_authors = $authors;

		// Get submitter
		$publication->submitter = $pa->getSubmitter($publication->version_id, $publication->created_by);

		// Get publication plugins
		JPluginHelper::importPlugin( 'publications' );
		$dispatcher = JDispatcher::getInstance();

		// Get category info
		$publication->_category = new PublicationCategory( $this->database );
		$publication->_category->load($publication->category);
		$publication->_category->_params = new JParameter( $publication->_category->params );

		// Get master type info
		$publication->_mastertype = new PublicationMasterType( $this->database );
		$publication->_mastertype->load($publication->master_type);
		$publication->_mastertype->_params = new JParameter( $publication->_mastertype->params );

		// Get pub type helper
		$publication->pubTypeHelper = new PublicationTypesHelper($this->database, $publication->_project);

		// Get attachments
		$pContent = new PublicationAttachment( $this->database );
		$publication->_attachments = $pContent->sortAttachments ( $publication->version_id );

		// Get content
		$pContent = new PublicationAttachment($this->database);
		$content = array();
		$content['primary']   = isset($publication->_attachments[1]) ? $publication->_attachments[1] : NULL;
		$content['secondary'] = isset($publication->_attachments[2]) ? $publication->_attachments[2] : NULL;

		// For curation we need somewhat different vars
		// TBD - streamline
		if ($useBlocks)
		{
			$publication->_submitter = $publication->submitter;
			$publication->version	 = $version;
			$publication->_type  	 = $publication->_mastertype;

			// Initialize helpers
			$publication->_helpers = new stdClass();
			$publication->_helpers->pubHelper 		= new PublicationHelper(
				$this->database,
				$publication->version_id,
				$publication->id
			);
			$publication->_helpers->htmlHelper	  	= new PublicationsHtml();
			$publication->_helpers->projectsHelper 	= new ProjectsHelper( $this->database );

			// Get manifest from either version record (published) or master type
			$manifest = $publication->curation
						? $publication->curation
						: $publication->_type->curation;

			// Get curation model
			$publication->_curationModel = new PublicationsCuration(
				$this->database,
				$manifest
			);

			// Set pub assoc and load curation
			$publication->_curationModel->setPubAssoc($publication);
		}

		// Build publication path (to access attachments)
		$base_path = $this->config->get('webpath');
		$path = $helper->buildPath($id, $publication->version_id, $base_path, $publication->secret);

		// Build log path (access logs)
		$logPath = $helper->buildPath($id, $publication->version_id, $base_path, 'logs');

		// Start sections
		$sections = array();
		$cats = array();
		$tab = $tab ? $tab : 'about';

		// Show extended pub info like reviews, questions etc.
		$extended = $lastPubRelease && $lastPubRelease->id == $publication->version_id ? true : false;

		// Get sections
		// Trigger the functions that return the areas we'll be using
		$cats = $dispatcher->trigger( 'onPublicationAreas', array($publication, $version, $extended, $authorized) );

		// Get the sections
		$sections = $dispatcher->trigger( 'onPublication',
			array($publication, $this->_option, array($tab), 'all',
			$version, $extended, $authorized) );

		$available = array('play');
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '')
			{
				$available[] = $name;
			}
		}
		if ($tab != 'about' && !in_array($tab, $available))
		{
			$tab = 'about';
		}

		// Get parameters and merge with the component params
		$rparams = new JParameter( $publication->params );
		$params = $this->config;

		// Merge params
		$params->merge( $publication->_mastertype->_params );
		$params->merge( $rparams );

		// Get license info
		$pLicense = new PublicationLicense($this->database);
		$license = $pLicense->getLicense($publication->license_type);

		$body = '';
		if ($tab == 'about')
		{
			// Build the HTML of the "about" tab
			$view = new \Hubzero\Component\View(array(
				'name'   => 'about',
				'layout' => 'default'
			));
			$view->option 		= $this->_option;
			$view->config 		= $this->config;
			$view->database 	= $this->database;
			$view->publication 	= $publication;
			$view->helper 		= $helper;
			$view->authorized 	= $authorized;
			$view->restricted 	= $restricted;
			$view->version 		= $version;
			$view->usersgroups 	= $usersgroups;
			$view->sections 	= $sections;
			$view->authors 		= $authors;
			$view->params 		= $params;
			$body = $view->loadTemplate();

			// Log page view (public pubs only)
			if ($this->_logging && $this->_task == 'view' && $publication->state == 1)
			{
				$pubLog = new PublicationLog($this->database);
				$pubLog->logAccess($publication, 'view', $logPath);
			}
		}

		// Add the default "About" section to the beginning of the lists
		$cat = array();
		$cat['about'] = JText::_('COM_PUBLICATIONS_ABOUT');
		array_unshift($cats, $cat);
		array_unshift($sections, array( 'html'=>$body, 'metadata'=>'' ));

		// Get filters (for series & workshops listing)
		$filters 			= array();
		$defaultsort 		= ($publication->cat_alias == 'series') ? 'date' : 'ordering';
		$defaultsort 		= ($publication->cat_alias == 'series'
							&& $this->config->get('show_ranking'))
							? 'ranking' : $defaultsort;
		$filters['sortby'] 	= JRequest::getVar( 'sortby', $defaultsort );
		$filters['limit']  	= JRequest::getInt( 'limit', 0 );
		$filters['start']  	= JRequest::getInt( 'limitstart', 0 );
		$filters['id']     	= $publication->id;

		// Write title & build pathway
		$document = JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_option)).': '.stripslashes($publication->title) );

		// Set the pathway
		$this->_buildPathway();

		// Determine the layout we're using
		$layout = 'default';
		$app = JFactory::getApplication();
		if ($publication->cat_alias
		 && (is_file(JPATH_ROOT . DS . 'templates' . DS .  $app->getTemplate()  . DS . 'html'
			. DS . $this->_option . DS . 'view' . DS . $publication->cat_url.'.php')
		 || is_file(JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'views' . DS . 'view'
			. DS . 'tmpl' . DS . $publication->cat_url.'.php')))
		{
			$layout = $type_alias;
		}

		$this->view->setLayout($layout);

		$this->view->version 		= $version;
		$this->view->config 		= $this->config;
		$this->view->option 		= $this->_option;
		$this->view->publication 	= $publication;
		$this->view->params 		= $params;
		$this->view->authorized 	= $authorized;
		$this->view->restricted 	= $restricted;
		$this->view->content 		= $content;
		$this->view->authors 		= $authors;
		$this->view->cats 			= $cats;
		$this->view->tab 			= $tab;
		$this->view->sections 		= $sections;
		$this->view->database 		= $this->database;
		$this->view->usersgroups 	= $usersgroups;
		$this->view->helper 		= $helper;
		$this->view->filters 		= $filters;
		$this->view->license 		= $license;
		$this->view->path 			= $path;
		$this->view->lastPubRelease = $lastPubRelease;
		$this->view->contributable 	= $this->_contributable;

		// Archival package
		$tarname  = JText::_('Publication').'_'.$publication->id.'.zip';
		$this->view->archPath = JPATH_ROOT . $helper->buildPath($id, $publication->version_id, $base_path) . DS . $tarname;

		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		if ($no_html)
		{
			// TBD - no_html view
		}

		// Output HTML
		$this->view->display();

		// Insert .rdf link in the header
		ResourceMapGenerator::putRDF($id);

		return;
	}

	/**
	 * Use handlers to deliver attachments
	 *
	 * @return     void
	 */
	protected function _handleContent()
	{
		// Incoming
		$aid	  = JRequest::getInt( 'a', 0 );             // Attachment id
		$element  = JRequest::getInt( 'el', 1 );            // Element id, default to first

		if (!$this->publication)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		// We need our curation model to parse elements
		if (JPATH_ROOT . DS . 'components' . DS . 'com_publications'
			. DS . 'models' . DS . 'curation.php')
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications'
				. DS . 'models' . DS . 'curation.php');
		}
		else
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		// Load master type
		$mt = new PublicationMasterType( $this->database );
		$this->publication->_type = $mt->getType($this->publication->base);
		$this->publication->version = $this->version;

		// Load publication project
		$this->publication->_project = new Project($this->database);
		$this->publication->_project->load($this->publication->project_id);

		// Get attachments
		$pContent = new PublicationAttachment( $this->database );
		$this->publication->_attachments = $pContent->sortAttachments ( $this->publication->version_id );

		// We do need attachments
		if (!isset($this->publication->_attachments['elements'][$element])
			|| empty($this->publication->_attachments['elements'][$element]))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS') );
			return;
		}

		// Initialize helpers
		$this->publication->_helpers = new stdClass();
		$this->publication->_helpers->pubHelper = new PublicationHelper(
			$this->database,
			$this->publication->version_id,
			$this->publication->id
		);
		$this->publication->_helpers->htmlHelper = new PublicationsHtml();

		// Get manifest from either version record (published) or master type
		$manifest = $this->publication->curation
					? $this->publication->curation
					: $this->publication->_type->curation;

		// Get curation model
		$this->publication->_curationModel = new PublicationsCuration(
			$this->database,
			$manifest
		);

		// Set pub assoc and load curation
		$this->publication->_curationModel->setPubAssoc($this->publication);

		// Get element manifest to deliver content as intended
		$curation = $this->publication->_curationModel->getElementManifest($element);

		// We do need manifest!
		if (!$curation || !isset($curation->element) || !$curation->element)
		{
			return false;
		}

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->database);

		// Serve content
		$content = $attModel->serve(
			$curation->element->params->type,
			$curation->element,
			$element,
			$this->publication,
			$curation->block->params,
			$aid
		);

		// No content served
		if ($content === NULL || $content == false)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS') );
			return;
		}
		else
		{
			// Do we need to redirect to content?
			if ($attModel->get('redirect'))
			{
				$this->_redirect = $attModel->get('redirect');
				return;
			}

			return $content;
		}

		return;
	}

	/**
	 * Serve publication content inline (if no JS)
	 * Play tab
	 *
	 * @return     void
	 */
	protected function _playContent()
	{
		if (!$this->publication)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		if (!$this->content)
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a . 'id=' . $this->publication->id);
			return;
		}

		// Get publication plugins
		JPluginHelper::importPlugin( 'publications' );
		$dispatcher = JDispatcher::getInstance();

		// Get type info
		$this->publication->_category = new PublicationCategory( $this->database );
		$this->publication->_category->load($this->publication->category);
		$this->publication->_category->_params = new JParameter( $this->publication->_category->params );

		// Get parameters and merge with the component params
		$rparams = new JParameter( $this->publication->params );
		$params = $this->config;
		$params->merge( $rparams );

		// Get publication helper
		$helper = new PublicationHelper($this->database);

		// Get cats
		$cats = $dispatcher->trigger( 'onPublicationAreas', array($this->publication, $this->version, false, true) );

		// Get the sections
		$sections = $dispatcher->trigger( 'onPublication',
			array($this->publication, $this->_option, array('play'), 'all',
			$this->version, false, true) );

		// Get license info
		$pLicense = new PublicationLicense($this->database);
		$license = $pLicense->getLicense($this->publication->license_type);

		// Get version authors
		$pa = new PublicationAuthor( $this->database );
		$authors = $pa->getAuthors($this->publication->version_id);
		$this->publication->_authors = $authors;

		// Build publication path
		$base_path = $this->config->get('webpath');
		$path = $helper->buildPath($this->publication->id, $this->publication->version_id, $base_path, $this->publication->secret);

		// Add the default "About" section to the beginning of the lists
		$cat = array();
		$cat['about'] = JText::_('COM_PUBLICATIONS_ABOUT');
		array_unshift($cats, $cat);
		array_unshift($sections, array( 'html'=> '', 'metadata'=>'' ));

		$cat = array();
		$cat['play'] = JText::_('COM_PUBLICATIONS_TAB_PLAY_CONTENT');
		$cats[] = $cat;
		$sections[] = array('html' => $this->content, 'metadata' => '', 'area' => 'play');

		// Write title & build pathway
		$document = JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_option)).': '.stripslashes($this->publication->title) );

		// Set the pathway
		$this->_buildPathway();

		$this->view->option 		= $this->_option;
		$this->view->config 		= $this->config;
		$this->view->database 		= $this->database;
		$this->view->publication 	= $this->publication;
		$this->view->cats 			= $cats;
		$this->view->tab 			= 'play';
		$this->view->sections 		= $sections;
		$this->view->database 		= $this->database;
		$this->view->helper 		= $helper;
		$this->view->filters 		= array();
		$this->view->license 		= $license;
		$this->view->path 			= $path;
		$this->view->authors 		= $authors;
		$this->view->authorized 	= true;
		$this->view->restricted 	= false;
		$this->view->usersgroups  	= NULL;
		$this->view->params 		= $params;
		$this->view->content 		= array('primary' => array());
		$this->view->version		= $this->version;
		$this->view->lastPubRelease = NULL;
		$this->view->contributable 	= false;

		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		// Output HTML
		$this->view->setName('view')
					->setLayout('default')
					->display();
		return;
	}

	 /**
	 * Serve publication content
	 * Determine how to render depending on master type, attachment type and user choice
	 * Defaults to download
	 *
	 * @return     void
	 */
	public function serveTask()
	{
		// Incoming
		$version  = JRequest::getVar( 'v', '' );            // Get version number of a publication
		$aid	  = JRequest::getInt( 'a', 0 );             // Attachment id
		$element  = JRequest::getInt( 'el', 0 );            // Element id
		$render	  = JRequest::getVar( 'render', '' );
		$disp	  = JRequest::getVar( 'disposition', 'attachment' );
		$disp	  = $disp == 'inline' ? $disp : 'attachment';
		$no_html  = JRequest::getInt('no_html', 0);

		// In dataview
		$vid   = JRequest::getInt( 'vid', '' );
		$file  = JRequest::getVar( 'file', '' );
		if ($vid && $file)
		{
			$this->_serveData();
			return;
		}

		$downloadable = array();

		// Make sure render type is available
		$renderTypes = array ('download' , 'inline', 'link', 'archive', 'video', 'presenter');
		$render = in_array($render, $renderTypes) ? $render : '';

		// Ensure we have an ID or alias to work with
		if (!$this->_id && !$this->_alias)
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Load version by ID
		$objPV 	  = new PublicationVersion( $this->database );
		if ($vid && !$objPV->load($vid))
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			$this->introTask();
			return;
		}
		elseif ($vid)
		{
			$version = $objPV->version_number;
		}

		// Which version is requested?
		$version = $version == 'dev' ? 'dev' : $version;
		$version = (($version && intval($version) > 0) || $version == 'dev') ? $version : 'default';

		// Get publication
		$objP 		 = new Publication( $this->database );
		$publication = $objP->getPublication($this->_id, $version, NULL, $this->_alias);

		// Make sure we got a result from the database
		if (!$publication)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		// Unpublished / deleted
		if ($publication->state == 0 || $publication->state == 2)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NO_ACCESS') );
			$this->introTask();
			return;
		}

		// Save loaded objects
		$this->publication = $publication;
		$this->version     = $version;

		// Check if the resource is for logged-in users only and the user is logged-in
		if (($token = JRequest::getVar('token', '', 'get')))
		{
			$token = base64_decode($token);

			jimport('joomla.utilities.simplecrypt');
			$crypter = new JSimpleCrypt();
			$session_id = $crypter->decrypt($token);

			$session = Hubzero\Session\Helper::getSession($session_id);

			$juser = JFactory::getUser($session->userid);
			$juser->guest = 0;
			$juser->id = $session->userid;
			$juser->usertype = $session->usertype;
		}
		else
		{
			$juser = JFactory::getUser();
		}

		// Check if user has access to content
		if ($this->_checkRestrictions($publication, $version))
		{
			return false;
		}

		// Use new curation flow?
		$useBlocks  = $this->config->get('curation', 0);

		// Serve attachments by element, with handler support (NEW)
		if ($element)
		{
			$this->_handleContent();
			return;
		}

		// Get publication helper
		$helper = new PublicationHelper($this->database);

		// Get primary attachments or requested attachment
		$objPA = new PublicationAttachment( $this->database );
		$filters = $aid ? array('id' => $aid) : array('role' => 1);
		$attachments = $objPA->getAttachments($publication->version_id, $filters);

		// Pass attachments for 'watch' and 'video'
		$this->attachments = $attachments;

		// We do need an attachment!
		if (count($attachments) == 0)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS') );
			return;
		}

		// Build publication path
		$base_path = $this->config->get('webpath');
		$path = $helper->buildPath($this->_id, $publication->version_id, $base_path, $publication->secret);

		// Build log path (access logs)
		$logPath = $helper->buildPath($this->_id, $publication->version_id, $base_path, 'logs');

		// First/requested attachment
		$primary = $attachments[0];
		$pType	 = $primary->type;
		$pPath 	 = $primary->path;

		// Load publication project
		$publication->project = new Project($this->database);
		$publication->project->load($publication->project_id);

		// Get pub type helper
		$pubTypeHelper = new PublicationTypesHelper($this->database, $publication->project);

		// Get user choice for serving content
		$pParams = new JParameter( $primary->params );
		$serveas = $pParams->get('serveas');

		// Log access
		if ($this->_logging && $publication->state == 1)
		{
			$pubLog = new PublicationLog($this->database);
			$aType  = $primary->role == 1 ? 'primary' : 'support';
			$pubLog->logAccess($publication, $aType, $logPath);
		}

		// Serve attachments differently depending on type
		if ($pType == 'data' && $render != 'archive')
		{
			// Databases: redirect to data view in first attachment
			$this->_redirect = DS . trim($pPath, DS);
			return;
		}
		elseif ($pType == 'tool' || $pType == 'svntool')
		{
			$v = "/^(http|https|ftp|nanohub):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";

			// Invoke tool
			$this->_redirect = (preg_match($v, $pPath) || preg_match("/index.php/", $pPath))
							? $pPath : DS . trim($pPath, DS);
			return;
		}
		elseif ($render == 'archive' || ($publication->base == 'files' && count($attachments) > 1
			&& $render != 'video' && $render != 'presenter'))
		{
			// Multi-file or archive
			$tarname  = JText::_('Publication') . '_' . $publication->id . '.zip';
			$archPath = $helper->buildPath($publication->id, $publication->version_id, $base_path);

			// Get archival package
			$downloadable = $this->_archiveFiles (
				$publication,
				$archPath,
				$tarname
			);
		}
		else
		{
			// File-type attachment - serve inline or as download
			if ($pType == 'file')
			{
				// Play resource inside special viewer
				if ($render == 'inline' || ($serveas == 'inlineview'
					&& $this->_task != 'download' && $render != 'download'))
				{
					// Instantiate a new view
					$this->view = new \Hubzero\Component\View(array(
						'name'   => 'view',
						'layout' => 'inline'
					));
					$this->view->option 		= $this->_option;
					$this->view->config 		= $this->config;
					$this->view->database 		= $this->database;
					$this->view->publication 	= $publication;
					$this->view->helper 		= $helper;
					$this->view->attachments 	= $attachments;
					$this->view->primary		= $primary;
					$this->view->aid 			= $aid ? $aid : $primary->id;
					$this->view->version 		= $version;

					// Get publication plugin params
					$pplugin 			= JPluginHelper::getPlugin( 'projects', 'publications' );
					$pparams 			= new JParameter($pplugin->params);

					$this->view->googleView	= $pparams->get('googleview');

					$mt = new \Hubzero\Content\Mimetypes();

					$this->view->mimetype 	= $mt->getMimeType(JPATH_ROOT . $path . DS . $pPath);
					$mParts 				= explode('/', $this->view->mimetype);
					$this->view->type 		= strtolower(array_shift($mParts));
					$eParts					= explode('.', $pPath);
					$this->view->ext 		= strtolower(array_pop($eParts));
					$this->view->url 		= $path . DS . $pPath;

					// Output HTML
					if ($this->getError())
					{
						$this->view->setError( $this->getError() );
					}

					// For inline content - if JS is unavailable
					if (!$no_html)
					{
						$this->content = $this->view->loadTemplate();
						$this->_playContent();
						return;
					}

					$this->view->display();
					return;
				}

				// Download - default action
				$downloadable['path'] 		= JPATH_ROOT . $path . DS . $pPath;
				$downloadable['name'] 		= basename($pPath);
				$downloadable['serveas'] 	= basename($pPath);
			}

			// Link-type attachment
			if ($pType == 'link')
			{
				$v = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";

				// Absolute or relative link?
				$this->_redirect = preg_match($v, $pPath) ? $pPath : DS . trim($pPath, DS);
				return;
			}

			if ($pType == 'note')
			{
				// Serve wiki page
				$this->wikipageTask();
				return;
			}
		}

		// Last resort - download attachment(s)
		// Ensure we have attachment information
		if (empty($downloadable))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND') );
			return;
		}

		// Ensure valid path
		if ($error = $helper->checkValidPath($downloadable['path']))
		{
			JError::raiseError( 404, $error );
			return;
		}

		// Ensure the file exist
		if (!file_exists($downloadable['path']))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND'));
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($downloadable['path']);
		$xserver->disposition($disp);
		$xserver->acceptranges(true);
		$xserver->saveas($downloadable['serveas']);

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_SERVER_ERROR') );
		}
		else
		{
			exit;
		}

		return;
	}

	/**
	 * Serve a supplementary file
	 *
	 * @return     void
	 */
	protected function _serveData()
	{
		// Incoming
		$pid      	= JRequest::getInt( 'id', 0 );
		$vid  	  	= JRequest::getInt( 'vid', 0 );
		$file	  	= JRequest::getVar( 'file', '' );
		$render   	= JRequest::getVar('render', '');
		$return   	= JRequest::getVar('return', '');
		$disp		= JRequest::getVar( 'disposition');
		$disp		= in_array($disp, array('inline', 'attachment')) ? $disp : 'attachment';

		// Ensure we what we need
		if (!$pid || !$vid || !$file )
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS') );
			return;
		}

		// Get publication version
		$objPV 		 = new PublicationVersion( $this->database );
		if (!$objPV->load($vid))
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			$this->introTask();
			return;
		}

		// Get publication
		$objP 		 = new Publication( $this->database );
		$publication = $objP->getPublication($pid, $objPV->version_number);

		// Make sure we got a result from the database
		if (!$publication)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			$this->introTask();
			return;
		}

		// Get publication helper
		$helper = new PublicationHelper($this->database);

		// Build publication data path
		$base_path = $this->config->get('webpath');
		$path = $helper->buildPath($pid, $vid, $base_path, 'data', $root = 0);

		// Ensure the file exist
		if (!file_exists(JPATH_ROOT . $path . DS . $file))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS') );
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename(JPATH_ROOT . $path . DS . $file);
		$xserver->disposition($disp);
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas(basename($file));

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_SERVER_ERROR') );
		}
		else
		{
			exit;
		}

		return;
	}

	/**
	 * Display wiki page
	 *
	 * @return     void
	 */
	public function wikipageTask()
	{
		// Get requested page id
		$pageid = count($this->attachments) > 0 && $this->attachments[0]->object_id
				? $this->attachments[0]->object_id : JRequest::getVar( 'p', 0 );

		// Get publication information (secondary page)
		if (!$this->publication)
		{
			// Incoming
			$version  = JRequest::getVar( 'v', '' );

			// Ensure we have an ID or alias to work with
			if (!$this->_id && !$this->_alias)
			{
				$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
				return;
			}

			// Which version is requested?
			$version = $version == 'dev' ? 'dev' : $version;
			$version = (($version && intval($version) > 0) || $version == 'dev') ? $version : 'default';

			// Get publication
			$objP 		 		= new Publication( $this->database );
			$this->publication 	= $objP->getPublication($this->_id, $version, NULL, $this->_alias);

			// Check if user has access to content
			if ($this->_checkRestrictions($this->publication, $version))
			{
				return false;
			}

			// Get primary attachment(s)
			$objPA = new PublicationAttachment( $this->database );
			$filters = array('role' => 1);
			$this->attachments = $objPA->getAttachments($this->publication->version_id, $filters);
		}

		$revision = NULL;

		// Retrieve wiki page by stamp
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');

			// Incoming
			$stamp = JRequest::getVar( 's', '' );

			// Clean up stamp value (only numbers and letters)
			$regex  = array('/[^a-zA-Z0-9]/');
			$stamp  = preg_replace($regex, '', $stamp);

			// Load item reference
			$objSt = new ProjectPubStamp( $this->database );
			if ($stamp  && $objSt->loadItem($stamp) && $objSt->projectid == $this->publication->project_id)
			{
				$data     = json_decode($objSt->reference);
				$pageid   = isset($data->pageid) ? $data->pageid : NULL;
				$revision = isset($data->revision) ? $data->revsiion : NULL;
			}
		}

		if (!$pageid)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS') );
			return;
		}

		// Make sure we got a result from the database
		if (!$this->publication)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		// Get publication helper
		$helper = new PublicationHelper($this->database);

		// Allowed page scope
		$masterscope = 'projects' . DS . $this->publication->project_alias . DS . 'notes';

		// Get page information
		$page = $helper->getWikiPage($pageid, $this->publication, $masterscope, $revision);
		if (!$page)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS') );
			return;
		}

		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'groups' . DS . 'wiki' . DS . 'wiki.css');

		// Set page title
		$document->setTitle( JText::_(strtoupper($this->_option)) . ': '
			. stripslashes($this->publication->title) );

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$this->view->option 		= $this->_option;
		$this->view->project_alias	= $this->publication->project_alias;
		$this->view->project_id		= $this->publication->project_id;
		$this->view->config 		= $this->config;
		$this->view->database 		= $this->database;
		$this->view->helper			= $helper;
		$this->view->masterscope	= $masterscope;
		$this->view->publication 	= $this->publication;
		$this->view->attachments	= $this->attachments;
		$this->view->page			= $page;

		// Output HTML
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		// Output HTML
		$this->view->setName('view')
					->setLayout('wiki')
					->display();
		return;

	}

	/**
	 * Create archive file
	 *
	 * @param      object 	$pub
	 * @param      object 	$objPV
	 * @param      string 	$path
	 * @param      string 	$tarname
	 *
	 * @return     mixed, array with data or success, False on failure
	 */
	private function _archiveFiles( $pub, $path, $tarname )
	{
		// Use new curation flow?
		$useBlocks  = $this->config->get('curation', 0);

		if ($useBlocks)
		{
			// We need our curation model to parse elements
			if (JPATH_ROOT . DS . 'components' . DS . 'com_publications'
				. DS . 'models' . DS . 'curation.php')
			{
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications'
					. DS . 'models' . DS . 'curation.php');
			}
			else
			{
				JError::raiseError( 404,
					JText::_('COM_PUBLICATIONS_ERROR_LOADING_REQUIRED_LIBRARY')
				);
				return;
			}
		}

		$tarpath = JPATH_ROOT . $path . DS . $tarname;

		$archive = array();
		$archive['path'] 	= $tarpath;
		$archive['name'] 	= $tarname;
		$archive['serveas']	= $pub->title . ' v.' . $pub->version_label . '.zip';

		// Check if archival is already there (locked version)
		if (($pub->state == 1 || $pub->state == 0 || $pub->state == 6) && file_exists($tarpath))
		{
			return $archive;
		}

		// Produce archive package
		require_once( JPATH_ROOT . DS . 'components' . DS
			. 'com_projects' . DS . 'helpers' . DS . 'helper.php' );

		if ($useBlocks)
		{
			$pub->version 	= $pub->version_number;

			// Load publication project
			$pub->_project = new Project($this->database);
			$pub->_project->load($pub->project_id);

			// Get master type info
			$mt = new PublicationMasterType( $this->database );
			$pub->_type = $mt->getType($pub->base);
			$typeParams = new JParameter( $pub->_type->params );

			// Get attachments
			$pContent = new PublicationAttachment( $this->database );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );

			// Get authors
			$pAuthors 			= new PublicationAuthor( $this->database );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);

			// Get manifest from either version record (published) or master type
			$manifest   = $pub->curation
						? $pub->curation
						: $pub->_type->curation;

			// Get curation model
			$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

			// Set pub assoc and load curation
			$pub->_curationModel->setPubAssoc($pub);

			// Produce archival package
			$pub->_curationModel->package();
		}
		else
		{
			// Archival for non-curated publications
			JPluginHelper::importPlugin( 'projects', 'publications' );
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger( 'archivePub', array($pub->id, $pub->version_id) );
		}

		return $archive;
	}

	/**
	 * Display a license for a publication
	 *
	 * @return     void
	 */
	public function licenseTask()
	{
		// Incoming
		$id       = JRequest::getInt( 'id', 0 );
		$version  = JRequest::getVar( 'v', '' );            // Get version number of a publication

		// Which version is requested?
		$version = $version == 'dev' ? 'dev' : $version;
		$version = (($version && intval($version) > 0) || $version == 'dev') ? $version : 'default';

		// Get publication
		$objP 		 = new Publication( $this->database );
		$publication = $objP->getPublication($id, $version);

		// Make sure we got a result from the database
		if (!$publication)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'));
		}

		// Output HTML
		if ($publication)
		{
			$title = stripslashes($publication->title).': '.JText::_('COM_PUBLICATIONS_LICENSE');
		}
		else
		{
			$title = JText::_('COM_PUBLICATIONS_PAGE_UNAVAILABLE');
		}

		$this->view->option 		= $this->_option;
		$this->view->config 		= $this->config;
		$this->view->publication 	= $publication;
		$this->view->title 			= $title;

		// Get license info
		$pLicense = new PublicationLicense($this->database);
		$this->view->license = $pLicense->getLicense($publication->license_type);

		// Output HTML
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}
		$this->view->display();
	}

	/**
	 * Download a citation for a publication
	 *
	 * @return     void
	 */
	public function citationTask()
	{
		$yearFormat = "Y";
		$monthFormat = "M";
		$tz = false;

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$format = JRequest::getVar( 'format', 'bibtex' );

		// Which version is requested?
		$version  = JRequest::getVar( 'v', '' );
		$version = $version == 'dev' ? 'dev' : $version;
		$version = (($version && intval($version) > 0) || $version == 'dev') ? $version : 'default';

		// Get the publication
		$objP = new Publication( $this->database );
		$publication = $objP->getPublication($id, $version);

		// Get HUB configuration
		$jconfig = JFactory::getConfig();
		$sitename = $jconfig->getValue('config.sitename');

		// Make sure we got a result from the database
		if (!$publication)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			$this->introTask();
			return;
		}

		// Release date
		$thedate = ($publication->published_up != '0000-00-00 00:00:00')
				 ? $publication->published_up
				 : $publication->created;

		// Get publication helper
		$helper = new PublicationHelper($this->database, $publication->version_id, $id);

		// Get version authors
		$pa = new PublicationAuthor( $this->database );
		$authors = $pa->getAuthors($publication->version_id);

		// Build publication path
		$base_path = $this->config->get('webpath');
		$path = JPATH_ROOT . $helper->buildPath($id, $publication->version_id, $base_path);

		if (!is_dir( $path ))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path ))
			{
				$this->setError( 'Error. Unable to create path.' );
			}
		}

		// Build the URL for this resource
		$sef = JRoute::_('index.php?option='.$this->_option.'&id='.$publication->id);
		$sef.= $version != 'default' ? '?v='.$version : '';
		if (substr($sef,0,1) == '/')
		{
			$sef = substr($sef,1,strlen($sef));
		}
		$juri = JURI::getInstance();
		$url = $juri->base().$sef;

		// Choose the format
		switch ($format)
		{
			case 'endnote':
				$doc  = "%0 ".JText::_('COM_PUBLICATIONS_GENERIC')."\r\n";
				$doc .= "%D " . JHTML::_('date', $thedate, $yearFormat, $tz) . "\r\n";
				$doc .= "%T " . trim(stripslashes($publication->title)) . "\r\n";

				if ($authors)
				{
					foreach ($authors as $author)
					{
						$name = $author->name ? $author->name : $author->p_name;
						$auth = preg_replace( '/{{(.*?)}}/s', '', $name );
						if (!strstr($auth,','))
						{
							$bits = explode(' ',$auth);
							$n = array_pop($bits).', ';
							$bits = array_map('trim',$bits);
							$auth = $n.trim(implode(' ',$bits));
						}
						$doc .= "%A " . trim($auth) . "\r\n";
					}
				}

				$doc .= "%U " . $url . "\r\n";
				if ($thedate)
				{
					$doc .= "%8 " . JHTML::_('date', $thedate, $monthFormat, $tz) . "\r\n";
				}
				if ($publication->doi)
				{
					$doc .= "%1 " .'doi:'. $publication->doi;
					$doc .= "\r\n";
				}

				$file = 'publication'.$id.'.enw';
				$mime = 'application/x-endnote-refer';
			break;

			case 'bibtex':
			default:
				include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'BibTex.php' );

				$bibtex = new Structures_BibTex();
				$addarray = array();
				$addarray['type']    = 'misc';
				$addarray['cite']    = $sitename.$publication->id;
				$addarray['title']   = stripslashes($publication->title);

				if ($authors)
				{
					$i = 0;
					foreach ($authors as $author)
					{
						$name = $author->name ? $author->name : $author->p_name;
						$author_arr = explode(',',$name);
						$author_arr = array_map('trim',$author_arr);

						$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? $author_arr[1] : '';
						$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? $author_arr[0] : '';
						$i++;
					}
				}
				$addarray['month'] = JHTML::_('date', $thedate, $monthFormat, $tz);
				$addarray['url']   = $url;
				$addarray['year']  = JHTML::_('date', $thedate, $yearFormat, $tz);
				if ($publication->doi)
				{
					$addarray['doi'] = 'doi:' . DS . $publication->doi;
				}

				$bibtex->addEntry($addarray);

				$file = 'publication_'.$id.'.bib';
				$mime = 'application/x-bibtex';
				$doc = $bibtex->bibTex();
			break;
		}

		// Write the contents to a file
		$fp = fopen($path . DS . $file, "w") or die("can't open file");
		fwrite($fp, $doc);
		fclose($fp);

		$this->_serveup(false, $path, $file, $mime);

		die; // REQUIRED
	}

	/**
	 * Call a plugin method
	 * NOTE: This view should normally only be called through AJAX
	 *
	 * @return     string
	 */
	public function pluginTask()
	{
		// Incoming
		$trigger = trim(JRequest::getVar( 'trigger', '' ));

		// Ensure we have a trigger
		if (!$trigger)
		{
			echo '<p class="error">' . JText::_('COM_PUBLICATIONS_NO_TRIGGER_FOUND') . '</p>';
			return;
		}

		// Get Publications plugins
		JPluginHelper::importPlugin( 'publications' );
		$dispatcher = JDispatcher::getInstance();

		// Call the trigger
		$results = $dispatcher->trigger( $trigger, array($this->_option) );
		if (is_array($results))
		{
			$html = $results[0]['html'];
		}

		// Output HTML
		echo $html;
	}

	/**
	 * Serve up a file
	 *
	 * @param      boolean $inline Disposition
	 * @param      string  $p      File path
	 * @param      string  $f      File name
	 * @param      string  $mime   Mimetype
	 * @return     void
	 */
	protected function _serveup($inline = false, $p, $f, $mime)
	{
		$user_agent = (isset($_SERVER["HTTP_USER_AGENT"]) )
					? $_SERVER["HTTP_USER_AGENT"]
					: $HTTP_USER_AGENT;

		// Clean all output buffers (needs PHP > 4.2.0)
		while (@ob_end_clean());
		$file = $p . DS . $f;

		$fsize = filesize( $file );
		$mod_date = date('r', filemtime( $file ) );

		$cont_dis = $inline ? 'inline' : 'attachment';

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="' . $f . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize .';'
			); //RFC2183
			header("Content-Type: "    . $mime ); // MIME type
			header("Content-Length: "  . $fsize);

		// No encoding - we aren't using compression... (RFC1945)

		$this->_readfile_chunked($file);
	}

	/**
	 * Read file contents
	 *
	 * @param      unknown $filename Parameter description (if any) ...
	 * @param      boolean $retbytes Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	protected function _readfile_chunked($filename, $retbytes=true)
	{
		$chunksize = 1*(1024*1024); // How many bytes per chunk
		$buffer = '';
		$cnt = 0;
		$handle = fopen($filename, 'rb');
		if ($handle === false)
		{
			return false;
		}
		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes)
			{
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status)
		{
			return $cnt; // Return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	/**
	 * Contribute a publication
	 *
	 * @return     void
	 */
	public function contributeTask()
	{
		// Incoming
		$pid     = JRequest::getInt('pid', 0);
		$action  = JRequest::getVar( 'action', '' );
		$active  = JRequest::getVar( 'active', 'publications' );
		$action  = $this->_task == 'start' ? 'start' : $action;
		$ajax 	 = JRequest::getInt( 'ajax', 0 );

		// Load projects config
		$pconfig = JComponentHelper::getParams( 'com_projects' );

		// Redirect if publishing is turned off
		if (!$this->_contributable || !$this->config->get('contribute', 0))
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Include needed classes
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS
			. 'helpers' . DS . 'html.php' );
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS
			. 'helpers' . DS . 'helper.php' );
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS
			. 'helpers' . DS . 'imghandler.php' );
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS
			. 'helpers' . DS . 'autocomplete.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_projects' . DS . 'tables' . DS . 'project.activity.php' );

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' .
			DS . 'models' . DS . 'publication.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' .
			DS . 'models' . DS . 'curation.php');

		$lang = JFactory::getLanguage();
		$lang->load('com_projects');

		// Instantiate a new view
		$this->view = new \Hubzero\Component\View(array(
			'name'   => 'submit',
			'layout' => 'default'
		));
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;

		// Add projects stylesheet
		\Hubzero\Document\Assets::addComponentStylesheet('com_projects');
		\Hubzero\Document\Assets::addComponentScript('com_projects', 'assets/js/projects');

		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'uploader.css');
		$document->addScript('plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'jquery.fileuploader.js');
		$document->addScript('plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'jquery.queueuploader.js');

		// Set page title
		$this->_task_title = JText::_('COM_PUBLICATIONS_SUBMIT');
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// What plugin requested?
		$allowed = array('team', 'files', 'notes', 'publications', 'links');
		$plugin  = in_array($active, $allowed) ? $active : 'publications';

		// Get output from plugin
		JPluginHelper::importPlugin( 'projects', $plugin);
		$dispatcher = JDispatcher::getInstance();

		if ($this->juser->get('guest') && ($action == 'login' || $this->_task == 'start'))
		{
			$this->_msg = $this->_task == 'start'
						? JText::_('COM_PUBLICATIONS_LOGIN_TO_START')
						: JText::_('COM_PUBLICATIONS_LOGIN_TO_VIEW_SUBMISSIONS');
			$this->_login();
			return;
		}

		// Get project information
		if ($pid)
		{
			$obj = new Project( $this->database );
			$project = $obj->getProject(NULL, $this->juser->get('id'), $pid);

			if (!$project)
			{
				$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=submit');
				$this->_task = 'submit';
				return;
			}
			else
			{
				// Check authorization
				$authorized = $this->_authorize($project->id);
			}

			// Block unauthorized access
			if (!$authorized)
			{
				$this->_blockAccess(NULL);
				return;
			}

			// Redirect to project if not provisioned
			if ($project->provisioned != 1)
			{
				$this->_redirect = JRoute::_('index.php?option=com_projects' . a . 'alias=' . $project->alias
				. a . 'active=publications' . a . 'pid=' . $pid).'?action='.$action;
				return;
			}
		}
		else
		{
			$authorized = true;
			$project 	= NULL;
		}

		// Is project registration restricted to a group?
		if ($action == 'start')
		{
			$pconfig = JComponentHelper::getParams( 'com_projects' );
			$creatorgroup = $pconfig->get('creatorgroup', '');

			if ($creatorgroup)
			{
				$cgroup = \Hubzero\User\Group::getInstance($creatorgroup);
				if ($cgroup)
				{
					if (!$cgroup->is_member_of('members',$this->juser->get('id')) &&
						!$cgroup->is_member_of('managers',$this->juser->get('id')))
					{
						$this->_buildPathway(null);
						$this->view = new \Hubzero\Component\View( array('name'=>'error', 'layout' =>'restricted') );
						$this->view->error  = JText::_('COM_PUBLICATIONS_ERROR_NOT_FROM_CREATOR_GROUP');
						$this->view->title = $this->title;
						$this->view->display();
						return;
					}
				}
			}
		}

		// Plugin params
		$plugin_params = array( $project,
								$this->_option,
								$authorized,
								$this->juser->get('id'),
								$this->getNotifications('success'),
								$this->getNotifications('error'),
								$action,
								$areas = array($plugin)
		);

		$content = $dispatcher->trigger( 'onProject', $plugin_params);
		$this->view->content = (is_array($content) && isset($content[0]['html'])) ? $content[0]['html'] : '';

		if (isset($content[0]['msg']) && !empty($content[0]['msg']))
		{
			$this->setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
		}

		if ($ajax)
		{
			echo $this->view->content;
			return;
		}
		elseif (!$this->view->content && isset($content[0]['referer']) && $content[0]['referer'] != '')
		{
			$this->_redirect = $content[0]['referer'];
			return;
		}
		elseif (empty($content))
		{
			// plugin disabled?
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Output HTML
		$this->view->project= isset($project) ? $project : '';
		$this->view->action = $action;
		$this->view->uid	= $this->juser->get('id');
		$this->view->pid 	= $pid;
		$this->view->title 	= $this->_title;
		$this->view->msg 	= $this->getNotifications('success');
		$error 				= $this->getError() ? $this->getError() : $this->getNotifications('error');
		if ($error)
		{
			$this->view->setError( $error );
		}
		$this->view->display();

		return;
	}

	/**
	 * Save tags on a publication
	 *
	 * @return     void
	 */
	public function savetagsTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			$this->pageTask();
			return;
		}

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$tags = JRequest::getVar( 'tags', '' );
		$no_html = JRequest::getInt( 'no_html', 0 );

		// Process tags
		$database = JFactory::getDBO();
		$rt = new PublicationTags( $database );
		$rt->tag_object($this->juser->get('id'), $id, $tags, 1, 0);

		if (!$no_html)
		{
			// Push through to the resource view
			$this->pageTask();
		}
	}

	/**
	 * Check user restrictions
	 *
	 * @param      object $publication
	 * @param      string $version
	 * @return     mixed False if no access, string if has access
	 */
	protected function _checkRestrictions ($publication, $version)
	{
		// Make sure we got a result from the database
		if (!$publication)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			$this->introTask();
			return true;
		}

		// Check if the resource is for logged-in users only and the user is logged-in
		if ($publication->access == 1 && $this->juser->get('guest'))
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NO_ACCESS') );
			$this->introTask();
			return true;
		}

		// Check authorization
		$authorized = $this->_authorize($publication->project_id);

		// Extra authorization for restricted publications
		if ($publication->access == 3 || $publication->access == 2)
		{
			if (!$authorized && $restricted = $this->_checkGroupAccess($publication, $version))
			{
				$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NO_ACCESS') );
				$this->introTask();
				return true;
			}
		}

		// Dev version/pending/posted/dark archive resource? Must be project owner
		if (($version == 'dev' || $publication->state == 4 || $publication->state == 3
			|| $publication->state == 5 || $publication->state == 6) && !$authorized)
		{
			$this->_blockAccess($publication);
			return true;
		}

		return false;
	}

	/**
	 * Block access to restricted publications
	 *
	 * @param  object $publication
	 *
	 * @return string
	 */
	protected function _blockAccess ($publication)
	{
		// Set the task
		$this->_task = 'block';

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_PUBLICATIONS_PRIVATE_PUB_LOGIN');
			$this->_login();
			return;
		}
		else
		{
			$this->setError(JText::_('COM_PUBLICATIONS_RESOURCE_NO_ACCESS') );
			$this->introTask();
			return;
		}
	}

	/**
	 * Check user access
	 *
	 * @param      integer $project_id
	 * @return     mixed False if no access, string if has access
	 */
	protected function _authorize( $project_id = 0, $curatorgroup = NULL )
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		$authorized = false;

		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage'))
		{
			$authorized = 'admin';
		}

		// Check if they are curator
		$curatorgroup = $curatorgroup ? $curatorgroup : $this->config->get('curatorgroup', '');
		if ($curatorgroup)
		{
			if ($group = \Hubzero\User\Group::getInstance($curatorgroup))
			{
				// Check if they're a member of this group
				$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
				if ($ugs && count($ugs) > 0)
				{
					foreach ($ugs as $ug)
					{
						if ($group && $ug->cn == $group->get('cn'))
						{
							$authorized = 'curator';
						}
					}
				}
			}
		}

		// Check if they're the project owner
		if ($project_id)
		{
			$objO = new ProjectOwner($this->database);
			$owner = $objO->isOwner($this->juser->get('id'), $project_id);
			if ($owner)
			{
				$authorized = $owner;
			}
		}

		return $authorized;
	}

	/**
	 * Check group access
	 *
	 * @param      object 	$publication
	 * @param      string 	$version
	 * @param      array 	$usergroups
	 * @return     boolean, True if access restricted
	 */
	private function _checkGroupAccess( $publication, $version = 'default', $usersgroups = array() )
	{
		if (!$this->juser->get('guest'))
		{
			// Check if they're a site admin (from Joomla)
			if ($this->juser->authorize($this->_option, 'manage'))
			{
				return false;
			}

			// Get the groups the user has access to
			if (empty($usersgroups))
			{
				$xgroups = \Hubzero\User\Helper::getGroups($this->juser->get('id'), 'all');
				$usersgroups = $this->getGroupProperty($xgroups);
			}
		}

		// Get the list of groups that can access this resource
		$paccess = new PublicationAccess( $this->database );
		$allowedgroups = $paccess->getGroups( $publication->version_id, $publication->id, $version );
		$allowedgroups = $this->getGroupProperty($allowedgroups);

		// Find what groups the user has in common with the publication, if any
		$common = array_intersect($usersgroups, $allowedgroups);

		// Make sure they have the proper group access
		$restricted = false;
		if ( $publication->access == 3 || $publication->access == 2 )
		{
			// Are they logged in?
			if ($this->juser->get('guest'))
			{
				// Not logged in
				$restricted = true;
			}
			else
			{
				// Logged in
				if (count($common) < 1)
				{
					$restricted = true;
				}
			}
		}

		return $restricted;
	}

	/**
	 * Get group property
	 *
	 * @param      object 	$groups
	 * @param      string 	$get
	 *
	 * @return     array
	 */
	public function getGroupProperty($groups, $get = 'cn')
	{
		$arr = array();
		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed)
				{
					$arr[] = $get == 'cn' ? $group->cn : $group->gidNumber;
				}
			}
		}
		return $arr;
	}
}
