<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Api\Controllers;

use Components\Projects\Models\Project;
use Components\Projects\Helpers;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Hubzero\Filesystem\Entity;
use Hubzero\Filesystem\Collection;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'project.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'project.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'connection.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'provider.php';

/**
 * API controller for the projects files
 */
class Filefsv1_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('files', 'list');
		$this->registerTask('update', 'save');
		$this->registerTask('insert', 'save');
		$this->_task = Request::getWord('task', 'list');

		// Load component language file
		Lang::load('com_projects') || Lang::load('com_projects', dirname(dirname(__DIR__)) . DS . 'site');

		// Incoming
		$id = Request::getString('id', '');

		$this->model = new Project($id);

		// Project did not load?
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		$contentTasks = array('insert', 'update', 'delete', 'move', 'rename', 'makedirectory');

		//tasks specific to adapters
		$connectionTasks = array('upload', 'download', 'getmetadata', 'setmetadata');

		// Check authorization
		if ((in_array($this->_task, $contentTasks) && !$this->model->access('content'))
			|| (in_array($this->_task, $connectionTasks) && !$this->model->access('content'))
			|| !$this->model->access('member'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 401);
		}

		// Check for local repo if no connection has been specified
		if (!$this->model->repo()->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_FILES_ERROR_NO_LOCAL_REPO'), 404);
		}

		parent::execute();
	}

	/**
	 * Get a list of project files
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "filter",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$response = new stdClass;
		$files = $this->model->repo()->filelist(array(
			'subdir'           => Request::getString('subdir', ''),
			'filter'           => Request::getString('filter', ''),
			'limit'            => Request::getInt('limit', 0),
			'start'            => Request::getInt('limitstart', 0),
			'sortby'           => 'localpath',
			'showFullMetadata' => true,
			'getParents'       => true,
			'getChildren'      => true
		));

		$response->results = $this->_parseResults($files);

		$response->count = is_array($files)?count($files):0;

		if ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Get file(s) metadata
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/get
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file/folder paths to get metadata for.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function getTask()
	{
		// Incoming
		$files = Request::getArray('asset', array());

		if (empty($files))
		{
			throw new Exception(Lang::txt('No asset path given'), 404);
		}

		$response = new stdClass;

		$files = $this->model->repo()->filelist(array(
			'subdir'           => Request::getString('subdir', '', 'post'),
			'files'            => $files,
			'showFullMetadata' => true,
			'getParents'       => true,
			'getChildren'      => true
		));
		$response->results = $this->_parseResults($files);

		$this->send($response);
	}

	/**
	 * Create a folder in project local repo
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/makedirectory
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "directory",
	 * 		"description":   "Directory path",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function makedirectoryTask()
	{
		$directory = trim(Request::getString('directory', ''));

		if (empty($directory))
		{
			throw new Exception(Lang::txt('No directory path given'), 404);
		}
		$response = new stdClass;


		// Set params
		$params = array(
			'subdir' => Request::getString('subdir', '', 'post'),
			'newDir' => urldecode($directory)
		);

		if ($this->model->repo()->makeDirectory($params))
		{
			$response->success = 1;
		}
		if ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Delete file or folder from project
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/delete
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file paths.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "folder",
	 * 		"description":   "Array of folder paths.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function deleteTask()
	{
		// Incoming
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			throw new Exception(Lang::txt('No asset/folder path(s) given'), 404);
		}

		$response = new stdClass;
		$deleted  = 0;

		foreach ($items as $element)
		{
			foreach ($element as $type => $item)
			{
				// Get type and item name
				break;
			}

			// Must have a name
			if (trim($item) == '')
			{
				continue;
			}

			$params = array(
				'type'   => $type,
				'item'   => $item,
				'subdir' => Request::getString('subdir', '', 'post')
			);

			if ($this->model->repo()->deleteItem($params))
			{
				$deleted++;
			}
		}
		$response->total   = count($items);
		$response->deleted = $deleted;

		if ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Move file or folder in project
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/move
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "target",
	 * 		"description":   "Target directory path within project repo",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file paths to move.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "folder",
	 * 		"description":   "Array of folder paths to move.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @return  void
	 */
	public function moveTask()
	{
		// Incoming
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			throw new Exception(Lang::txt('No asset/folder path(s) given'), 404);
		}

		// Incoming
		$target = trim(urldecode(Request::getString('target', '')), DS);

		$response = new stdClass;
		$moved  = 0;

		foreach ($items as $element)
		{
			foreach ($element as $type => $item)
			{
				// Get type and item name
				break;
			}

			// Must have a name
			if (trim($item) == '')
			{
				continue;
			}

			$params = array(
				'type'            => $type,
				'item'            => $item,
				'targetDir'       => $target,
				'createTargetDir' => true // allow new directories
			);

			if ($this->model->repo()->moveItem($params))
			{
				$moved++;
			}
		}
		$response->total = count($items);
		$response->moved = $moved;

		if ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Move file or folder in project
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/rename
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "type",
	 * 		"description":   "File or folder.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "file",
	 * 		"allowedValues": "file, folder"
	 * }
	 * @apiParameter {
	 * 		"name":          "from",
	 * 		"description":   "Name of file/folder to rename (do not include local path - use subdir param).",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "to",
	 * 		"description":   "New name for file/folder (do not include local path - use subdir param).",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function renameTask()
	{
		$response = new stdClass;
		$response->success = 0;


		$params = array(
			'subdir'  => Request::getString('subdir', ''),
			'from'    => Request::getString('from', ''),
			'to'      => Request::getString('to', ''),
			'type'    => Request::getString('type', 'file')
		);

		if ($this->model->repo()->rename($params))
		{
			$response->success = 1;
		}

		if ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * upload/replace a project file (only for non-default connection providers)
	 *
	 * @apiMethod POST
	 * @apiUri    /projects/{id}/files/upload
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project filespace",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *              "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "file",
	 * 		"description":   "File contents to upload",
	 * 		"type":          "binary",
	 * 		"required":      true,
	 *              "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function uploadTask()
	{

		$response = new stdClass;

		// Check permission
		if (!$this->model->access('content')) {
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}
		$render = Request::getString('render', 'download');
		$hash   = Request::getString('hash', '');
		$this->repo = new \Components\Projects\Models\Repo($this->model, "local");

		$this->_database   = \App::get('db');
		$this->_uid        = User::get('id');
		$this->subdir      = trim(urldecode(Request::getString('subdir', '')), DS);

		// Set params
		$params = array(
			'subdir'     => $this->subdir,
			'expand'     => Request::getInt('expand_zip', 0),
			'ajaxUpload' => 1,
			'path'       => $this->repo->get('path')
		);

		// Upload file
		$results = $this->repo->insert($params);

		$response->results = $results;
		$this->send($response);
	}

	/**
	 * Download file or folder from project (non-default connection providers only)
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/download
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file paths.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "folder",
	 * 		"description":   "Array of folder paths.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *              "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function downloadTask()
	{
		// Incoming
		$render = Request::getString('render', 'download');
		$hash   = Request::getString('hash', '');
		$this->repo = new \Components\Projects\Models\Repo($this->model, "local");
		// Metadata collector
		$collector = array();

		// Combine file and folder data
		$items = $this->_sortIncoming();

		$this->_database   = \App::get('db');
		$this->_uid        = User::get('id');
		$this->subdir      = trim(urldecode(Request::getString('subdir', '')), DS);


		// Params for repo call
		$params = array(
			'subdir'            => $this->subdir,
			'remoteConnections' => []
		);

		// Collect items
		if (!$items) {
			App::abort(404, Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		} else {
			foreach ($items as $element) {
				foreach ($element as $type => $item) {
					// Get type and item name
					break;
				}

				// Must have a name
				if (trim($item) == '') {
					continue;
				}

				// Build metadata object
				$collector[] = $this->repo->getMetadata($item, $type, $params);
			}
		}

		// Check that we have item(s) to download
		if (empty($collector)) {
			// Throw error
			App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
		}

		// File preview?
		if ($render == 'preview') {
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'projects',
					'element' => 'files',
					'name'    => 'preview'
				)
			);

			$view->file = isset($collector[0]) ? $collector[0] : null;

			// Get last revision
			if (!$view->file->get('converted') && !$hash) {
				$params['file'] = $view->file;
				$hash = $this->repo->getLastRevision($params);
				$view->file->set('hash', $hash);
			}
			$view->option = $this->_option;
			$view->model  = $this->model;

			if (!($view->file instanceof \Components\Projects\Models\File)) {
				$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_FILE_INFO_NOT_FOUND'));
			}
			return $view->loadTemplate();
		}

		// Other rendering?
		if ($render == 'thumb' || $render == 'inline' || $render == 'medium') {
			$file = isset($collector[0]) ? $collector[0] : null;
			if (!($file instanceof \Components\Projects\Models\File)) {
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
			}
			// Get last revision
			if (!$file->get('converted') && !$hash) {
				$params['file'] = $file;
				$hash = $this->repo->getLastRevision($params);
			}

			$image = $file->getPreview($this->model, $hash, 'fullPath', $render);

			// Serve image
			if ($image && is_file($image)) {
				$server = new \Hubzero\Content\Server();
				$server->filename($image);
				$server->serve_inline($image);
				exit;
			}
		}

		// File download
		if (count($items) > 1) {
			$archive = $this->_archiveFiles($items);

			if (!$archive) {
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_ARCHIVE_ERROR'));
			} else {
				$downloadPath   = $archive['path'];
				$serveas        = 'Project Files ' . Date::toSql() . '.zip';
			}
		} else {
			$file = isset($collector[0]) ? $collector[0] : null;
			if (!($file instanceof \Components\Projects\Models\File)) {
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
			}
			$serveas = $file->get('name');


			// Download local revision
			if ($hash) {
				$tempPath = 'temp-' . \Components\Projects\Helpers\Html::generateCode(4, 4, 0, 1, 0) . $serveas;
				$downloadPath = sys_get_temp_dir() . DS . $tempPath;

				// Get file content
				$params = array('fileName' => $file->get('localPath'), 'hash' => $hash, 'target' => $downloadPath);
				$this->repo->getFileContent($params);
			} else {
				// Viewing current file
				$serveas      = urldecode(Request::getString('serveas', $file->get('name')));
				$downloadPath = $file->get('fullPath');
			}
		}

		// Now we can actually download
		if (!empty($downloadPath)) {
			// Ensure the file exist
			if (!file_exists($downloadPath)) {
				// Throw error
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
			}
			if (!is_file($downloadPath)) {
				// Throw error
				App::abort(416, Lang::txt('PLG_PROJECTS_FILES_FOLDER_NOT_DOWNLOAD'));
			}
			// Cannot download zero byte files
			if (filesize($downloadPath) == 0) {
				exit;
			}

			// Proceed with download
			// Initiate a new content server and serve up the file
			$server = new \Hubzero\Content\Server();
			$server->filename($downloadPath);
			$server->disposition('attachment');
			$server->acceptranges(false);
			$server->saveas($serveas);
			$result = $server->serve_attachment($downloadPath, $serveas, false);

			if (!$result) {
				// Should only get here on error
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'));
			} else {
				// Clean up the /tmp directory from zip files (download multiple files)
				$temp_path = sys_get_temp_dir();
				$matches = array();
				preg_match('/^(\\/tmp.*?\\.zip)/is', $downloadPath, $matches);
				if (!empty($matches)) {
					\Hubzero\Filesystem::delete($downloadPath);
				}

				exit;
			}
		}


		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Insert/update a project file
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/insert OR /projects/{id}/files/update
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "data_path",
	 * 		"description":   "Path to local or remote file.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function saveTask()
	{
		// Incoming
		$dataPath = Request::getString('data_path', '', 'POST');

		if (empty($dataPath))
		{
			throw new Exception(Lang::txt('No data path given'), 404);
		}

		// Insert file
		$response = new stdClass;
		$response->results     = $this->model->repo()->insert(
			array(
				'dataPath'    => $dataPath,
				'allowReplace'=> $this->_task == 'insert' ? false : true,
				'update'      => $this->_task == 'insert' ? false : true,
				'subdir'      => Request::getString('subdir', '')
			)
		);

		// Parse results
		if (!empty($response->results))
		{
			$parsedResults = array();
			$names = null;
			foreach ($response->results as $updateType => $files)
			{
				foreach ($files as $file)
				{
					if ($updateType == 'uploaded' || $updateType == 'updated')
					{
						// Get metadata
						$parsedResults[] = $this->model->repo()->getMetadata($file, 'file');
						$names .= $names ? ', ' . $file : $file;
					}
				}
			}

			// Register event with the project
			if (!empty($parsedResults))
			{
				$updateType = $this->_task == 'insert' ? 'uploaded' : 'updated';
				// Plugin params
				$plugin_params = array(
					$this->model,
					array($updateType => $names)
				);

				Event::trigger('projects.onAfterUpdate', $plugin_params);
			}

			$response->results = $parsedResults;
		}
		elseif ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Get file annotation
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/getmetadata
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of files (do not include local path - use subdir param).",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "fields",
	 * 		"description":   "Fields to get metadata for (if empty, return all fields).",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function getmetadataTask()
	{
		$response = new stdClass;
		$response->success = 0;

		$files = Request::getArray('asset', array());
		$fields   = Request::getArray('fields', array());

		$response->metadata = array();

		if (is_array($files))
		{
			foreach ($files as $file)
			{
				$entity = Entity::fromPath(Request::getString('subdir', '', 'post') . DS . $file, $this->ormconn->adapter());

				if ($entity->exists())
				{
					try
					{
						$metadata = Event::trigger('metadata.onMetadataGet', [$entity]);
						if (empty($fields))
						{
							$response->metadata[$file] = $metadata[0];
						}
						else
						{
							$response->metadata[$file] = $this->_filter($metadata[0], $fields);
						}
						$response->success = 1;
					}
					catch (Exception $e)
					{
						$response->error .= " Error fetching entity metadata for ".$file;
					}
				}
				else
				{
					$response->error .= " Entity ".$file." does not exist";
				}
			}
		}
		else
		{
			$response->error = "asset parameter needs to be an array of file names";
		}

		$this->send($response);
	}

	/**
	 * Set file annotation
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/setmetadata
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of files (do not include local path - use subdir param).",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "metadata",
	 * 		"description":   "Associative array of metadata to update.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function setmetadataTask()
	{
		$response = new stdClass;
		$response->success = 0;

		$files = Request::getArray('asset', array());
		$metadata = Request::getArray('metadata', array());

		if (is_array($files))
		{
			if (is_array($metadata))
			{
				foreach ($files as $file)
				{
					$entity = Entity::fromPath(Request::getString('subdir', '', 'post') . DS . $file, $this->ormconn->adapter());

					if ($entity->exists())
					{
						try
						{
							$tmpoldmetadata = Event::trigger('metadata.onMetadataGet', [$entity]);
							$oldmetadata = $this->_packMetadata($tmpoldmetadata[0]);

							$error = Event::trigger('metadata.onMetadataSave', [$entity, array_merge($oldmetadata, $metadata)]);

							if (empty($error))
							{
								$response->success = 1;
							}
							else
							{
								$response->success = 0;
								$response->error .= " Error setting metadata for ".$file;
							}
						}
						catch (Exception $e)
						{
							$response->error .= " Error setting metadata for ".$file;
						}
					}
					else
					{
						$response->error .= " Entity ".$file." does not exist";
					}
				}
			}
			else
			{
				$response->error = "metadata parameter needs to be an associative array of fields and values";
			}
		}
		else
		{
			$response->error = "asset parameter needs to be an array of file names";
		}

		$this->send($response);
	}

	/**
	 * Get file(s) metadata for Flysystem adapters
	 *
	 * @param   array $files
	 * @return  array
	 */
	protected function _parseFlysystemListing($files)
	{
		// Get array of file metadata
		if (!empty($files))
		{
			$results = array();
			foreach ($files as $result)
			{
				// Access file metadata
				$results[] = $this->ormconn->adapter()->getMetadata($result->getPath());
			}
			return $results;
		}

		return array();
	}

	/**
	 * Get file(s) metadata
	 *
	 * @param   array $files
	 * @return  array
	 */
	protected function _parseResults($files)
	{
		// Get array of file metadata
		if (!empty($files))
		{
			$results = array();
			foreach ($files as $result)
			{
				// Access private _data container
				$results[] = $result->getData();
			}
			return $results;
		}

		return array();
	}

	/**
	 * Filter an array by another array of field names
	 *
	 * @param   array $source
	 * @param   array $filter
	 * @return  array
	 */
	protected function _filter($source,$filter)
	{
		$result = array();
		foreach ($source as $key => $value)
		{
			if (in_array($key, $filter))
			{
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * Sorts incoming file/folder data
	 *
	 * @return  array
	 */
	protected function _getCollection()
	{
		// Incoming
		$files       = $this->_prune((array) Request::getArray('asset', []));
		$directories = $this->_prune((array) Request::getArray('folder', []));
		$collection  = new Collection;

		$entities = array_merge($files, $directories);

		if (!empty($entities) && is_array($entities))
		{
			foreach ($entities as $entity)
			{
				$path = trim(Request::getString('subdir', ''), '/') . '/' . urldecode($entity);

				$collection->add(Entity::fromPath($path, $this->ormconn->adapter()));
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
	private function _prune($vars)
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

	/**
	 * Sort incoming file/folder data
	 *
	 * @return     array
	 */
	protected function _sortIncoming()
	{
		// Incoming
		$checked = Request::getArray('asset', array());
		$folders = Request::getArray('folder', array());

		$combined = array();
		if (!empty($checked) && is_array($checked))
		{
			foreach ($checked as $ch)
			{
				if (trim($ch) != '')
				{
					$combined[] = array('file' => urldecode($ch));
				}
			}
		}
		elseif ($file = Request::getString('asset', ''))
		{
			$combined[] = array('file' => urldecode($file));
		}

		if (!empty($folders) && is_array($folders))
		{
			foreach ($folders as $f)
			{
				if (trim($f) != '')
				{
					$combined[] = array('folder' => urldecode($f));
				}
			}
		}
		elseif ($folder = Request::getString('folder', ''))
		{
			$combined[] = array('folder' => urldecode($folder));
		}

		return $combined;
	}

	/**
	 * Pack metadata from onMetadataGet into form usable by onMetadataSave
	 *
	 * @param      array  $metadata  incoming metadata
	 * @return     array
	 */
	protected function _packMetadata($metadata)
	{
		$packedMetadata = array();
		if (!empty($metadata) && is_array($metadata))
		{
			foreach ($metadata as $key => $avu)
			{
				if (trim($avu['units']) != '')
				{
					$packedMetadata[$key.':'.$avu['units']] = $avu['value'];
				}
				else
				{
					$packedMetadata[$key] = $avu['value'];
				}
			}
		}

		return $packedMetadata;
	}
}

