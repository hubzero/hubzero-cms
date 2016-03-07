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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Include [temporary] ORM models (these will be merged with existing models at some point in the future)
require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'orm' . DS . 'project.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'orm' . DS . 'connection.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'orm' . DS . 'provider.php';

use Components\Projects\Models\Orm\Project;
use Components\Projects\Models\Orm\Connection;
use Components\Projects\Models\Orm\Provider;
use Hubzero\Filesystem\Collection;
use Hubzero\Filesystem\Entity;
use Hubzero\Filesystem\Manager;

/**
 * Projects Files plugin (connections extension)
 */
class connections
{
	/**
	 * Constructs a new connections object
	 *
	 * @param   object  $plugin      the plugin that spawned this object
	 * @param   string  $option      the option being used
	 * @param   int     $connection  the connection id
	 * @return  void
	 **/
	public function __construct($plugin, $option, $connection=null)
	{
		$this->plugin  = $plugin;
		$this->_option = $option;

		if (isset($connection) && $connection > 0)
		{
			$this->connection = Connection::oneOrFail($connection);
		}
	}

	/**
	 * Gets undefined vars, assumed to be coming from the original plugin
	 *
	 * @param   string  $key  the key we're getting
	 * @return  mixed
	 **/
	public function __get($key)
	{
		return $this->plugin->$key;
	}

