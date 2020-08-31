<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Site\Controllers;

use Components\Resources\Models\Entry;
use Components\Resources\Models\Type;
use Components\Resources\Models\Doi;
use Components\Resources\Models\License;
use Components\Resources\Models\Audience\Level;
use Components\Resources\Models\MediaTracking;
use Components\Resources\Models\MediaTracking\Detailed;
use Components\Resources\Helpers\Html;
use Components\Resources\Helpers\Tags;
use Components\Resources\Helpers\Hubpresenter;
use Components\Resources\Helpers\Helper;
use Hubzero\Component\SiteController;
use stdClass;
use Document;
use Pathway;
use Request;
use Route;
use Event;
use Lang;
use User;
use App;

/**
 * Resources controller class
 */
class Resources extends SiteController
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations
	 * @return  void
	 */
	public function __construct($config=array())
	{
		$this->_base_path = dirname(__DIR__);
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
	 * @return  void
	 */
	public function execute()
	{
		$task = Request::getCmd('task', '');
		$this->_id    = Request::getInt('id', 0);
		$this->_alias = Request::getString('alias', '');
		$this->_resid = Request::getInt('resid', 0);

		if ($this->_resid && !$task)
		{
			Request::setVar('task', 'play');
		}
		if (($this->_id || $this->_alias) && !$task)
		{
			Request::setVar('task', 'view');
		}

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (Pathway::count() <= 1 && $this->_task)
		{
			switch ($this->_task)
			{
				case 'browse':
					if ($this->_task_title)
					{
						Pathway::append(
							$this->_task_title,
							'index.php?option=' . $this->_option . '&task=' . $this->_task
						);
					}
				break;
				case 'browsetags':
					if ($this->_task_title)
					{
						Pathway::append(
							$this->_task_title,
							'index.php?option=' . $this->_option . '&type=' . $this->type
						);
					}
				break;
				default:
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
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
			$this->_title = Lang::txt(strtoupper($this->_option));
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
						$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
					break;
				}
			}
		}

		Document::setTitle($this->_title);
	}

	/**
	 * Component front page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Get major types
		$categories = Type::getMajorTypes();

		$this->view
			->set('categories', $categories)
			->set('title', $this->_title)
			->setName('intro')
			->setLayout('default')
			->display();
	}

	/**
	 * Browse entries
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

		// Incoming
		$filters = array(
			'type'   => Request::getString('type', ''),
			'sortby' => Request::getCmd('sortby', $default_sort),
			'limit'  => Request::getInt('limit', Config::get('list_limit')),
			'start'  => Request::getInt('limitstart', 0),
			'search' => Request::getString('search', ''),
			'tag'    => trim(Request::getString('tag', '', 'request', 'none', 2)),
			'tag_ignored' => array(),
			'access' => [0, 3]
		);
		if (!in_array($filters['sortby'], array('date', 'date_published', 'date_created', 'date_modified', 'title', 'rating', 'ranking', 'random')))
		{
			App::abort(404, Lang::txt('Invalid sort value of "%s" used.', $filters['sortby']));
		}

		if (!User::isGuest())
		{
			$filters['access'][] = 1;
		}

		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'tags.php';

			$tagging = new Tags(0);
			$tags = $tagging->parseTags($filters['tag']);
			if (count($tags) > 5)
			{
				$keep = array();
				foreach ($tags as $i => $tag)
				{
					if ($i < 5)
					{
						$keep[] = $tag;
					}
					else
					{
						$filters['tag_ignored'][] = $tag;
					}
				}
				$filters['tag'] = implode(',', $keep);
			}
		}

		// Determine if user can edit
		$authorized = $this->_authorize();

		// Get major types
		$types = Type::getMajorTypes();

		// Get type if not given
		$this->_title = Lang::txt(strtoupper($this->_option)) . ': ';

		if (!is_numeric($filters['type']))
		{
			// Normalize the title
			// This is so we can determine the type of resource to display from the URL
			// For example, /resources/onlinepresentation => Oneline Presentation
			foreach ($types as $type)
			{
				if (trim($filters['type']) == $type->get('alias'))
				{
					$filters['type'] = $type->get('id');

					$this->_title .= $type->get('type');
					$this->_task_title = $type->get('type');
					break;
				}
			}
		}

		$query = Entry::all()
			->select('#__resources.id')
			->select('title')
			->select('params')
			->select('access')
			->select('ranking')
			->select('type')
			->select('rating')
			->select('#__resources.created')
			->select('#__resources.modified')
			->select('publish_up')
			->select('introtext')
			->select('fulltxt');

		$r = $query->getTableName();

		$query->whereEquals($r . '.standalone', 1);
		$query->whereIn($r . '.access', $filters['access']);

		if ($filters['tag'] != '')
		{
			// Aliased to avoid ID collisions
			$query->select('#__tags.id', 'tag_id');

			$to = \Components\Tags\Models\Objct::blank()->getTableName();
			$tg = \Components\Tags\Models\Tag::blank()->getTableName();

			$cloud = new Tags();
			$tags = $cloud->parse($filters['tag']);

			$query->join($to, $to . '.objectid', $r . '.id');
			$query->join($tg, $tg . '.id', $to . '.tagid', 'inner');
			$query->whereEquals($to . '.tbl', 'resources');
			$query->whereIn($tg . '.tag', $tags);
		}

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$query->whereLike($r . '.title', $filters['search'], 1)
					->orWhereLike($r . '.fulltxt', $filters['search'], 1)
					->resetDepth();
		}

		if ($filters['type'])
		{
			$query->whereEquals($r . '.type', $filters['type']);
		}

		$query->whereEquals($r . '.publish_up', '0000-00-00 00:00:00', 1)
			->orWhere($r . '.publish_up', 'IS', null, 1)
			->orWhere($r . '.publish_up', '<=', Date::toSql(), 1)
			->resetDepth();

		$query->whereEquals($r . '.publish_down', '0000-00-00 00:00:00', 1)
			->orWhere($r . '.publish_down', 'IS', null, 1)
			->orWhere($r . '.publish_down', '>=', Date::toSql(), 1)
			->resetDepth();

		$query->whereEquals($r . '.published', Entry::STATE_PUBLISHED);

		switch ($filters['sortby'])
		{
			case 'date_created':
				$query->order($r . '.created', 'desc');
				break;
			case 'date_modified':
				$query->order($r . '.modified', 'desc');
				break;
			case 'title':
				$query->order($r . '.title', 'asc');
				$query->order($r . '.publish_up', 'asc');
				break;
			case 'rating':
				$query->order($r . '.rating', 'desc');
				$query->order($r . '.times_rated', 'desc');
				break;
			case 'ranking':
				$query->order($r . '.ranking', 'desc');
				break;
			case 'date':
			case 'date_published':
			default:
				$query->order($r . '.publish_up', 'desc');
				$query->order($r . '.created', 'desc');
				break;
		}

		$results = $query
			->paginated()
			->rows();

		if (!$filters['type'])
		{
			$this->_title .= Lang::txt('COM_RESOURCES_ALL');
			$this->_task_title = Lang::txt('COM_RESOURCES_ALL');
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('types', $types)
			->set('filters', $filters)
			->set('authorized', $authorized)
			->set('title', $this->_title)
			->set('config', $this->config)
			->set('results', $results)
			->setName('browse')
			->setLayout('default')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Browse resources by tags
	 *
	 * @return  void
	 */
	public function browsetagsTask()
	{
		// Check if we're using this view type
		if ($this->config->get('browsetags') == 'off')
		{
			return $this->browseTask();
		}

		// Incoming
		$tag  = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(Request::getString('tag', '')));
		$tag2 = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(Request::getString('with', '')));
		$type = strtolower(Request::getString('type', 'tools'));

		// default tag in tag browser is config var
		$supportedtag = $this->config->get('supportedtag');
		$supportedtag_default = $this->config->get('browsetags_defaulttag', '');
		if (!$tag && $supportedtag_default != '' && $type == 'tools')
		{
			$tag = $supportedtag_default;
		}

		// Get major types
		$types = Type::getMajorTypes();

		// Normalize the title
		// This is so we can determine the type of resource to display from the URL
		$activetype  = 0;
		$activetitle = '';
		foreach ($types as $typ)
		{
			if ($type == $typ->get('alias'))
			{
				$activetype  = $typ->get('id');
				$activetitle = $typ->get('type');
			}
		}

		// Ensure we have a type to display
		if (!$activetype)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
		}

		// Determine if user can edit
		$authorized = $this->_authorize();

		// Set the default sort
		$default_sort = 'rating';
		if ($this->config->get('show_ranking'))
		{
			$default_sort = 'ranking';
		}

		// Set some filters
		$filters = array(
			'tag'    => ($tag2 ? $tag2 : ''),
			'type'   => $activetype,
			'sortby' => $default_sort,
			'standalone' => 1,
			'published' => 1,
			'now'    => Date::toSql(),
			'limit'  => 10,
			'start'  => 0,
			'access' => array(0)
		);

		if (!User::isGuest())
		{
			$filters['access'][] = 1;
		}

		$query = Entry::allWithFilters($filters);

		/*$r = $query->getTableName();

		$query->whereEquals($r . '.standalone', 1);

		if ($filters['type'])
		{
			$query->whereEquals($r . '.type', $activetype);
		}

		if ($filters['tag'] != '')
		{
			$to = \Components\Tags\Models\Objct::blank()->getTableName();
			$tg = \Components\Tags\Models\Tag::blank()->getTableName();

			$cloud = new Tags();
			$tags = $cloud->parse($filters['tag']);

			$query->join($to, $to . '.objectid', $r . '.id');
			$query->join($tg, $tg . '.id', $to . '.tagid', 'inner');
			$query->whereEquals($to . '.tbl', 'resources');
			$query->whereIn($tg . '.tag', $tags);
		}*/

		// Run query with limit
		$results = $query
			->order($filters['sortby'], 'desc')
			->limit($filters['limit'])
			->rows();

		$this->type = $type;
		if ($activetitle)
		{
			$this->_task_title = $activetitle;
		}
		else
		{
			$this->_task_title = Lang::txt('COM_RESOURCES_ALL');
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('filters', $filters)
			->set('types', $types)
			->set('type', $type)
			->set('title', $this->_title)
			->set('config', $this->config)
			->set('results', $results)
			->set('authorized', $authorized)
			->set('tag', $tag)
			->set('tag2', $tag2)
			->set('supportedtag', $supportedtag)
			->setName('browse')
			->setLayout('tags')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Display a level of the tag browser
	 * NOTE: This view should only be called through AJAX
	 *
	 * @return  void
	 */
	public function browserTask()
	{
		// Incoming
		$level = Request::getInt('level', 0);

		// A container for info to pass to the HTML for the view
		$bits = array();
		$bits['supportedtag'] = $this->config->get('supportedtag');

		// Process the level
		switch ($level)
		{
			case 1:
				// Incoming
				$bits['type'] = Request::getInt('type', 7);
				$bits['id']   = Request::getInt('id', 0);
				$bits['tg']   = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(Request::getString('input', '')));
				$bits['tg2']  = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(Request::getString('input2', '')));

				$rt = new Tags($bits['id']);

				// Get tags that have been assigned
				$bits['tags'] = $rt->get_tags_with_objects($bits['id'], $bits['type'], $bits['tg2']);

				$bits['type'] = Type::oneOrNew($bits['type']);
			break;

			case 2:
				// Incoming
				$bits['type']    = Request::getInt('type', 7);
				$bits['id']      = Request::getInt('id', 0);
				$bits['tag']     = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(Request::getString('input', '')));
				$bits['tag2']    = preg_replace("/[^a-zA-Z0-9]/", '', strtolower(Request::getString('input2', '')));
				$bits['sortby']  = Request::getWord('sortby', 'title');
				$bits['filter']  = Request::getArray('filter', array('level0', 'level1', 'level2', 'level3', 'level4'));
				$bits['ranking'] = $this->config->get('show_ranking');

				if ($bits['tag'] == $bits['tag2'])
				{
					$bits['tag2'] = '';
				}

				// Get parameters
				$bits['params'] = $this->config;

				// Get extra filter options
				$bits['filters'] = array();
				if ($this->config->get('show_audience') && $bits['type'] == 7)
				{
					include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'audience.php';

					$bits['filters'] = Level::all();
				}

				$rt = new Tags($bits['id']);
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

				$bits['type'] = Type::oneOrNew($bits['type']);

				$bits['supportedtagusage'] = $rt->getTagUsage($bits['supportedtag'], 'id');
			break;

			case 3:
				// Incoming (should be a resource ID)
				$id = Request::getInt('input', 0);

				$model = Entry::getInstance($id);

				if (!$model->get('id'))
				{
					App::abort(404);
				}

				$rt = new Tags($id);
				$bits['rt'] = $rt;
				$bits['config'] = $this->config;
				$bits['params'] = $model->params;

				// Get resource
				$model->set('ranking', round($model->get('ranking'), 1));

				// Generate the SEF
				$sef = Route::url($model->link());

				$bits['authorized'] = $model->access('edit'); //$this->_authorize($helper->contributorIDs, $resource);

				$firstChild = $model->children()
					->whereEquals('standalone', 0)
					->whereEquals('published', Entry::STATE_PUBLISHED)
					->ordered()
					->rows()
					->first();

				// Get the first child
				if ($firstChild || $model->isTool())
				{
					$xact = 'data-pop-out="true"';
					$bits['primary_child'] = Html::primary_child($this->_option, $model, $firstChild, $xact);
				}

				// Get the sections
				$bits['sections'] = Event::trigger('resources.onResources', array($model, $this->_option, array('about'), 'metadata'));

				// Fill our container
				$bits['resource'] = $model;
				$bits['sef'] = $sef;
			break;
		}

		// Output HTML
		$this->view
			->set('config', $this->config)
			->set('level', $level)
			->set('bits', $bits)
			->setName('browse')
			->setLayout('tags_list')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * 'play' a resource
	 * This displays a Breeze presentation, iframe, Google doc viewer, etc.
	 *
	 * @return  void
	 */
	public function playTask()
	{
		// Incoming
		$this->resid = Request::getInt('resid', 0);
		$id = Request::getInt('id', 0);

		$resource = Entry::getInstance($id);

		if (!$resource->get('id'))
		{
			App::abort(404);
		}

		// Do we have a child ID?
		if (!$this->resid)
		{
			// No ID, default to the first child
			//$helper->getFirstChild();
			$activechild = $resource->children()
				->whereEquals('published', Entry::STATE_PUBLISHED)
				->order('ordering', 'asc')
				->limit(1)
				->start(0)
				->row();

			$this->resid = $activechild->get('id');
		}

		// We have an ID, load it
		if (!isset($activechild))
		{
			$activechild = Entry::oneOrFail($this->resid);
		}

		// Store the object in our registry
		$this->activechild = $activechild;

		// Viewing via AJAX?
		$no_html = Request::getInt('no_html', 0);

		if ($no_html)
		{
			//$resource = Entry::oneOrFail($id);
			// Instantiate a new view
			//$this->view->resid = $this->resid;
			//$this->view->fsize = 0;

			// Output HTML
			$this->view
				->set('option', $this->_option)
				->set('config', $this->config)
				->set('resource', $resource)
				->set('activechild', $activechild)
				->set('no_html', $no_html)
				->setErrors($this->getErrors())
				->setLayout('play')
				->setName('view')
				->display();
			return;
		}

		// Push on through to the view
		$this->viewTask();
	}

	/**
	 * Select a presentation
	 *
	 * @return  void
	 */
	protected function selectPresentation()
	{
		$presentation = Request::getInt('presentation', 0);

		$firstchild = Entry::oneOrFail($presentation)
			->whereEquals('published', Entry::STATE_PUBLISHED)
			->children()
			->order('ordering', 'asc')
			->limit(1)
			->start(0)
			->row();

		$resid = $firstChild->id;

		App::redirect(
			Route::url('index.php?option=com_resources&id=' . $presentation . '&task=watch&resid=' . $resid . '&tmpl=component')
		);
	}

	/**
	 * Perform a some setup needed for presenter()
	 *
	 * @return  array
	 */
	protected function preWatch()
	{
		//var to hold error messages
		$errors = array();

		//inlude the HUBpresenter library
		require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'hubpresenter.php';

		//get the presentation id
		//$id = Request::getInt('id', '');
		$resid = Request::getInt('resid', '');
		if (!$resid)
		{
			$this->setError(Lang::txt('Unable to find presentation.'));
		}

		//load resource
		$activechild = Entry::oneOrFail($resid);

		$path = '';
		// match YYYY/MM/#/something
		if (preg_match('/(\d{4}\/\d{2}\/\d+)\/.+/i', $activechild->path, $matches))
		{
			$path = '/' . rtrim($matches[1], '/');
		}

		//base url for the resource
		$base = substr(PATH_APP, strlen(PATH_ROOT)) . DS . trim($this->config->get('uploadpath', '/site/resources'), DS);

		//build the rest of the resource path and combine with base
		$path = $path ? $path : $activechild->relativepath(); //Html::build_path($activechild->created, $activechild->id, '');
		$path = $base . $path;

		// we must have a folder
		if (!\Filesystem::exists(PATH_ROOT . DS . $path))
		{
			$this->setError(Lang::txt('Folder containing assets does not exist.'));

			$return = array();
			$return['errors'] = $this->getErrors();
			$return['content_folder'] = $path;
			$return['manifest'] = null;
			return $return;
		}

		//check to make sure we have a presentation document defining cuepoints, slides, and media
		//$manifest_path_json = PATH_ROOT . $path . DS . 'presentation.json';
		$manifests = \Filesystem::files(PATH_ROOT . DS . $path, '.json');
		$manifest_path_json = (isset($manifests[0])) ? $manifests[0] : null;
		$manifest_path_xml  = PATH_ROOT . $path . DS . 'presentation.xml';

		//check if the formatted json exists
		if (!file_exists(PATH_ROOT . $path . DS . $manifest_path_json))
		{
			//check to see if we just havent converted yet
			if (!file_exists($manifest_path_xml))
			{
				$this->setError(Lang::txt('Missing outline used to build presentation.'));
			}
			else
			{
				$job = Hubpresenter::createJsonManifest($path, $manifest_path_xml);
				if ($job != '')
				{
					$this->setError($job);
				}
			}
		}

		//path to media
		$media_path = PATH_ROOT . $path;

		//check if path exists
		if (!is_dir($media_path))
		{
			$this->setError(Lang::txt('Path to media does not exist.'));
		}
		else
		{
			//get all files matching  /.mp4|.webs|.ogv|.m4v|.mp3/
			$media = \Filesystem::files($media_path, '.mp4|.webm|.ogv|.m4v|.mp3|.ogg', false, false);
			$ext = array();
			foreach ($media as $m)
			{
				$parts = explode('.', $m);
				$ext[] = array_pop($parts);
			}

			//if we dont have all the necessary media formats
			if ((in_array('mp4', $ext) && count($ext) < 3) || (in_array('mp3', $ext) && count($ext) < 2))
			{
				$this->setError(Lang::txt('Missing necessary media formats for video or audio.'));
			}

			//make sure if any slides are video we have three formats of video and backup image for mobile
			$slide_path = $media_path . DS . 'slides';
			$slides = \Filesystem::files($slide_path, '', false, false);

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
					$this->setError(Lang::txt('Video Slides must be Uploaded in the Three Standard Formats. You currently only have ' . count($v) . " ({$k}." . implode(", {$k}.", array_keys($v)) . ').'));
				}

				if (!file_exists($slide_path . DS . $k .'.png') && !file_exists($slide_path . DS . $k .'.jpg'))
				{
					$this->setError(Lang::txt('Slides containing video must have a still image of the slide for mobile support. Please upload an image with the filename "' . $k . '.png" or "' . $k . '.jpg".'));
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
	 * @return  void
	 */
	public function watchTask()
	{
		$parent = $this->_id;
		$child = Request::getInt('resid', '');

		//media tracking object
		require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'mediatracking.php';

		//get tracking for this user for this resource
		$tracking = MediaTracking::oneByUserAndResource(User::get('id'), $child);

		//check to see if we already have a time query param
		$hasTime = (Request::getString('time', '') != '') ? true : false;

		//do we want to redirect user with time added to url
		if (is_object($tracking) && !$hasTime && $tracking->current_position > 0 && $tracking->current_position != $tracking->object_duration)
		{
			$redirect = 'index.php?option=com_resources&task=watch&id='.$parent.'&resid='.$child;
			if (Request::getCmd('tmpl', '') == 'component')
			{
				$redirect .= '&tmpl=component';
			}

			//append current position to redirect
			$redirect .= '&time=' . gmdate("H:i:s", $tracking->current_position);

			//redirect
			App::redirect(Route::url($redirect, false), '', '', false);
		}

		//do we have javascript?
		$js = Request::getCmd('tmpl', '');
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
				// Instantiate a new view
				$this->view = new \Hubzero\Component\View(array(
					'name'   => 'view',
					'layout' => 'watch_error'
				));
				$this->view->errors = $errors;
				$this->view->display();
				return;
			}
			else
			{
				// Instantiate a new view
				$this->view = new \Hubzero\Component\View(array(
					'name'   => 'view',
					'layout' => 'watch'
				));
				$this->view->option         = $this->_option;
				$this->view->config         = $this->config;
				$this->view->database       = $this->database;
				$this->view->doc            = Document::getRoot();
				$this->view->manifest       = $manifest;
				$this->view->content_folder = $content_folder;
				$this->view->pid            = $parent;
				$this->view->resid          = $child;

				// Output HTML
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}

				$this->view->display();
				return;
			}
		}

		$this->viewTask();
	}

	/**
	 * Display an HTML5 video
	 *
	 * @return  void
	 */
	public function videoTask()
	{
		//get the request vars
		$parent = Request::getInt('id', 0);
		$child  = Request::getInt('resid', '');

		if (!$parent || !$child)
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		// Load the resource
		$model = Entry::oneOrFail($parent);

		// Make sure we got a result from the database
		if ($model->isDeleted())
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		// Make sure the resource is published and standalone
		if (!$model->get('standalone')) // || !$this->model->isPublished())
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// Is the visitor authorized to view this resource?
		if (User::isGuest() && ($model->access == 1 || $model->access == 4))
		{
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url($model->link(), false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
				Lang::txt('COM_RESOURCES_ALERTLOGIN_REQUIRED'),
				'warning'
			);
		}

		if ($model->get('group_owner') && !$model->access('view-all'))
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH_GROUP', $model->get('group_owner'), Route::url('index.php?option=com_groups&cn=' . $model->get('group_owner'))));
		}

		if (!$model->access('view'))
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// Load resource
		$activechild = Entry::oneOrFail($child);

		// Check to see if we have a manifest
		if (!$this->videoManifestExistsForResource($activechild))
		{
			$this->createVideoManifestForResource($activechild);
		}

		// Get manifest
		$manifest = $this->getVideoManifestForResource($activechild);

		if (!file_exists(PATH_APP . $manifest))
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		$manifest = json_decode(file_get_contents(PATH_APP . $manifest));

		// Media tracking object
		require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'mediatracking.php';

		// Get tracking for this user for this resource
		$tracking = MediaTracking::oneByUserAndResource(User::get('id'), $activechild->id);

		// Check to see if we already have a time query param
		$hasTime = (Request::getString('time', '') != '') ? true : false;

		// Do we want to redirect user with time added to url
		if (is_object($tracking) && !$hasTime && $tracking->current_position > 0 && $tracking->current_position != $tracking->object_duration)
		{
			$redirect = 'index.php?option=com_resources&task=video&id=' . $parent . '&resid=' . $child;
			if (Request::getCmd('tmpl', '') == 'component')
			{
				$redirect .= '&tmpl=component';
			}

			// Append current position to redirect
			$redirect .= '&time=' . gmdate("H:i:s", $tracking->current_position);

			// Redirect
			App::redirect(Route::url($redirect, false), '', '', false);
		}

		if (!Request::getInt('no_html'))
		{
			// Set breadcrumbs
			Pathway::append(
				stripslashes($model->type->type),
				Route::url('index.php?option=' . $this->_option . '&type=' . $model->type->alias)
			);

			Pathway::append(
				stripslashes($model->title),
				Route::url($model->link())
			);

			Pathway::append(
				Lang::txt('COM_RESOURCES_VIEW_PRESENTATION'),
				Route::url($model->link() . '&task=view&resid=' . $child)
			);

			// Write title
			Document::setTitle(Lang::txt(strtoupper($this->_option)) . ': ' . stripslashes($model->title));
		}

		// Instantiate a new view
		$this->view = new \Hubzero\Component\View(array(
			'name'   => 'view',
			'layout' => 'video'
		));
		$this->view->option   = $this->_option;
		$this->view->config   = $this->config;
		$this->view->database = $this->database;
		$this->view->resource = $activechild;
		$this->view->manifest = $manifest;

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Get Video Manifest for resource
	 *
	 * @param   object   $resource  HUB Resource
	 * @return  boolean
	 */
	private function getVideoManifestForResource($resource)
	{
		// Base url for the resource
		$base = DS . trim($this->config->get('uploadpath'), DS);

		$path = '';
		// Match YYYY/MM/#/something
		if (preg_match('/(\d{4}\/\d{2}\/\d+)\/.+/i', $resource->path, $matches))
		{
			$path = '/' . rtrim($matches[1], '/');
		}

		// Build the rest of the resource path and combine with base
		$path = $path ? $path : $resource->relativepath();

		// Get manifests
		$manifests = \Filesystem::files(PATH_APP . DS . $base . $path, '.json');

		// Return path to manifest if we have one
		return (count($manifests) > 0) ? $base . $path . DS . $manifests[0] : array();
	}

	/**
	 * Check for Video manifest file
	 *
	 * @param   object   $resource  HUB Resource
	 * @return  boolean
	 */
	private function videoManifestExistsForResource($resource)
	{
		// Get video manifest
		$manifest = $this->getVideoManifestForResource($resource);

		// Do we have a manifest already?
		return (count($manifest) < 1) ? false : true;
	}

	/**
	 * Create manifest file for video
	 *
	 * @param   object  $resource  HUB Resource
	 * @return  boolean
	 */
	private function createVideoManifestForResource($resource)
	{
		//base url for the resource
		//$base = DS . trim($this->config->get('uploadpath'), DS);

		//build the rest of the resource path and combine with base
		$path = $resource->filespace();

		//instantiate params object then parse resource attributes
		$attributes = $resource->attribs;

		//var to hold manifest data
		$manifest = new stdClass;
		$manifest->presentation = new stdClass;
		$manifest->presentation->title     = $resource->title;
		$manifest->presentation->type      = 'Video';
		$manifest->presentation->width     = intval($attributes->get('width', 0));
		$manifest->presentation->height    = intval($attributes->get('height', 0));
		$manifest->presentation->duration  = intval($attributes->get('duration', 0));
		$manifest->presentation->media     = array();
		$manifest->presentation->subtitles = array();

		//get the videos
		$videos = \Filesystem::files($path, '.mp4|.MP4|.ogv|.OGV|.webm|.WEBM');

		//add each video to manifest
		foreach ($videos as $k => $video)
		{
			// get info about video
			$videoInfo = pathinfo($video);

			// object to hold media type & source
			$media         = new stdClass;
			$media->type   = $videoInfo['extension'];
			$media->source = $path . DS . $video;

			// add media object to array of media
			$manifest->presentation->media[] = $media;
		}

		//get the subs
		$subtitles = \Filesystem::files($path, '.srt|.SRT');

		//add each subtitle to manifest
		foreach ($subtitles as $k => $subtitle)
		{
			//get name
			$info = pathinfo( $subtitle );
			$name = str_replace('-auto', '', $info['filename']);
			$name = ucfirst( $name );

			// object to hold subtitle info
			$sub = new stdClass;
			$sub->type     = 'SRT';
			$sub->name     = $name;
			$sub->source   = $path . DS . $subtitle;
			$sub->autoplay = (strstr($subtitle, '-')) ? 1 : 0;

			// add sub object to array of subtitles
			$manifest->presentation->subtitles[] = $sub;
		}

		//reset array of subs and media
		$manifest->presentation->media     = array_values($manifest->presentation->media);
		$manifest->presentation->subtitles = array_values($manifest->presentation->subtitles);

		// json encode manifest
		$manifest = json_encode($manifest, JSON_PRETTY_PRINT);

		// attempt to create manifest file
		if (!\Filesystem::write($path . DS . 'presentation.json', $manifest))
		{
			return false;
		}

		return true;
	}

	/**
	 * View a resource
	 *
	 * @return  void
	 */
	public function viewTask()
	{
		// Incoming
		$id       = Request::getInt('id', 0);            // Rsource ID (primary method of identifying a resource)
		$alias    = Request::getString('alias', '');        // Alternate method of identifying a resource
		$fsize    = Request::getString('fsize', '');        // A parameter to see file size without formatting

		// XSS fix. Revision gets pumped all over and dumped in URLs via plugins, easier to fix at the input instead of risking missing an output. See ticket 1416
		$revision = htmlentities(Request::getString('rev', ''));          // Get svk revision of a tool

		$tab      = Request::getCmd('active', 'about');  // The active tab (section)

		// Ensure we have an ID or alias to work with
		if (!$id && !$alias)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Load the resource
		$this->model = Entry::getInstance(($alias ? $alias : $id), $revision);

		// Make sure we got a result from the database
		if (!$this->model->get('id') || $this->model->isDeleted())
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		// Make sure the resource is published and standalone
		if (!$this->model->get('standalone')) // || !$this->model->isPublished())
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// Is the visitor authorized to view this resource?
		if (User::isGuest() && ($this->model->get('access') == 1 || $this->model->get('access') == 4))
		{
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url($this->model->link(), false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
				Lang::txt('COM_RESOURCES_ALERTLOGIN_REQUIRED'),
				'warning'
			);
		}

		if ($this->model->get('group_owner') && !$this->model->access('view'))
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH_GROUP', $this->model->get('group_owner'), Route::url('index.php?option=com_groups&cn=' . $this->model->get('group_owner'))));
		}

		if (!$this->model->access('view'))
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// // Make sure they have access to view this resource
		//if ($this->checkGroupAccess($this->model))
		//{
		//	App::abort(403, \Lang::txt('COM_RESOURCES_ALERTNOTAUTH_GROUP', $this->model->get('group_owner'), Route::url('index.php?option=com_groups&cn=' . $this->model->get('group_owner'))));
		//	return;
		//}

		// Build the pathway
		if ($this->model->inGroup())
		{
			// Alter the pathway to reflect a group owned resource
			$group = $this->model->group;

			if ($group)
			{
				Pathway::clear();

				Pathway::append(
					'Groups',
					Route::url('index.php?option=com_groups')
				);
				Pathway::append(
					stripslashes($group->get('description')),
					Route::url('index.php?option=com_groups&cn=' . $group->get('cn'))
				);
				Pathway::append(
					'Resources',
					Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=resources')
				);
				Pathway::append(
					stripslashes($this->model->type->get('type')),
					Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=resources&area=' . $this->model->type->get('alias'))
				);
			}
			else
			{
				Pathway::append(
					stripslashes($this->model->type->get('type')),
					Route::url('index.php?option=' . $this->_option . '&type=' . $this->model->type->get('alias'))
				);
			}
		}
		else
		{
			Pathway::append(
				stripslashes($this->model->type->get('type')),
				Route::url('index.php?option=' . $this->_option . '&type=' . $this->model->type->get('alias'))
			);
		}

		// Tool development version requested
		if (User::isGuest() && $revision == 'dev')
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// Access check for tools
		if ($this->model->isTool())
		{
			// if (development revision
			//   or (specific revision that is NOT published))
			if (($revision == 'dev')
			 or (!$revision && $this->model->get('published') != 1))
			{
				// Check if the user has access to the tool
				if (!$this->model->access('view-all'))
				{
					// Denied, punk! How do you like them apples?!
					App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
				}
			}
		}

		// Whew! Finally passed all the checks
		// Let's get down to business...

		// Get contribtool params
		$tconfig = \Component::params('com_tools');

		// Trigger the functions that return the areas we'll be using
		$cats = Event::trigger('resources.onResourcesAreas', array(
				$this->model
			)
		);

		// We need to do this here because we need some stats info to pass to the body
		/*if (isset($this->model->revision) && $this->model->revision)
		{
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
					if ($name == 'about' || $name == 'versions' || $name == 'supportingdocs')
					{
						$cts[] = $cat;
					}
				}
			}

			$cats = $cts;
		}*/

		// Get the sections
		$sections = Event::trigger('resources.onResources', array(
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
		if ($this->getTask() == 'play')
		{
			$activechild = null;

			if (is_object($this->activechild))
			{
				$activechild = $this->activechild;
			}

			$view = new \Hubzero\Component\View(array(
				'base_path' => $this->_base_path,
				'name'      => 'view',
				'layout'    => 'play'
			));
			$view->option      = $this->_option;
			$view->config      = $this->config;
			$view->tconfig     = $tconfig;
			$view->resource    = $this->model;
			$view->resid       = $this->resid;
			$view->activechild = $activechild;
			$view->no_html     = 0;
			$view->fsize       = 0;
			$view->setErrors($this->getErrors());

			$body = $view->loadTemplate();

			$cats[] = array(
				'play' => Lang::txt('COM_RESOURCES_PLAY')
			);
			$sections[] = array(
				'html'     => $body,
				'metadata' => '',
				'area'     => 'play'
			);
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
				// Instantiate a new view
				$this->view = new \Hubzero\Component\View(array(
					'name'   => 'view',
					'layout' => 'watch_error'
				));
				$this->view->setErrors($errors);
				$body = $this->view->loadTemplate();
			}
			else
			{
				// Instantiate a new view
				$view = new \Hubzero\Component\View(array(
					'base_path' => $this->_base_path,
					'name'      => 'view',
					'layout'    => 'watch'
				));
				$view->set('config', $this->config);
				//$view->tconfig        = $tconfig;
				//$view->database       = $this->database;
				$view->set('manifest', $manifest);
				$view->set('content_folder', $content_folder);
				$view->set('pid', $id);
				$view->set('resid', Request::getInt('resid', ''));
				$view->set('doc', Document::getRoot());

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
				'watch' => Lang::txt('Watch Presentation')
			);
			$sections[] = array(
				'html'     => $body,
				'metadata' => '',
				'area'     => 'watch'
			);
			$tab = 'watch';
		}

		// Write title
		Document::setTitle(Lang::txt(strtoupper($this->_option)) . ': ' . stripslashes($this->model->title));

		if ($canonical = $this->model->attribs->get('canonical', ''))
		{
			if (!preg_match('/^(https?:|mailto:|ftp:|gopher:|news:|file:|rss:)/i', $canonical))
			{
				$canonical = rtrim(Request::base(), '/') . '/' . ltrim($canonical, '/');
			}
			Document::addHeadLink($canonical, 'canonical');
		}

		Pathway::append(
			stripslashes($this->model->title),
			Route::url($this->model->link())
		);

		$type_alias = $this->model->type->get('alias');

		// Determine the layout we're using
		$layout = 'default';

		if ($type_alias
		 && (is_file(App::get('template')->path  . DS . 'html' . DS . $this->_option . DS . 'view' . DS . $type_alias . '.php')
			|| is_file(dirname(__DIR__) . DS . 'views' . DS . 'view' . DS . 'tmpl' . DS . $type_alias . '.php')))
		{
			$layout = $type_alias;
		}
		// Instantiate a new view
		$this->view->setLayout($layout);

		if ($this->model->isTool())
		{
			$this->view->set('thistool', $this->model->thistool);
			$this->view->set('curtool', $this->model->curtool);
			$this->view->set('alltools', $this->model->alltools);
			$this->view->set('revision', $this->model->revision);
		}
		$this->view->set('model', $this->model);
		$this->view->set('tconfig', $tconfig);
		$this->view->set('option', $this->_option);
		$this->view->set('cats', $cats);
		$this->view->set('tab', $tab);
		$this->view->set('sections', $sections);
		$this->view->setErrors($this->getErrors());

		// Output HTML
		if (Request::get('noview', null))
		{
			return $this->view->setName('view');
		}

		$this->view
			->setName('view')
			->display();
	}

	/**
	 * Display an RSS feed
	 *
	 * @return  void
	 */
	public function feedTask()
	{
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();

		// Incoming
		$id    = Request::getInt('id', 0);
		$alias = Request::getString('alias', '');

		// Ensure we have an ID or alias to work with
		if (!$id && !$alias)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		// Load the resource
		if ($alias)
		{
			$resource = Entry::getInstance($alias);
			$id = $resource->get('id');
		}
		else
		{
			$resource = Entry::getInstance($id);
			$alias = $resource->get('alias');
		}

		// Make sure we got a result from the database
		if (!$resource->get('id'))
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		// Make sure the resource is published and standalone
		if ($resource->get('published') == 0 || $resource->get('standalone') != 1)
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// Make sure they have access to view this resource
		if ($this->checkGroupAccess($resource))
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// Incoming
		$filters = array();
		if ($resource->get('type') == 2)
		{
			$filters['sortby'] = Request::getString('sortby', 'ordering');
		}
		else
		{
			$filters['sortby'] = Request::getString('sortby', 'ranking');
		}
		$filters['limit'] = Request::getInt('limit', 100);
		$filters['start'] = Request::getInt('limitstart', 0);
		$filters['year']  = Request::getInt('year', 0);
		$filters['id']    = $resource->get('id');

		$feedtype = Request::getString('content', 'audio');

		$rows = $resource->children()
			->whereEquals('published', Entry::STATE_PUBLISHED)
			->whereEquals('standalone', 1)
			->limit($filters['limit'])
			->start($filters['start'])
			->order($filters['sortby'], 'asc')
			->rows();

		$base = rtrim(Request::base(), '/');

		$title = $resource->title;
		$feedtypes_abr = array(" ", "slides", "audio", "video", "sd_video", "hd_video");
		$feedtypes_full = array(" & ", "Slides", "Audio", "Video", "SD full", "HD");
		$type = str_replace($feedtypes_abr, $feedtypes_full, $feedtype);
		$title = '[' . $type . '] ' . $title;

		// Build some basic RSS document information
		$dtitle = \Hubzero\Utility\Sanitize::clean(stripslashes($title));

		$doc->title = trim(\Hubzero\Utility\Str::truncate(html_entity_decode($dtitle), 250));
		$doc->description = htmlspecialchars(html_entity_decode(\Hubzero\Utility\Sanitize::clean(stripslashes($resource->introtext))), ENT_COMPAT, 'UTF-8');
		$doc->copyright = \Lang::txt('COM_RESOURCES_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));
		$doc->category = Lang::txt('COM_RESOURCES_RSS_CATEGORY');
		$doc->link = Route::url('index.php?option=' . $this->_option . '&id=' . $resource->id);

		$rt = new Tags($resource->id);
		$rtags = $rt->tags();
		$tagarray = array();
		$categories = array();
		$subcategories = array();
		if ($rtags)
		{
			foreach ($rtags as $tag)
			{
				if (substr($tag->get('tag'), 0, 6) == 'itunes')
				{
					$tbits = explode(':', $tag->get('raw_tag'));
					if (count($tbits) > 2)
					{
						$subcategories[] = end($tbits);
					}
					else
					{
						$categories[] = str_replace('itunes:', '', $tag->get('raw_tag'));
					}
				}
				elseif ($tag->get('admin') == 0)
				{
					$tagarray[] = $tag->get('raw_tag');
				}
			}
		}
		$tags = implode(', ', $tagarray);
		$tags = trim(\Hubzero\Utility\Str::truncate($tags, 250));
		$tags = rtrim($tags, ',');

		$author = '';
		foreach ($resource->authors()->ordered()->rows() as $con)
		{
			if ($con->get('role') != 'submitter')
			{
				$author = trim($con->get('name'));
				break;
			}
		}

		$doc->itunes_summary = html_entity_decode(\Hubzero\Utility\Sanitize::clean(stripslashes($resource->introtext)));
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

		$dimg = $this->_checkForImage($itunes_image_name, $this->config->get('uploadpath'), $resource->created, $resource->id);
		if ($dimg)
		{
			$dimage = new \Hubzero\Document\Type\Feed\Image();
			$dimage->url = $dimg;
			$dimage->title = trim(\Hubzero\Utility\Str::truncate(html_entity_decode($dtitle . ' ' . Lang::txt('COM_RESOURCES_RSS_ARTWORK')), 250));
			$dimage->link = $base . $doc->link;
			$doc->itunes_image = $dimage;
		}

		$owner = new \Hubzero\Document\Type\Feed\ItunesOwner;
		$owner->email = Config::get('mailfrom');
		$owner->name  = Config::get('sitename');

		$doc->itunes_owner = $owner;

		// Start outputing results if any found
		if (count($rows) > 0)
		{
			$all_logical_types = Type::all()
				->whereEquals('category', 28) // 28 means 'logical' types.
				->rows();

			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to resource
				$link = '/' . ltrim(Route::url('index.php?option=' . $this->_option . '&id=' . $row->id), '/');

				// Strip html from feed item description text
				$description = trim($row->introtext);
				$description = $description ?: trim($row->fulltxt);
				$description = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', stripslashes($description));
				$description = html_entity_decode(\Hubzero\Utility\Sanitize::stripAll($description));
				$description = strip_tags($description);

				$author = '';
				@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');

				// Get any podcast/vodcast files
				$podcast = '';

				$queried_logical_types = @explode(' ', $feedtype);

				if (is_null($queried_logical_types) || !is_array($queried_logical_types))
				{
					App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_FEED_BAD_REQUEST'));
					return;
				}

				$relevant_logical_types_by_id = array();
				foreach ($queried_logical_types as $queried)
				{
					$as_mnemonic = preg_replace('/[_-]/', ' ', $queried);
					foreach ($all_logical_types as $logical_type)
					{
						//if (preg_match_all('/Podcast \(([^()]+)\)/', $logical_type->type, $matches) == 1
						// && strcasecmp($matches[1][0], $as_mnemonic) == 0)
						if (preg_match_all('/Podcast \(([^()]+)\)/', $logical_type->type, $matches) == 1
						 && substr(strtolower($matches[1][0]), -strlen($as_mnemonic)) == $as_mnemonic)
						{
							$relevant_logical_types_by_id[$logical_type->id] = $logical_type;
							break;
						}
						elseif ($as_mnemonic == 'slides' && $logical_type->type == 'Presentation Slides')
						{
							$relevant_logical_types_by_id[$logical_type->id] = $logical_type;
							break;
						}
						elseif ($as_mnemonic == 'notes' && $logical_type->type == 'Lecture Notes')
						{
							$relevant_logical_types_by_id[$logical_type->id] = $logical_type;
							break;
						}
					}
				}

				$grandchildren = $row->children()
					->whereEquals('published', Entry::STATE_PUBLISHED)
					->ordered()
					->rows();

				$podcasts = array();
				$children = array();
				if ($grandchildren && count($grandchildren) > 0)
				{
					foreach ($grandchildren as $grandchild)
					{
						if (isset($relevant_logical_types_by_id[(int)$grandchild->get('logical_type')]))
						{
							if (stripslashes($grandchild->introtext) != '')
							{
								$gdescription = trim($grandchild->introtext);
								$gdescription = $gdescription ?: trim($grandchild->fulltxt);
								$gdescription = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', stripslashes($gdescription));
								$gdescription = html_entity_decode(\Hubzero\Utility\Sanitize::clean($gdescription));
								$gdescription = strip_tags($gdescription);
							}
							array_push($podcasts, $grandchild->path);
							array_push($children, $grandchild);
						}
					}
				}

				// Get the contributors of this resource
				$author = strip_tags($row->authorsList());

				$rtt = new Tags($row->id);
				$rtags = $rtt->render('string');
				if (trim($rtags))
				{
					$rtags = trim(\Hubzero\Utility\Str::truncate($rtags, 250));
					$rtags = rtrim($rtags, ',');
				}

				// Get attributes
				if ($children)
				{
					$attribs = $children[0]->attribs;
				}

				foreach ($podcasts as $podcast)
				{
					// Load individual item creator class
					$item = new \Hubzero\Document\Type\Feed\Item();
					$item->title       = $title;
					$item->link        = $link;
					$item->description = $description;
					$item->date        = $date;
					$item->category    = $row->type->get('type');
					$item->author      = $author;

					$img = $this->_checkForImage('ituness_artwork', $this->config->get('uploadpath'), $row->created, $row->id);
					if ($img)
					{
						$image = new \Hubzero\Document\Type\Feed\Image();
						$image->url   = $img;
						$image->title = $title . ' ' . Lang::txt('COM_RESOURCES_RSS_ARTWORK');
						$image->link  = $base . $link;

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
						$podcast = ltrim($this->_fullPath($podcast), '/');

						if (substr($podcastp, 0, strlen($this->config->get('uploadpath'))) == $this->config->get('uploadpath'))
						{
							// Do nothing
						}
						else
						{
							$podcastp = trim($this->config->get('uploadpath'), DS) . DS . ltrim($podcastp, DS);
						}
						$podcastp = PATH_APP . DS . ltrim($podcastp, DS);
						if (file_exists($podcastp))
						{
							$fs = filesize($podcastp);

							$enclosure = new \Hubzero\Document\Type\Feed\Enclosure;
							$enclosure->url = $podcast;
							switch (\Filesystem::extension($podcast))
							{
								case 'm4v':
									$enclosure->type = 'video/x-m4v';
									break;
								case 'mp4':
									$enclosure->type = 'video/mp4';
									break;
								case 'wmv':
									$enclosure->type = 'video/wmv';
									break;
								case 'mov':
									$enclosure->type = 'video/quicktime';
									break;
								case 'qt':
									$enclosure->type = 'video/quicktime';
									break;
								case 'mpg':
									$enclosure->type = 'video/mpeg';
									break;
								case 'mpeg':
									$enclosure->type = 'video/mpeg';
									break;
								case 'mpe':
									$enclosure->type = 'video/mpeg';
									break;
								case 'mp2':
									$enclosure->type = 'video/mpeg';
									break;
								case 'mpv2':
									$enclosure->type = 'video/mpeg';
									break;
								case 'mp3':
									$enclosure->type = 'audio/mpeg';
									break;
								case 'm4a':
									$enclosure->type = 'audio/x-m4a';
									break;
								case 'aiff':
									$enclosure->type = 'audio/x-aiff';
									break;
								case 'aif':
									$enclosure->type = 'audio/x-aiff';
									break;
								case 'wav':
									$enclosure->type = 'audio/x-wav';
									break;
								case 'ra':
									$enclosure->type = 'audio/x-pn-realaudio';
									break;
								case 'ram':
									$enclosure->type = 'audio/x-pn-realaudio';
									break;
								case 'ppt':
									$enclosure->type = 'application/vnd.ms-powerpoint';
									break;
								case 'pps':
									$enclosure->type = 'application/vnd.ms-powerpoint';
									break;
								case 'pdf':
									$enclosure->type = 'application/pdf';
									break;
								case 'doc':
									$enclosure->type = 'application/msword';
									break;
								case 'txt':
									$enclosure->type = 'text/plain';
									break;
								case 'html':
									$enclosure->type = 'text/html';
									break;
								case 'htm':
									$enclosure->type = 'text/html';
									break;
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
	}

	/**
	 * Short description for 'checkForImage'
	 *
	 * Long description (if any) ...
	 *
	 * @param   string   $filename
	 * @param   string   $upath
	 * @param   string   $created
	 * @param   integer  $id
	 * @return  string
	 */
	private function _checkForImage($filename, $upath, $created, $id)
	{
		$path = Html::build_path($created, $id, '');

		// Ensure the path has format of /path
		$path = DS . trim($path, DS);

		// Ensure the upath has format of /upath
		$upath = DS . trim($upath, DS);

		$d = @dir(PATH_APP . $upath . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(PATH_APP . $upath . $path . DS . $img_file)
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

		// http://base/upath/path/img
		return rtrim(Request::base(), '/') . $upath . $path . '/' . $img;
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
		$trigger = trim(Request::getString('trigger', ''));

		// Ensure we have a trigger
		if (!$trigger)
		{
			echo '<p class="error">' . Lang::txt('COM_RESOURCES_NO_TRIGGER_FOUND') . '</p>';
			return;
		}

		// Call the trigger
		$html = '';

		$results = Event::trigger('resources.' . $trigger, array($this->_option));
		if (is_array($results) && !empty($results))
		{
			foreach ($results as $result)
			{
				$html .= (is_array($result) ? $result['html'] : (string)$result);
			}
		}

		// Output HTML
		echo $html;
	}

	/**
	 * Download a file
	 * Runs through various permissions checks to ensure user has access
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Incoming
		$id    = Request::getInt('id', 0);
		$alias = Request::getString('alias', '');
		$d     = Request::getString('d', 'inline');

		//make sure we have a proper disposition
		if ($d != "inline" && $d != "attachment")
		{
			$d = "inline";
		}

		// Load the resource
		if ($alias)
		{
			$resource = Entry::oneByAlias($alias);

			if (!$resource->get('id'))
			{
				App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			}
		}
		// allow for temp resource uploads
		elseif (substr($id, 0, 4) == '9999')
		{
			$resource = Entry::blank();
			$resource->set('id', $id);
			$resource->set('standalone', 1);
			$resource->set('created', Date::of('now')->format('Y-m-d 00:00:00'));
		}
		else
		{
			$resource = Entry::oneOrNew($id);

			if (!$resource->get('id'))
			{
				App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			}
		}

		// Check if direct access is restricted. If so, look for a token
		$activeParams = $resource->params;
		$typeParams = $resource->type->params;
		$restrictDirectDownload = $activeParams->get('restrict_direct_access') ? $activeParams->get('restrict_direct_access') : $typeParams->get('restrict_direct_access');
		if ($restrictDirectDownload == 2)
		{
			Request::checkToken('get');
		}

		// Check if the resource is for logged-in users only and the user is logged-in
		if (($token = Request::getString('token', '', 'get')))
		{
			$token = base64_decode($token);

			$key = App::hash(@$_SERVER['HTTP_USER_AGENT']);
			$crypter = new \Hubzero\Encryption\Encrypter(
				new \Hubzero\Encryption\Cipher\Simple,
				new \Hubzero\Encryption\Key('simple', $key, $key)
			);
			$session_id = $crypter->decrypt($token);

			$session = \Hubzero\Session\Helper::getSession($session_id);

			$user = User::getInstance($session->userid);
			$user->set('guest', 0);
			$user->set('id', $session->userid);
			$user->set('usertype', $session->usertype);
		}
		else
		{
			$user = User::getInstance();
		}

		// Get parent Access level and use it if it is more restrictive
		if ($resource->standalone != 1)
		{
			$parents = $resource->parents;

			if (count($parents) == 1)
			{
				$parent = $parents->first();
			}
		}
		$accessLevel = (isset($parent) && ($parent->access > $resource->access)) ? $parent->access : $resource->access;
		if ($accessLevel == 1 && $user->isGuest())
		{
			//App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&id=' . $this->id . '&d=' . $d, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
				Lang::txt('COM_RESOURCES_ALERTLOGIN_REQUIRED'),
				'warning'
			);
		}

		// Check if the resource is "private" and the user is allowed to view it
		if ($accessLevel == 4  // private
		 || ($accessLevel == 3 && $resource->get('path')))  // protected -- We need to allow images in the sbtract to come through
		{
			if ($user->isGuest())
			{
				$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&id=' . $this->id . '&d=' . $d, false, true), 'server'));
				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . $return, false),
					Lang::txt('COM_RESOURCES_ALERTLOGIN_REQUIRED'),
					'warning'
				);
			}
			if ($this->checkGroupAccess($resource, $user))
			{
				App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
			}
		}

		if ($resource->get('standalone') && !$resource->get('path'))
		{
			$resource->set('path', $resource->filespace() . DS . 'media' . DS . Request::getString('file'));
		}

		$path = trim($resource->get('path'));
		$path = DS . trim($path, DS);

		// Ensure we have a path
		// Ensure resource is published - stemedhub #472
		if (!$path && $resource->published != 1)
		{
			App::abort(404, Lang::txt('COM_RESOURCES_FILE_NOT_FOUND'));
		}

		// Get the configured upload path
		$base_path = $resource->basepath();

		// Does the beginning of the $resource->path match the config path?
		if (substr($path, 0, strlen($base_path)) != $base_path)
		{
			// No - append it
			$path = $base_path . $path;
		}

		// Ensure the file exist
		if (!file_exists($path))
		{
			App::abort(404, Lang::txt('COM_RESOURCES_FILE_NOT_FOUND') . ' ' . $path);
		}

		$ext = strtolower(\Filesystem::extension($path));

		if (!in_array($ext, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'pdf', 'htm', 'html', 'txt', 'json', 'xml')))
		{
			$d = 'attachment';
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($path);
		$xserver->disposition($d);
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('COM_RESOURCES_SERVER_ERROR'));
		}

		exit;
	}

	/**
	 * Download source code for a tool
	 *
	 * @return  void
	 */
	public function sourcecodeTask()
	{
		// Get tool instance
		$tool = Request::getString('tool', 0);

		// Ensure we have a tool
		if (!$tool)
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		// Load the tool version
		$tv = new \Components\Tools\Tables\Version($this->database);
		$tv->loadFromInstance($tool);

		// Concat tarball name for this version
		$tarname = $tv->toolname . '-r' . $tv->revision . '.tar.gz';

		// Get contribtool params
		$tparams = Component::params('com_tools');
		$tarball_path = $tparams->get('sourcecodePath');
		if (empty($tarball_path))
		{
			$tarball_path = "site/protected/source";
		}

		if ($tarball_path[0] != DS)
		{
			$tarball_path = rtrim(PATH_APP . DS . $tarball_path, DS);
		}
		$tarpath = $tarball_path . DS . $tv->toolname . DS;
		$opencode = ($tv->codeaccess=='@OPEN') ? 1 : 0;

		// Is a tarball available?
		if (!file_exists($tarpath . $tarname))
		{
			// File not found
			App::abort(404, Lang::txt('COM_RESOURCES_FILE_NOT_FOUND'));
		}

		if (!$opencode)
		{
			// This tool is not open source
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTNOTAUTH'));
		}

		// Serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($tarpath . $tarname);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas($tarname);

		if (!$xserver->serve_attachment($tarpath . $tarname, $tarname, false))
		{
			App::abort(500, Lang::txt('COM_RESOURCES_SERVER_ERROR'));
		}

		exit;
	}

	/**
	 * Display a license for a resource
	 *
	 * @return  void
	 */
	public function licenseTask()
	{
		// Get tool instance
		$resource = Request::getInt('resource', 0);
		$tool     = Request::getString('tool', '');

		// Ensure we have a tool to work with
		if (!$tool && !$resource)
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
			return;
		}

		if ($tool)
		{
			// Load the tool version
			$row = new \Components\Tools\Tables\Version($this->database);
			$row->loadFromInstance($tool);
		}
		else
		{
			$row = Entry::oneOrFail($resource);

			$rt = License::oneByName('custom' . $resource);

			$row->set('license', stripslashes($rt->text));
		}

		// Output HTML
		if (!$row)
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		// Set the page title
		$title = stripslashes($row->title) . ': ' . Lang::txt('COM_RESOURCES_LICENSE');

		// Write title
		Document::setTitle($title);

		// Output HTML
		$this->view
			->set('title', $title)
			->set('row', $row)
			->set('tool', $tool)
			->set('config', $this->config)
			->set('no_html', Request::getInt('no_html', 0))
			->setName('license')
			->setLayout('default')
			->display();
	}

	/**
	 * Download a citation for a resource
	 *
	 * @return  void
	 */
	public function citationTask()
	{
		$yearFormat = 'Y';
		$monthFormat = 'M';

		// Get contribtool params
		$tconfig = Component::params('com_tools');

		// Incoming
		$id = Request::getInt('id', 0);
		$format = Request::getString('citationFormat', 'bibtex');

		// Append DOI handle
		$revision = Request::getString('rev', 0);
		$handle = '';
		if ($revision)
		{
			require_once dirname(dirname(__DIR__)) . '/models/doi.php';

			$rdoi = Doi::oneByResource($id, $revision);

			if ($rdoi->get('doi') && ($rdoi->get('doi_shoulder') || $tconfig->get('doi_shoulder')))
			{
				$handle = 'doi:' . ($rdoi->get('doi_shoulder') ? $rdoi->get('doi_shoulder') : $tconfig->get('doi_shoulder')) . '/' . strtoupper($rdoi->doi);
			}
		}

		// Load the resource
		$row = Entry::oneOrNew($id);

		if (!$row->get('id'))
		{
			App::abort(404, Lang::txt('COM_RESOURCES_RESOURCE_NOT_FOUND'));
		}

		if (!$row->get('publish_up') || $row->get('publish_up') == '0000-00-00 00:00:00')
		{
			$row->set('publish_up', $row->get('created'));
		}
		$thedate = $row->get('publish_up');

		// Build the download path
		$path  = PATH_APP . DS . trim($this->config->get('cachepath', '/cache/resources'), DS);
		$path .= $row->relativePath();

		if (!is_dir($path))
		{
			if (!\Filesystem::makeDirectory($path))
			{
				$this->setError('Error. Unable to create path.');
			}
		}

		// Build the URL for this resource
		$sef = Route::url($row->link());

		$url = Request::base() . ltrim($sef, '/');

		// Choose the format
		switch ($format)
		{
			case 'endnote':
				$doc = '';
				switch ($row->type->get('type'))
				{
					case 'misc':
					default:
						$doc .= "%0 " . Lang::txt('COM_RESOURCES_GENERIC') . "\r\n";
						break; // generic
				}
				$doc .= "%D " . Date::of($thedate)->toLocal($yearFormat) . "\r\n";
				$doc .= "%T " . trim(stripslashes($row->title)) . "\r\n";

				foreach ($row->contributors('!submitter') as $auth)
				{
					$auth = preg_replace('/{{(.*?)}}/s', '', $auth->name);
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
					$doc .= "%8 " . Date::of($thedate)->toLocal($monthFormat) . "\r\n";
				}
				//$doc .= "\r\n";
				if ($handle)
				{
					$doc .= "%1 " .'doi:'.  $handle;
					$doc .= "\r\n";
				}

				$file = 'resource_' . $id . '.enw';
				$mime = 'application/x-endnote-refer';
			break;

			case 'bibtex':
			default:
				include_once \Component::path('com_citations') . DS . 'helpers' . DS . 'BibTex.php';

				$bibtex = new \Structures_BibTex();
				$addarray = array();
				$addarray['type']  = 'misc';
				$addarray['cite']  = $this->_config['sitename'] . $row->id;
				$addarray['title'] = stripslashes($row->title);

				//$auths = explode(';', $row->author);
				//for ($i=0, $n=count($auths); $i < $n; $i++)
				foreach ($row->contributors('!submitter') as $i => $auth)
				{
					//$author = trim($auths[$i]);
					$author = preg_replace('/\{\{(.+)\}\}/i', '', $auth->name);
					if (strstr($author, ','))
					{
						$author_arr = explode(',', $author);
						$author_arr = array_map('trim', $author_arr);

						$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? trim($author_arr[1]) : '';
						$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? trim($author_arr[0]) : '';
					}
					else
					{
						$author_arr = explode(' ', $author);
						$author_arr = array_map('trim', $author_arr);

						$last = array_pop($author_arr);
						$addarray['author'][$i]['first'] = (count($author_arr) > 0) ? implode(' ', $author_arr) : '';
						$addarray['author'][$i]['last']  = ($last) ? trim($last) : '';
					}
				}
				$addarray['month'] = Date::of($thedate)->toLocal($monthFormat);
				$addarray['url']   = $url;
				$addarray['year']  = Date::of($thedate)->toLocal($yearFormat);
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

		$this->_serveup(false, $path, $file, $mime);

		die; // REQUIRED
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
			continue;
		}

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

		$this->_readfileChunked($p . $f);
		// The caller MUST 'die();'
	}

	/**
	 * Read file contents
	 *
	 * @param   string   $filename
	 * @param   boolean  $retbytes
	 * @return  mixed
	 */
	protected function _readfileChunked($filename, $retbytes=true)
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
	 * Check if a user is authorized
	 *
	 * @param   array    $contributorIDs   Authors of a resource
	 * @param   object   $resource
	 * @return  boolean  True if user has access
	 */
	protected function _authorize($contributorIDs=array(), $resource=null)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		// Check if they're a site admin
		$this->config->set('access-admin-component', User::authorise('core.admin', null));
		$this->config->set('access-manage-component', User::authorise('core.manage', null));
		if ($this->config->get('access-admin-component') || $this->config->get('access-manage-component'))
		{
			return true;
		}

		// Check if they're the resource creator
		if (is_object($resource) && $resource->created_by == User::get('id'))
		{
			return true;
		}

		// Check if they're a resource "contributor"
		if (is_array($contributorIDs))
		{
			if (in_array(User::get('id'), $contributorIDs))
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
	 * @param   object   $resource  Resource
	 * @param   object   $user      User (optional)
	 * @return  boolean  True if user has access to a group-owned resource
	 */
	private function checkGroupAccess($resource, $user=null)
	{
		if (!$user)
		{
			$user = User::getInstance();
		}

		$usersgroups = array();

		if (!$user->isGuest())
		{
			// Check if they're a site admin
			$this->config->set('access-admin-component', $user->authorise('core.admin', null));
			$this->config->set('access-manage-component', $user->authorise('core.manage', null));
			if ($this->config->get('access-admin-component') || $this->config->get('access-manage-component'))
			{
				return false;
			}

			$xgroups = \Hubzero\User\Helper::getGroups($user->get('id'), 'all');

			// Get the groups the user has access to
			if (!empty($xgroups))
			{
				foreach ($xgroups as $group)
				{
					if ($group->regconfirmed)
					{
						$usersgroups[] = $group->cn;
					}
				}
			}
		}

		// Get the list of groups that can access this resource
		$allowedgroups = $resource->groups;
		if ($resource->standalone != 1)
		{
			//$helper = new Helper($resource->id, $this->database);
			//$helper->getParents();
			$parents = $resource->parents()
				->whereEquals('published', Entry::STATE_PUBLISHED)
				->rows();

			if (count($parents) == 1)
			{
				$p = Entry::oneOrFail($parents->first()->id);
				$allowedgroups = $p->groups;
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
			if ($user->get('guest'))
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
				$p = Entry::oneOrFail($parents->first()->id);
			}
			if (isset($p) && ($p->access == 4 || $p->access == 3) && count($common) < 1)
			{
				$restricted = true;
			}
		}
		return $restricted;
	}

	/**
	 * Generate the full (absolute) path to a file
	 *
	 * @param   string  $path  Relative path
	 * @return  string
	 */
	private function _fullPath($path)
	{
		if (substr($path, 0, 7) == 'http://'
		 || substr($path, 0, 8) == 'https://'
		 || substr($path, 0, 6) == 'ftp://'
		 || substr($path, 0, 9) == 'mailto://'
		 || substr($path, 0, 9) == 'gopher://'
		 || substr($path, 0, 7) == 'file://'
		 || substr($path, 0, 7) == 'news://'
		 || substr($path, 0, 7) == 'feed://'
		 || substr($path, 0, 6) == 'mms://')
		{
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

		return rtrim(Request::base(), '/') . $path;
	}

	/**
	 * Check if a user has access to a tool
	 *
	 * @param   integer  $toolid  Tool ID
	 * @return  boolean  True if user has access, false if not
	 */
	/*private function _checkToolaccess($toolid)
	{
		// Check if they're a site admin
		if (User::authorize($this->_option, 'manage'))
		{
			return true;
		}

		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);
		// check if user in tool dev team
		$developers = $obj->getToolDevelopers($toolid);
		if ($developers)
		{
			foreach ($developers as $dv)
			{
				if ($dv->uidNumber == User::get('id'))
				{
					return true;
				}
			}
		}

		return false;
	}*/
}
