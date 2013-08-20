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

ximport('Hubzero_Controller');

/**
 * Resources controller class
 */
class ResourcesControllerResources extends Hubzero_Controller
{
	/**
	 * Constructor
	 * 
	 * @param      array $config Optional configurations
	 * @return     void
	 */
	public function __construct($config=array())
	{
		$this->_base_path = JPATH_ROOT . DS . 'components' . DS . 'com_resources';
		if (isset($config['base_path'])) 
		{
			$this->_base_path = $config['base_path'];
		}

		$this->_sub = false;
		if (isset($config['sub'])) 
		{
			$this->_sub = $config['sub'];
		}

		$this->_group = false;
		if (isset($config['group'])) 
		{
			$this->_group = $config['group'];
		}

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_task  = JRequest::getVar('task', '');
		$this->_id    = JRequest::getInt('id', 0);
		$this->_alias = JRequest::getVar('alias', '');
		$this->_resid = JRequest::getInt('resid', 0);

		if ($this->_resid && !$this->_task) 
		{
			$this->_task = 'play';
		}
		if (($this->_id || $this->_alias) && !$this->_task) 
		{
			$this->_task = 'view';
		}

		switch ($this->_task)
		{
			// Individual resource specific actions/views
			case 'view':       $this->view();       break;
			case 'play':       $this->play();       break;
			case 'watch':	   $this->watch();		break;
			case 'video':	   $this->video();		break;
			case 'citation':   $this->citation();   break;
			case 'download':   $this->download();   break;
			case 'sourcecode': $this->sourcecode(); break;
			case 'license':    $this->license();    break;
			case 'feed.rss':   $this->feed();       break;
			case 'feed':       $this->feed();       break;

			// Resource discovery
			case 'browse':     $this->browse();     break;
			case 'browsetags': $this->browsetags(); break;

			// Should only be called via AJAX
			case 'browser':    $this->browser();    break;
			case 'plugin':     $this->plugin();     break;
			case 'savetags':   $this->savetags();   break;

			case 'selectpresentation': $this->selectPresentation(); break;

			default: $this->intro(); break;
		}
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (count($pathway->getPathWay()) <= 1 && $this->_task) 
		{
			switch ($this->_task)
			{
				case 'browse':
					if ($this->_task_title) 
					{
						$pathway->addItem(
							$this->_task_title,
							'index.php?option=' . $this->_option . '&task=' . $this->_task
						);
					}
				break;
				case 'browsetags':
					if ($this->_task_title) 
					{
						$pathway->addItem(
							$this->_task_title,
							'index.php?option=' . $this->_option . '&type=' . $this->type
						);
					}
				break;
				default:
					$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . $this->_task
					);
				break;
			}
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
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
					case 'browsetags':
						if ($this->_task_title) 
						{
							$this->_title .= ': ' . $this->_task_title;
						}
					break;
					default:
						$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
					break;
				}
			}
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Component front page
	 * 
	 * @return     void
	 */
	protected function intro()
	{
		// Push some styles to the template
		$this->_getStyles('', 'introduction.css', true); // component, stylesheet name, look in media system dir
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$view = new JView(array('name'=>'intro'));
		$view->title = $this->_title;
		$view->option = $this->_option;

		// Get major types
		$t = new ResourcesType($this->database);
		$view->categories = $t->getMajorTypes();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * Browse entries
	 * 
	 * @return     void
	 */
	protected function browse()
	{
		// Instantiate a new view
		$view = new JView(array('name'=>'browse'));
		$view->option = $this->_option;
		$view->config = $this->config;

		// Set the default sort
		$default_sort = 'date';
		if ($this->config->get('show_ranking')) 
		{
			$default_sort = 'ranking';
		}

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$view->filters = array();

		$view->filters['type']   = JRequest::getVar('type', '');
		$view->filters['sortby'] = JRequest::getCmd('sortby', $default_sort);
		$view->filters['limit']  = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$view->filters['start']  = JRequest::getInt('limitstart', 0);
		$view->filters['search'] = JRequest::getVar('search', '');

		$tagstring = trim(JRequest::getVar('tag', '', 'request', 'none', 2));
		$view->filters['tag']    = $tagstring;

		// Determine if user can edit
		$view->authorized = $this->_authorize();

		//$juser =& JFactory::getUser();

		// Get major types
		$t = new ResourcesType($this->database);
		$view->types = $t->getMajorTypes();

		if (!is_numeric($view->filters['type'])) 
		{
			// Normalize the title
			// This is so we can determine the type of resource to display from the URL
			// For example, /resources/learningmodules => Learning Modules
			for ($i = 0; $i < count($view->types); $i++)
			{
				$normalized = ($t->alias ? $t->alias : $t->normalize($view->types[$i]->type));

				if (trim($view->filters['type']) == $normalized) 
				{
					$view->filters['type'] = $view->types[$i]->id;
					break;
				}
			}
		}

		// Instantiate a resource object
		$rr = new ResourcesResource($this->database);

		// Execute count query
		$results = $rr->getCount($view->filters);
		$view->total = ($results && is_array($results)) ? count($results) : 0;

		// Run query with limit
		$view->results = $rr->getRecords($view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination($view->total, $view->filters['start'], $view->filters['limit']);

		// Get type if not given
		$this->_title = JText::_(strtoupper($this->_option)) . ': ';
		if ($view->filters['type'] != '') 
		{
			$t->load($this->database->getEscaped($view->filters['type']));
			$this->_title .= $t->type;
			$this->_task_title = $t->type;
		} 
		else 
		{
			$this->_title .= JText::_('COM_RESOURCES_ALL');
			$this->_task_title = JText::_('COM_RESOURCES_ALL');
		}

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		$view->config = $this->config;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * Browse resources by tags
	 * 
	 * @return     void
	 */
	protected function browsetags()
	{
		// Check if we're using this view type
		if ($this->config->get('browsetags') == 'off') 
		{
			$this->browse();
			return;
		}

		// Instantiate a new view
		$view = new JView(array('name'=>'browse','layout'=>'tags'));
		$view->option = $this->_option;
		$view->config = $this->config;

		// Incoming
		$view->tag  = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(JRequest::getVar('tag', '')));
		$view->tag2 = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(JRequest::getVar('with', '')));
		$view->type = strtolower(JRequest::getVar('type', 'tools'));

		$view->supportedtag = $this->config->get('supportedtag');
		if (!$view->tag && $view->supportedtag && $view->type == 'tools') 
		{
			$view->tag = $view->supportedtag;
		}

		// Get major types
		$t = new ResourcesType($this->database);
		$view->types = $t->getMajorTypes();

		// Normalize the title
		// This is so we can determine the type of resource to display from the URL
		// For example, /resources/learningmodules => Learning Modules
		$activetype = 0;
		$activetitle = '';
		for ($i = 0; $i < count($view->types); $i++)
		{
			//$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $view->types[$i]->type);
			//$normalized = strtolower($normalized);
			//$view->types[$i]->title = $normalized;

			if (trim($view->type) == $view->types[$i]->alias) 
			{
				$activetype  = $view->types[$i]->id;
				$activetitle = $view->types[$i]->type;
			}
		}
		asort($view->types);

		// Ensure we have a type to display
		if (!$activetype) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Instantiate a resource object
		$rr = new ResourcesResource($this->database);

		// Determine if user can edit
		$view->authorized = $this->_authorize();

		// Set the default sort
		$default_sort = 'rating';
		if ($this->config->get('show_ranking')) 
		{
			$default_sort = 'ranking';
		}

		// Set some filters
		$view->filters = array();
		$view->filters['tag']    = ($view->tag2) ? $view->tag2 : '';
		$view->filters['type']   = $activetype;
		$view->filters['sortby'] = $default_sort;
		$view->filters['limit']  = 10;
		$view->filters['start']  = 0;

		// Run query with limit
		$view->results = $rr->getRecords($view->filters);

		$this->type = $view->type;
		if ($activetitle) 
		{
			$this->_task_title = $activetitle;
		} 
		else 
		{
			$this->_task_title = JText::_('COM_RESOURCES_ALL');
		}

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);
		$this->_getScripts('assets/js/tagbrowser');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * Display a level of the tag browser
	 * NOTE: This view should only be called through AJAX
	 * 
	 * @return     void
	 */
	protected function browser()
	{
		// Incoming
		$level = JRequest::getInt('level', 0);

		// A container for info to pass to the HTML for the view
		$bits = array();
		$bits['supportedtag'] = $this->config->get('supportedtag');

		// Process the level
		switch ($level)
		{
			case 1:
				// Incoming
				$bits['type'] = JRequest::getInt('type', 7);
				$bits['id']   = JRequest::getInt('id', 0);
				$bits['tg']   = JRequest::getVar('input', '');
				$bits['tg2']  = JRequest::getVar('input2', '');

				$rt = new ResourcesTags($this->database);

				// Get tags that have been assigned
				$bits['tags'] = $rt->get_tags_with_objects($bits['id'], $bits['type'], $bits['tg2']);
			break;

			case 2:
				// Incoming
				$bits['type'] = JRequest::getInt('type', 7);
				$bits['id'] = JRequest::getInt('id', 0);
				$bits['tag'] = JRequest::getVar('input', '');
				$bits['tag2'] = JRequest::getVar('input2', '');
				$bits['sortby'] = JRequest::getVar('sortby', 'title');
				$bits['filter']  = JRequest::getVar('filter', array('level0','level1','level2','level3','level4'));

				if ($bits['tag'] == $bits['tag2']) {
					$bits['tag2'] = '';
				}

				// Get parameters
				$bits['params'] = $this->config;

				// Get extra filter options
				$bits['filters'] = array();
				if ($this->config->get('show_audience') && $bits['type'] == 7) 
				{
					include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->_option . DS . 'tables' . DS . 'audience.php');
					include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->_option . DS . 'tables' . DS . 'audience.level.php');
					$rL = new ResourceAudienceLevel($this->database);
					$bits['filters'] = $rL->getLevels();
				}

				$rt = new ResourcesTags($this->database);
				$bits['rt'] = $rt;

				// Get resources assigned to this tag
				$bits['tools'] = $rt->get_objects_on_tag(
					$bits['tag'], 
					$bits['id'], 
					$bits['type'], 
					$bits['sortby'], 
					$bits['tag2'], 
					$bits['filter']
				);

				// Set the typetitle
				$bits['typetitle'] = JText::_('COM_RESOURCES');

				// See if we can load the type so we can set the typetitle
				if (isset($bits['type']) && $bits['type'] != 0) 
				{
					$t = new ResourcesType($this->database);
					$t->load($bits['type']);
					$bits['typetitle'] = stripslashes($t->type);
				}

				$bits['supportedtagusage'] = $rt->getTagUsage($bits['supportedtag'], 'id');
			break;

			case 3:
				/*$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}*/
				// Incoming (should be a resource ID)
				$id = JRequest::getInt('input', 0);

				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'resource.php');
				$model = ResourcesModelResource::getInstance($id);

				$rt = new ResourcesTags($this->database);
				$bits['rt'] = $rt;
				$bits['config'] = $this->config;
				$bits['params'] = $model->params;

				// Get resource
				//$resource = new ResourcesResource($this->database);
				//$resource->load($id);
				$model->resource->ranking = round($model->resource->ranking, 1);

				// Get parameters and merge with the component params
				/*$rparams = new $paramsClass($resource->params);
				$params = $this->config;
				$params->merge($rparams);
				$bits['params'] = $params;

				$resource->_type = new ResourcesType($this->database);
				$resource->_type->load($resource->type);
				$resource->_type->_params = new $paramsClass($resource->_type->params);*/

				// Version checks (tools only)
				/*if ($model->isTool()) 
				{
					$tables = $this->database->getTableList();
					$table  = $this->database->getPrefix() . 'tool_version';

					if (in_array($table, $tables)) 
					{
						// Load the tool version
						$tv = new ToolVersion($this->database);
						//$tool = new ToolVersion($this->database);
						$tool = $tv->getVersionInfo('', 'current', $resource->alias);

						// Replace resource info with requested version
						if ($tool) 
						{
							$tool = $tool[0];
						}
						// get contribtool params
						$tparams =& JComponentHelper::getParams('com_tools');
						$tv->compileResource($tool, '', &$resource, '', $tparams);
					}
				}*/

				// Generate the SEF
				if ($model->resource->alias) 
				{
					$sef = JRoute::_('index.php?option=' . $this->_option . '&alias=' . $model->resource->alias);
				} 
				else 
				{
					$sef = JRoute::_('index.php?option=' . $this->_option . '&id=' . $model->resource->id);
				}

				// Get resource helper
				$helper = new ResourcesHelper($model->resource->id, $this->database);
				//$helper->getFirstChild();

				//$helper->getContributorIDs();
				$bits['authorized'] = $model->access('edit'); //$this->_authorize($helper->contributorIDs, $resource);

				$firstChild = $model->children(0);

				// Get the first child
				if ($firstChild || $model->isTool()) 
				{
					$bits['primary_child'] = ResourcesHtml::primary_child($this->_option, $model->resource, $firstChild, '');
				}

				// Get Resources plugins
				JPluginHelper::importPlugin('resources');
				$dispatcher =& JDispatcher::getInstance();

				// Get the sections
				$bits['sections'] = $dispatcher->trigger('onResources', array($model, $this->_option, array('about'), 'metadata'));

				// Fill our container
				$bits['resource'] = $model->resource;
				$bits['helper'] = $helper;
				$bits['sef'] = $sef;
			break;
		}

		// Instantiate a new view
		$this->view = new JView(array('name'=>'browse', 'layout'=>'tags_list'));
		$this->view->option = $this->_option;
		$this->view->config = $this->config;
		$this->view->level  = $level;
		$this->view->bits   = $bits;

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * 'play' a resource
	 * This displays a Breeze presentation, iframe, Google doc viewer, etc.
	 * 
	 * @return     void
	 */
	protected function play()
	{
		// Incoming
		$this->resid = JRequest::getInt('resid', 0);
		$id = JRequest::getInt('id', 0);

		$helper = new ResourcesHelper($id, $this->database);

		// Do we have a child ID?
		if (!$this->resid) 
		{
			// No ID, default to the first child
			$helper->getFirstChild();

			$this->resid = $helper->firstChild->id;
		}

		// We have an ID, load it
		$activechild = new ResourcesResource($this->database);
		$activechild->load($this->resid);

		// Do some work on the child's path to make sure it's kosher
		if ($activechild->path) 
		{
			$activechild->path = stripslashes($activechild->path);

			if (preg_match("/(?:https?:|mailto:|ftp:|gopher:|news:|file:)/", $activechild->path)) 
			{
				// Do nothing
			} 
			else 
			{
				if (substr($activechild->path, 0, 1) != DS) 
				{
					$activechild->path = DS . $activechild->path;
					if (substr($activechild->path, 0, strlen($this->config->get('uploadpath'))) == $this->config->get('uploadpath')) 
					{
						// Do nothing
					} 
					else 
					{
						$activechild->path = $this->config->get('uploadpath') . $activechild->path;
					}
				}
			}
		}

		// Store the object in our registry
		$this->activechild = $activechild;

		// Viewing via AJAX?
		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html) 
		{
			$resource = new ResourcesResource($this->database);
			$resource->load($id);

			// Instantiate a new view
			$view = new JView(array('name'=>'view', 'layout'=>'play'));
			$view->option = $this->_option;
			$view->config = $this->config;
			$view->database = $this->database;
			$view->resource = $resource;
			$view->helper = $helper;
			$view->resid = $this->resid;
			$view->activechild = $activechild;
			$view->no_html = $no_html;
			$view->fsize = 0;

			// Output HTML
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}
			$view->display();
		} 
		else 
		{
			// Push on through to the view
			$this->view();
		}
	}

	/**
	 * Select a presentation
	 * 
	 * @return     void
	 */
	protected function selectPresentation()
	{
		$presentation = JRequest::getVar('presentation', 0);

		$helper = new ResourcesHelper($presentation, $this->database);
		$helper->getFirstChild();
		$resid = $helper->firstChild->id;

		$this->setRedirect(
			JRoute::_('index.php?option=com_resources&id=' . $presentation . '&task=watch&resid=' . $resid . '&tmpl=component')
		);
		return;
	}

	/**
	 * Perform a some setup needed for presenter()
	 * 
	 * @return     array
	 */
	protected function preWatch()
	{
		//var to hold error messages
		$errors = array();

		//inlude the HUBpresenter library
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'hubpresenter.php');

		//get the presentation id
		//$id = JRequest::getVar('id', '');
		$resid = JRequest::getVar('resid', '');
		if (!$resid) 
		{
			$this->setError(JText::_('Unable to find presentation.'));
		}

		//load resource
		$activechild = new ResourcesResource($this->database);
		$activechild->load($resid);

		//base url for the resource
		$base = DS . trim($this->config->get('uploadpath', '/site/resources'), DS);

		//build the rest of the resource path and combine with base
		$path = ResourcesHtml::build_path($activechild->created, $activechild->id, '');
		$path =  $base . $path;

		//check to make sure we have a presentation document defining cuepoints, slides, and media
		//$manifest_path_json = JPATH_ROOT . $path . DS . 'presentation.json';
		$manifests = JFolder::files( JPATH_ROOT . DS . $path, '.json' );
		$manifest_path_json = (isset($manifests[0])) ? $manifests[0] : null;
		$manifest_path_xml  = JPATH_ROOT . $path . DS . 'presentation.xml';

		//check if the formatted json exists
		if (!file_exists(JPATH_ROOT . $path . DS . $manifest_path_json)) 
		{
			//check to see if we just havent converted yet
			if (!file_exists($manifest_path_xml)) 
			{
				$this->setError(JText::_('Missing outline used to build presentation.'));
			} 
			else 
			{
				$job = HUBpresenterHelper::createJsonManifest($path, $manifest_path_xml);
				if ($job != '') 
				{
					$this->setError($job);
				}
			}
		}

		//path to media
		$media_path = JPATH_ROOT . $path;

		//check if path exists
		if (!is_dir($media_path)) 
		{
			$this->setError(JText::_('Path to media does not exist.'));
		} 
		else 
		{
			//get all files matching  /.mp4|.webs|.ogv|.m4v|.mp3/
			$media = JFolder::files($media_path, '.mp4|.webm|.ogv|.m4v|.mp3|.ogg', false, false);
			$ext = array();
			foreach ($media as $m) 
			{
				$ext[] = array_pop(explode('.', $m));
			}
			
			//if we dont have all the necessary media formats
			if ((in_array('mp4', $ext) && count($ext) < 3) || (in_array('mp3', $ext) && count($ext) < 2)) 
			{
				$this->setError(JText::_('Missing necessary media formats for video or audio.'));
			}

			//make sure if any slides are video we have three formats of video and backup image for mobile
			$slide_path = $media_path . DS . 'slides';
			$slides = JFolder::files($slide_path, '', false, false);

			//array to hold slides with video clips
			$slide_video = array();

			//build array for checking slide video formats
			if ($slides && is_array($slides))
			{
				foreach ($slides as $s) 
				{
					$parts = explode('.', $s);
					$ext = array_pop($parts);
					$name = implode('.', $parts);

					if (in_array($ext, array('mp4', 'm4v', 'webm', 'ogv'))) 
					{
						$slide_video[$name][$ext] = $name . '.' . $ext;
					}
				}
			}

			//make sure for each of the slide videos we have all three formats
			//and has a backup image for the slide
			foreach ($slide_video as $k => $v) 
			{
				if (count($v) < 3) 
				{
					$this->setError(JText::_('Video Slides must be Uploaded in the Three Standard Formats. You currently only have ' . count($v) . " ({$k}." . implode(", {$k}.", array_keys($v)) . ').'));
				}

				if (!file_exists($slide_path . DS . $k .'.png') && !file_exists($slide_path . DS . $k .'.jpg')) 
				{
					$this->setError(JText::_('Slides containing video must have a still image of the slide for mobile support. Please upload an image with the filename "' . $k . '.png" or "' . $k . '.jpg".'));
				}
			}
		}

		$return = array();
		$return['errors'] = $this->getErrors();
		$return['content_folder'] = $path;
		$return['manifest'] = $path . DS . $manifest_path_json;
		
		return $return;
	}

	/**
	 * Display presenter
	 * 
	 * @return     void
	 */
	protected function watch()
	{
		$parent = $this->_id;
		$child = JRequest::getVar('resid', '');
		
		//document object
		$document =& JFactory::getDocument();
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		//add the HUBpresenter stylesheet
		$this->_getStyles($this->_option,'hubpresenter.css');

		//add the HUBpresenter required javascript files
		if(!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$document->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
			$document->addScript("https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js");
		}
		$document->addScript('/components/' . $this->_option . '/assets/js/hubpresenter.jquery.js');
		$document->addScript('/components/' . $this->_option . '/assets/js/hubpresenter.plugins.jquery.js');
		
		//media tracking object
		require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'media.tracking.php');
		$mediaTracking = new ResourceMediaTracking( $database );
		
		//get tracking for this user for this resource
		$tracking = $mediaTracking->getTrackingInformationForUserAndResource( $juser->get('id'), $child );
		
		//check to see if we already have a time query param
		$hasTime = (JRequest::getVar('time', '') != '') ? true : false;
		
		//do we want to redirect user with time added to url
		if(is_object($tracking) && !$hasTime && $tracking->current_position > 0 && $tracking->current_position != $tracking->object_duration)
		{
			$redirect = 'index.php?option=com_resources&amp;task=watch&amp;id='.$parent.'&amp;resid='.$child;
			if(JRequest::getVar('tmpl', '') == 'component')
			{
				$redirect .= '&amp;tmpl=component';
			}
			
			//append current position to redirect
			$redirect .= "&time=" . gmdate("H:i:s", $tracking->current_position);
			
			//redirect
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_($redirect, false), '','',false);
		}
		
		//do we have javascript?
		$js = JRequest::getVar("tmpl", "");
		if ($js != '') 
		{
			$pre = $this->preWatch();

			//get the errors
			$errors = $pre['errors'];

			//get the manifest
			$manifest = $pre['manifest'];

			//get the content path
			$content_folder = $pre['content_folder'];

			//if we have no errors
			if (count($errors) > 0) 
			{
				echo HUBpresenterHelper::errorMessage($errors);
			} 
			else 
			{
				// Instantiate a new view
				$view 					= new JView(array('name'=>'view', 'layout'=>'watch'));
				$view->option 			= $this->_option;
				$view->config			= $this->config;
				$view->database 		= $this->database;
				$view->doc 				= $document;
				$view->manifest 		= $manifest;
				$view->content_folder 	= $content_folder;
				$view->pid 				= $parent;
				$view->resid 			= $child;
				
				// Output HTML
				if ($this->getError()) 
				{
					foreach ($this->getErrors() as $error)
					{
						$view->setError($error);
					}
				}
				$view->display();
			}
		} 
		else 
		{
			$this->view();
		}
	}

	/**
	 * Display an HTML5 video
	 * 
	 * @return     void
	 */
	protected function video()
	{
		//get the document to push resources to
		$document =& JFactory::getDocument();
		
		//add the video css
		$this->_getStyles($this->_option,'video.css');
		
		//add the HUBpresenter required javascript files
		if(!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$document->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
			$document->addScript("https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js");
		}
		$document->addScript('/components/' . $this->_option . "/assets/js/video.jquery.js");
		$document->addScript('/components/' . $this->_option . "/assets/js/hubpresenter.plugins.jquery.js");
		
		//get the request vars
		$parent = JRequest::getInt("id", "");
		$child = JRequest::getVar("resid", "");
		
		//load resource
		$activechild = new ResourcesResource($this->database);
		$activechild->load($child);
		
		//check to see if we have a manifest
		if (!$this->videoManifestExistsForResource( $activechild ))
		{
			$this->createVideoManifestForResource( $activechild );
		}
		
		//get manifest
		$manifest = $this->getVideoManifestForResource( $activechild );
		$manifest = json_decode( file_get_contents( JPATH_ROOT . $manifest ) );
		
		//media tracking object
		require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'media.tracking.php');
		$mediaTracking = new ResourceMediaTracking( $this->database );
		
		//get tracking for this user for this resource
		$tracking = $mediaTracking->getTrackingInformationForUserAndResource( $this->juser->get('id'), $activechild->id );
		
		//check to see if we already have a time query param
		$hasTime = (JRequest::getVar('time', '') != '') ? true : false;
		
		//do we want to redirect user with time added to url
		if(is_object($tracking) && !$hasTime && $tracking->current_position > 0 && $tracking->current_position != $tracking->object_duration)
		{
			$redirect = 'index.php?option=com_resources&amp;task=video&amp;id='.$parent.'&amp;resid='.$child;
			if(JRequest::getVar('tmpl', '') == 'component')
			{
				$redirect .= '&amp;tmpl=component';
			}
			
			//append current position to redirect
			$redirect .= "&time=" . gmdate("H:i:s", $tracking->current_position);
			
			//redirect
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_($redirect, false), '','',false);
		}
		
		// Instantiate a new view
		$view = new JView(array(
			'name'   => 'view', 
			'layout' => 'video'
		));
		$view->option   = $this->_option;
		$view->config   = $this->config;
		$view->database = $this->database;
		$view->resource = $activechild;
		$view->manifest = $manifest;

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}
	
	/**
	 * Get Video Manifest for resource
	 * 
	 * @param      $resource     HUB Resource
	 * @return     BOOL
	 */
	private function getVideoManifestForResource( $resource )
	{
		//base url for the resource
		$base = DS . trim($this->config->get('uploadpath'), DS);
		
		//build the rest of the resource path and combine with base
		$path = ResourcesHtml::build_path($resource->created, $resource->id, '');
		
		//get manifests
		$manifests = JFolder::files( JPATH_ROOT . DS . $base . $path, '.json' );
		
		//return path to manifest if we have one
		return (count($manifests) > 0) ? $base.$path.DS.$manifests[0] : array();
	}
	
	
	/**
	 * Check for Video manifest file
	 * 
	 * @param      $resource     HUB Resource
	 * @return     BOOL
	 */
	private function videoManifestExistsForResource( $resource )
	{
		//get video manifest 
		$manifest = $this->getVideoManifestForResource( $resource );
		
		//do we have a manifest already?
		return (count($manifest) < 1) ? false : true;
	}
	
	
	/**
	 * Create manifest file for video
	 * 
	 * @param      $resource     HUB Resource
	 * @return     Void
	 */
	private function createVideoManifestForResource( $resource )
	{
		//var to hold manifest data
		$manifest = new stdClass;
		
		//base url for the resource
		$base = DS . trim($this->config->get('uploadpath'), DS);
		
		//build the rest of the resource path and combine with base
		$path = ResourcesHtml::build_path( $resource->created, $resource->id, '' );
		
		//instantiate params object then parse resource attributes
		$paramsClass = (version_compare(JVERSION, '1.6', 'ge')) ? 'JRegistry' : 'JParameter';
		$attributes  = new $paramsClass( $resource->attribs );
		
		//set vars for manifest
		$manifest->presentation->title     = $resource->title;
		$manifest->presentation->type      = 'Video';
		$manifest->presentation->width     = intval($attributes->get('width', 0));
		$manifest->presentation->height    = intval($attributes->get('height', 0));
		$manifest->presentation->duration  = intval($attributes->get('duration', 0));
		$manifest->presentation->media     = array();
		$manifest->presentation->subtitles = array();
		
		//get the videos
		$videos = JFolder::files(JPATH_ROOT . DS . $base . $path, '.mp4|.MP4|.ogv|.OGV|.webm|.WEBM');
		
		//add each video to manifest
		foreach ($videos as $k => $video)
		{
			$videoInfo = pathinfo( $video );
			$manifest->presentation->media[$k]->type   = $videoInfo['extension'];
			$manifest->presentation->media[$k]->source = $path . DS . $video;
		}
		
		//get the subs
		$subtitles = JFolder::files(JPATH_ROOT . DS . $base . $path, '.srt|.SRT');
		
		//add each subtitle to manifest
		foreach ($subtitles as $k => $subtitle)
		{
			//get name
			$info = pathinfo( $subtitle );
			$name = str_replace('-auto', '', $info['filename']);
			$name = ucfirst( $name );
			
			$manifest->presentation->subtitles[$k]->type     = 'SRT';
			$manifest->presentation->subtitles[$k]->name     = $name;
			$manifest->presentation->subtitles[$k]->source   = $path . DS . $subtitle;
			$manifest->presentation->subtitles[$k]->autoplay = 0;
			
			//do we want to autoplay
			if (strstr($subtitle, '-'))
			{
				$manifest->presentation->subtitles[$k]->autoplay = 1;
			}
		}
		
		//reset array of subs and media
		$manifest->presentation->media     = array_values($manifest->presentation->media);
		$manifest->presentation->subtitles = array_values($manifest->presentation->subtitles);
		
		//attempt to create manifest file
		if (!JFile::write(JPATH_ROOT . DS . $base . $path . DS . 'presentation.json', json_encode($manifest)))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * View a resource
	 * 
	 * @return     void
	 */
	protected function view()
	{
		// Incoming
		$id       = JRequest::getInt('id', 0);            // Rsource ID (primary method of identifying a resource)
		$alias    = JRequest::getVar('alias', '');        // Alternate method of identifying a resource
		$fsize    = JRequest::getVar('fsize', '');        // A parameter to see file size without formatting

		// XSS fix. Revision gets pumped all over and dumped in URLs via plugins, easier to fix at the input instead of risking missing an output. See ticket 1416
		$revision = htmlentities(JRequest::getVar('rev', ''));          // Get svk revision of a tool

		$tab      = JRequest::getVar('active', 'about');  // The active tab (section)

		// Ensure we have an ID or alias to work with
		if (!$id && !$alias) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		// Load the resource
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'resource.php');
		$this->model = ResourcesModelResource::getInstance(($alias ? $alias : $id), $revision);

		// Make sure we got a result from the database
		if (!$this->model->exists() || $this->model->deleted())
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			return;
		}

		// Make sure the resource is published and standalone
		if (!$this->model->resource->standalone) // || !$this->model->published()) 
		{
			JError::raiseError(403, JText::_('COM_RESOURCES_ALERTNOTAUTH'));
			return;
		}

		// Is the visitor authorized to view this resource?
		if (!$this->model->access('view')) 
		{
			JError::raiseError(403, JText::_('COM_RESOURCES_ALERTNOTAUTH'));
			return;
		}

		// Initiate a resource helper class
		$helper = new ResourcesHelper($this->model->resource->id, $this->database);

		// Build the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if ($this->model->inGroup()) 
		{
			// Alter the pathway to reflect a group owned resource
			ximport('Hubzero_Group');
			$group = Hubzero_Group::getInstance($this->model->resource->group_owner);

			if ($group)
			{
				$pathway->_pathway = array();
				$pathway->_count = 0;

				$pathway->addItem(
					'Groups',
					JRoute::_('index.php?option=com_groups')
				);
				$pathway->addItem(
					stripslashes($group->get('description')),
					JRoute::_('index.php?option=com_groups&cn=' . $this->model->resource->group_owner)
				);
				$pathway->addItem(
					'Resources',
					JRoute::_('index.php?option=com_groups&cn=' . $this->model->resource->group_owner . '&active=resources')
				);
				$pathway->addItem(
					stripslashes($this->model->type->type),
					JRoute::_('index.php?option=com_groups&cn=' . $this->model->resource->group_owner . '&active=resources&area=' . $this->model->type->alias)
				);
			}
			else
			{
				$pathway->addItem(
					stripslashes($this->model->type->type),
					JRoute::_('index.php?option=' . $this->_option . '&type=' . $this->model->type->alias)
				);
			}
		} 
		else 
		{
			$pathway->addItem(
				stripslashes($this->model->type->type),
				JRoute::_('index.php?option=' . $this->_option . '&type=' . $this->model->type->alias)
			);
		}

		// Tool development version requested
		//$juser =& JFactory::getUser();
		if ($this->juser->get('guest') && $revision == 'dev') 
		{
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		// Access check for tools
		if ($this->model->isTool())
		{
			// if (development revision
			//   or (specific revision that is NOT published))
			if (($revision=='dev') 
			 or (!$revision && $this->model->resource->published != 1)) 
			{
				// Check if the user has access to the tool
				$objT = new Tool($this->database);
				$toolid = $objT->getToolId($this->model->resource->alias);
				if (!$this->check_toolaccess($toolid)) 
				{
					// Denied, punk! How do you like them apples?!
					JError::raiseError(403, JText::_('ALERTNOTAUTH'));
					return;
				}
			}
		}

		// Whew! Finally passed all the checks
		// Let's get down to business...

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);

		// Get contribtool params
		$tconfig =& JComponentHelper::getParams('com_tools');

		// Record the hit
		$this->model->resource->hit();

		// Get Resources plugins
		JPluginHelper::importPlugin('resources');
		$dispatcher =& JDispatcher::getInstance();

		$sections = array();
		$cats = array();

		// We need to do this here because we need some stats info to pass to the body
		if (!isset($this->model->thistool) || !$this->model->thistool) 
		{
			// Trigger the functions that return the areas we'll be using
			$cats = $dispatcher->trigger('onResourcesAreas', array(
					$this->model
				)
			);
		}
		else if ($revision)
		{
			$cats = $dispatcher->trigger('onResourcesAreas', array(
					$this->model
				)
			);
			$cts = array();
			foreach ($cats as $cat)
			{
				if (empty($cat))
				{
					$cts[] = $cat;
					continue;
				}
				foreach ($cat as $name => $title)
				{
					if ($name == 'about')
					{
						$cts[] = $cat;
					}
				}
			}
			/*$cats = array(
				array('about' => JText::_('About'))
			);*/
			$cats = $cts;
		}
		// Get the sections
		$sections = $dispatcher->trigger('onResources', array(
				$this->model,
				$this->_option,
				array($tab),
				'all',
			)
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

		// Display different main text if "playing" a resource
		if ($this->_task == 'play') 
		{
			$activechild = NULL;
			if (is_object($this->activechild)) 
			{
				$activechild = $this->activechild;
			}

			$view = new JView(array(
				'base_path' => $this->_base_path,
				'name'      => 'view', 
				'layout'    => 'play'
			));
			$view->option 		= $this->_option;
			$view->config 		= $this->config;
			$view->tconfig 		= $tconfig;
			$view->database 	= $this->database;
			$view->resource 	= $this->model->resource;
			$view->helper 		= $helper;
			$view->resid 		= $this->resid;
			$view->activechild 	= $activechild;
			$view->no_html 		= 0;
			$view->fsize 		= 0;
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}
			$body = $view->loadTemplate();

			$cat = array();
			$cat['play'] = JText::_('COM_RESOURCES_PLAY');
			$cats[] = $cat;
			$sections[] = array('html' => $body, 'metadata' => '', 'area' => 'play');
			$tab = 'play';
		} 
		elseif ($this->_task == 'watch') 
		{
			//test to make sure HUBpresenter is ready to go
			$pre = $this->preWatch();

			//get the errors
			$errors = $pre['errors'];

			//get the manifest
			$manifest = $pre['manifest'];

			//get the content path
			$content_folder = $pre['content_folder'];

			//if we have no errors
			if (count($errors) > 0) 
			{
				$body = HUBpresenterHelper::errorMessage($errors);
			} 
			else 
			{
				// Instantiate a new view
				$view = new JView(array(
					'base_path' => $this->_base_path,
					'name'      => 'view', 
					'layout'    => 'watch'
				));
				$view->option         = $this->_option;
				$view->config         = $this->config;
				$view->tconfig        = $tconfig;
				$view->database       = $this->database;
				$view->manifest       = $manifest;
				$view->content_folder = $content_folder;
				$view->pid            = $id;
				$view->resid          = JRequest::getVar('resid', '');
				$view->doc            =& JFactory::getDocument();

				// Output HTML
				if ($this->getError()) 
				{
					foreach ($this->getErrors() as $error)
					{
						$view->setError($error);
					}
				}
				$body = $view->loadTemplate();
			}

			$cats[] = array(
				'watch' => JText::_('Watch Presentation')
			);

			$sections[] = array(
				'html'     => $body, 
				'metadata' => ''
			);
			$tab = 'watch';
		}

		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_option)) . ': ' . stripslashes($this->model->resource->title));

		if ($canonical = $this->model->attribs->get('canonical', '')) 
		{
			if (!preg_match('/^(https?:|mailto:|ftp:|gopher:|news:|file:|rss:)/i', $canonical))
			{
				$juri =& JURI::getInstance();
				$canonical = rtrim($juri->base(), DS) . DS . ltrim($canonical, DS);
			}
			$document->addHeadLink($canonical, 'canonical');
		}

		$pathway->addItem(
			stripslashes($this->model->resource->title),
			JRoute::_('index.php?option=' . $this->_option . '&id=' . $this->model->resource->id)
		);

		// Normalize the title
		// This is so we can determine the type of resource template to display
		// For example, Learning Modules => learningmodules
		$type_alias = $this->model->type->alias ? $this->model->type->alias : $this->model->type->normalize($this->model->type->type);

		// Determine the layout we're using
		$v = array(
			'base_path' => $this->_base_path,
			'name'      => 'view'
		);

		$app =& JFactory::getApplication();
		if ($type_alias
		 && (is_file(JPATH_ROOT . DS . 'templates' . DS .  $app->getTemplate()  . DS . 'html' . DS . $this->_option . DS . 'view' . DS . $type_alias . '.php')
			|| is_file(JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'views' . DS . 'view' . DS . 'tmpl' . DS . $type_alias . '.php'))) 
		{
			$v['layout'] = $type_alias;
		}
		// Instantiate a new view
		$view = new JView($v);
		//$view->filters = $filters;
		if ($this->model->isTool()) 
		{
			$view->thistool = $this->model->thistool;
			$view->curtool  = $this->model->curtool;
			$view->alltools = $this->model->alltools;
			$view->revision = $revision;
		}
		$view->model = $this->model;
		//$view->config 		= $this->config;
		$view->tconfig 		= $tconfig;
		$view->option 		= $this->_option;
		//$view->resource 	= $this->model->resource;
		//$view->params 		= $this->model->params;
		//$view->authorized 	= $authorized;
		//$view->attribs 		= $this->model->attribs;
		$view->fsize 		= $fsize;
		$view->cats 		= $cats;
		$view->tab 			= $tab;
		$view->sections 	= $sections;
		$view->database 	= $this->database;
		//$view->usersgroups 	= $usersgroups;
		$view->helper 		= $helper;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		// Output HTML
		$view->display();
	}

	/**
	 * Display an RSS feed
	 * 
	 * @return     void
	 */
	protected function feed()
	{
		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		ximport('Hubzero_Document_Feed');
		$doc = new Hubzero_Document_Feed;
		$app =& JFactory::getApplication();
		$params =& $app->getParams();

		// Incoming
		$id = JRequest::getInt('id', 0);
		$alias = JRequest::getVar('alias', '');

		// Ensure we have an ID or alias to work with
		if (!$id && !$alias) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		// Load the resource
		$resource = new ResourcesResource($this->database);
		if ($alias) 
		{
			$resource->load($alias);
			$id = $resource->id;
		} 
		else 
		{
			$resource->load($id);
			$alias = $resource->alias;
		}

		// Make sure we got a result from the database
		if (!$resource) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			return;
		}

		// Make sure the resource is published and standalone
		if ($resource->published == 0 || $resource->standalone != 1) 
		{
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		// Make sure they have access to view this resource
		if ($this->checkGroupAccess($resource)) 
		{
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		// Incoming
		$filters = array();
		if ($resource->type == 2) 
		{
			$filters['sortby'] = JRequest::getVar('sortby', 'ordering');
		} 
		else 
		{
			$filters['sortby'] = JRequest::getVar('sortby', 'ranking');
		}
		$filters['limit'] = JRequest::getInt('limit', 100);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['year']  = JRequest::getInt('year', 0);
		$filters['id']    = $resource->id;

		$feedtype = JRequest::getVar('format', 'audio');

		// Initiate a resource helper class
		$helper = new ResourcesHelper($resource->id, $this->database);

		$rows = $helper->getStandaloneChildren($filters);

		// Get HUB configuration
		$jconfig =& JFactory::getConfig();

		$juri =& JURI::getInstance();
		$base = rtrim($juri->base(), DS);

		$title = $resource->title;
		$feedtypes_abr = array(" ", "slides", "audio", "video", "sd_video", "hd_video");
		$feedtypes_full = array(" & ", "Slides", "Audio", "Video", "SD full", "HD");
		$type = str_replace($feedtypes_abr, $feedtypes_full, $feedtype);
		$title = '[' . $type . '] ' . $title;

		// Build some basic RSS document information
		$dtitle = Hubzero_View_Helper_Html::purifyText(stripslashes($title));

		$doc->title = trim(Hubzero_View_Helper_Html::shortenText(html_entity_decode($dtitle), 250, 0));
		$doc->description = Hubzero_View_Helper_Html::xhtml(html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($resource->introtext))));
		$doc->copyright = JText::sprintf('COM_RESOURCES_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('COM_RESOURCES_RSS_CATEGORY');
		$doc->link = JRoute::_('index.php?option=' . $this->_option . '&id=' . $resource->id);

		$rt = new ResourcesTags($this->database);
		$rtags = $rt->get_tags_on_object($resource->id, 0, 0, null, 0, 1);
		$tagarray = array();
		$categories = array();
		$subcategories = array();
		if ($rtags) 
		{
			foreach ($rtags as $tag)
			{
				if (substr($tag['tag'], 0, 6) == 'itunes') 
				{
					$tbits = explode(':', $tag['raw_tag']);
					if (count($tbits) > 2) 
					{
						$subcategories[] = end($tbits);
					} 
					else 
					{
						$categories[] = str_replace('itunes:', '', $tag['raw_tag']);
					}
				} 
				elseif ($tag['admin'] == 0) 
				{
					$tagarray[] = $tag['raw_tag'];
				}
			}
		}
		$tags = implode(', ', $tagarray);
		//$tags = $rt->get_tag_string($resource->id, 0, 0, 0, 0, 0);
		$tags = trim(Hubzero_View_Helper_Html::shortenText($tags, 250, 0));
		$tags = rtrim($tags, ',');

		$helper->getUnlinkedContributors();
		$cons = $helper->ul_contributors;
		$cons = explode(';', $cons);
		$author = '';
		foreach ($cons as $con)
		{
			if ($con) 
			{
				$author = trim($con);
				break;
			}
		}

		$doc->itunes_summary = html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($resource->introtext)));
		if (count($categories) > 0) 
		{
			$doc->itunes_category = $categories[0];
			if (count($subcategories) > 0) 
			{
				$doc->itunes_subcategories = $subcategories;
			}
		}
		$doc->itunes_explicit = 'no';
		$doc->itunes_keywords = $tags;
		$doc->itunes_author = $author;

		$itunes_image_name = 'itunes_' . str_replace(' ', '_', strtolower($feedtype));

		$dimg = $this->checkForImage($itunes_image_name, $this->config->get('uploadpath'), $resource->created, $resource->id);
		if ($dimg) 
		{
			$dimage = new Hubzero_Document_Feed_Image();
			$dimage->url = $dimg;
			$dimage->title = trim(Hubzero_View_Helper_Html::shortenText(html_entity_decode($dtitle . ' ' . JText::_('COM_RESOURCES_RSS_ARTWORK')), 250, 0));
			$dimage->link = $base.$doc->link;
			$doc->itunes_image = $dimage;
		}

		$owner = new Hubzero_Document_Feed_ItunesOwner;
		$owner->email = $jconfig->getValue('config.mailfrom');
		$owner->name  = $jconfig->getValue('config.sitename');

		$doc->itunes_owner = $owner;

		// Start outputing results if any found
		if (count($rows) > 0) 
		{
			$paramsClass = 'JParameter';
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$paramsClass = 'JRegistry';
			}
			
			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to resource
				$link = DS . ltrim(JRoute::_('index.php?option=' . $this->_option . '&id=' . $row->id), DS);

				// Strip html from feed item description text
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($row->introtext)));
				$author = '';
				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				// Instantiate a resource helper
				$rhelper = new ResourcesHelper($row->id, $this->database);

				// Get any podcast/vodcast files
				$podcast = '';

				$type_model            = new ResourcesType($this->database);
				$all_logical_types     = $type_model->getTypes(28);    // 28 means 'logical' types.
				$queried_logical_types = @explode(' ', $feedtype);

				if (is_null($queried_logical_types) || !is_array($queried_logical_types))
				{
					JError::raiseError(404, JText::_('COM_RESOURCES_RESOURCE_FEED_BAD_REQUEST'));
					return;
				}

				$relevant_logical_types_by_id = array();
				foreach ($queried_logical_types as $queried)
				{
					$as_mnemonic = preg_replace('/[_-]/', ' ', $queried);
					foreach($all_logical_types as $logical_type)
					{
						if (preg_match_all('/Podcast \(([^()]+)\)/', $logical_type->type, $matches) == 1 
						 && strcasecmp($matches[ 1 ][ 0 ], $as_mnemonic) == 0)
						{
							$relevant_logical_types_by_id[ $logical_type->id ] = $logical_type;
							break;
						}
						elseif ($as_mnemonic == 'slides' && $logical_type->type == 'Presentation Slides')
						{
							$relevant_logical_types_by_id[ $logical_type->id ] = $logical_type;
							break;
						}
						elseif ($as_mnemonic == 'notes' && $logical_type->type == 'Lecture Notes')
						{
							$relevant_logical_types_by_id[ $logical_type->id ] = $logical_type;
							break;
						}
					}
				}

				$rhelper->getChildren();

				$podcasts = array();
				$children = array();
				if ($rhelper->children && count($rhelper->children) > 0) 
				{
					$grandchildren = $rhelper->children;
					foreach ($grandchildren as $grandchild)
					{
						if (isset($relevant_logical_types_by_id[ (int)$grandchild->logicaltype ]))
						{
							if (stripslashes($grandchild->introtext) != '')
							{
								$gdescription = html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($grandchild->introtext)));
							}
							array_push($podcasts, $grandchild->path);
							array_push($children, $grandchild);
						}
					}
				}

				// Get the contributors of this resource
				$rhelper->getContributors();
				$author = strip_tags($rhelper->contributors);

				$rtt = new ResourcesTags($this->database);
				$rtags = $rtt->get_tag_string($row->id, 0, 0, 0, 0, 0);
				if (trim($rtags))
				{
					$rtags = trim(Hubzero_View_Helper_Html::shortenText($rtags, 250, 0));
					$rtags = rtrim($rtags, ',');
				}

				// Get attributes
				//$attribs = new $paramsClass($row->attribs);
				if ($children)
				{
					$attribs = new $paramsClass($children[0]->attribs);
				}

				foreach ($podcasts as $podcast)
				{
					// Load individual item creator class
					$item = new Hubzero_Document_Feed_Item();
					$item->title       = $title;
					$item->link        = $link;
					$item->description = $description;
					$item->date        = $date;
					$item->category    = ($row->typetitle) ? $row->typetitle : '';
					$item->author      = $author;

					$img = $this->checkForImage('ituness_artwork', $this->config->get('uploadpath'), $row->created, $row->id);
					if ($img) 
					{
						$image = new Hubzero_Document_Feed_Image();
						$image->url = $img;
						$image->title = $title.' '.JText::_('COM_RESOURCES_RSS_ARTWORK');
						$image->link = $base.$link;

						$item->itunes_image = $image;
					}

					$item->itunes_summary  = $description;
					$item->itunes_explicit = 'no';
					$item->itunes_keywords = $rtags;
					$item->itunes_author   = $author;

					if ($attribs->get('duration')) 
					{
						$item->itunes_duration = $attribs->get('duration');
					}

					if ($podcast) 
					{
						$podcastp = $podcast;
						$podcast = DS . ltrim($this->fullPath($podcast), DS);

						if (substr($podcastp, 0, strlen($this->config->get('uploadpath'))) == $this->config->get('uploadpath')) 
						{
							// Do nothing
						} 
						else 
						{
							$podcastp = trim($this->config->get('uploadpath'), DS) . DS . ltrim($podcastp, DS);
						}
						$podcastp = JPATH_ROOT . DS . ltrim($podcastp, DS);
						if (true /* file_exists($podcastp) */) 
						{
							$fs = filesize($podcastp);

							$enclosure = new Hubzero_Document_Feed_Enclosure; //JObject;
							$enclosure->url = $podcast;
							switch (ResourcesHtml::getFileExtension($podcast))
							{
								case 'm4v': $enclosure->type = 'video/x-m4v'; break;
								case 'mp4': $enclosure->type = 'video/mp4'; break;
								case 'wmv': $enclosure->type = 'video/wmv'; break;
								case 'mov': $enclosure->type = 'video/quicktime'; break;
								case 'qt': $enclosure->type = 'video/quicktime'; break;
								case 'mpg': $enclosure->type = 'video/mpeg'; break;
								case 'mpeg': $enclosure->type = 'video/mpeg'; break;
								case 'mpe': $enclosure->type = 'video/mpeg'; break;
								case 'mp2': $enclosure->type = 'video/mpeg'; break;
								case 'mpv2': $enclosure->type = 'video/mpeg'; break;

								case 'mp3': $enclosure->type = 'audio/mpeg'; break;
								case 'm4a': $enclosure->type = 'audio/x-m4a'; break;
								case 'aiff': $enclosure->type = 'audio/x-aiff'; break;
								case 'aif': $enclosure->type = 'audio/x-aiff'; break;
								case 'wav': $enclosure->type = 'audio/x-wav'; break;
								case 'ra': $enclosure->type = 'audio/x-pn-realaudio'; break;
								case 'ram': $enclosure->type = 'audio/x-pn-realaudio'; break;

								case 'ppt': $enclosure->type = 'application/vnd.ms-powerpoint'; break;
								case 'pps': $enclosure->type = 'application/vnd.ms-powerpoint'; break;
								case 'pdf': $enclosure->type = 'application/pdf'; break;
								case 'doc': $enclosure->type = 'application/msword'; break;
								case 'txt': $enclosure->type = 'text/plain'; break;
								case 'html': $enclosure->type = 'text/html'; break;
								case 'htm': $enclosure->type = 'text/html'; break;
							}
							$enclosure->length = $fs;

							$item->guid = $podcast;
							$item->enclosure = $enclosure;
						}
						// Loads item info into rss array
						$doc->addItem($item);
					}
				}
			}
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Short description for 'checkForImage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $filename Parameter description (if any) ...
	 * @param      string $upath Parameter description (if any) ...
	 * @param      unknown $created Parameter description (if any) ...
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function checkForImage($filename, $upath, $created, $id)
	{
		$path = ResourcesHtml::build_path($created, $id, '');

		// Ensure the path has format of /path
		$path = DS . trim($path, DS);

		// Ensure the upath has format of /upath
		$upath = DS . trim($upath, DS);

		$d = @dir(JPATH_ROOT . $upath . $path);

		$images = array();

		if ($d) 
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(JPATH_ROOT . $upath . $path . DS . $img_file) 
				 && substr($entry, 0, 1) != '.' 
				 && strtolower($entry) !== 'index.html') 
				{
					if (preg_match("#bmp|jpg|png#i", $img_file)) 
					{
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		}

		$b = 0;
		$img = '';
		if ($images) 
		{
			foreach ($images as $ima)
			{
				if (substr($ima, 0, strlen($filename)) == $filename) 
				{
					$img = $ima;
					break;
				}
			}
		}

		if (!$img) 
		{
			return '';
		}

		$juri =& JURI::getInstance();

		// http://base/upath/path/img
		return rtrim($juri->base(), DS) . $upath . $path . DS . $img;
	}

	/**
	 * Call a plugin method
	 * NOTE: This view should normally only be called through AJAX
	 * 
	 * @return     string
	 */
	protected function plugin()
	{
		// Incoming
		$trigger = trim(JRequest::getVar('trigger', ''));

		// Ensure we have a trigger
		if (!$trigger) 
		{
			echo '<p class="error">' . JText::_('COM_RESOURCES_NO_TRIGGER_FOUND') . '</p>';
			return;
		}

		// Get Resources plugins
		JPluginHelper::importPlugin('resources');
		$dispatcher =& JDispatcher::getInstance();

		// Call the trigger
		$results = $dispatcher->trigger($trigger, array($this->_option));
		if (is_array($results)) 
		{
			$html = $results[0]['html'];
		}

		// Output HTML
		echo $html;
	}

	/**
	 * Download a file
	 * Runs through various permissions checks to ensure user has access
	 * 
	 * @return     void
	 */
	protected function download()
	{
		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		// Ensure we have a database object
		if (!$this->database) 
		{
			JError::raiseError(500, JText::_('COM_RESOURCES_DATABASE_NOT_FOUND'));
			return;
		}

		// Incoming
		$id    = JRequest::getInt('id',0);
		$alias = JRequest::getVar('alias','');
		$d     = JRequest::getVar('d', 'inline');

		//make sure we have a proper disposition
		if ($d != "inline" && $d != "attachment")
		{
			$d = "inline";
		}

		// Load the resource
		$resource = new ResourcesResource($this->database);
		if ($alias && !$resource->loadAlias($alias)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			return;
		} 
		elseif (!$resource->load($id)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			return;
		}

		// Check if the resource is for logged-in users only and the user is logged-in
		if (($token = JRequest::getVar('token', '', 'get'))) 
		{
			$token = base64_decode($token);

			jimport('joomla.utilities.simplecrypt');
			$crypter = new JSimpleCrypt();
			$session_id = $crypter->decrypt($token);

			$db	=& JFactory::getDBO();
			$query = "SELECT * FROM #__session WHERE session_id = " . $db->Quote($session_id);
			$db->setQuery($query);
			$session = $db->loadObject();

			$juser =& JFactory::getUser($session->userid);
			$juser->guest = 0;
			$juser->id = $session->userid;
			$juser->usertype = $session->usertype;
		} 
		else 
		{
			$juser =& JFactory::getUser();
		}

		if ($resource->access == 1 && $juser->get('guest')) 
		{
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		// Check if the resource is "private" and the user is allowed to view it
		if ($resource->access == 4  // private
		 || $resource->access == 3  // protected
		 || !$resource->standalone) // child, no direct access
		{
			if ($this->checkGroupAccess($resource, $juser)) 
			{
				JError::raiseError(403, JText::_('ALERTNOTAUTH'));
				return;
			}
		}

		// Ensure we have a path
		if (empty($resource->path)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $resource->path)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $resource->path)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $resource->path)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $resource->path)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $resource->path)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
			return;
		}

		// Get the configured upload path
		$base_path = $this->config->get('uploadpath', '/site/resources');
		if ($base_path) 
		{
			$base_path = DS . trim($base_path, DS);
		}

		// Does the path start with a slash?
		if (substr($resource->path, 0, 1) != DS) 
		{
			$resource->path = DS . $resource->path;
			// Does the beginning of the $resource->path match the config path?
			if (substr($resource->path, 0, strlen($base_path)) == $base_path) 
			{
				// Yes - this means the full path got saved at some point
			} 
			else 
			{
				// No - append it
				$resource->path = $base_path . $resource->path;
			}
		}

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $resource->path;

		// Ensure the file exist
		if (!file_exists($filename)) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		jimport('joomla.filesystem.file');
		$ext = strtolower(JFile::getExt($filename));
		if (!in_array($ext, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'pdf', 'htm', 'html', 'txt', 'json', 'xml')))
		{
			$d = 'attachment';
		}

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition($d);
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve()) 
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_RESOURCES_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}

	/**
	 * Download source code for a tool
	 * 
	 * @return     void
	 */
	protected function sourcecode()
	{
		// Get tool instance
		$tool = JRequest::getVar('tool', 0);

		// Ensure we have a tool
		if (!$tool) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			return;
		}

		ximport('Hubzero_Content_Server');

		// Load the tool version
		$tv = new ToolVersion($this->database);
		$tv->loadFromInstance($tool);

		// Concat tarball name for this version
		$tarname = $tv->toolname . '-r' . $tv->revision . '.tar.gz';

		// Get contribtool params
		$tparams =& JComponentHelper::getParams('com_tools');
		$tarball_path = $tparams->get('sourcecodePath');
		if (empty($tarball_path))
		{
			$tarball_path = "site/protected/source";
		}
		
		if ($tarball_path[0] != DS)
		{
			$tarball_path = rtrim(JPATH_ROOT . DS . $tarball_path, DS);
		}
		$tarpath = $tarball_path . DS . $tv->toolname . DS;
		$opencode = ($tv->codeaccess=='@OPEN') ? 1 : 0;

		// Is a tarball available?
		if (!file_exists($tarpath . $tarname)) 
		{
			// File not found
			JError::raiseError(404, JText::_('COM_RESOURCES_FILE_NOT_FOUND'));
			return;
		}

		if (!$opencode) 
		{
			// This tool is not open source
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return;
		}

		// Serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($tarpath . $tarname);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas($tarname);
		if (!$xserver->serve_attachment($tarpath . $tarname, $tarname, false)) 
		{ // @TODO fix byte range support
			JError::raiseError(404, JText::_('COM_RESOURCES_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}

	/**
	 * Display a license for a resource
	 * 
	 * @return     void
	 */
	protected function license()
	{
		// Get tool instance
		$resource = JRequest::getInt('resource', 0);
		$tool = JRequest::getVar('tool', '');

		// Ensure we have a tool to work with
		if (!$tool && !$resource) 
		{
			JError::raiseError(404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			return;
		}

		if ($tool)
		{
			// Load the tool version
			$row = new ToolVersion($this->database);
			$row->loadFromInstance($tool);
		}
		else 
		{
			$row = new ResourcesResource($this->database);
			$row->load($resource);

			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'license.php');

			$rt = new ResourcesLicense($this->database);
			$rt->load('custom' . $resource);

			$row->license = stripslashes($rt->text);
		}

		// Output HTML
		if ($row) 
		{
			// Set the page title
			$title = stripslashes($row->title) . ': ' . JText::_('COM_RESOURCES_LICENSE');

			// Write title
			$document =& JFactory::getDocument();
			$document->setTitle($title);
		} 
		else 
		{
			// Set the page title
			$title = JText::_('COM_RESOURCES_PAGE_UNAVAILABLE');
		}

		// Instantiate a new view
		$view = new JView(array('name'=>'license'));
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->row = $row;
		$view->title = $title;
		$view->tool = $tool;
		$view->no_html = JRequest::getVar('no_html', 0);

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * Download a citation for a resource
	 * 
	 * @return     void
	 */
	protected function citation()
	{
		$monthFormat = '%b';
		$yearFormat = '%Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$yearFormat = 'Y';
			$monthFormat = 'M';
			$tz = false;
		}

		// Get contribtool params
		$tconfig =& JComponentHelper::getParams('com_tools');

		// Incoming
		$id = JRequest::getInt('id', 0);
		$format = JRequest::getVar('format', 'bibtex');

		// Append DOI handle
		$revision = JRequest::getVar('rev', 0);
		$handle = '';
		if ($revision) 
		{
			$rdoi = new ResourcesDoi($this->database);
			$rdoi->loadDoi($id, $revision);

			if (isset($rdoi->doi) && $rdoi->doi && $tconfig->get('doi_shoulder'))
			{
				$handle = 'doi:' . $tconfig->get('doi_shoulder') . DS . strtoupper($rdoi->doi);
			}
			else if ($rdoi->doi_label)
			{
				$handle = 'doi:10254/' . $tconfig->get('doi_prefix') . $id . '.' . $rdoi->doi_label;
			}
		}

		// Load the resource
		$row = new ResourcesResource($this->database);
		$row->load($id);

		$thedate = ($row->publish_up != '0000-00-00 00:00:00')
				 ? $row->publish_up
				 : $row->created;

		$helper = new ResourcesHelper($row->id, $this->database);
		$helper->getUnlinkedContributors();
		$row->author = $helper->ul_contributors;

		// Build the download path
		$path = JPATH_ROOT . $this->config->get('webpath', '/site/resources');
		$date = $row->created;
		$dir_resid = Hubzero_View_Helper_Html::niceidformat($row->id);
		if ($date && preg_match("#([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})#", $date, $regs)) 
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		$path .= DS . $dir_year . DS . $dir_month . DS . $dir_resid . DS;

		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError('Error. Unable to create path.');
			}
		}

		// Build the URL for this resource
		$sef = JRoute::_('index.php?option=' . $this->_option . '&id=' . $row->id);

		$juri =& JURI::getInstance();
		$url = $juri->base() . ltrim($sef, DS);

		// Choose the format
		switch ($format)
		{
			case 'endnote':
				$doc = '';
				switch ($row->type)
				{
					case 'misc':
					default:
						$doc .= "%0 " . JText::_('COM_RESOURCES_GENERIC') . "\r\n";
						break; // generic
				}
				$doc .= "%D " . JHTML::_('date', $thedate, $yearFormat, $tz) . "\r\n";
				$doc .= "%T " . trim(stripslashes($row->title)) . "\r\n";

				$author_array = explode(';', $row->author);
				foreach($author_array as $auth)
				{
					$auth = preg_replace('/{{(.*?)}}/s', '', $auth);
					if (!strstr($auth, ',')) 
					{
						$bits = explode(' ', $auth);
						$n = array_pop($bits) . ', ';
						$bits = array_map('trim', $bits);
						$auth = $n . trim(implode(' ', $bits));
					}
					$doc .= "%A " . trim($auth) . "\r\n";
				}
				$doc .= "%U " . $url . "\r\n";
				if ($thedate) 
				{
					$doc .= "%8 " . JHTML::_('date', $thedate, $monthFormat, $tz) . "\r\n";
				}
				//$doc .= "\r\n";
				if ($handle) 
				{
					$doc .= "%1 " .'doi:'.  $handle;
					$doc .= "\r\n";
				}

				$file = 'resource' . $id . '.enw';
				$mime = 'application/x-endnote-refer';
			break;

			case 'bibtex':
			default:
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'BibTex.php');

				$bibtex = new Structures_BibTex();
				$addarray = array();
				$addarray['type']  = 'misc';
				$addarray['cite']  = $this->_config['sitename'] . $row->id;
				$addarray['title'] = stripslashes($row->title);
				$auths = explode(';', $row->author);
				for ($i=0, $n=count($auths); $i < $n; $i++)
				{
					$author = trim($auths[$i]);
					$author = preg_replace('/\{\{(.+)\}\}/i','',$author);
					if (strstr($author, ',')) 
					{
						$author_arr = explode(',', $author);
						$author_arr = array_map('trim', $author_arr);

						$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? trim($author_arr[1]) : '';
						$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? trim($author_arr[0]) : '';
					} 
					else 
					{
						$author_arr = explode(' ',$author);
						$author_arr = array_map('trim',$author_arr);

						$last = array_pop($author_arr);
						$addarray['author'][$i]['first'] = (count($author_arr) > 0) ? implode(' ', $author_arr) : '';
						$addarray['author'][$i]['last']  = ($last) ? trim($last) : '';
					}
				}
				$addarray['month'] = JHTML::_('date', $thedate, $monthFormat, $tz);
				$addarray['url']   = $url;
				$addarray['year']  = JHTML::_('date', $thedate, $yearFormat, $tz);
				if ($handle) 
				{
					$addarray['doi'] = $handle;
				}

				$bibtex->addEntry($addarray);

				$file = 'resource_' . $id . '.bib';
				$mime = 'application/x-bibtex';
				$doc = $bibtex->bibTex();
			break;
		}

		// Write the contents to a file
		$fp = fopen($path . $file, "w") or die("can't open file");
		fwrite($fp, $doc);
		fclose($fp);

		$this->serveup(false, $path, $file, $mime);

		die; // REQUIRED
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
	protected function serveup($inline = false, $p, $f, $mime)
	{
		$user_agent = (isset($_SERVER["HTTP_USER_AGENT"]))
					? $_SERVER["HTTP_USER_AGENT"]
					: $HTTP_USER_AGENT;

		// Clean all output buffers (needs PHP > 4.2.0)
		while (@ob_end_clean());

		$fsize = filesize($p . $f);
		$mod_date = date('r', filemtime($p.$f));

		$cont_dis = $inline ? 'inline' : 'attachment';

		header('Pragma: public');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');

		header('Content-Transfer-Encoding: binary');
		header(
			'Content-Disposition:' . $cont_dis . ';'
			. ' filename="' . $f . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize . ';'
		); //RFC2183
		header("Content-Type: " . $mime); // MIME type
		header("Content-Length: " . $fsize);

		// No encoding - we aren't using compression... (RFC1945)

		$this->readfile_chunked($p . $f);
		// The caller MUST 'die();'
	}

	/**
	 * Read file contents
	 * 
	 * @param      unknown $filename Parameter description (if any) ...
	 * @param      boolean $retbytes Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	protected function readfile_chunked($filename, $retbytes=true)
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
	 * Save tags on a resource
	 * 
	 * @return     void
	 */
	protected function savetags()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($this->juser->get('guest')) 
		{
			$this->view();
			return;
		}

		// Incoming
		$id      = JRequest::getInt('id', 0);
		$tags    = JRequest::getVar('tags', '');
		$no_html = JRequest::getInt('no_html', 0);

		// Process tags
		$rt = new ResourcesTags($this->database);
		$rt->tag_object($this->juser->get('id'), $id, $tags, 1, 0);

		if (!$no_html) 
		{
			// Push through to the resource view
			$this->view();
		}
	}

	/**
	 * Check if a user is authorized
	 * 
	 * @param      array $contributorIDs Authors of a resource
	 * @return     boolean True if user has access
	 */
	protected function _authorize($contributorIDs=array(), $resource=null)
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			if ($this->juser->authorize($this->_option, 'manage')) 
			{
				return true;
			}
		}
		else 
		{
			$this->config->set('access-admin-component', $this->juser->authorise('core.admin', null));
			$this->config->set('access-manage-component', $this->juser->authorise('core.manage', null));
			if ($this->config->get('access-admin-component') || $this->config->get('access-manage-component'))
			{
				return true;
			}
		}

		// Check if they're the resource creator
		if (is_object($resource) && $resource->created_by == $this->juser->get('id')) 
		{
			return true;
		}

		// Check if they're a resource "contributor"
		if (is_array($contributorIDs)) 
		{
			if (in_array($this->juser->get('id'), $contributorIDs)) 
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a user has access to a group-owned resource
	 * Uses current user session if no user object is supplied
	 *
	 * @param      object $resource ResourcesResource
	 * @param      object $juser    JUser (optional)
	 * @return     boolean True if user has access to a group-owned resource
	 */
	private function checkGroupAccess($resource, $juser=null)
	{
		//$juser =& Hubzero_Factory::getUser();
		if (!$juser) 
		{
			$juser =& JFactory::getUser();
		}
		if (!$juser->get('guest')) 
		{
			// Check if they're a site admin (from Joomla)
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				if ($juser->authorize($this->_option, 'manage')) 
				{
					return false;
				}
			}
			else 
			{
				$this->config->set('access-admin-component', $juser->authorise('core.admin', null));
				$this->config->set('access-manage-component', $juser->authorise('core.manage', null));
				if ($this->config->get('access-admin-component') || $this->config->get('access-manage-component'))
				{
					return true;
				}
			}

			ximport('Hubzero_User_Helper');
			$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = $this->getUsersGroups($xgroups);
		} 
		else 
		{
			$usersgroups = array();
		}

		// Get the list of groups that can access this resource
		$allowedgroups = $resource->getGroups();
		if ($resource->standalone != 1) 
		{
			$helper = new ResourcesHelper($resource->id, $this->database);
			$helper->getParents();
			$parents = $helper->parents;
			if (count($parents) == 1) 
			{
				$p = new ResourcesResource($this->database);
				$p->load($parents[0]->id);
				$allowedgroups = $p->getGroups();
			}
		}
		$this->allowedgroups = $allowedgroups;

		// Find what groups the user has in common with the resource, if any
		$common = array_intersect($usersgroups, $allowedgroups);

		// Make sure they have the proper group access
		$restricted = false;
		if ($resource->access == 4 || $resource->access == 3) 
		{
			// Are they logged in?
			if ($juser->get('guest')) 
			{
				// Not logged in
				$restricted = true;
			} 
			else 
			{
				// Logged in

				// Check if the user is apart of the group that owns the resource
				// or if they have any groups in common
				if (!in_array($resource->group_owner, $usersgroups) && count($common) < 1) 
				{
					$restricted = true;
				}
			}
		}
		if (!$resource->standalone) 
		{
			if (!isset($p) && isset($parents) && count($parents) == 1) 
			{
				$p = new ResourcesResource($this->database);
				$p->load($parents[0]->id);
			}
			if (isset($p) && ($p->access == 4 || $p->access == 3) && count($common) < 1) 
			{
				$restricted = true;
			}
		}
		return $restricted;
	}

	/**
	 * Push group aliases into an array for easier searching
	 * 
	 * @param      array $groups Users' groups
	 * @return     array
	 */
	public function getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) 
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) 
				{
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}

	/**
	 * Generate the full (absolute) path to a file
	 * 
	 * @param      string $path Relative path
	 * @return     string
	 */
	private function fullPath($path)
	{
		if (substr($path, 0, 7) == 'http://'
		 || substr($path, 0, 8) == 'https://'
		 || substr($path, 0, 6) == 'ftp://'
		 || substr($path, 0, 9) == 'mailto://'
		 || substr($path, 0, 9) == 'gopher://'
		 || substr($path, 0, 7) == 'file://'
		 || substr($path, 0, 7) == 'news://'
		 || substr($path, 0, 7) == 'feed://'
		 || substr($path, 0, 6) == 'mms://') {
			// Do nothing
		} 
		else 
		{
			$uploadPath = DS . trim($this->config->get('uploadpath', '/site/resources'), DS);

			$path = DS . trim($path, DS);
			if (substr($path, 0, strlen($uploadPath)) == $uploadPath) 
			{
				// Do nothing
			} 
			else 
			{
				$path = $uploadPath . $path;
			}
		}

		$juri =& JURI::getInstance();

		return rtrim($juri->base(), DS) . $path;
	}

	/**
	 * Check if a user has access to a tool
	 * 
	 * @param      integer $toolid Tool ID
	 * @return     boolean True if user has access, false if not
	 */
	private function check_toolaccess($toolid)
	{
		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage')) 
		{
			return true;
		}

		// Create a Tool object
		$obj = new Tool($this->database);
		// check if user in tool dev team
		$developers = $obj->getToolDevelopers($toolid);
		if ($developers) 
		{
			foreach ($developers as $dv)
			{
				if ($dv->uidNumber == $this->juser->get('id')) 
				{
					return true;
				}
			}
		}

		return false;
	}
}

