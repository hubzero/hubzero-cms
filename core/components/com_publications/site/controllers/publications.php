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
use Component;
use Exception;
use Document;
use Pathway;
use Request;
use Plugin;
use Notify;
use Route;
use Lang;
use User;
use App;

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
					$this->view->filters['category'] = (int)$cat->id;
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
		if ($content === null || $content == false)
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
						if (!strstr($auth, ','))
						{
							$bits = explode(' ', $auth);
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
						$author_arr = array_map('trim', $author_arr);

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
		while (@ob_end_clean())
		{
		}

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
		$doiErr  = Request::getInt('doierr', 0);

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

		// @FIXME: Handle errors appropriately. [QUBES][#732]
		if ($doiErr == 1)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
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
	 * Fork a publication
	 *
	 * @return  void
	 */
	public function forkTask()
	{
		// Incoming
		$pid = Request::getInt('project', 0);
		$vid = Request::getInt('version', 0);

		$this->_task_title = Lang::txt('COM_PUBLICATIONS_FORK');

		// Redirect if publishing is turned off
		if (!$this->_contributable || !$vid)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Redirect if forks aren't allowed
		if (!$this->config->get('forks'))
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Load language file
		Lang::load('com_projects') ||
		Lang::load('com_projects', PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site');

		// Set page title
		$this->_task_title = Lang::txt('COM_PUBLICATIONS_FORK');
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_PUBLICATIONS_LOGIN_TO_START');
			$this->_login();
			return;
		}

		// Get project model
		$project = new \Components\Projects\Models\Project($pid);

		// Get project information
		if ($pid)
		{
			//$project->loadProvisioned($pid);

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
			/*if (!$project->isProvisioned())
			{
				App::redirect(
					Route::url($project->link('publications'))
				);
				return;
			}*/
		}
		else
		{
			include_once Component::path('com_projects') . '/helpers/html.php';

			// Need to provision a project
			$alias = 'pub-' . strtolower(\Components\Projects\Helpers\Html::generateCode(10, 10, 0, 1, 1));

			$project->set('provisioned', 1);
			$project->set('alias', $alias);
			$project->set('title', $alias);
			$project->set('type', 2); // publication
			$project->set('state', 1);
			$project->set('setup_stage', 3);
			$project->set('created', Date::toSql());
			$project->set('created_by_user', User::get('id'));
			$project->set('owned_by_user', User::get('id'));

			$project->set('params', $project->type()->params);
		}

		// Is project registration restricted?
		if (!$pid && !$project->access('create'))
		{
			$this->_buildPathway(null);

			$this->view
				->set('error', Lang::txt('COM_PUBLICATIONS_ERROR_NOT_FROM_CREATOR_GROUP'))
				->set('title', $this->title)
				->set('option', $this->_option)
				->setName('error')
				->setLayout('restricted')
				->display();
			return;
		}

		// Move creation of the project to _after_ the access check
		// for project creation
		if (!$project->get('id'))
		{
			// Save changes
			if (!$project->store())
			{
				App::abort(500, $project->getError());
			}

			// Save the user as member and owner of the project
			$objO = $project->table('Owner');

			if (!$objO->saveOwners($project->get('id'), User::get('id'), User::get('id'), 0, 1, 1, 1, '', 0))
			{
				App::abort(500, $objO->getError());
			}

			// Create and initialize local repo
			/*if (!$project->repo()->iniLocal())
			{
				App::abort(500, Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
			}*/
		}

		//
		// Let's start copying...
		//

		include_once dirname(dirname(__DIR__)) . '/models/orm/publication.php';

		// Load the version
		$version = Models\Orm\Version::oneOrFail($vid);

		// Make sure the license applied allows for derivations
		if (!$version->license->get('derivatives'))
		{
			Notify::warning(Lang::txt('The license applied to this publication does not allow for derivatives.'));

			App::redirect(
				Route::url('index.php?option=com_publications&id=' . $version->get('publication_id') . '&v=' . $version->get('id'), false)
			);
		}

		// Set some paths
		$repoPath = Component::params('com_projects')->get('webpath') . '/' . $project->get('alias') . '/files';

		$pubfilespace = $version->filespace();
		$prjfilespace = $repoPath . ($pid ? '/publication_' . $vid : '');

		// We're going to need the original ID later
		$pub_id = $version->get('publication_id');

		// Copy the publication's database entry
		$publication = $version->publication;
		$publication->set('id', 0);
		$publication->set('checked_out', 0);
		$publication->set('checked_out_time', '0000-00-00 00:00:00');
		$publication->set('created', Date::of('now')->toSql());
		$publication->set('created_by', User::get('id'));
		$publication->set('rating', 0.0);
		$publication->set('times_rated', 0);
		$publication->set('group_owner', 0);
		$publication->set('master_doi', '');
		$publication->set('project_id', $project->get('id'));

		if (!$publication->save())
		{
			// Abort! If we don't have the plublication record
			// at this point, there's nothing else we can do.
			App::abort(500, $publication->getError());
		}

		$authors = $version->authors;
		$attachments = $version->attachments;

		// Copy the version info and set it as the first version
		$version->set('id', 0);
		$version->set('publication_id', $publication->get('id'));
		$version->set('doi', '');
		$version->set('rating', 0.0);
		$version->set('times_rated', 0);
		$version->set('main', 1);
		$version->set('state', 3);
		$version->set('created', Date::of('now')->toSql());
		$version->set('created_by', User::get('id'));
		$version->set('published_up', '0000-00-00 00:00:00');
		$version->set('published_down', '0000-00-00 00:00:00');
		$version->set('modified', '0000-00-00 00:00:00');
		$version->set('modified_by', 0);
		$version->set('accepted', '0000-00-00 00:00:00');
		$version->set('archived', '0000-00-00 00:00:00');
		$version->set('submitted', '0000-00-00 00:00:00');
		$version->set('version_label', '1.0.0');
		$version->set('version_number', 1);
		$version->set('curation', '');
		$version->set('reviewed', '0000-00-00 00:00:00');
		$version->set('reviewed_by', 0);
		$version->set('curator', 0);
		$version->set('curation_version_id', 0);

		// Make sure we know where the info came from
		$version->set('forked_from', $vid);

		if (!$version->save())
		{
			App::abort(500, $version->getError());
		}

		$prjfilespace .= ($pid ? '_' . $version->get('id') : '');
		$newpubfilespace = $version->filespace();

		// Copy tags
		include_once dirname(dirname(__DIR__))  . DS . 'helpers' . DS . 'tags.php';

		$rt = new Helpers\Tags($this->database);
		if ($tags = $rt->get_tag_string($pub_id))
		{
			$rt->tag_object(User::get('id'), $version->get('publication_id'), $tags, 1);
		}

		// Copy citations
		include_once Component::path('com_citations')  . '/tables/association.php';

		$c = new \Components\Citations\Tables\Association($this->database);
		$citations = $c->getRecords(array(
			'tbl' => 'publication',
			'oid' => $pub_id
		));
		foreach ($citations as $citation)
		{
			$ca = new \Components\Citations\Tables\Association($this->database);
			$ca->cid = $citation->cid;
			$ca->tbl = $citation->tbl;
			$ca->oid = $pub_id;

			if (!$ca->save())
			{
				App::abort(500, $ca->getError());
			}
		}

		// Copy authors
		if (!isset($objO))
		{
			$objO = $project->table('Owner');
		}
		$ownrs = $objO->getOwners($project->get('id'));
		$owners = array();
		foreach ($ownrs as $owner)
		{
			$owners[$owner->userid] = $owner->id;
		}
		foreach ($authors as $author)
		{
			$owner_id = $author->get('project_owner_id');

			if (!isset($owners[$author->get('user_id')]))
			{
				$objO->load($owner_id);
				$objO->projectid  = $project->get('id');
				$objO->id         = null;
				$objO->added      = Date::of('now')->toSql();
				$objO->num_visits = 0;
				$objO->lastvisit  = null;
				$objO->status     = 2;
				$objO->role       = 0;
				if ($objO->groupid != $project->get('owned_by_group'))
				{
					$objO->groupid = 0;
				}
				$objO->store();

				$owners[$author->get('user_id')] = $objO->id;
			}

			$author->set('id', 0);
			$author->set('publication_version_id', $version->get('id'));
			$author->set('project_owner_id', $owners[$author->get('user_id')]);
			$author->set('created', Date::of('now')->toSql());
			$author->set('created_by', User::get('id'));
			$author->set('modified', '0000-00-00 00:00:00');
			$author->set('modified_by', 0);

			if (!$author->save())
			{
				App::abort(500, $author->getError());
			}
		}

		// Copy attachments
		// Get manifest from either version record (published) or master type
		$manifest = $version->get('curation', $publication->type->get('curation'));
		$curation = json_decode($manifest, true);
		$fileParams = array(
			'directory'    => '',
			'dirHierarchy' => 1
		);
		$galleryParams = array(
			'directory'    => 'gallery',
			'dirHierarchy' => 0
		);
		if (isset($curation['blocks']))
		{
			foreach ($curation['blocks'] as $block)
			{
				if (!isset($block['name']))
				{
					continue;
				}
				if ($block['name'] == 'content' && isset($block['elements']))
				{
					foreach ($block['elements'] as $element)
					{
						if (!isset($element['type']))
						{
							continue;
						}
						if ($element['type'] == 'attachment')
						{
							if ($element['params']['type'] == 'file')
							{
								$fileParams = $element['params']['typeParams'];
							}
						}
					}
				}
				if ($block['name'] == 'extras' && isset($block['elements']))
				{
					foreach ($block['elements'] as $element)
					{
						if (!isset($element['type']))
						{
							continue;
						}
						if ($element['type'] == 'attachment')
						{
							$params = $element['params']['typeParams'];
							if ($params['handler'] == 'imageviewer')
							{
								$galleryParams = $params;
							}
						}
					}
				}
			}
		}

		if (!is_dir($prjfilespace))
		{
			if (!Filesystem::makeDirectory($prjfilespace, 0755, true, true))
			{
				App::abort(500, Lang::txt('COM_PROJECTS_FILES_ERROR_UNABLE_TO_CREATE_PATH'));
			}
		}
		foreach ($attachments as $attachment)
		{
			$oldid = $attachment->get('id');

			$attachment->set('id', 0);
			$attachment->set('publication_id', $version->get('publication_id'));
			$attachment->set('publication_version_id', $version->get('id'));
			$attachment->set('created', Date::of('now')->toSql());
			$attachment->set('created_by', User::get('id'));
			$attachment->set('modified', '0000-00-00 00:00:00');
			$attachment->set('modified_by', 0);

			if (!$attachment->save())
			{
				App::abort(500, $attachment->getError());
			}

			$sub = 'publication_' . $vid . '_' . $version->get('id');

			if ($attachment->get('type') == 'file')
			{
				$dirHierarchy = $fileParams['dirHierarchy'];

				// Copy the files into the project
				$path = explode('/', $attachment->get('path'));
				$orig = array_pop($path);
				// If copying to a project, files are placed in a sub-directory
				// So, update the path info on the attachment record to reflect
				if ($pid)
				{
					//array_unshift($path, 'publication_' . $vid);
					$attachment->set('path', $sub . '/' . $attachment->get('path'));
					$attachment->save();
				}
				$path = implode('/', $path);
				$file = $orig;
				$filenew = $orig;

				$file2 = Filesystem::name($file) . '-' . $oldid . '.' . Filesystem::extension($file);
				$file2new = Filesystem::name($file) . '-' . $attachment->get('id') . '.' . Filesystem::extension($file);

				$from   = $pubfilespace . '/' . ($path ? $path . '/' : ''); // . $file;
				$toProj = $prjfilespace . '/' . ($path ? $path . '/' : ''); // . $file;
				$toPub  = $newpubfilespace . '/' . ($path ? $path . '/' : ''); // . $file;

				// Check the default location
				if (!file_exists($from . $file))
				{
					// OK, maybe it's in the gallery
					$from  = dirname($pubfilespace) . '/' . $galleryParams['directory'] . '/' . ($path ? $path . '/' : '');
					$toPub = dirname($newpubfilespace) . '/' . $galleryParams['directory'] . '/' . ($path ? $path . '/' : '');

					if (!file_exists($from . $file))
					{
						// Let's try an alternate file name
						if (!file_exists($from . $file2))
						{
							Notify::error('File does not exist: ' . $from . $filenames['main']);
							continue;
						}
						// Found it
						else
						{
							$dirHierarchy = $galleryParams['dirHierarchy'];

							$file = $file2;
							$filenew = $file2new;
						}
					}
					// Found it
					else
					{
						$dirHierarchy = $galleryParams['dirHierarchy'];
					}
				}

				$source = array();
				$source['main'] = $file;
				$source['hash'] = $file . '.hash';
				$source['thmb'] = \Components\Projects\Helpers\Html::createThumbName($file, '_tn', 'png');

				$dest = array();
				$dest['main'] = $filenew;
				$dest['hash'] = $filenew . '.hash';
				$dest['thmb'] = \Components\Projects\Helpers\Html::createThumbName($filenew, '_tn', 'png'); // thumbnails aren't in sub?

				if (!is_dir($toProj))
				{
					Filesystem::makeDirectory($toProj, 0755, true, true);

					if ($pid)
					{
						// Commit to GIT
						$fileObj = new \Components\Projects\Models\File(
							substr($toProj, (strlen($repoPath) + 1)),
							$repoPath
						);
						$fileObj->set('type', 'folder');
						$fileObj->clear('ext');

						$committed = $project->repo()->call('makeDirectory', array(
							'file'    => $fileObj,
							'replace' => false
						));
						if (!$committed)
						{
							App::abort(500, Lang::txt('Error committing directory: %s', $project->repo()->getError()));
						}
					}
				}

				foreach ($source as $type => $filename)
				{
					if (!file_exists($from . $filename))
					{
						if ($type == 'main')
						{
							Notify::warning('File does not exist: ' . $from . $filename);
						}
						continue;
					}

					// We only copy the file itself to the project
					// The hash and thumbnail go only to the publication space
					if ($type == 'main')
					{
						// Copy to the project space but to its original name
						// Note: gallery items' filenames will have a suffix with attachment record ID but the database entry
						// will point to the un-suffixed filename. DB: foo.jpg (in project space) = foo-123.jpg (in publication space)
						if (!Filesystem::copy($from . $filename, $toProj . $orig)) //$filename
						{
							App::abort(500, Lang::txt('Failed to copy file "' . $from . $filename . '" to "' . $toProj . $orig . '"'));
						}

						if ($pid)
						{
							// Commit to GIT
							$fileObj = new \Components\Projects\Models\File(
								substr($toProj . $orig, (strlen($repoPath) + 1)),
								$repoPath
							);

							$committed = $project->repo()->call('checkin', array(
								'file'    => $fileObj,
								'replace' => false,
								'message' => Lang::txt('Files forked from publication #%s', $pub_id),
								'author'  => null,
								'date'    => null
							));
							if (!$committed)
							{
								App::abort(500, Lang::txt('Error committing file: %s', $project->repo()->getError()));
							}
						}
					}

					// Make sure directory exist in publication space
					$to = $toPub;

					// Preserve hierarchhy?
					if ($dirHierarchy == 1)
					{
						$to .= ($pid ? $sub . '/' : '');
					}

					if (!is_dir($to))
					{
						Filesystem::makeDirectory($to, 0755, true, true);
					}

					$to .= $dest[$type];

					// Copy to the publication space
					if (!Filesystem::copy($from . $filename, $to))
					{
						App::abort(500, Lang::txt('Failed to copy file "' . $from . $filename . '" to "' . $to . '"'));
					}
				}
			}
		}

		// Copy publication thumbnail
		$files = array('master.png', 'thumb.gif');
		$from  = dirname($pubfilespace);
		$to    = dirname($newpubfilespace);

		foreach ($files as $filename)
		{
			if (!file_exists($from . $filename))
			{
				continue;
			}

			// Copy to the publication space
			if (!Filesystem::copy($from . $filename, $to . $filename))
			{
				App::abort(500, Lang::txt('Failed to copy file "' . $from . $filename . '" to "' . $to . $filename . '"'));
			}
		}

		Notify::success(Lang::txt('COM_PUBLICATIONS_PUBLICATION_FORKED'));

		// Redirect to the project
		if ($pid)
		{
			App::redirect(
				Route::url($project->link('publications') . '&pid=' . $version->get('publication_id') . '&version=' . $version->get('version_number'))
			);
		}

		// Redirect to the publication submission page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=submit&pid=' . $version->get('publication_id') . '&version=' . $version->get('version_number'))
		);
	}

	/**
	 * Show differences between two publications
	 *
	 * @return  void
	 */
	public function compareTask()
	{
		$lft = Request::getInt('left', 0);
		$rgt = Request::getInt('right', 0);

		// Make sure we have values for both sides
		if (!$lft || !$rgt)
		{
			Notify::error(Lang::txt('COM_PUBLICATIONS_ERROR_MISSING_VERSION'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		// Can't compare against itself
		if ($lft == $rgt)
		{
			Notify::error(Lang::txt('COM_PUBLICATIONS_ERROR_SAME_VERSIONS'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		// Get our model and load publication data
		include_once dirname(dirname(__DIR__)) . '/models/orm/publication.php';

		// Load the lft version and make sure the user has access
		$lversion = Models\Orm\Version::oneOrFail($lft);
		$lpublica = $lversion->publication;

		if (!$lversion->get('id') || $lversion->isDeleted()
		 || !$lpublica->get('id'))
		{
			Notify::error(Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		/*if (!$lpublica->access('view'))
		{
			return $this->_blockAccess();
		}*/

		// Load the rgt version and make sure the user has access
		$rversion = Models\Orm\Version::oneOrFail($rgt);

		if (!$rversion->get('id') || $rversion->isDeleted())
		{
			Notify::error(Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		$rpublica = new Models\Publication(null, 'default', $rversion->get('id'));

		if (!$rpublica->access('view'))
		{
			return $this->_blockAccess();
		}

		$rpublica->setCuration();
		$customFields = json_decode($rpublica->_curationModel->getMetaSchema(), true);

		// Diff the two versions
		require_once dirname(dirname(__DIR__)) . '/helpers/Diff.php';
		require_once dirname(dirname(__DIR__)) . '/helpers/Diff/Renderer/Html/SideBySide.php';

		$diffs = array();

		$l = explode("\n", $lversion->get('title'));
		$r = explode("\n", $rversion->get('title'));

		$diff = new \Diff($l, $r);
		$diffs['title'] = $diff->render(new \Diff_Renderer_Html_SideBySide);

		$l = array();
		foreach ($lversion->authors as $author)
		{
			$l[] = $author->get('name') . ' (' . $author->get('organization') . ')';
		}
		$r = array();
		foreach ($rversion->authors as $author)
		{
			$r[] = $author->get('name') . ' (' . $author->get('organization') . ')';
		}

		$diff = new \Diff($l, $r);
		$diffs['authors'] = $diff->render(new \Diff_Renderer_Html_SideBySide);

		$l = explode("\n", $lversion->get('description'));
		$r = explode("\n", $rversion->get('description'));

		$diff = new \Diff($l, $r);
		$diffs['description'] = $diff->render(new \Diff_Renderer_Html_SideBySide);

		$diffs['metadata'] = array();

		$lmetadata = $lversion->metadata;
		$rmetadata = $rversion->metadata;
		foreach ($lmetadata as $key => $l)
		{
			$r = (isset($rmetadata[$key]) ? $rmetadata[$key] : '');
			$l = explode("\n", $l);
			$r = explode("\n", $r);

			$diff = new \Diff($l, $r);
			$diffs['metadata'][$key] = $diff->render(new \Diff_Renderer_Html_SideBySide);
		}

		foreach ($rmetadata as $key => $r)
		{
			if (isset($diffs['metadata'][$key]))
			{
				continue;
			}
			$l = (isset($lmetadata[$key]) ? $lmetadata[$key] : '');
			$l = explode("\n", $l);
			$r = explode("\n", $r);

			$diff = new \Diff($l, $r);
			$diffs['metadata'][$key] = $diff->render(new \Diff_Renderer_Html_SideBySide);
		}

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			'index.php?option=' . $this->_option . '&task=' . $this->_task . '&lft=' . $lft . '&rgt=' . $rgt
		);

		// Display the view
		$this->view
			->set('lft', $lversion)
			->set('rgt', $rversion)
			->set('diffs', $diffs)
			->set('customFields', $customFields)
			->display();
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
