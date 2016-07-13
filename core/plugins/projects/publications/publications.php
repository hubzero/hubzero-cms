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

// No direct access
defined('_HZEXEC_') or die();

include_once(PATH_CORE . DS . 'components' . DS . 'com_publications'
	. DS . 'models' . DS . 'publication.php');

/**
 * Project publications
 */
class plgProjectsPublications extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var	   array
	 */
	protected $_msg = NULL;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas($alias = NULL)
	{
		$area = array();

		// Check if plugin is restricted to certain projects
		$projects = $this->params->get('restricted') ? \Components\Projects\Helpers\Html::getParamArray($this->params->get('restricted')) : array();

		if (!empty($projects) && $alias)
		{
			if (!in_array($alias, $projects))
			{
				return $area;
			}
		}

		$area = array(
			'name'    => 'publications',
			'title'   => Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS'),
			'submenu' => NULL,
			'show'    => true
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param      object  $model 		Project
	 * @return     array   integer
	 */
	public function &onProjectCount( $model )
	{
		// Get this area details
		$this->_area = $this->onProjectAreas();

		if (empty($this->_area) || !$model->exists())
		{
			return $counts['publications'] = 0;
		}
		else
		{
			$database = App::get('db');

			// Instantiate project publication
			$objP = new \Components\Publications\Tables\Publication( $database );

			$filters = array();
			$filters['project']  		= $model->get('id');
			$filters['ignore_access']   = 1;
			$filters['dev']   	 		= 1;

			$counts['publications'] = $objP->getCount($filters);
			return $counts;
		}
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      object  $model           Project model
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onProject( $model, $action = '', $areas = null )
	{
		$returnhtml = true;

		$arr = array(
			'html'      =>'',
			'metadata'  =>'',
			'message'   =>'',
			'error'     =>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}

		// Check authorization
		if ($model->exists() && !$model->access('member'))
		{
			return $arr;
		}

		// Model
		$this->model = $model;

		// Incoming
		$this->_task = Request::getVar('action', '');
		$this->_pid  = Request::getInt('pid', 0);
		if (!$this->_task)
		{
			$this->_task = $this->_pid ? 'publication' : $action;
		}

		$this->_uid       = User::get('id');
		$this->_database  = App::get('db');
		$this->_config    = $this->model->config();
		$this->_pubconfig = Component::params( 'com_publications' );

		// Common extensions (for gallery)
		$this->_image_ext = \Components\Projects\Helpers\Html::getParamArray(
			$this->params->get('image_types', 'bmp, jpeg, jpg, png' ));
		$this->_video_ext = \Components\Projects\Helpers\Html::getParamArray(
			$this->params->get('video_types', 'avi, mpeg, mov, wmv' ));

		// Check if exists or new
		if (!$this->model->exists())
		{
			// Contribute process outside of projects
			$this->model->set('provisioned', 1);

			$ajax_tasks  = array('showoptions', 'save', 'showitem');
			$this->_task = $action == 'start' ? 'start' : 'contribute';
			if ($action == 'publication')
			{
				$this->_task = 'publication';
			}
			elseif (in_array($action, $ajax_tasks))
			{
				$this->_task = $action;
			}
		}
		elseif ($this->model->isProvisioned())
		{
			// No browsing within provisioned project
			$this->_task = $action == 'browse' ? 'contribute' : $action;
		}

		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications','curation');
		\Hubzero\Document\Assets::addPluginScript('projects', 'publications', 'curation');

		// Actions
		switch ($this->_task)
		{
			case 'browse':
			default:
				$arr['html'] = $this->browse();
				break;

			case 'start':
			case 'new':
				$arr['html'] = $this->startDraft();
				break;

			case 'edit':
			case 'publication':
			case 'continue':
			case 'review':
				$arr['html'] = $this->editDraft();
				break;

			case 'newversion':
			case 'savenew':
				$arr['html'] = $this->newVersion();
				break;

			case 'checkstatus':
				$arr['html'] = $this->checkStatus();
				break;

			case 'select':
				$arr['html'] = $this->select();
				break;

			case 'saveparam':
				$arr['html'] = $this->saveParam();
				break;

			// Change publication state
			case 'publish':
			case 'republish':
			case 'archive':
			case 'revert':
			case 'post':
				$arr['html'] = $this->publishDraft();
				break;

			case 'apply':
			case 'save':
			case 'rewind':
			case 'reorder':
			case 'deleteitem':
			case 'additem':
			case 'dispute':
			case 'skip':
			case 'undispute':
			case 'saveitem':
				$arr['html'] = $this->saveDraft();
				break;

			// Individual items editing
			case 'edititem':
			case 'editauthor':
				$arr['html'] = $this->editItem();
				break;

			case 'suggest_license':
			case 'save_license':
				$arr['html'] = $this->_suggestLicense();
				break;

			// Show all publication versions
			case 'versions':
				$arr['html'] = $this->versions();
				break;

			// Unpublish/delete
			case 'cancel':
				$arr['html'] = $this->cancelDraft();
				break;

			// Contribute process outside of projects
			case 'contribute':
				$arr['html'] = $this->contribute();
				break;

			// Show stats
			case 'stats':
				$arr['html'] = $this->_stats();
				break;

			case 'diskspace':
				$arr['html'] = $this->pubDiskSpace($this->model);
				break;

			// Handlers
			case 'handler':
				$arr['html'] = $this->handler();
				break;
		}

		// Return data
		return $arr;
	}

	/**
	 * Event call to get side content for main project page
	 *
	 * @return
	 */
	public function onProjectMiniList($model)
	{
		if (!$model->exists() || !$model->access('content'))
		{
			return false;
		}

		// Instantiate a publication object
		$pub = new \Components\Publications\Models\Publication();

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'publications',
				'name'    => 'mini'
			)
		);

		// Filters for returning results
		$view->filters = array(
			'project'	 => $model->get('id'),
			'limit'      => $model->config()->get('sidebox_limit', 5),
			'start'		 => 0,
			'dev'        => 1,
			'sortby'	 => 'date_created',
			'sortdir'	 => 'DESC'
		);

		$view->items = $pub->entries( 'list', $view->filters );
		$view->model = $model;
		return $view->loadTemplate();
	}

	/**
	 * Event call to get content for public project page
	 *
	 * @return
	 */
	public function onProjectPublicList($model)
	{
		if (!$model->exists() || !$model->isPublic())
		{
			return false;
		}
		if (!$model->params->get('publications_public', 0))
		{
			return false;
		}

		// Instantiate a publication object
		$pub = new \Components\Publications\Models\Publication();

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'publications',
				'name'    => 'publist'
			)
		);

		// Filters for returning results
		$view->filters = array(
			'project'	 => $model->get('id'),
			'sortby'	 => 'date_published',
			'sortdir'	 => 'DESC'
		);

		$view->items = $pub->entries( 'list', $view->filters );
		$view->model = $model;
		return $view->loadTemplate();
	}

	/**
	 * Browse publications
	 *
	 * @return     string
	 */
	public function browse()
	{
		// Build query
		$filters = array();
		$filters['limit'] 	 		= Request::getInt('limit', Config::get('list_limit'));
		$filters['start'] 	 		= Request::getInt('limitstart', 0);
		$filters['sortby']   		= Request::getVar('sortby', 'title');
		$filters['sortdir']  		= Request::getVar('sortdir', 'ASC');
		$filters['project']  		= $this->model->get('id');
		$filters['ignore_access']   = 1;
		$filters['dev']   	 		= 1; // get dev versions

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder' =>'projects',
				'element'=>'publications',
				'name'   =>'browse'
			)
		);

		// Instantiate a publication object
		$view->pub = new \Components\Publications\Models\Publication();

		// Get all publications
		$view->rows = $view->pub->entries( 'list', $filters );

		// Get total count
		$view->total = $view->pub->entries( 'count', $filters );

		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files', 'diskspace');
		\Hubzero\Document\Assets::addPluginScript('projects', 'files', 'diskspace');

		// Need to calculate ALL publications
		$allRows = $view->pub->entries('list', array('project' => $this->model->get('id'), 'dev' => 1));

		// Get used space
		$view->dirsize = \Components\Publications\Helpers\Html::getDiskUsage($allRows, false);
		$view->params  = $this->model->params;
		$view->quota   = $view->params->get('pubQuota')
						? $view->params->get('pubQuota')
						: \Components\Projects\Helpers\Html::convertSize(floatval($this->model->config()->get('pubQuota', '1')), 'GB', 'b');

		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->model;
		$view->filters 		= $filters;
		$view->title		= $this->_area['title'];

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Handler manager
	 *
	 * @return     string
	 */
	public function handler()
	{
		// Incoming
		$props  = Request::getVar( 'p', '' );
		$ajax   = Request::getInt( 'ajax', 0 );
		$pid    = Request::getInt( 'pid', 0 );
		$vid    = Request::getInt( 'vid', 0 );
		$handler= trim(Request::getVar( 'h', '' ));
		$action = trim(Request::getVar( 'do', '' ));

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0])) ? $parts[0] : 'content';
		$blockId = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 0;

		// Output HTML
		$view = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'site',
			'name'   => 'handlers',
			'layout' => 'editor',
		));

		$view->publication = new \Components\Publications\Models\Publication( $pid, NULL, $vid );

		if (!$view->publication->exists())
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_NO_PUBID'));
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'files',
					'name'		=>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( $this->getError() );
			return $view->loadTemplate();
		}

		// Set curation
		$view->publication->setCuration();

		// Set block
		if (!$view->publication->_curationModel->setBlock( $block, $blockId ))
		{
			$view->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_LOADING_CONTENT') );
		}

		// Load handler
		$modelHandler = new \Components\Publications\Models\Handlers($this->_database);
		$view->handler = $modelHandler->ini($handler);
		if (!$view->handler)
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_LOADING_HANDLER') );
		}
		else
		{
			// Perform request
			if ($action)
			{
				$modelHandler->update($view->handler, $view->publication, $element, $action);
			}

			// Load editor
			$view->editor = $modelHandler->loadEditor($view->handler, $view->publication, $element);
		}

		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->uid 			= $this->_uid;
		$view->ajax			= $ajax;
		$view->task			= $this->_task;
		$view->element		= $element;
		$view->block		= $block;
		$view->blockId 		= $blockId;
		$view->props		= $props;
		$view->config		= $this->_pubconfig;

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * View for selecting items (currently used for license selection)
	 *
	 * @return     string
	 */
	public function select()
	{
		// Incoming
		$props  = Request::getVar( 'p', '' );
		$ajax   = Request::getInt( 'ajax', 0 );
		$pid    = Request::getInt( 'pid', 0 );
		$vid    = Request::getInt( 'vid', 0 );
		$filter = urldecode(Request::getVar( 'filter', '' ));

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0])) ? $parts[0] : 'content';
		$blockId = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 0;

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'selector'
			)
		);

		$view->publication = new \Components\Publications\Models\Publication( $pid, NULL, $vid );

		if (!$view->publication->exists())
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_NO_PUBID'));
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'files',
					'name'		=>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( $this->getError() );
			return $view->loadTemplate();
		}

		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications','selector');

		// Set curation
		$view->publication->setCuration();

		// Set block
		if (!$view->publication->_curationModel->setBlock( $block, $blockId ))
		{
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'files',
					'name'		=>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_LOADING_CONTENT') );
			return $view->loadTemplate();
		}

		$view->option 		= $this->model->isProvisioned() ? 'com_publications' : $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->ajax			= $ajax;
		$view->task			= $this->_task;
		$view->element		= $element;
		$view->block		= $block;
		$view->blockId 		= $blockId;
		$view->props		= $props;
		$view->filter		= $filter;

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();

	}

	/**
	 * Save param in version table (AJAX)
	 *
	 * @return     string
	 */
	public function saveParam()
	{
		// Incoming
		$pid  	= $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$vid  	= Request::getInt('vid', 0);
		$param  = Request::getVar('param', '');
		$value  = urldecode(Request::getVar('value', ''));

		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Load publication
		$publication = new \Components\Publications\Models\Publication( $pid, NULL, $vid );

		// Make sure the publication belongs to the project
		if (!$publication->exists() || !$publication->belongsToProject($this->model->get('id')))
		{
			$this->setError(Lang::txt('Failed to save a setting'));
			return json_encode(array('error' => $this->getError(), 'result' => $result));
		}

		if ($result = $publication->saveParam($param, $value))
		{
			return json_encode(array('success' => true, 'result' => $result));
		}
		else
		{
			$this->setError(Lang::txt('Failed to save a setting'));
			return json_encode(array('error' => $this->getError(), 'result' => $result));
		}
	}

	/**
	 * Check completion status for a section via AJAX call
	 *
	 * @return     string
	 */
	public function checkStatus()
	{
		// Incoming
		$pid  		= $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$version 	= Request::getVar( 'version', 'default' );
		$ajax 		= Request::getInt('ajax', 0);
		$block  	= Request::getVar( 'section', '' );
		$blockId  	= Request::getInt( 'step', 0 );
		$element  	= Request::getInt( 'element', 0 );
		$props  	= Request::getVar( 'p', '' );
		$parts   	= explode('-', $props);

		// Parse props for curation
		if (!$block || !$blockId)
		{
			$block   	 = (isset($parts[0])) ? $parts[0] : 'content';
			$blockId    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
			$element 	 = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 1;
		}

		// Load publication
		$pub = new \Components\Publications\Models\Publication( $pid, $version );

		$status = new \Components\Publications\Models\Status();

		// If publication not found, raise error
		if (!$pub->exists() || !$publication->belongsToProject($this->model->get('id')))
		{
			return json_encode($status);
		}

		// Set curation
		$pub->setCuration();

		if ($element && $block)
		{
			// Get block element status
			$status = $pub->_curationModel->getElementStatus($block, $element, $pub, $blockId);
		}
		elseif ($block)
		{
			// Getting block status
			$status = $pub->_curationModel->getStatus($block, $pub, $blockId);
		}

		return json_encode($status);
	}

	/**
	 * Save publication draft
	 *
	 * @return     string
	 */
	public function saveDraft()
	{
		// Incoming
		$pid      = $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$version = Request::getVar( 'version', 'dev' );
		$block   = Request::getVar( 'section', '' );
		$blockId = Request::getInt( 'step', 0 );
		$element = Request::getInt( 'element', 0 );
		$next    = Request::getInt( 'next', 0 );
		$json    = Request::getInt( 'json', 0 );
		$move    = Request::getVar( 'move', '' ); // draft flow?
		$back    = Request::getVar( 'backUrl', Request::getVar('HTTP_REFERER', NULL, 'server') );
		$new	 = false;
		$props   = Request::getVar( 'p', '' );
		$parts   = explode('-', $props);

		// Check permission
		if ($this->model->exists() && !$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Parse props for curation
		if ($this->_task == 'saveitem'
			|| $this->_task == 'deleteitem'
			|| (!$block || !$blockId))
		{
			$block   	 = (isset($parts[0])) ? $parts[0] : 'content';
			$blockId    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
			$element 	 = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 0;
		}

		// Load publication
		$pub = new \Components\Publications\Models\Publication( $pid, $version );

		// Error loading publication record
		if (!$pub->exists() && $new == false)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'), 'error', 'projects');
			App::redirect(Route::url($pub->link('editbase')));
			return;
		}

		// Is this pub from this project?
		if (!$pub->belongsToProject($this->model->get('id')))
		{
			Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_PROJECT_ASSOC'), 'error', 'projects');
			App::redirect(Route::url($this->model->link('publications')));
			return;
		}

		// Get curation
		$pub->setCuration();

		Event::trigger('publications.onBeforeSave', $pub);

		// Make sure block exists, else redirect to status
		if (!$pub->_curationModel->setBlock( $block, $blockId ))
		{
			$block = 'status';
		}

		// Save incoming
		switch ($this->_task)
		{
			case 'additem':
				$pub->_curationModel->addItem($this->_uid, $element);
				break;

			case 'saveitem':
				$pub->_curationModel->saveItem($this->_uid, $element);
				break;

			case 'deleteitem':
				$pub->_curationModel->deleteItem($this->_uid, $element);
				break;

			case 'reorder':
				$pub->_curationModel->reorder($this->_uid, $element);
				$json = 1; // return result as json
				break;

			case 'dispute':
				$pub->_curationModel->dispute($this->_uid, $element);
				break;

			case 'skip':
				$pub->_curationModel->skip($this->_uid, $element);
				break;

			case 'undispute':
				$pub->_curationModel->undispute($this->_uid, $element);
				break;

			default:
				if ($this->_task != 'rewind')
				{
					$pub->_curationModel->saveBlock($this->_uid, $element);
				}
				break;
		}

		// Save new version label
		if ($block == 'status')
		{
			$pub->_curationModel->saveVersionLabel($this->_uid);
		}

		// Pick up error messages
		if ($pub->_curationModel->getError())
		{
			$this->setError($pub->_curationModel->getError());
		}

		// Pick up success message
		$this->_msg = $pub->_curationModel->get('_message')
			? $pub->_curationModel->get('_message')
			: Lang::txt(ucfirst($block) . ' information successfully saved');

		Event::trigger('publications.onAfterSave', $pub);

		// Record action, notify team
		$this->onAfterSave( $pub );

		// Report only status action
		if ($json)
		{
			return json_encode(
				array(
					'success' => 1,
					'error'   => $this->getError(),
					'message' => $this->_msg
				)
			);
		}

		// Go back to panel after changes to individual attachment
		if ($this->_task == 'saveitem' || $this->_task == 'deleteitem')
		{
			App::redirect($back);
			return;
		}

		// Get blockId & count
		$blockId = $pub->_curationModel->_blockorder;
		$total	 = $pub->_curationModel->_blockcount;

		// Get next element
		if ($next)
		{
			$next = $pub->_curationModel->getNextElement($block, $blockId, $element);
		}

		// What's next?
		$nextnum 	 = $pub->_curationModel->getNextBlock($block, $blockId);
		$nextsection = isset($pub->_curationModel->_blocks->$nextnum)
					 ? $pub->_curationModel->_blocks->$nextnum->name : 'status';

		// Get previous section
		$prevnum 	 = $pub->_curationModel->getPreviousBlock($block, $blockId);
		$prevsection = isset($pub->_curationModel->_blocks->$prevnum)
					 ? $pub->_curationModel->_blocks->$prevnum->name : 'status';

		// Build route
		$route  = $pub->link('edit');
		$route .= $move ? '&move=continue' : '';

		// Append version label
		$route .= $version != 'default' ? '&version=' . $version : '';

		// Determine which panel to go to
		if ($this->_task == 'apply' || !$move)
		{
			// Stay where you were
			$route .= '&section=' . $block;
			$route .= $block == 'status' ? '' : '&step=' . $blockId;

			if ($next)
			{
				if ($next == $element)
				{
					// Move to next block
					$route .= '&section=' . $nextsection;
					$route .= $nextnum ? '&step=' . $nextnum : '';
				}
				else
				{
					$route .= '&el=' . $next . '#element' . $next;
				}
			}
			elseif ($element)
			{
				$route .= '&el=' . $element . '#element' . $element;
			}
		}
		elseif ($this->_task == 'rewind')
		{
			// Go back one step
			$route .= '&section=' . $prevsection;
			$route .= $prevnum ? '&step=' . $prevnum : '';
		}
		else
		{
			// Move next
			$route .= '&section=' . $nextsection;
			$route .= $nextnum ? '&step=' . $nextnum : '';

			if ($next)
			{
				$route .= '&el=' . $next . '#element' . $next;
			}
		}

		// Redirect
		App::redirect(htmlspecialchars_decode(Route::url($route)));
		return;
	}

	/**
	 * Actions after publication draft is saved
	 *
	 * @return     string
	 */
	public function onAfterSave( $pub )
	{
		// No afterSave actions when backing one step
		if ($this->_task == 'rewind')
		{
			return false;
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Record activity
		if ($this->get('_activity'))
		{
			$pubTitle = \Hubzero\Utility\String::truncate($pub->title, 100);
			$aid = $this->model->recordActivity(
				   $this->get('_activity'), $pub->id, $pubTitle,
				   Route::url('index.php?option=' . $this->_option .
				   '&alias=' . $this->model->get('alias') . '&active=publications' .
				   '&pid=' . $pub->id . '&version=' . $pub->get('version_number')), 'publication', 1 );
		}
	}

	/**
	 * Actions after publication draft is started
	 *
	 * @return     string
	 */
	public function onAfterCreate($pub)
	{
		// Record activity
		if (!$this->model->isProvisioned() && $pub->exists() && !$this->getError())
		{
			$aid   = $this->model->recordActivity(
				   Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_STARTED_NEW_PUB')
					.' (id ' . $pub->get('id') . ')', $pub->get('id'), 'publication',
				   Route::url($pub->link('edit')), 'publication', 1 );
		}
	}

	/**
	 * Start a new publication draft
	 *
	 * @return     string
	 */
	public function startDraft()
	{
		// Get contributable types
		$mt = new \Components\Publications\Tables\MasterType( $this->_database );
		$choices = $mt->getTypes('*', 1, 0, 'ordering', $this->model->config());

		// Contribute process outside of projects
		if (!$this->model->exists())
		{
			$this->model->set('provisioned', 1);
		}

		// Check permission
		if ($this->model->exists() && !$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'draft',
				'layout'	=>'start'
			)
		);

		// Build pub url
		$view->route = $this->model->isProvisioned()
					? 'index.php?option=com_publications&task=submit'
					: $this->model->link('publications');

		// Do we have a choice?
		if (count($choices) <= 1 )
		{
			App::redirect(Route::url($view->route . '&action=edit'));
			return;
		}

		// Append breadcrumbs
		Pathway::append(
			stripslashes(Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')),
			Route::url($view->route . '&action=start')
		);

		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->model->config();
		$view->choices 		= $choices;
		$view->title		= $this->_area['title'];

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Provision a new publication draft
	 *
	 * @return     object
	 */
	public function createDraft()
	{
		// Incoming
		$base = Request::getVar( 'base', 'files' );

		// Check permission
		if ($this->model->exists() && !$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Load publication & version classes
		$objP = new \Components\Publications\Tables\Publication( $this->_database );
		$objC = new \Components\Publications\Tables\Category( $this->_database );
		$mt   = new \Components\Publications\Tables\MasterType( $this->_database );

		// Determine publication master type
		$choices  = $mt->getTypes('alias', 1);
		if (count($choices) == 1)
		{
			$base = $choices[0];
		}

		// Default to file type
		$mastertype = in_array($base, $choices) ? $base : 'files';

		// Need to provision a project
		if (!$this->model->exists())
		{
			$alias = 'pub-' . strtolower(\Components\Projects\Helpers\Html::generateCode(10, 10, 0, 1, 1));
			$this->model->set('provisioned', 1);
			$this->model->set('alias', $alias);
			$this->model->set('title', $alias);
			$this->model->set('type', 2); // publication
			$this->model->set('state', 1);
			$this->model->set('setup_stage', 3);
			$this->model->set('created', Date::toSql());
			$this->model->set('created_by_user', $this->_uid);
			$this->model->set('owned_by_user', $this->_uid);
			$this->model->set('params', $this->model->type()->params);

			// Save changes
			if (!$this->model->store())
			{
				throw new Exception($this->model->getError());
				return false;
			}
		}

		// Get type params
		$mType = $mt->getType($mastertype);

		// Make sure we got type info
		if (!$mType)
		{
			throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_LOAD_TYPE'));
			return false;
		}

		// Get curation model for the type
		$curationModel = new \Components\Publications\Models\Curation($mType->curation);

		// Get default category from manifest
		$cat = isset($curationModel->_manifest->params->default_category)
				? $curationModel->_manifest->params->default_category : 1;
		if (!$objC->load($cat))
		{
			$cat = 1;
		}

		// Get default title from manifest
		$title = isset($curationModel->_manifest->params->default_title)
				? $curationModel->_manifest->params->default_title : Lang::txt('Untitled Draft');

		// Make a new publication entry
		$objP->master_type 		= $mType->id;
		$objP->category 		= $cat;
		$objP->project_id 		= $this->model->get('id');
		$objP->created_by 		= $this->_uid;
		$objP->created 			= Date::toSql();
		$objP->access 			= 0;
		if (!$objP->store())
		{
			throw new Exception( $objP->getError() );
			return false;
		}
		if (!$objP->id)
		{
			$objP->checkin();
		}
		$this->_pid = $objP->id;

		// Initizalize Git repo and transfer files from member dir
		if ($this->model->isProvisioned())
		{
			if (!$this->_prepDir())
			{
				// Roll back
				$this->model->delete();
				$objP->delete();

				throw new Exception( Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_FAILED_INI_GIT_REPO') );
				return false;
			}
			else
			{
				// Add creator as project owner
				$objO = $this->model->table('Owner');
				if (!$objO->saveOwners ( $this->model->get('id'),
					$this->_uid, $this->_uid,
					0, 1, 1, 1 ))
				{
					throw new Exception( Lang::txt('COM_PROJECTS_ERROR_SAVING_AUTHORS') . ': ' . $objO->getError() );
					return false;
				}
			}
		}

		// Make a new dev version entry
		$row 					= new \Components\Publications\Tables\Version( $this->_database );
		$row->publication_id 	= $this->_pid;
		$row->title 			= $row->getDefaultTitle($this->model->get('id'), $title);
		$row->state 			= 3; // dev
		$row->main 				= 1;
		$row->created_by 		= $this->_uid;
		$row->created 			= Date::toSql();
		$row->version_number 	= 1;
		$row->license_type 		= 0;
		$row->access 			= 0;
		$row->secret 			= strtolower(\Components\Projects\Helpers\Html::generateCode(10, 10, 0, 1, 1));

		if (!$row->store())
		{
			// Roll back
			$objP->delete();

			throw new Exception( $row->getError(), 500 );
			return false;
		}
		if (!$row->id)
		{
			$row->checkin();
		}

		// Models\Publication
		$pub = new \Components\Publications\Models\Publication( $this->_pid, 'dev' );

		// Record action, notify team
		$this->onAfterCreate($pub);

		// Return publication object
		return $pub;
	}

	/**
	 * View/Edit publication draft
	 *
	 * @return     string
	 */
	public function editDraft()
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$version 	= Request::getVar( 'version', 'default' );
		$block  	= Request::getVar( 'section', 'status' );
		$blockId  	= Request::getInt( 'step', 0 );

		// Provision draft
		if (!$pid)
		{
			$pub = $this->createDraft();
			if (!$pub || !$pub->exists())
			{
				throw new Exception(Lang::txt('Error creating a publication draft'), 500);
				return;
			}

			// Get curation model
			$pub->setCuration();
			$blockId 	   = $pub->_curationModel->getFirstBlock();
			$firstBlock    = $pub->_curationModel->_blocks->$blockId->name;

			// Redirect to first block
			App::redirect(htmlspecialchars_decode(Route::url($pub->link('editversion')
				. '&move=continue&step=' . $blockId . '&section=' . $firstBlock)));
			return;
		}
		else
		{
			$pub = new \Components\Publications\Models\Publication( $pid, $version );
		}

		// If publication not found, raise error
		if (!$pub->exists() || $pub->isDeleted())
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'), 'error', 'projects');
			App::redirect(Route::url($pub->link('editbase')));
			return;
		}

		// Make sure the publication belongs to the project
		if (!$pub->belongsToProject($this->model->get('id')))
		{
			Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_PROJECT_ASSOC'), 'error', 'projects');
			App::redirect(Route::url($this->model->link('publications')));
			return;
		}

		// Get curation model
		$pub->setCuration();

		// For publications created in a non-curated flow - convert
		$pub->_curationModel->convertToCuration($pub);

		// Go to last incomplete section
		if ($this->_task == 'continue')
		{
			$blocks 	= $pub->curation('blocks');
			$blockId	= $pub->curation('firstBlock');
			$block		= $blockId ? $blocks->$blockId->name : 'status';
		}

		// Go to review screen
		if ($this->_task == 'review'
			|| ($this->_task == 'continue' && $pub->curation('complete') == 1)
		)
		{
			$blockId	= $pub->curation('lastBlock');
			$block		= 'review';
		}

		// Certain publications go to status page
		if ($pub->state == 5 || $pub->state == 0 || ($block == 'review' && $pub->state == 1))
		{
			$block = 'status';
			$blockId = 0;
		}

		// Make sure block exists, else redirect to status
		if (!$pub->_curationModel->setBlock( $block, $blockId ) || !$this->model->access('content'))
		{
			$block = 'status';
		}

		// Get requested block
		$name = $block == 'status' ? 'status' : 'draft';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=> $name,
			)
		);

		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project		= $this->model;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->model->config();
		$view->title		= $this->_area['title'];
		$view->active		= $block;
		$view->pub 			= $pub;
		$view->pubconfig 	= $this->_pubconfig;
		$view->task			= $this->_task;

		// Append breadcrumbs
		$this->_appendBreadcrumbs( $pub->get('title'), $pub->link('edit'), $version);

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Edit content item
	 *
	 * @return     string
	 */
	public function editItem()
	{
		// Incoming
		$id 	= Request::getInt( 'aid', 0 );
		$props  = Request::getVar( 'p', '' );

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0])) ? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 0;

		if ($this->_task == 'editauthor')
		{
			// Get author information
			$row 	= new \Components\Publications\Tables\Author( $this->_database );
			$error 	= Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_LOAD_AUTHOR');
			$layout = 'author';
		}
		else
		{
			// Load attachment
			$row 	= new \Components\Publications\Tables\Attachment( $this->_database );
			$error 	= Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT');
			$layout = 'attachment';
		}

		// We need attachment record
		if (!$id || !$row->load($id))
		{
			$this->setError($error);
		}

		// Load publication
		$pub = new \Components\Publications\Models\Publication(NULL, NULL, $row->publication_version_id);
		if (!$pub->exists())
		{
			$this->setError($error);
		}
		// Make sure the publication belongs to the project
		if (!$pub->belongsToProject($this->model->get('id')))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_PROJECT_ASSOC'));
		}

		// On error
		if ($this->getError())
		{
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'publications',
					'name'		=>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( $this->getError() );
			return $view->loadTemplate();
		}

		// Set curation
		$pub->setCuration();

		// On success
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> 'edititem',
				'layout'	=> $layout
			)
		);

		// Get project path
		if ($this->_task != 'editauthor')
		{
			$view->path = $pub->project()->repo()->get('path');
		}

		$view->step 	= $step;
		$view->block	= $block;
		$view->element  = $element;
		$view->database = $this->_database;
		$view->option 	= $this->_option;
		$view->project 	= $this->model;
		$view->pub		= $pub;
		$view->row		= $row;
		$view->backUrl	= Request::getVar('HTTP_REFERER', NULL, 'server');
		$view->ajax		= Request::getInt( 'ajax', 0 );
		$view->props	= $props;

		return $view->loadTemplate();
	}

	/**
	 *  Append breadcrumbs
	 *
	 * @return   void
	 */
	protected function _appendBreadcrumbs( $title, $url, $version = 'default')
	{
		// Append breadcrumbs
		$url = $version != 'default' ? $url . '&version=' . $version : $url;
		Pathway::append(
			stripslashes($title),
			$url
		);
	}

	/**
	 *  Publication stats
	 *
	 * @return     string
	 */
	protected function _stats()
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$version 	= Request::getVar( 'version', 'default' );

		require_once( PATH_CORE . DS . 'components'. DS
				.'com_publications' . DS . 'tables' . DS . 'logs.php');

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'publications',
				'name'    =>'stats'
			)
		);

		// Get pub stats for each publication
		$pubLog = new \Components\Publications\Tables\Log($this->_database);
		$view->pubstats = $pubLog->getPubStats($this->model->get('id'), $pid);

		// Get date of first log
		$view->firstlog = $pubLog->getFirstLogDate();

		// Test
		$view->totals = $pubLog->getTotals($this->model->get('id'), 'project');

		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->pid 			= $pid;
		$view->pub			= new \Components\Publications\Models\Publication( $pid, $version );
		$view->task 		= $this->_task;
		$view->config 		= $this->model->config();
		$view->pubconfig 	= $this->_pubconfig;
		$view->version 		= $version;
		$view->title		= $this->_area['title'];

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Suggest license
	 *
	 * @return     string
	 */
	protected function _suggestLicense()
	{
		// Incoming
		$pid  		= $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$version 	= Request::getVar( 'version', 'default' );
		$ajax 		= Request::getInt('ajax', 0);

		// Load publication
		$pub = new \Components\Publications\Models\Publication($pid, $version);
		if (!$pub->exists())
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			$this->_task = '';
			return $this->browse();
		}

		// File a support ticket
		if ($this->_task == 'save_license')
		{
			$l_title 	= htmlentities(Request::getVar('license_title', '', 'post'));
			$l_url 		= htmlentities(Request::getVar('license_url', '', 'post'));
			$l_text 	= htmlentities(Request::getVar('details', '', 'post'));

			if (!$l_title && !$l_url && !$l_text)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_ERROR'));
			}
			else
			{
				// Include support scripts
				include_once( PATH_CORE . DS . 'components'
					. DS . 'com_support' . DS . 'tables' . DS . 'ticket.php' );
				include_once( PATH_CORE . DS . 'components'
					. DS . 'com_support' . DS . 'tables' . DS . 'comment.php' );

				// Load the support config
				$sparams = Component::params('com_support');

				$row = new \Components\Support\Tables\Ticket( $this->_database );
				$row->created = Date::toSql();
				$row->login   = User::get('username');
				$row->email   = User::get('email');
				$row->name    = User::get('name');
				$row->summary = Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_NEW');

				$report 	  = Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_TITLE') . ': ' . $l_title ."\r\n";
				$report 	 .= Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_URL') . ': ' . $l_url . "\r\n";
				$report 	 .= Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_COMMENTS') . ': ' . $l_text ."\r\n";
				$row->report 	= $report;
				$row->referrer 	= Request::getVar('HTTP_REFERER', NULL, 'server');
				$row->type	 	= 0;
				$row->severity	= 'normal';

				$admingroup = $this->model->config()->get('admingroup', '');
				$group = \Hubzero\User\Group::getInstance($admingroup);
				$row->group = $group ? $group->get('cn') : '';

				if (!$row->store())
				{
					$this->setError($row->getError());
				}
				else
				{
					$ticketid = $row->id;

					// Notify project admins
					$message  = $row->name . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTED')."\r\n";
					$message .= '----------------------------'."\r\n";
					$message .=	$report;
					$message .= '----------------------------'."\r\n";

					if ($ticketid)
					{
						$message .= Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_TICKET_PATH') ."\n";
						$message .= Request::base() . 'support/ticket/' . $ticketid . "\n\n";
					}

					if ($group)
					{
						$members 	= $group->get('members');
						$managers 	= $group->get('managers');
						$admins 	= array_merge($members, $managers);
						$admins 	= array_unique($admins);

						// Send out email to admins
						if (!empty($admins))
						{
							\Components\Projects\Helpers\Html::sendHUBMessage(
								$this->_option,
								$this->model,
								$admins,
								Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_NEW'),
								'projects_new_project_admin',
								'admin',
								$message
							);
						}
					}

					$this->_msg = Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_SENT');
				}
			}
		}
		else
		{
			 $view = new \Hubzero\Plugin\View(
				array(
					'folder' =>'projects',
					'element'=>'publications',
					'name'   =>'suggestlicense'
				)
			);

			// Output HTML
			$view->option 		= $this->_option;
			$view->database 	= $this->_database;
			$view->project 		= $this->model;
			$view->uid 			= $this->_uid;
			$view->pid 			= $pid;
			$view->pub 			= $pub;
			$view->task 		= $this->_task;
			$view->config 		= $this->model->config();
			$view->pubconfig 	= $this->_pubconfig;
			$view->ajax 		= $ajax;
			$view->version 		= $version;
			$view->title		= $this->_area['title'];

			// Get messages	and errors
			$view->msg = $this->_msg;
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($pub->link('editversion') . '&section=license'));
		return;
	}

	/**
	 * Start/save a new version
	 *
	 * @return     string
	 */
	public function newVersion()
	{
		// Incoming
		$pid   = $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$ajax  = Request::getInt('ajax', 0);
		$label = trim(Request::getVar( 'version_label', '', 'post' ));

		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Load default version
		$pub = new \Components\Publications\Models\Publication( $pid, 'default' );
		if (!$pub->exists())
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			$this->_task = '';
			return $this->browse();
		}

		// Make sure the publication belongs to the project
		if (!$pub->belongsToProject($this->model->get('id')))
		{
			Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_PROJECT_ASSOC'), 'error', 'projects');
			App::redirect(Route::url($this->model->link('publications')));
			return;
		}

		// Set curation model
		$pub->setCuration();

		// Check if dev version is already there
		if ($pub->version->checkVersion($pid, 'dev'))
		{
			// Redirect
			App::redirect(Route::url($pub->link('editdev')));
			return;
		}

		// Can't start a new version if there is a finalized or submitted draft
		if ($pub->version->get('state') == 4
			|| $pub->version->get('state') == 5
			|| $pub->version->get('state') == 7
		)
		{
			// Determine redirect path
			App::redirect(Route::url($pub->link('editdefault')));
			return;
		}

		// Saving new version
		if ($this->_task == 'savenew')
		{
			$used_labels = $pub->version->getUsedLabels( $pid, 'dev');
			if (!$label)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_NONE') );
			}
			elseif ($label && in_array($label, $used_labels))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_USED') );
			}
			else
			{
				// Create new version
				$new 				=  new \Components\Publications\Tables\Version( $this->_database );
				// Copy from previous version
				$new->publication_id = $pub->get('publication_id');
				$new->title          = $pub->get('title');
				$new->abstract       = $pub->get('abstract');
				$new->description    = $pub->get('description');
				$new->metadata       = $pub->get('metadata');
				$new->release_notes  = $pub->get('release_notes');
				$new->license_type   = $pub->get('license_type');
				$new->license_text   = $pub->get('license_text');
				$new->access         = $pub->get('access');

				$new->created 		 = Date::toSql();
				$new->created_by 	 = $this->_uid;
				$new->modified 		 = Date::toSql();
				$new->modified_by 	 = $this->_uid;
				$new->state 		 = 3;
				$new->main 			 = 0;
				$new->version_label  = $label;
				$new->version_number = $pub->versionCount() + 1;
				$new->secret 		 = strtolower(\Components\Projects\Helpers\Html::generateCode(10, 10, 0, 1, 1));

				if ($new->store())
				{
					// Transfer data
					$pub->_curationModel->transfer($pub, $pub->version, $new);

					// Set response message
					$this->set('_msg', Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NEW_VERSION_STARTED'));

					// Set activity message
					$pubTitle = \Hubzero\Utility\String::truncate($new->title, 100);
					$action   = Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_STARTED_VERSION')
								. ' ' . $new->version_label . ' ';
					$action .=  Lang::txt('PLG_PROJECTS_PUBLICATIONS_OF_PUBLICATION') . ' "' . $pubTitle . '"';
					$this->set('_activity', $action);

					// Record action, notify team
					$pub->set('version_number', $new->version_number);
					$pub->set('version_label', $new->version_label);
					$this->onAfterSave( $pub );
				}
				else
				{
					$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ERROR_SAVING_NEW_VERSION'));
				}
			}
		}
		// Need to ask for new version label
		else
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'publications',
					'name'    =>'newversion'
				)
			);

			// Output HTML
			$view->option 		= $this->_option;
			$view->database 	= $this->_database;
			$view->project 		= $this->model;
			$view->uid 			= $this->_uid;
			$view->pid 			= $pid;
			$view->pub 			= $pub;
			$view->task 		= $this->_task;
			$view->config 		= $this->model->config();
			$view->pubconfig 	= $this->_pubconfig;
			$view->ajax 		= $ajax;
			$view->title		= $this->_area['title'];

			// Get messages	and errors
			$view->msg = $this->_msg;
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}

		// Pass success or error message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($pub->link('editdev')));
		return;
	}

	/**
	 * Check if there is available space for publishing
	 *
	 * @return     string
	 */
	protected function _overQuota()
	{
		// Instantiate project publication
		$objP = new \Components\Publications\Tables\Publication( $this->_database );

		// Get all publications
		$rows = $objP->getRecords(array('project' => $this->model->get('id'), 'dev' => 1, 'ignore_access' => 1));

		// Get used space
		$dirsize = \Components\Publications\Helpers\Html::getDiskUsage($rows, false);
		$quota   = $this->model->params->get('pubQuota')
				? $this->model->params->get('pubQuota')
						: \Components\Projects\Helpers\Html::convertSize(floatval($this->model->config()->get('pubQuota', '1')), 'GB', 'b');

		if (($quota - $dirsize) <= 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Change publication status
	 *
	 * @return     string
	 */
	public function publishDraft()
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$confirm 	= Request::getInt('confirm', 0);
		$version 	= Request::getVar('version', 'dev');
		$agree   	= Request::getInt('agree', 0);
		$pubdate 	= Request::getVar('publish_date', '', 'post');
		$submitter 	= Request::getInt('submitter', $this->_uid, 'post');
		$notify 	= 1;

		$block  	= Request::getVar( 'section', '' );
		$blockId  	= Request::getInt( 'step', 0 );
		$element  	= Request::getInt( 'element', 0 );

		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Load review step
		if (!$confirm && $this->_task != 'revert')
		{
			$this->_task = 'review';
			return $this->editDraft();
		}

		// Load publication model
		$pub  = new \Components\Publications\Models\Publication($pid, $version);

		// Error loading publication record
		if (!$pub->exists())
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'), 'error', 'projects');
			App::redirect(Route::url($pub->link('editbase')));
			return;
		}

		// Agreement to terms is required
		if ($confirm && !$agree)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_REVIEW_AGREE_TERMS_REQUIRED'), 'error', 'projects');
			App::redirect(Route::url($pub->link('editversion') . '&action=' . $this->_task));
			return;
		}

		// Check against quota
		if ($this->_overQuota())
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NO_DISK_SPACE'), 'error', 'projects');
			App::redirect(Route::url($pub->link('editversion') . '&action=' . $this->_task));
			return;
		}

		// Set curation
		$pub->setCuration();

		// Require DOI?
		$requireDoi = isset($pub->_curationModel->_manifest->params->require_doi)
					? $pub->_curationModel->_manifest->params->require_doi : 0;

		// Make sure the publication belongs to the project
		if (!$pub->belongsToProject($this->model->get('id')))
		{
			Notify::message(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_PROJECT_ASSOC'), 'error', 'projects');
			App::redirect(Route::url($this->model->link('publications')));
			return;
		}

		// Check that version label was not published before
		$used_labels = $pub->version->getUsedLabels( $pid, $version );
		if (!$pub->version->version_label || in_array($pub->version->version_label, $used_labels))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_USED') );
		}

		// Is draft complete?
		if (!$pub->curation('complete') && $this->_task != 'revert')
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_ALLOWED') );
		}

		// Is revert allowed?
		$revertAllowed = $this->_pubconfig->get('graceperiod', 0);
		if ($revertAllowed && $pub->version->state == 1
			&& $pub->version->accepted && $pub->version->accepted != '0000-00-00 00:00:00')
		{
			$monthFrom = Date::of($pub->version->accepted . '+1 month')->toSql();
			if (strtotime($monthFrom) < strtotime(Date::of()))
			{
				$revertAllowed = 0;
			}
		}

		// Embargo?
		if ($pubdate)
		{
			$pubdate = $this->_parseDate($pubdate);

			$tenYearsFromNow = Date::of(strtotime("+10 years"))->toSql();

			// Stop if more than 10 years from now
			if ($pubdate > $tenYearsFromNow)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ERROR_EMBARGO') );
			}
		}

		// Contact info is required for repositories
		if ($pub->config()->get('repository'))
		{
			$contact = Request::getVar('contact', array(), 'post');

			if (!$contact || empty($contact))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ERROR_CONTACT_INFO_MISSING'));
			}

			$db = \App::get('db');

			foreach ($contact as $key)
			{
				$author = new \Components\Publications\Tables\Author($db);
				if (!$author->load($key))
				{
					$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ERROR_CONTACT_NOT_FOUND'));
					continue;
				}

				$author->repository_contact = 1;

				if (!$author->store())
				{
					$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ERROR_CONTACT_NOT_SAVED'));
				}
			}
		}

		// Main version?
		$main = $this->_task == 'republish' ? $pub->version->main : 1;
		$main_vid = $pub->version->getMainVersionId($pid); // current default version

		// Save version before changes
		$originalStatus = $pub->version->state;

		// Checks
		if ($this->_task == 'republish' && $pub->version->state != 0)
		{
			// Can only re-publish unpublished version
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_CANNOT_REPUBLISH') );
		}
		elseif ($this->_task == 'revert' && $pub->version->state != 5 && !$revertAllowed)
		{
			// Can only revert a pending resource
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_CANNOT_REVERT') );
		}

		// On error
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
			App::redirect(Route::url($pub->link('editversion') . '&action=' . $this->_task));
			return;
		}

		// Determine state
		$state = 5; // Default - pending approval
		if ($this->_task == 'share' || $this->_task == 'revert')
		{
			$state = 4; // No approval needed
		}
		elseif ($this->_task == 'republish')
		{
			$state = 1; // No approval needed
		}
		else
		{
			$pub->version->set('submitted', Date::toSql());

			// Save submitter
			$pa = new \Components\Publications\Tables\Author( $this->_database );
			$pa->saveSubmitter($pub->version->id, $submitter, $this->model->get('id'));

			if ($this->_pubconfig->get('autoapprove') == 1 )
			{
				$state = 1;
			}
			else
			{
				$apu = $this->_pubconfig->get('autoapproved_users');
				$apu = explode(',', $apu);
				$apu = array_map('trim',$apu);

				if (in_array(User::get('username'),$apu))
				{
					// Set status to published
					$state = 1;
				}
				else
				{
					// Set status to pending review (submitted)
					$state = 5;
				}
			}
		}

		// Save state
		$pub->version->set('state', $state);
		$pub->version->set('main', $main);

		if ($this->_task != 'revert')
		{
			$publishedUp = $this->_task == 'republish'
				? $pub->version->published_up : Date::toSql();
			$publishedUp = $pubdate ? $pubdate : $publishedUp;

			$pub->version->set('rating', '0.0');
			$pub->version->set('published_up', $publishedUp);
			$pub->version->set('published_down', '');
		}
		$pub->version->set('modified', Date::toSql());
		$pub->version->set('modified_by', $this->_uid);

		// Issue DOI
		if ($requireDoi > 0 && $this->_task == 'publish' && !$pub->version->doi)
		{
			// Get DOI service
			$doiService = new \Components\Publications\Models\Doi($pub);
			$extended = $state == 5 ? false : true;
			$doi = $doiService->register($extended, ($state == 5 ? 'reserved' : 'public'));

			// Store DOI
			if ($doi)
			{
				$pub->version->set('doi', $doi);
			}

			// Can't proceed without a valid DOI
			if (!$doi || $doiService->getError())
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_DOI') . ' ' . $doiService->getError());
			}
		}

		// Proceed if no error
		if (!$this->getError())
		{
			if ($state == 1)
			{
				// Get and save manifest and its version
				$versionNumber = $pub->_curationModel->checkCurationVersion();
				$pub->version->set('curation', json_encode($pub->_curationModel->_manifest));
				$pub->version->set('curation_version_id', $versionNumber);
			}

			// Save data
			if (!$pub->version->store())
			{
				throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED'), 403);
				return;
			}

			// Remove main flag from previous default version
			if ($main && $main_vid && $main_vid != $pub->version->get('id'))
			{
				$pub->version->removeMainFlag($main_vid);
			}
		}

		// OnAfterPublish
		$this->onAfterChangeState( $pub, $originalStatus );

		// Redirect
		App::redirect(Route::url($pub->link('editversion')));
		return;
	}

	/**
	 * On after change status
	 *
	 * @return     string
	 */
	public function onAfterChangeState( $pub, $originalStatus = 3 )
	{
		$notify = 1; // Notify administrators/curators?

		// Log activity in curation history
		if (isset($pub->_curationModel))
		{
			$pub->_curationModel->saveHistory($this->_uid, $originalStatus, $pub->version->get('state'), 0 );
		}

		// Display status message
		switch ($pub->version->get('state'))
		{
			case 1:
			default:
				$this->_msg = Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_PUBLISHED');
				$action 	= $this->_task == 'republish'
							? Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_REPUBLISHED')
							: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_PUBLISHED');
				break;

			case 4:
				$this->_msg = $this->_task == 'revert'
							? Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_REVERTED')
							: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_SAVED') ;
				$action 	= $this->_task == 'revert'
							? Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_REVERTED')
							: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_SAVED');
				$notify = 0;
				break;

			case 5:
				$this->_msg = $originalStatus == 7
							? Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_PENDING_RESUBMITTED')
							: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_PENDING');
				$action 	= $originalStatus == 7
							? Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_RESUBMITTED')
							: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_SUBMITTED');
				break;
		}
		$this->_msg .= ' <a href="' . Route::url($pub->link('version')) . '">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VIEWIT') . '</a>';

		$pubtitle = \Hubzero\Utility\String::truncate($pub->version->get('title'), 100);
		$action  .= ' ' . $pub->version->get('version_label') . ' ';
		$action  .=  Lang::txt('PLG_PROJECTS_PUBLICATIONS_OF_PUBLICATION')
					. ' "' . html_entity_decode($pubtitle).'"';
		$action   = htmlentities($action, ENT_QUOTES, "UTF-8");

		// Record activity
		if (!$this->model->isProvisioned() && !$this->getError())
		{
			$aid = $this->model->recordActivity(
					$action, $pub->id, $pubtitle,
					Route::url($pub->link('editversion')), 'publication', 1 );
		}

		// Send out notifications
		$profile = User::getInstance($this->_uid);
		$actor  = $profile
				? $profile->get('name')
				: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PROJECT_MEMBER');
		$sef	 = Route::url($pub->link('version'));
		$link 	 = rtrim(Request::base(), DS) . DS . trim($sef, DS);
		$message = $actor . ' ' . html_entity_decode($action) . '  - ' . $link;

		// Notify admin group
		if ($notify)
		{
			$admingroup = $this->model->config()->get('admingroup', '');
			$group = \Hubzero\User\Group::getInstance($admingroup);
			$admins = array();

			if ($admingroup && $group)
			{
				$members 	= $group->get('members');
				$managers 	= $group->get('managers');
				$admins 	= array_merge($members, $managers);
				$admins 	= array_unique($admins);

				\Components\Projects\Helpers\Html::sendHUBMessage(
					'com_projects',
					$this->model,
					$admins,
					Lang::txt('COM_PROJECTS_EMAIL_ADMIN_NEW_PUB_STATUS'),
					'projects_new_project_admin',
					'publication',
					$message
				);
			}

			// Notify curators by email
			$curatorMessage = ($pub->version->get('state') == 5) ? $message . "\n" . "\n" . Lang::txt('PLG_PROJECTS_PUBLICATIONS_EMAIL_CURATORS_REVIEW') . ' ' . rtrim(Request::base(), DS) . DS . 'publications/curation' : $message;

			$curatorgroups = array($pub->masterType()->curatorgroup);
			if ($this->_pubconfig->get('curatorgroup', ''))
			{
				$curatorgroups[] = $this->_pubconfig->get('curatorgroup', '');
			}
			$admins = array();
			foreach ($curatorgroups as $curatorgroup)
			{
				if (trim($curatorgroup) && $group = \Hubzero\User\Group::getInstance($curatorgroup))
				{
					$members 	= $group->get('members');
					$managers 	= $group->get('managers');
					$admins 	= array_merge($members, $managers, $admins);
					$admins 	= array_unique($admins);
				}
			}
			\Components\Publications\Helpers\Html::notify(
				$pub,
				$admins,
				Lang::txt('PLG_PROJECTS_PUBLICATIONS_EMAIL_CURATORS'),
				$curatorMessage
			);
		}

		// Notify project managers (in all cases)
		$objO = $this->model->table('Owner');
		$managers = $objO->getIds($this->model->get('id'), 1, 1);
		if (!$this->model->isProvisioned() && !empty($managers))
		{
			\Components\Projects\Helpers\Html::sendHUBMessage(
				'com_projects',
				$this->model,
				$managers,
				Lang::txt('COM_PROJECTS_EMAIL_MANAGERS_NEW_PUB_STATUS'),
				'projects_admin_notice',
				'publication',
				$message
			);
		}

		// Produce archival package
		if ($pub->version->get('state') == 1 || $pub->version->get('state') == 5)
		{
			$pub->_curationModel->package();
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		return;
	}

	/**
	 * Parse embargo date
	 *
	 * @return     string
	 */
	private function _parseDate( $pubdate )
	{
		$date = explode('-', $pubdate);
		if (count($date) == 3)
		{
			$year 	= $date[0];
			$month 	= $date[1];
			$day 	= $date[2];
			if (intval($month) && intval($day) && intval($year))
			{
				if (strlen($day) == 1)
				{
					$day = '0' . $day;
				}

				if (strlen($month) == 1)
				{
					$month = '0' . $month;
				}
				if (checkdate($month, $day, $year))
				{
					$pubdate = Date::of(gmmktime(0, 0, 0, $month, $day, $year))->toSql();
				}
				// Prevent date before current
				if ($pubdate < Date::toSql())
				{
					$pubdate = Date::toSql();
				}
			}
		}

		return $pubdate;
	}

	/**
	 * Unpublish version/delete draft
	 *
	 * @return     string
	 */
	public function cancelDraft()
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : Request::getInt('pid', 0);
		$confirm 	= Request::getInt('confirm', 0);
		$version 	= Request::getVar('version', 'default');
		$ajax 		= Request::getInt('ajax', 0);

		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Load publication model
		$pub  = new \Components\Publications\Models\Publication( $pid, $version);

		if (!$pub->exists() || !$pub->belongsToProject($this->model->get('id')))
		{
			throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND'), 404);
			return;
		}

		// Save version ID
		$vid = $pub->version->get('id');

		// Append breadcrumbs
		if (!$ajax)
		{
			Pathway::append(
				stripslashes($pub->version->get('title')),
				$pub->link('edit')
			);
		}

		$baseUrl  = Route::url($pub->link('editbase'));
		$baseEdit = Route::url($pub->link('edit'));

		// Can only unpublish published version or delete a draft
		if ($pub->version->get('state') != 1
			&& $pub->version->get('state') != 3
			&& $pub->version->get('state') != 4
		)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CANT_DELETE'));
		}

		// Unpublish/delete version
		if ($confirm)
		{
			if (!$this->getError())
			{
				$pubtitle = \Hubzero\Utility\String::truncate($pub->version->get('title'), 100);

				if ($pub->version->get('state') == 1)
				{
					// Unpublish published version
					$pub->version->set('published_down', Date::toSql());
					$pub->version->set('modified', Date::toSql());
					$pub->version->set('modified_by', $this->_uid);
					$pub->version->set('state', 0);

					if (!$pub->version->store())
					{
						throw new Exception( Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNPUBLISH_FAILED'), 403);
						return;
					}
					else
					{
						$this->_msg = Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_UNPUBLISHED');

						// Add activity
						$action  = Lang::txt('PLG_PROJECTS_PUBLICATIONS_ACTIVITY_UNPUBLISHED');
						$action .= ' '.strtolower(Lang::txt('version'))
								. ' ' . $pub->version->get('version_label') . ' '
								. Lang::txt('PLG_PROJECTS_PUBLICATIONS_OF')
								. ' ' . strtolower(Lang::txt('publication')) . ' "'
								. $pubtitle . '" ';

						$aid = $this->model->recordActivity(
							   $action, $pid, $pubtitle,
							   Route::url($pub->link('editversion')), 'publication', 0 );
					}
				}
				elseif ($pub->version->get('state') == 3 || $pub->version->get('state') == 4)
				{
					$vlabel = $pub->version->get('version_label');

					// Delete draft version
					if (!$pub->version->delete())
					{
						throw new Exception( Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DELETE_DRAFT_FAILED'), 403);
						return;
					}

					// Delete authors
					$pa = new \Components\Publications\Tables\Author( $this->_database );
					$authors = $pa->deleteAssociations($vid);

					// Delete attachments
					$pContent = new \Components\Publications\Tables\Attachment( $this->_database );
					$pContent->deleteAttachments($vid);

					// Delete screenshots
					$pScreenshot = new \Components\Publications\Tables\Screenshot( $this->_database );
					$pScreenshot->deleteScreenshots($vid);

					// Build publication path
					$path =  PATH_APP . DS . trim($this->_pubconfig->get('webpath'), DS)
							. DS .  \Hubzero\Utility\String::pad( $pid );

					// Build version path
					$vPath = $path . DS . \Hubzero\Utility\String::pad( $vid );

					// Delete all version files
					if (is_dir($vPath))
					{
						Filesystem::deleteDirectory($vPath);
					}

					// Delete access accosiations
					$pAccess = new \Components\Publications\Tables\Access( $this->_database );
					$pAccess->deleteGroups($vid);

					// Delete audience
					$pAudience = new \Components\Publications\Tables\Audience( $this->_database );
					$pAudience->deleteAudience($vid);

					// Delete publication existence
					if ($pub->versionCount() == 0)
					{
						// Delete all files
						if (is_dir($path))
						{
							Filesystem::delete($path);
						}

						$pub->publication->delete($pid);
						$pub->publication->deleteExistence($pid);

						// Delete related publishing activity from feed
						$objAA = $this->model->table('Activity');
						$objAA->deleteActivityByReference($this->model->get('id'), $pid, 'publication');
					}

					// Add activity
					$action  = Lang::txt('PLG_PROJECTS_PUBLICATIONS_ACTIVITY_DRAFT_DELETED');
					$action .= ' ' . $vlabel . ' ';
					$action .=  Lang::txt('PLG_PROJECTS_PUBLICATIONS_OF_PUBLICATION') . ' "' . $pubtitle . '"';

					$aid = $this->model->recordActivity($action, $pid, '', '', 'publication', 0 );

					$this->_msg = Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DRAFT_DELETED');
				}
			}
		}
		else
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'publications',
					'name'    =>'cancel'
				)
			);

			// Output HTML
			$view->option 			= $this->_option;
			$view->database 		= $this->_database;
			$view->project 			= $this->model;
			$view->uid 				= $this->_uid;
			$view->pid 				= $pid;
			$view->pub 				= $pub;
			$view->publishedCount 	= $pub->version->getPublishedCount($pid);
			$view->task 			= $this->_task;
			$view->config 			= $this->model->config();
			$view->pubconfig 		= $this->_pubconfig;
			$view->ajax 			= $ajax;
			$view->title		  	= $this->_area['title'];

			// Get messages	and errors
			$view->msg = $this->_msg;
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		App::redirect($baseUrl);
		return;
	}

	/**
	 * Show publication versions
	 *
	 * @return     string (html)
	 */
	public function versions()
	{
		// Incoming
		$pid = $this->_pid ? $this->_pid : Request::getInt('pid', 0);

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder' =>'projects',
				'element'=>'publications',
				'name'   =>'versions'
			)
		);

		// Load publication model
		$view->pub  = new \Components\Publications\Models\Publication($pid, 'default');

		if (!$view->pub->exists() || !$view->pub->belongsToProject($this->model->get('id')))
		{
			throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND'), 404);
			return;
		}

		// Append breadcrumbs
		Pathway::append(
			stripslashes($view->pub->title),
			$view->pub->link('edit')
		);

		// Get versions
		$view->versions = $view->pub->version->getVersions( $pid, $filters = array('withdev' => 1));

		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->pid 			= $pid;
		$view->task 		= $this->_task;
		$view->config 		= $this->model->config();
		$view->pubconfig 	= $this->_pubconfig;
		$view->title		= $this->_area['title'];

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Contribute from outside projects
	 *
	 * @return     string (html)
	 */
	public function contribute()
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'publications',
				'name'    =>'browse',
				'layout'  =>'provisioned'
			)
		);
		$view->option   = $this->_option;
		$view->juser    = User::getInstance();
		$view->database = $this->_database;

		// Instantiate a publication object
		$view->pub     = new \Components\Publications\Models\Publication();
		$view->project = $this->model;

		return $view->loadTemplate();
	}

	/**
	 * Get member path
	 *
	 * @return     string
	 */
	protected function _getMemberPath()
	{
		// Get members config
		$mconfig = Component::params( 'com_members' );

		// Build upload path
		$dir  = \Hubzero\Utility\String::pad( $this->_uid );
		$path = DS . trim($mconfig->get('webpath', '/site/members'), DS) . DS . $dir . DS . 'files';

		if (!is_dir( PATH_APP . $path ))
		{
			if (!Filesystem::makeDirectory( PATH_APP . $path ))
			{
				$this->setError(\Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		return PATH_APP . $path;
	}

	/**
	 * Prep file directory (provisioned project)
	 *
	 * @param      boolean		$force
	 *
	 * @return     boolean
	 */
	protected function _prepDir($force = true)
	{
		if (!$this->model->exists())
		{
			$this->setError( Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH') );
			return;
		}

		// Get member files path
		$memberPath = $this->_getMemberPath();

		// Create and initialize local repo
		if (!$this->model->repo()->iniLocal())
		{
			$this->setError( Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH') );
			return;
		}

		// Copy files from member directory
		if (!Filesystem::copyDirectory($memberPath, $this->model->repo()->get('path')))
		{
			$this->setError( Lang::txt('COM_PROJECTS_FAILED_TO_COPY_FILES') );
			return false;
		}

		// Read copied files
		$get = Filesystem::files($this->model->repo()->get('path'));

		$num = count($get);
		$checkedin = 0;

		// Check-in copied files
		if ($get)
		{
			foreach ($get as $file)
			{
				$file = str_replace($this->model->repo()->get('path') . DS, '', $file);
				if (is_file($this->model->repo()->get('path') . DS . $file))
				{
					// Checkin into repo
					$this->model->repo()->call('checkin', array(
						'file' => $this->model->repo()->getMetadata($file, 'file', array())
						)
					);
					$checkedin++;
				}
			}
		}
		if ($num == $checkedin)
		{
			// Clean up member files
			Filesystem::deleteDirectory($memberPath);
			return true;
		}

		return false;
	}

	/**
	 * Get disk space
	 *
	 * @param      object  	$model
	 *
	 * @return     string
	 */
	public function pubDiskSpace($model)
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'publications',
				'name'    =>'diskspace'
			)
		);

		// Include styling and js
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files', 'diskspace');
		\Hubzero\Document\Assets::addPluginScript('projects', 'files', 'diskspace');

		$database = App::get('db');

		// Build query
		$filters = array();
		$filters['limit'] 	 		= Request::getInt('limit', 25);
		$filters['start'] 	 		= Request::getInt('limitstart', 0);
		$filters['sortby']   		= Request::getVar( 't_sortby', 'title');
		$filters['sortdir']  		= Request::getVar( 't_sortdir', 'ASC');
		$filters['project']  		= $model->get('id');
		$filters['ignore_access']   = 1;
		$filters['dev']   	 		= 1; // get dev versions

		// Instantiate project publication
		$objP = new \Components\Publications\Tables\Publication( $database );

		// Get all publications
		$view->rows = $objP->getRecords($filters);

		// Get used space
		$view->dirsize = \Components\Publications\Helpers\Html::getDiskUsage($view->rows, false);
		$view->params  = $model->params;
		$view->quota   = $view->params->get('pubQuota')
						? $view->params->get('pubQuota')
						: \Components\Projects\Helpers\Html::convertSize(floatval($model->config()->get('pubQuota', '1')), 'GB', 'b');

		// Get total count
		$view->total = $objP->getCount($filters);

		$view->project 	= $model;
		$view->option 	= $this->_option;
		$view->title	= isset($this->_area['title']) ? $this->_area['title'] : '';

		return $view->loadTemplate();
	}

	/**
	 * Serve publication-related file (via public link)
	 *
	 * @param   int  	$projectid
	 * @return  void
	 */
	public function serve( $type = '', $projectid = 0, $query = '')
	{
		$this->_area = $this->onProjectAreas();
		if ($type != $this->_area['name'])
		{
			return false;
		}
		$data = json_decode($query);

		if (!isset($data->pid) || !$projectid)
		{
			return false;
		}

		$disp 	 = isset($data->disp) ? $data->disp : 'inline';
		$type 	 = isset($data->type) ? $data->type : 'file';
		$folder  = isset($data->folder) ? $data->folder : 'wikicontent';
		$fpath	 = isset($data->path) ? $data->path : 'inline';
		$limited = isset($data->limited) ? $data->limited : 0;

		if ($type != 'file')
		{
			return false;
		}

		$database = App::get('db');

		// Instantiate a project
		$model = new \Components\Projects\Models\Project($projectid);

		if (!$model->exists() || ($limited == 1 && !$model->access('member')))
		{
			// Throw error
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_ACTION_NOT_AUTHORIZED'), 403);
			return;
		}

		// Get referenced path
		$pubconfig = Component::params( 'com_publications' );
		$base_path = $pubconfig->get('webpath');
		$pubPath = \Components\Publications\Helpers\Html::buildPubPath($data->pid, $data->vid, $base_path, $folder, $root = 0);

		$serve = PATH_APP . $pubPath . DS . $fpath;

		// Ensure the file exist
		if (!file_exists($serve))
		{
			// Throw error
			throw new Exception(Lang::txt('COM_PROJECTS_FILE_NOT_FOUND'), 404);
			return;
		}

		// Initiate a new content server and serve up the file
		$server = new \Hubzero\Content\Server();
		$server->filename($serve);
		$server->disposition($disp);
		$server->acceptranges(false); // @TODO fix byte range support
		$server->saveas(basename($fpath));

		if (!$server->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_PUBLICATIONS_SERVER_ERROR'), 404);
		}
		else
		{
			exit;
		}

		return;
	}
}
