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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Publications\Tables;
use Components\Publications\Models;
use Components\Publications\Helpers;
use Exception;

/**
 * Primary component controller
 */
class Publications extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Set configs
		$this->_setConfigs();

		// Incoming
		$this->_incoming();

		// Resource map
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

		$this->_task = trim(Request::getVar('task', ''));
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
	 * Parse component params and set configs
	 *
	 * @return  void
	 */
	protected function _setConfigs()
	{
		// Is component enabled?
		if ($this->config->get('enabled', 0) == 0)
		{
			App::redirect(Route::url('index.php?option=com_resources'));
			return;
		}

		// Logging
		$this->_logging = $this->config->get('enable_logs', 1);

		// Are we allowing contributions
		$this->_contributable = Plugin::isEnabled('projects', 'publications') ? true : false;
	}

	/**
	 * Receive incoming data, get model and set url
	 *
	 * @return  void
	 */
	protected function _incoming()
	{
		$this->_id      = Request::getInt('id', 0);
		$this->_alias   = Request::getVar('alias', '');
		$this->_version = Request::getVar('v', 'default');

		$this->_identifier = $this->_alias ? $this->_alias : $this->_id;

		$pointer       = $this->_id ? '&id=' . $this->_id : '&alias=' . $this->_alias;
		$this->_route  = 'index.php?option=' . $this->_option;
		$this->_route .= $this->_identifier ? $pointer : '';
	}

	/**
	 * Build the "trail"
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}

		if (!empty($this->model) && $this->_identifier && ($this->_task == 'view'
			|| $this->_task == 'serve' || $this->_task == 'wiki'))
		{
			$url = $this->_route;

			// Link to category
			Pathway::append(
				$this->model->_category->name,
				'index.php?option=' . $this->_option . '&category=' . $this->model->_category->url_alias
			);

			// Link to publication
			if ($this->_version && $this->_version != 'default')
			{
				$url .= '&v=' . $this->_version;
			}
			Pathway::append(
				$this->model->version->title,
				$url
			);

			if ($this->_task == 'serve' || $this->_task == 'wiki')
			{
				Pathway::append(
					Lang::txt('COM_PUBLICATIONS_SERVING_CONTENT'),
					$this->_route . '&task=' . $this->_task
				);
			}
		}
		elseif (Pathway::count() <= 1 && $this->_task)
		{
			switch ($this->_task)
			{
				case 'browse':
				case 'submit':
					if ($this->_task_title)
					{
						Pathway::append(
							$this->_task_title,
							'index.php?option=' . $this->_option . '&task=' . $this->_task
						);
					}
					break;

				case 'start':
					if ($this->_task_title)
					{
						Pathway::append(
							$this->_task_title,
							'index.php?option=' . $this->_option . '&task=submit'
						);
					}
					break;

				case 'block':
				case 'intro':
					// Nothing
					break;

				default:
					Pathway::append(
						Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
						'index.php?option=' . $this->_option . '&task=' . $this->_task
					);
					break;
			}
		}
	}

	/**
	 * Build the title for this component
	 *
	 * @return  void
	 */
	protected function _buildTitle()
	{
		if (!$this->_title)
		{
			$this->_title = Lang::txt(strtoupper($this->_option));
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
							$this->_title .= ': ' . $this->_task_title;
						}
						break;

					case 'serve':
					case 'wiki':
						$this->_title .= ': ' . Lang::txt('COM_PUBLICATIONS_SERVING_CONTENT');
						break;

					default:
						$this->_title .= ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task));
						break;
				}
			}
		}

		Document::setTitle($this->_title);
	}

	/**
	 * Set notifications
	 *
	 * @param  string $message
	 * @param  string $type
	 * @return void
	 */
	public function setNotification($message, $type = 'success')
	{
		// If message is set push to notifications
		if ($message != '')
		{
			\Notify::message($message, $type, $this->_option);
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
		$messages = \Notify::messages($this->_option);

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

		return false;
	}

	/**
	 * Login view
	 *
	 * @return  void
	 */
	protected function _login()
	{
		$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
			$this->_msg,
			'warning'
		);
	}

	/**
	 * View for main DOI
	 *
	 * @return  void
	 */
	public function mainTask()
	{
		// Redirect to version panel of current version (TEMP)
		App::redirect(
			Route::url($this->_route . '&active=versions')
		);
		return;
	}

	/**
	 * Intro to publications (main view)
	 *
	 * @return  void
	 */
	public function introTask()
	{
		$this->view->setLayout('intro');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$this->view->title         = $this->_title;
		$this->view->option        = $this->_option;
		$this->view->database      = $this->database;
		$this->view->config        = $this->config;
		$this->view->contributable = $this->_contributable;

		$this->view->filters = array(
			'sortby' => 'date_published',
			'limit'  => $this->config->get('listlimit', 10),
			'start'  => Request::getInt('limitstart', 0)
		);

		// Instantiate a publication object
		$model = new Models\Publication();

		// Get most recent pubs
		$this->view->results = $model->entries('list', $this->view->filters);

		// Get most popular/oldest pubs
		$this->view->filters['sortby'] = 'popularity';
		$this->view->best = $model->entries('list', $this->view->filters);

		// Get major types
		$t = new Tables\Category($this->database);
		$this->view->categories = $t->getCategories(array('itemCount' => 1));

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
		return;
	}

	/**
	 * Browse publications
	 *
	 * @return  void
	 */
	public function browseTask()
	{
		// Set the default sort
		$default_sort = 'date';
		if ($this->config->get('show_ranking'))
		{
			$default_sort = 'ranking';
		}

		// Incoming
		$this->view->filters = array(
			'category'    => Request::getVar('category', ''),
			'sortby'      => Request::getCmd('sortby', $default_sort),
			'limit'       => Request::getInt('limit', Config::get('list_limit')),
			'start'       => Request::getInt('limitstart', 0),
			'search'      => Request::getVar('search', ''),
			'tag'         => trim(Request::getVar('tag', '', 'request', 'none', 2)),
			'tag_ignored' => array()
		);

		if (!in_array($this->view->filters['sortby'], array('date', 'title', 'id', 'rating', 'ranking', 'popularity')))
		{
			$this->view->filters['sortby'] = $default_sort;
		}

		// Get projects user has access to
		if (!User::isGuest())
		{
			$obj = new \Components\Projects\Tables\Project($this->database);
			$this->view->filters['projects'] = $obj->getUserProjectIds(User::get('id'));
		}

		// Get major types
		$t = new Tables\Category($this->database);
		$this->view->categories = $t->getCategories();

		if (is_numeric($this->view->filters['category']))
		{
			$this->view->filters['category'] = (int)$this->view->filters['category'];
		}
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

			if (!is_int($this->view->filters['category']))
			{
				$this->view->filters['category'] = null;
			}
		}

		// Instantiate a publication object
		$model = new Models\Publication();

		// Execute count query
		$this->view->total = $model->entries('count', $this->view->filters);

		// Run query with limit
		$this->view->results = $model->entries('list', $this->view->filters);

		// Initiate paging
		$this->view->pageNav = new \Hubzero\Pagination\Paginator(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Get type if not given
		$this->_title = Lang::txt(strtoupper($this->_option)) . ': ';
		if ($this->view->filters['category'] != '')
		{
			$t->load($this->view->filters['category']);
			$this->_title .= $t->name;
			$this->_task_title = $t->name;
		}
		else
		{
			$this->_title .= Lang::txt('COM_PUBLICATIONS_ALL');
			$this->_task_title = Lang::txt('COM_PUBLICATIONS_ALL');
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->title = $this->_title;
		$this->view->config = $this->config;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setName('browse')
			->setLayout('default')
			->display();
	}

	/**
     * Retrieves the data from database and compose the RDF file for download.
     *
     * @return  void
     */
	protected function _resourceMap()
	{
		$resourceMap = new \ResourceMapGenerator();
		$id = '';

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
	 * @return  void
	 */
	public function pageTask()
	{
		$this->view->setName('view');

		// Incoming
		$tab      = Request::getVar('active', '');   // The active tab (section)
		$no_html  = Request::getInt('no_html', 0);   // No-html display?

		// Ensure we have an ID or alias to work with
		if (!$this->_identifier)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Get our model and load publication data
		$this->model = new Models\Publication($this->_identifier, $this->_version);

		// Last public release
		$lastPubRelease = $this->model->lastPublicRelease();

		// Version invalid but publication exists or no version specified?
		if ($this->model->masterExists() && !$this->model->exists()
			|| ($this->_version == 'default' && isset($lastPubRelease->id)))
		{
			if ($lastPubRelease && $lastPubRelease->id)
			{
				// Go to last public release
				App::redirect(
					Route::url($this->_route . '&v=' . $lastPubRelease->version_number)
				);
				return;
			}
		}

		// Make sure we got a result from the database
		if (!$this->model->exists() || $this->model->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Is the visitor authorized to view this resource?
		if (!$this->model->access('view'))
		{
			if ($this->_version == 'default' && $lastPubRelease && $lastPubRelease->id)
			{
				// Go to last public release
				App::redirect(
					Route::url($this->_route . '&v=' . $lastPubRelease->version_number)
				);
				return;
			}
			else
			{
				$this->_blockAccess();
				return;
			}
		}

		$authorized    = $this->model->access('manage');
		$contentAccess = $this->model->access('view-all');
		$restricted    = $contentAccess ? false : true;

		$this->model->setCuration(false);

		// For publications created in a non-curated flow - convert
		if ($this->model->_curationModel->convertToCuration($this->model))
		{
			// Reload attachments (updated)
			$this->model->attachments(true);
		}

		// Start sections
		$sections = array();
		$cats = array();
		$tab = $tab ? $tab : 'about';

		// Show extended pub info like reviews, questions etc.
		$extended = $lastPubRelease && $lastPubRelease->id == $this->model->version->id ? true : false;

		// Trigger the functions that return the areas we'll be using
		$cats = Event::trigger('publications.onPublicationAreas', array(
			$this->model,
			$this->model->versionAlias,
			$extended)
		);

		// Get the sections
		$sections = Event::trigger('publications.onPublication', array(
			$this->model,
			$this->_option,
			array($tab),
			'all',
			$this->model->versionAlias,
			$extended)
		);

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

		$body = '';
		if ($tab == 'about')
		{
			// Build the HTML of the "about" tab
			$view = new \Hubzero\Component\View(array(
				'name'   => 'about',
				'layout' => 'default'
			));
			$view->option      = $this->_option;
			$view->config      = $this->config;
			$view->database    = $this->database;
			$view->publication = $this->model;
			$view->authorized  = $authorized;
			$view->restricted  = $restricted;
			$view->version     = $this->model->versionAlias;
			$view->sections    = $sections;
			$body              = $view->loadTemplate();

			// Log page view (public pubs only)
			if ($this->_logging && $this->_task == 'view')
			{
				$this->model->logAccess('view');
			}
		}

		// Add the default "About" section to the beginning of the lists
		$cat = array();
		$cat['about'] = Lang::txt('COM_PUBLICATIONS_ABOUT');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html' => $body, 'metadata' => ''));

		// Get filters (for series & workshops listing)
		$defaultsort = ($this->model->_category->alias == 'series') ? 'date' : 'ordering';
		$defaultsort = ($this->model->_category->alias == 'series' && $this->config->get('show_ranking')) ? 'ranking' : $defaultsort;

		$filters = array(
			'sortby' => Request::getVar('sortby', $defaultsort),
			'limit'  => Request::getInt('limit', 0),
			'start'  => Request::getInt('limitstart', 0),
			'id'     => $this->model->publication->id
		);

		// Write title & build pathway
		Document::setTitle(Lang::txt(strtoupper($this->_option)) . ': ' . stripslashes($this->model->version->title));

		// Set the pathway
		$this->_buildPathway();

		$this->view->version        = $this->model->versionAlias;
		$this->view->config         = $this->config;
		$this->view->option         = $this->_option;
		$this->view->publication    = $this->model;
		$this->view->authorized     = $authorized;
		$this->view->restricted     = $restricted;
		$this->view->cats           = $cats;
		$this->view->tab            = $tab;
		$this->view->sections       = $sections;
		$this->view->database       = $this->database;
		$this->view->filters        = $filters;
		$this->view->lastPubRelease = $lastPubRelease;
		$this->view->contributable  = $this->_contributable;

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output HTML
		$this->view->display();

		// Insert .rdf link in the header
		\ResourceMapGenerator::putRDF($this->model->publication->id);
	}

	/**
	 * Serve publication content
	 * Determine how to render depending on master type, attachment type and user choice
	 * Defaults to download
	 *
	 * @return  void
	 */
	public function serveTask()
	{
		// Incoming
		$aid        = Request::getInt('a', 0);             // Attachment id
		$elementId  = Request::getInt('el', 1);            // Element id
		$render     = Request::getVar('render', '');
		$vid        = Request::getInt('vid', '');
		$file       = Request::getVar('file', '');
		$disp       = Request::getVar('disposition');
		$disp       = in_array($disp, array('inline', 'attachment')) ? $disp : 'attachment';

		// Get our model and load publication data
		$this->model = new Models\Publication($this->_identifier, $this->_version, $vid);

		if (!$this->model->exists() || $this->model->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Is the visitor authorized to view content?
		if (!$this->model->access('view-all'))
		{
			$this->_blockAccess();
			return true;
		}

		// Set curation
		$this->model->setCuration();

		// Bundle requested?
		if ($render == 'archive')
		{
			// Produce archival package
			if ($this->model->_curationModel->package())
			{
				// Log access
				if ($this->model->isPublished())
				{
					$this->model->logAccess('primary');
				}
				$this->model->_curationModel->serveBundle();
				return;
			}
			else
			{
				throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS'), 404);
				return;
			}
		}

		// Bundle requested?
		if ($render == 'showcontents')
		{
			// Produce archival package
			if ($this->model->_curationModel->package())
			{
				// Build the HTML of the "about" tab
				$view = new \Hubzero\Component\View([
					'name'   => 'view',
					'layout' => '_contents'
				]);
				$view->model  = $this->model;
				$view->option = $this->_option;
				$view->display();

				return;
			}
			else
			{
				throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS'), 404);
				return;
			}
		}

		// Serving data file (dataview)
		if ($file)
		{
			// Ensure the file exist
			if (!file_exists($this->model->path('data', true) . DS . trim($file)))
			{
				throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS'), 404);
				return;
			}
			// Initiate a new content server and serve up the file
			$server = new \Hubzero\Content\Server();
			$server->filename($this->model->path('data', true) . DS . trim($file));
			$server->disposition($disp);
			$server->acceptranges(true);
			$server->saveas(basename($file));

			if (!$server->serve())
			{
				// Should only get here on error
				throw new Exception(Lang::txt('COM_PUBLICATIONS_SERVER_ERROR'), 404);
			}
			else
			{
				exit;
			}
		}

		$this->model->attachments();

		// Individual attachment is requested? Find element ID
		if ($aid)
		{
			$elementId = $this->model->_curationModel->getElementIdByAttachment($aid);
		}

		// We do need attachments
		if (!isset($this->model->_attachments['elements'][$elementId])
			|| empty($this->model->_attachments['elements'][$elementId]))
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS'), 404);
			return;
		}

		// Get element manifest to deliver content as intended
		$curation = $this->model->_curationModel->getElementManifest($elementId);

		// We do need manifest!
		if (!$curation || !isset($curation->element) || !$curation->element)
		{
			return false;
		}

		// Get attachment type model
		$attModel = new Models\Attachments($this->database);

		// Log access
		if ($this->model->isPublished())
		{
			$aType = $curation->element->params->role == 1 ? 'primary' : 'support';
			$this->model->logAccess($aType);
		}

		// Serve content
		$content = $attModel->serve(
			$curation->element->params->type,
			$curation->element,
			$elementId,
			$this->model,
			$curation->block->params,
			$aid
		);

		// No content served
		if ($content === NULL || $content == false)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_FINDING_ATTACHMENTS'), 404);
		}

		// Do we need to redirect to content?
		if ($attModel->get('redirect'))
		{
			App::redirect($attModel->get('redirect'));
			return;
		}

		return $content;
	}

	/**
	 * Display a license for a publication
	 *
	 * @return  void
	 */
	public function licenseTask()
	{
		// Get our model and load publication data
		$this->model = new Models\Publication($this->_identifier, $this->_version);

		if (!$this->model->exists() || $this->model->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'),
				'error'
			);
			return;
		}

		// get license details
		$this->model->license();

		$this->view->option      = $this->_option;
		$this->view->config      = $this->config;
		$this->view->publication = $this->model;
		$this->view->title       = stripslashes($this->model->version->get('title')) . ': ' . Lang::txt('COM_PUBLICATIONS_LICENSE');

		// Output HTML
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->display();
	}

	/**
	 * Download a citation for a publication
	 *
	 * @return  void
	 */
	public function citationTask()
	{
		// Incoming
		$format = Request::getVar('type', 'bibtex');

		// Get our model and load publication data
		$this->model = new Models\Publication($this->_identifier, $this->_version);

		// Make sure we got a result from the database
		if (!$this->model->exists() || $this->model->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Get version authors
		$authors = $this->model->table('Author')->getAuthors($this->model->version->get('id'));

		// Build publication path
		$path = $this->model->path('base', true);

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory(PATH_APP . $path, 0755, true, true))
			{
				$this->setError('Error. Unable to create path.');
			}
		}

		// Build the URL for this resource
		$sef  = Route::url($this->model->link('version'));
		$url  = Request::base() . ltrim($sef, '/');

		// Choose the format
		switch ($format)
		{
			case 'endnote':
				$doc  = "%0 " . Lang::txt('COM_PUBLICATIONS_GENERIC') . "\r\n";
				$doc .= "%D " . Date::of($this->model->published())->toLocal('Y') . "\r\n";
				$doc .= "%T " . trim(stripslashes($this->model->version->get('title'))) . "\r\n";

				if ($authors)
				{
					foreach ($authors as $author)
					{
						$name = $author->name ? $author->name : $author->p_name;
						$auth = preg_replace('/{{(.*?)}}/s', '', $name);
						if (!strstr($auth,','))
						{
							$bits = explode(' ',$auth);
							$n    = array_pop($bits) . ', ';
							$bits = array_map('trim', $bits);
							$auth = $n . trim(implode(' ', $bits));
						}
						$doc .= "%A " . trim($auth) . "\r\n";
					}
				}

				$doc .= "%U " . $url . "\r\n";
				if ($this->model->published())
				{
					$doc .= "%8 " . Date::of($this->model->published())->toLocal('M') . "\r\n";
				}
				if ($this->model->version->get('doi'))
				{
					$doc .= "%1 " . 'doi:' . $this->model->version->get('doi');
					$doc .= "\r\n";
				}

				$file = 'publication' . $this->model->get('id') . '.enw';
				$mime = 'application/x-endnote-refer';
			break;

			case 'bibtex':
			default:
				include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'BibTex.php');

				$bibtex = new \Structures_BibTex();
				$addarray = array();
				$addarray['type']    = 'misc';
				$addarray['cite']    = Config::get('sitename') . $this->model->get('id');
				$addarray['title']   = stripslashes($this->model->version->get('title'));

				if ($authors)
				{
					$i = 0;
					foreach ($authors as $author)
					{
						$name = $author->name ? $author->name : $author->p_name;
						$author_arr = explode(',', $name);
						$author_arr = array_map('trim',$author_arr);

						$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? $author_arr[1] : '';
						$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? $author_arr[0] : '';
						$i++;
					}
				}
				$addarray['month'] = Date::of($this->model->published())->toLocal('M');
				$addarray['url']   = $url;
				$addarray['year']  = Date::of($this->model->published())->toLocal('Y');
				if ($this->model->version->get('doi'))
				{
					$addarray['doi'] = 'doi:' . DS . $this->model->version->get('doi');
				}

				$bibtex->addEntry($addarray);

				$file = 'publication_' . $this->model->get('id') . '.bib';
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
	 * @return  string
	 */
	public function pluginTask()
	{
		// Incoming
		$trigger = trim(Request::getVar('trigger', ''));

		// Ensure we have a trigger
		if (!$trigger)
		{
			echo '<p class="error">' . Lang::txt('COM_PUBLICATIONS_NO_TRIGGER_FOUND') . '</p>';
			return;
		}

		// Call the trigger
		$results = Event::trigger('publications.' . $trigger, array($this->_option));
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
	 * @param   boolean  $inline  Disposition
	 * @param   string   $p       File path
	 * @param   string   $f       File name
	 * @param   string   $mime    Mimetype
	 * @return  void
	 */
	protected function _serveup($inline = false, $p, $f, $mime)
	{
		$user_agent = (isset($_SERVER["HTTP_USER_AGENT"]))
					? $_SERVER["HTTP_USER_AGENT"]
					: $HTTP_USER_AGENT;

		// Clean all output buffers (needs PHP > 4.2.0)
		while (@ob_end_clean());
		$file = $p . DS . $f;

		$fsize = filesize($file);
		$mod_date = date('r', filemtime($file));

		$cont_dis = $inline ? 'inline' : 'attachment';

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header(
			'Content-Disposition:' . $cont_dis .';'
			. ' filename="' . $f . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize .';'
		); //RFC2183
		header("Content-Type: "    . $mime); // MIME type
		header("Content-Length: "  . $fsize);

		// No encoding - we aren't using compression... (RFC1945)

		$this->_readfile_chunked($file);
	}

	/**
	 * Read file contents
	 *
	 * @param   string   $filename
	 * @param   boolean  $retbytes
	 * @return  mixed
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
	 * @return  void
	 */
	public function contributeTask()
	{
		// Incoming
		$pid     = Request::getInt('pid', 0);
		$action  = Request::getVar('action', '');
		$active  = Request::getVar('active', 'publications');
		$action  = $this->_task == 'start' ? 'start' : $action;
		$ajax    = Request::getInt('ajax', 0);

		// Redirect if publishing is turned off
		if (!$this->_contributable)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Load language file
		Lang::load('com_projects') ||
		Lang::load('com_projects', PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site');

		// Instantiate a new view
		$this->view  = new \Hubzero\Component\View(array(
			'name'   => 'submit',
			'layout' => 'default'
		));
		$this->view->option = $this->_option;
		$this->view->config = $this->config;

		// Set page title
		$this->_task_title = Lang::txt('COM_PUBLICATIONS_SUBMIT');
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// What plugin requested?
		$allowed = array('team', 'files', 'notes', 'databases', 'publications', 'links');
		$plugin  = in_array($active, $allowed) ? $active : 'publications';

		if (User::isGuest() && ($action == 'login' || $action == 'start' || $action == 'publication'))
		{
			$this->_msg = $this->_task == 'start'
						? Lang::txt('COM_PUBLICATIONS_LOGIN_TO_START')
						: Lang::txt('COM_PUBLICATIONS_LOGIN_TO_VIEW_SUBMISSIONS');
			$this->_login();
			return;
		}

		// Get project model
		$project = new \Components\Projects\Models\Project();

		// Get project information
		if ($pid)
		{
			$project->loadProvisioned($pid);

			if (!$project->exists())
			{
				App::redirect(Route::url('index.php?option=' . $this->_option . '&task=submit'));
				return;
			}

			// Block unauthorized access
			if (!$project->access('owner') && !$project->access('content'))
			{
				$this->_blockAccess();
				return;
			}

			// Redirect to project if not provisioned
			if (!$project->isProvisioned())
			{
				App::redirect(
					Route::url($project->link('publications') . '&pid=' . $pid . '&action=' . $action)
				);
				return;
			}
		}

		// Is project registration restricted?
		if ($action == 'start' && !$project->access('create'))
		{
			$this->_buildPathway(null);
			$this->view = new \Hubzero\Component\View(array('name'=>'error', 'layout' =>'restricted'));
			$this->view->error  = Lang::txt('COM_PUBLICATIONS_ERROR_NOT_FROM_CREATOR_GROUP');
			$this->view->title  = $this->title;
			$this->view->option = $this->_option;
			$this->view->display();
			return;
		}

		// No action requested ?
		if (!$action)
		{
			$action = $pid ? 'publication' : 'contribute';
		}

		// Plugin params
		$plugin_params = array(
			$project,
			$action,
			$areas = array($plugin)
		);

		$content = Event::trigger('projects.onProject', $plugin_params);
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
			App::redirect($content[0]['referer']);
			return;
		}
		elseif (empty($content))
		{
			// plugin disabled?
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Output HTML
		$this->view->project = $project;
		$this->view->action  = $action;
		$this->view->pid     = $pid;
		$this->view->title   = $this->_title;
		$this->view->msg     = $this->getNotifications('success');
		$error               = $this->getError() ? $this->getError() : $this->getNotifications('error');
		if ($error)
		{
			$this->view->setError($error);
		}
		$this->view->display();

		return;
	}

	/**
	 * Save tags on a publication
	 *
	 * @return  void
	 */
	public function savetagsTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$this->_task = 'page';
			$this->pageTask();
			return;
		}

		// Incoming
		$id      = Request::getInt('id', 0);
		$tags    = Request::getVar('tags', '');
		$no_html = Request::getInt('no_html', 0);

		// Process tags
		$rt = new Helpers\Tags($this->database);
		$rt->tag_object(User::get('id'), $id, $tags, 1, 0);

		if (!$no_html)
		{
			// Push through to the resource view
			$this->_task = 'page';
			$this->pageTask();
			return;
		}
	}

	/**
	 * Block access to restricted publications
	 *
	 * @param   object  $publication
	 * @return  string
	 */
	protected function _blockAccess()
	{
		// Set the task
		$this->_task = 'block';

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_PUBLICATIONS_PRIVATE_PUB_LOGIN');
			return $this->_login();
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option),
			Lang::txt('COM_PUBLICATIONS_RESOURCE_NO_ACCESS'),
			'error'
		);
	}
}