	/**
	 * Calls undefined functions, again, assuming to be coming from plugin
	 *
	 * @param   string  $name       the function name being called
	 * @param   array   $arguments  the arguments to be passed to the function
	 * @return  mixed
	 **/
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->plugin, $name), $arguments);
	}

	/**
	 * Handles executing the appropriate task
	 *
	 * @param   string  $task  the task (action) being called
	 * @return  string
	 **/
	public function execute($task)
	{
		// Set task in case referenced later
		$this->_task = $task;

		$reflection = with(new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC);
		$excludes   = ['__construct', '__get', '__call', 'execute'];
		$methods    = [];

		foreach ($reflection as $method)
		{
			if (!in_array($method->name, $excludes))
			{
				$methods[] = $method->name;
			}
		}

		if (in_array($task, $methods))
		{
			return call_user_func([$this, $task]);
		}

		throw new Exception("Call to undefined action", 500);
	}

	/**
	 * Renders project files connections view
	 *
	 * @return  string
	 */
	public function connections()
	{
		// Set up view
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connections',
			'layout'  => 'display'
		]);

		// Assign vars
		$view->model       = $this->model;
		$view->connections = Project::oneOrFail($this->model->get('id'))->connections()->thatICanView();

		// Load and return view content
		return $view->loadTemplate();
	}

	/**
	 * Creates a new connection
	 *
	 * @return  void
	 **/
	public function newconnection()
	{
		$connection = Connection::blank();
		$connection->set([
			'project_id'  => $this->model->get('id'),
			'provider_id' => Request::getInt('provider_id')
		])->save();

		// Redirect
		App::redirect(Route::url($this->model->link('files') . '&action=editconnection&connection=' . $connection->id, false));
	}

	/**
	 * Renders project files edit connection view
	 *
	 * @return  string
	 */
	public function editconnection()
	{
		// Set up view
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connections',
			'layout'  => 'edit'
		]);

		// Assign vars
		$view->connection = $this->connection;

		// Load and return view content
		return $view->loadTemplate();
	}

	/**
	 * Renders file browser view
	 *
	 * @return  string
	 */
	public function browse()
	{
		// Set up view
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'browse'
		]);

		// Do we have any changes to report?
		$this->onAfterUpdate();

		// Get sorting variables
		$sortby  = Request::getVar('sortby', 'basename');
		$sortasc = Request::getVar('sortdir', 'ASC') == 'ASC' ? true : false;

		// Get directory that we're interested in
		$dir = Entity::fromPath($this->subdir, $this->connection->adapter());

		// Assign view vars
		$view->items      = $dir->listContents()->sort($sortby, $sortasc);
		$view->title      = $this->_area['title'];
		$view->option     = $this->_option;
		$view->sortby     = $sortby;
		$view->sortdir    = $sortasc;
		$view->subdir     = $this->subdir;
		$view->model      = $this->model;
		$view->fileparams = $this->params;
		$view->connection = $this->connection;

		// Load and return view content
		return $view->loadTemplate();
	}

	/**
	 * Downloads file(s)
	 *
	 * @return  mixed
	 */
	public function download()
	{
		$items = $this->getCollection();

		if (Request::getVar('render', 'download') == 'preview')
		{
			return $this->preview();
		}

		// Check items
		if (!$items || count($items) == 0)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
			return;
		}

		if (count($items) > 1)
		{
			$archive = $items->compress();
			$result  = $archive->serve('project_files_' . \Components\Projects\Helpers\Html::generateCode(6, 6, 0, 1, 1) . '.zip');

			// Delete the tmp file for serving
			$archive->delete();
		}
		else
		{
			$result = $items->first()->serve();
		}

		if (!$result)
		{
			// Should only get here on error
			throw new Exception(Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'), 404);
		}
		else
		{
			exit;
		}
	}

	/**
	 * Previews a file
	 *
	 * @return  string
	 **/
	private function preview()
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'files',
				'name'    => 'connected',
				'layout'  => 'preview'
			)
		);

		$file         = Request::getVar('asset');
		$path         = trim($this->subdir, '/') . '/' . $file;
		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->file   = Entity::fromPath($path, $this->connection->adapter());

		return $view->loadTemplate();
	}

	/**
	 * Renders the file upload view
	 *
	 * @return  string
	 */
	public function upload()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$ajax = Request::getInt('ajax', 0);

		// Output HTML
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'upload'
		]);

		$view->url        = $this->model->link('files') . '&connection=' . $this->connection->id;
		$view->option     = $this->_option;
		$view->model      = $this->model;
		$view->subdir     = $this->subdir;
		$view->ajax       = $ajax;
		$view->sizelimit  = $this->params->get('maxUpload', '104857600');
		$view->title      = $this->_area['title'];
		$view->params     = $this->params;
		$view->connection = $this->connection;

		return $view->loadTemplate();
	}

	/**
	 * Uploads file(s) and adds them to repository
	 *
	 * @return  void
	 */
	public function save()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Incoming
		$no_html    = Request::getVar('no_html', 0);
		$expand     = Request::getInt('expand_zip', 0);
		$ajaxUpload = $no_html ? true : false;

		$files   = [];
		$results = [];

		// Grab files from one of three potential sources
		if (isset($_FILES['qqfile']))
		{
			$path = trim($this->subdir, '/') . '/' . $_FILES['qqfile']['name'];
			$file = Entity::fromPath($path, $this->connection->adapter());

			$file->contents = file_get_contents($_FILES['qqfile']['tmp_name']);
			$file->size     = (int) $_FILES['qqfile']['size'];

			$files[] = $file;
		}
		elseif (isset($_GET['qqfile']))
		{
			$path = trim($this->subdir, '/') . '/' . $_GET['qqfile'];
			$file = Entity::fromPath($path, $this->connection->adapter());

			$file->contents = fopen('php://input', 'r');
			$file->size     = (int) $_SERVER["CONTENT_LENGTH"];

			$files[] = $file;
		}
		else
		{
			// Regular upload
			$upload = Request::getVar('upload', '', 'files', 'array');

			if (empty($upload['name']) or $upload['name'][0] == '')
			{
				throw new Exception(Lang::txt('COM_PROJECTS_UPLOAD_NO_FILES'), 404);
			}

			// Go through uploaded files
			for ($i=0; $i < count($upload['name']); $i++)
			{
				$path = trim($this->subdir, '/') . '/' . $upload['name'][$i];
				$file = Entity::fromPath($path, $this->connection->adapter());

				$file->contents = file_get_contents($upload['tmp_name'][$i]);
				$file->size     = (int) $upload['size'][$i];

				$files[] = $file;
			}
		}

		foreach ($files as $file)
		{
			// @FIXME: how do we want to do virus scanning?
			if ($file->save())
			{
				$results['uploaded'][] = $file->getPath();

				if ($file->isExpandable() && $expand)
				{
					if ($file->expand())
					{
						// Delete the archive itself
						$file->delete();
					}
				}
			}
			else
			{
				$results['failed'][] = $file->getPath();
			}
		}

		// Register changes for active projects
		if (!empty($results))
		{
			foreach ($results as $updateType => $files)
			{
				foreach ($files as $file)
				{
					$this->registerUpdate($updateType, $file);

					// Ajax requires output right here
					if ($ajaxUpload)
					{
						if ($updateType == 'failed')
						{
							return json_encode(array(
								'error' => 'error uploading file'
							));
						}
						else
						{
							return json_encode(array(
								'success'   => 1,
								'file'      => $file,
								'isNew'		=> $updateType == 'uploaded' ? true : false
								)
							);
						}
					}
				}
			}
		}

		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$url .= '&connection=' . $this->connection->id;
		$url  = Route::url($url, false);

		// Redirect
		App::redirect($url);
		return;
	}

	/**
	 * Renders delete view
	 *
	 * @return  stromg
	 */
	public function delete()
	{
		// Check permissions
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'delete'
		]);

		$view->items  = $this->getCollection();
		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->ajax   = Request::getInt('ajax', 0);
		$view->subdir = $this->subdir;
		$view->url    = $this->model->link('files') . '&connection=' . $this->connection->id;

		if (count($view->items) == 0)
		{
			$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_DELETE'));
		}

		return $view->loadTemplate();
	}

	/**
	 * Processes the delete
	 *
	 * @return  void
	 */
	public function removeit()
	{
		// Check permissions
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Get incoming array of items
		$items = $this->getCollection();

		// Set counts
		$deleted = 0;

		// Delete checked items
		if (count($items) > 0)
		{
			foreach ($items as $item)
			{
				if ($item->delete())
				{
					// Store in session
					$this->registerUpdate('deleted', $item->getName());
					$deleted++;
				}
			}
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse&connection=' . $this->connection->id;
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url, false));
		return;
	}

	/**
	 * Renders move file view
	 *
	 * @return  string
	 */
	public function move()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'move'
		]);

		$view->list   = Entity::fromPath('', $this->connection->adapter())->listContents(true);
		$view->items  = $this->getCollection();
		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->ajax   = Request::getInt('ajax', 0);
		$view->subdir = $this->subdir;
		$view->url    = $this->model->link('files') . '&connection=' . $this->connection->id;

		if (count($view->items) == 0)
		{
			$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_MOVE'));
		}

		// Return view content
		return $view->loadTemplate();
	}

	/**
	 * Processes file move
	 *
	 * @return  void
	 */
	public function moveit()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Get incoming array of items
		$items = $this->getCollection();

		// Set counts
		$moved = 0;

		// Incoming
		$newpath = trim(urldecode(Request::getVar('newpath', '')), '/');
		$newdir  = Request::getVar('newdir', '');
		$dest    = $newdir ? $newdir : $newpath;

		// Delete checked items
		if (count($items) > 0)
		{
			foreach ($items as $item)
			{
				if ($item->move($dest))
				{
					// Store in session
					$this->registerUpdate('moved', $item->getName());
					$moved++;
				}
			}
		}

		// Output message
		if ($moved > 0)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_MOVED'). ' '
				. $moved . ' ' . Lang::txt('PLG_PROJECTS_FILES_S'), 'success', 'projects');
		}
		else
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_NEW_FILE_LOCATION'), 'error', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse&connection=' . $this->connection->id;
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url, false));
		return;
	}

	/**
	 * Renders rename view
	 *
	 * @return  string
	 */
	public function rename()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Get incoming array of items
		$items = $this->getCollection();

		// Output HTML
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'rename'
		]);

		if (count($items) == 0)
		{
			$view->setError(Lang::txt('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
		}

		$view->item   = $items->first();
		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->ajax   = 1;
		$view->subdir = $this->subdir;
		$view->url    = $this->model->link('files') . '&connection=' . $this->connection->id;

		return $view->loadTemplate();
	}

	/**
	 * Processes file/folder rename
	 *
	 * @return  void
	 */
	public function renameit()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Rename
		$entity = Entity::fromPath(trim($this->subdir, '/') . '/' . Request::getVar('oldname', ''), $this->connection->adapter());

		if ($entity->rename(Request::getVar('newname', '')))
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_RENAMED_SUCCESS'), 'success', 'projects');
		}
		else
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_FAILED'), 'error', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse&connection=' . $this->connection->id;
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url, false));
	}

	/**
	 * Displays the new directory form
	 *
	 * @return  string
	 */
	public function newdir()
	{
		// Incoming
		$newdir = Request::getVar('newdir', '', 'post');

		// Output HTML
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'newfolder'
		]);

		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->ajax   = 1;
		$view->subdir = $this->subdir;
		$view->url    = $this->model->link('files') . '&connection=' . $this->connection->id;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Saves a new directory
	 *
	 * @return  void
	 */
	public function savedir()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Create
		$entity = Entity::fromPath(trim($this->subdir, '/') . '/' . trim(Request::getVar('newdir', '')), $this->connection->adapter());

		if (!$entity->create())
		{
			\Notify::message('', 'error', 'projects');
		}
		else
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_CREATED_DIRECTORY'), 'success', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse&connection=' . $this->connection->id;
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url, false));
	}

	/**
	 * Displays the file annotation form
	 *
	 * @return  void
	 **/
	public function annotate()
	{
		// Get incoming array of items
		$items = $this->getCollection();

		// Output HTML
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'annotate'
		]);

		// See if there is an edit interface that we should use
		// in favor of the default one (this is slightly better
		// than a view override in the sense that the view can 
		// still be packaged with the plugin, rather than having
		// to be overriden in the template on a per-hub basis)
		$editors = Event::trigger('metadata.onMetadataEdit');
		if ($editors && isset($editors[0]) > 0 && $editors[0] instanceof \Hubzero\Plugin\View)
		{
			$view = $editors[0];
		}

		// Get any existing metadata
		$entity   = $items->first();
		$metadata = Event::trigger('metadata.onMetadataGet', [$entity]);

		$view->item     = $entity;
		$view->metadata = $metadata[0];
		$view->option   = $this->_option;
		$view->model    = $this->model;
		$view->ajax     = 1;
		$view->subdir   = $this->subdir;
		$view->url      = $this->model->link('files') . '&connection=' . $this->connection->id;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Processes file annotations
	 *
	 * @return  void
	 */
	public function annotateit()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Get the file entity
		$file   = trim($this->subdir, '/') . '/' . trim(Request::getVar('item', ''));
		$entity = Entity::fromPath($file, $this->connection->adapter());

		// Grab annotations
		$keys     = Request::getVar('key', []);
		$values   = Request::getVar('value', []);
		$metadata = [];

		foreach ($keys as $idx => $key)
		{
			$key   = trim($key);
			$value = trim($values[$idx]);

			if (!empty($key) && !empty($value))
			{
				$metadata[$key] = $value;
			}
		}

		// Look for plugins that know how to handle them
		$plugins = Plugin::byType('metadata');

		if (count($plugins) == 0)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_ANNOTATION_PLUGINS'), 'error', 'projects');
		}
		else
		{
			// Send the data off to the plugins
			$response = Event::trigger('metadata.onMetadataSave', [
				$entity,
				$metadata
			]);

			if (empty($response))
			{
				\Notify::message(Lang::txt('PLG_PROJECTS_FILES_ANNOTATED_SUCCESS'), 'success', 'projects');
			}
			else
			{
				\Notify::message(Lang::txt('PLG_PROJECTS_FILES_ERROR_ANNOTATE_FAILED'), 'error', 'projects');
			}
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse&connection=' . $this->connection->id;
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url, false));
	}

	/**
	 * Compiles PDF/image preview for any kind of file
	 *
	 * @return  string
	 */
	public function compile()
	{
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'projects',
			'element' => 'files',
			'name'    => 'connected',
			'layout'  => 'compiled'
		]);

		// Combine file and folder data
		$items  = $this->getCollection();
		$output = Event::trigger('handlers.onHandleView', [$items]);

		// Check return type and for multiple responses
		if ($output && count($output) > 0)
		{
			foreach ($output as $o)
			{
				if ($o instanceof \Hubzero\Plugin\View)
				{
					$handler = $o;
				}
			}
		}
		else
		{
			$view->setError(Lang::txt('No handlers are currently available to view file(s).'));
		}

		if (!isset($handler))
		{
			$view->setError(Lang::txt('Failed to compile a view for this combination of file(s).'));
		}
		else
		{
			$view->handler = $handler;
		}

		$view->items      = $items;
		$view->subdir     = $this->subdir;
		$view->option     = $this->_option;
		$view->connection = $this->connection;

		return $view->loadTemplate();
	}

	/**
	 * Sorts incoming file/folder data
	 *
	 * @return  array
	 */
	private function getCollection()
	{
		// Incoming
		$files       = $this->prune((array) Request::getVar('asset', []));
		$directories = $this->prune((array) Request::getVar('folder', []));
		$collection  = new Collection;

		$entities = array_merge($files, $directories);

		if (!empty($entities) && is_array($entities))
		{
			foreach ($entities as $entity)
			{
				$path = trim($this->subdir, '/') . '/' . urldecode($entity);
				$collection->add(Entity::fromPath($path, $this->connection->adapter()));
			}
		}

		return $collection;
	}

	/**
	 * Trims vars, unsetting if empty
	 *
	 * @param   array  $vars  the variables to trim
	 * @return  array
	 **/
	private function prune($vars)
	{
		foreach ($vars as $k => $v)
		{
			if (trim($v) == '')
			{
				unset($vars[$k]);
			}
			else
			{
				$vars[$k] = $v;
			}
		}

		return $vars;
	}
}