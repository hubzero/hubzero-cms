<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Models;

use Hubzero\Base\Object;
use Components\Projects\Tables;
use Components\Projects\Helpers;
use Components\Projects\Models;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'repo.php');
require_once(__DIR__ . DS . 'file.php');
require_once(__DIR__ . DS . 'adapter.php');

/**
 * Project Repository model
 */
class Repo extends Object
{
	/**
	 * Tables\Repo
	 *
	 * @var  object
	 */
	private $_tbl = null;

	/**
	 * \JDatabase
	 *
	 * @var  object
	 */
	private $_db = NULL;

	/**
	 * \JRegistry
	 *
	 * @var  object
	 */
	private $_config;

	/**
	 * Data management adapter
	 *
	 * @var  string
	 */
	private $_adapter;

	/**
	 * Constructor
	 * @param    object   $project Project model
	 *
	 * @return  void
	 */
	public function __construct($project = NULL, $name = 'local')
	{
		$this->_db = \JFactory::getDBO();

		$this->set('project', $project);
		$this->set('name', $name);

		$this->fileSystem = new \Hubzero\Filesystem\Filesystem();

		// Initialiaze repo
		$this->_ini();

		// Set adapter
		$this->_adapter();
	}

	/**
	 * Initialize repo
	 *
	 * @return  object
	 */
	private function _ini()
	{
		if (!is_object($this->get('project')))
		{
			return false;
		}

		if ($this->get('name') !== 'local')
		{
			// Load repo info from db
			if (!$this->_tbl)
			{
				$this->_tbl = new Tables\Repo($this->_db);
			}
			$this->_tbl->loadRepo($this->get('project')->get('id'), $this->get('name'));
		}
		else
		{
			// Local Git repo  (/files)
			$this->set('project_id', $this->get('project')->get('id'));
			$this->set('engine', 'git');
			$this->set('remote', 0);
			$this->set('status', 1);
			$this->set('path', Helpers\Html::getProjectRepoPath(
				$this->get('project')->get('alias'),
				'files',
				false)
			);
		}
	}

	/**
	 * Return the adapter for this repo
	 *
	 * @return  object
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			$engine = strtolower($this->get('engine', 'git'));

			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($engine);

			if (!class_exists($cls))
			{
				$path = __DIR__ . '/adapters/' . $engine . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(Lang::txt('Invalid engine of "%s"', $engine));
				}
				include_once($path);
			}

			$this->_adapter = new $cls($this->get('path'), $this->get('remote'));
		}

		return $this->_adapter;
	}

	/**
	 * Check that repo exists and connection established
	 *
	 * @return  boolean
	 */
	public function exists()
	{
		if (!$this->get('path'))
		{
			return false;
		}
		elseif (!$this->get('remote') && !is_dir($this->get('path')))
		{
			return false;
		}
		elseif ($this->get('remote') == 1)
		{
			// Check connection
			// TBD
		}

		return true;
	}

	/**
	 * Make adapter call
	 *
	 * @param	string	$call	Method name
	 * @param	array	$params Method params
	 * @return  mixed
	 */
	public function call($call = NULL, $params = array())
	{
		if ($call == NULL)
		{
			$this->setError(Lang::txt('Empty request'));
			return false;
		}
		if (!isset($this->_adapter))
		{
			$this->_adapter();
		}

		// Perform request
		try
		{
			$result = $this->_adapter->$call($params);
			if ($this->_adapter->getError())
			{
				$this->setError($this->_adapter->getError());
			}
			return $result;
		}
		catch (Exception $e)
		{
			$this->setError($e);
			return false;
		}
	}

	/**
	 * Get file count
	 *
	 * @param   array	$params
	 * @return  integer
	 */
	public function count($params = array())
	{
		return $this->call('count', $params);
	}

	/**
	 * Get file list (retrieve and sort)
	 *
	 * @param      array	$params
	 * @return     array
	 */
	public function filelist($params = array())
	{
		return $this->call('filelist', $params);
	}

	/**
	 * Is local repo?
	 *
	 * @return  boolean
	 */
	public function isLocal()
	{
		if ($this->get('name') == 'local')
		{
			return true;
		}
		return false;
	}

	/**
	 * Check that directory within repo exists
	 *
	 * @param	string	$dirPath	Directory path
	 * @return  boolean
	 */
	public function dirExists($dirPath = NULL)
	{
		if (!$dirPath)
		{
			return false;
		}
		if ($this->get('remote'))
		{
			// TBD - remote check
		}
		elseif (!file_exists($this->get('path') . DS . $dirPath))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check that file within repo exists
	 *
	 * @param	string	$filePath	File path
	 * @return  boolean
	 */
	public function fileExists($filePath = NULL)
	{
		if (!$filePath)
		{
			return false;
		}
		if ($this->get('remote'))
		{
			// TBD - remote check
		}
		elseif (!file_exists($this->get('path') . DS . $filePath))
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete a file or a directory within repo
	 *
	 * @return  boolean
	 */
	public function deleteItem($params = array())
	{
		$path      = isset($params['path']) ? $params['path'] : $this->get('path');
		$dirPath   = isset($params['subdir']) ? $params['subdir'] : NULL;
		$item      = isset($params['item']) ? $params['item'] : NULL;
		$type      = isset($params['type']) ? $params['type'] : 'file';

		if (!$item)
		{
			return false;
		}
		$localPath = $dirPath ? $dirPath . DS . $item : $item;

		// File object
		$fileObject = new Models\File(trim($localPath), $path);
		$fileObject->set('type', $type);
		$params['file'] = $fileObject;

		if ($type == 'file' && $this->call('deleteFile', $params))
		{
			return true;
		}
		elseif ($type == 'folder' && $this->call('deleteDirectory', $params))
		{
			return true;
		}

		return false;
	}

	/**
	 * Delete a directory within repo
	 *
	 * @return  boolean
	 */
	public function deleteDirectory($params = array())
	{
		$path      = isset($params['path']) ? $params['path'] : $this->get('path');
		$dirPath   = isset($params['subdir']) ? $params['subdir'] : NULL;
		$dir       = isset($params['dir']) ? $params['dir'] : NULL;

		$localDirPath = $dirPath ? $dirPath . DS . $dir : $dir;

		if (!$dir || $dir == '.' || !$this->dirExists($localDirPath))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_DIR_TO_DELETE'));
			return false;
		}

		// File object
		$fileObject = new Models\File(trim($localDirPath), $path);
		$fileObject->set('type', 'folder');
		$params['file'] = $fileObject;

		// Adapter call
		if ($this->call('deleteDirectory', $params))
		{
			return true;
		}
		else
		{
			// Failed to delete directory
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_DIR_TO_DELETE'));
			return false;
		}

	}

	/**
	 * Create a new directory within repo
	 *
	 * @return  boolean
	 */
	public function makeDirectory($params = array())
	{
		$path      = isset($params['path']) ? $params['path'] : $this->get('path');
		$dirPath   = isset($params['subdir']) ? $params['subdir'] : NULL;
		$newDir    = isset($params['newDir']) ? $params['newDir'] : NULL;
		$reserved  = isset($params['reserved']) ? $params['reserved'] : array();

		$newDir = \Components\Projects\Helpers\Html::makeSafeDir($newDir);
		$localDirPath = $dirPath ? $dirPath . DS . $newDir : $newDir;

		// Check that we have directory to create
		if (!$newDir)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_DIR_TO_CREATE'));
			return false;
		}

		// Check that we directory name is not reserved for other purposes
		if (dirname($localDirPath) == '.' && in_array(strtolower($newDir), $reserved))
		{
			$this->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_DIR_RESERVED_NAME') );
			return false;
		}

		// Directory already exists ?
		if ($this->dirExists($localDirPath))
		{
			$this->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_DIR_CREATE') . ' "' . $newDir . '". '
			. Lang::txt('PLG_PROJECTS_FILES_ERROR_DIRECTORY_EXISTS') );
			return false;
		}

		// File object
		$fileObject = new Models\File(trim($localDirPath), $path);
		$fileObject->set('type', 'folder');
		$params['file']    = $fileObject;
		$params['replace'] = false;

		// Adapter call
		if ($this->call('makeDirectory', $params))
		{
			return true;
		}
		else
		{
			// Failed to create directory
			$this->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_DIR_CREATE') );
			return false;
		}
	}

	/**
	 * Rename a file or folder within repo
	 *
	 * @return  boolean
	 */
	public function rename($params = array())
	{
		$path      = isset($params['path']) ? $params['path'] : $this->get('path');
		$dirPath   = isset($params['subdir']) ? $params['subdir'] : NULL;
		$from      = isset($params['from']) ? $params['from'] : NULL;
		$to        = isset($params['to']) ? $params['to'] : NULL;
		$type      = isset($params['type']) ? $params['type'] : 'file';

		if (!$to)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_NO_NAME'));
			return false;
		}

		if (!$from)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
			return false;
		}

		// Make dir/file name safe
		if ($type == 'folder')
		{
			$to = \Components\Projects\Helpers\Html::makeSafeDir($to);
		}
		else
		{
			$to = \Components\Projects\Helpers\Html::makeSafeFile($to);
		}

		// Compare new and old name
		if ($from == $to)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_SAME_NAMES'));
			return false;
		}

		// Set paths
		$fromLocalPath = $dirPath ? $dirPath . DS . $from : $from;
		$toLocalPath   = $dirPath ? $dirPath . DS . $to : $to;

		// Already exists?
		if ($type == 'folder')
		{
			if ($this->dirExists($toLocalPath))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_ALREADY_EXISTS_DIR') . ' ' . $to);
				return false;
			}
			if (!$this->dirExists($fromLocalPath))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
				return false;
			}
		}
		if ($type == 'file')
		{
			if ($this->fileExists($toLocalPath))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_ALREADY_EXISTS_FILE'));
				return false;
			}
			if (!$this->fileExists($fromLocalPath))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
				return false;
			}

			$newExt = \Components\Projects\Helpers\Html::getFileExtension($toLocalPath);
			$fromExt = \Components\Projects\Helpers\Html::getFileExtension($fromLocalPath);

			// Do not remove extension
			$toLocalPath = $newExt && $newExt == $fromExt  ? $toLocalPath : $toLocalPath . '.' . $fromExt;
		}

		// File object - From
		$fromFile = new Models\File(trim($fromLocalPath), $path);
		$fromFile->set('type', $type);
		$params['fromFile'] = $fromFile;

		// File object - To
		$toFile = new Models\File(trim($toLocalPath), $path);
		$toFile->set('type', $type);
		$params['toFile'] = $toFile;

		// Adapter call
		if ($this->call('move', $params))
		{
			return true;
		}
		else
		{
			// Failed to create directory
			$this->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME') );
			return false;
		}
	}

	/**
	 * Insert file into the repo
	 *
	 * @return  boolean
	 */
	public function insert($params = array())
	{
		$ajaxUpload  = isset($params['ajaxUpload']) ? $params['ajaxUpload'] : false;
		$dataPath    = isset($params['dataPath']) ? $params['dataPath'] : NULL;
		$path        = isset($params['path']) ? $params['path'] : $this->get('path');
		$dirPath     = isset($params['subdir']) ? $params['subdir'] : NULL;
		$sizeLimit   = isset($params['sizelimit']) ? $params['sizelimit'] : '104857600';
		$quota       = isset($params['quota']) ? $params['quota'] : '104857600';
		$dirsize     = isset($params['dirsize']) ? $params['dirsize'] : 0;
		$available   = $quota - $dirsize;

		// Collector
		$results = array(
			'uploaded' => array(),
			'updated'  => array(),
			'failed'   => array(),
			'expanded' => array()
		);

		// Destination path
		$target = $path;
		$target.= $dirPath ? DS . $dirPath : '';

		// Get incoming file(s)
		if ($dataPath)
		{
			// Via remote/local copy
		}
		elseif ($ajaxUpload)
		{
			// Ajax upload
			if (isset($_FILES['qqfile']))
			{
				$file = $_FILES['qqfile']['name'];
				$size = (int) $_FILES['qqfile']['size'];
				$tmp_name = $_FILES['qqfile']['tmp_name'];
			}
			elseif (isset($_GET['qqfile']))
			{
				$file = $_GET['qqfile'];
				$size = (int) $_SERVER["CONTENT_LENGTH"];
				$tmp_name = NULL;
			}
			else
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILE'));
				return false;
			}

			// Upload and pick up output
			if ($item = $this->_upload($file, $tmp_name, $target, $size, $available, $params))
			{
				if (!empty($item['replace']))
				{
					$results['updated'][] = $item['localPath'];
				}
				else
				{
					$results['uploaded'][] = $item['localPath'];
				}
			}
			else
			{
				$results['failed'][] = $file;
			}
		}
		else
		{
			// Regular upload
			$upload = Request::getVar( 'upload', '', 'files', 'array' );

			if (empty($upload['name']) or $upload['name'][0] == '')
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_NO_FILES'));
				return false;
			}

			// Go through uploaded files
			for ($i=0; $i < count($upload['name']); $i++)
			{
				$file     = $upload['name'][$i];
				$tmp_name = $upload['tmp_name'][$i];
				$size     = $upload['size'][$i];

				// Upload and pick up output
				if ($item = $this->_upload($file, $tmp_name, $target, $size, $available, $params))
				{
					if (!empty($item['replace']))
					{
						$results['updated'][] = $item['localPath'];
					}
					else
					{
						$results['uploaded'][] = $item['localPath'];
					}
				}
				else
				{
					$results['failed'][] = $file;
				}
			}
		}

		return $results;
	}

	/**
	 * Perform Upload
	 *
	 * @return  mixed
	 */
	protected function _upload($file, $tmp_name, $target, $size, &$available, $params)
	{
		$dirPath     = isset($params['subdir']) ? $params['subdir'] : NULL;
		$sizeLimit   = isset($params['sizelimit']) ? $params['sizelimit'] : '104857600';
		$expand      = isset($params['expand']) ? $params['expand'] : false;

		$file        = \Components\Projects\Helpers\Html::makeSafeFile($file);
		$localPath   = $dirPath ? $dirPath . DS . $file : $file;
		$exists      = false;

		// Expand archive
		if ($expand && ($this->_isZip($file) || $this->_isTar($file)))
		{
			if (!$this->_expand($file, $tmp_name, $target, $size, $available, $params))
			{
				return false;
			}
		}
		else
		{
			// Run some checks
			if (!$this->_check($file, $tmp_name, $size, $available, $sizeLimit))
			{
				return false;
			}

			$where = $target . DS . $file;
			$exists = is_file($where) ? true : false;

			if (isset($_GET['qqfile']))
			{
				// Stream
				copy("php://input", $where);
			}
			else
			{
				if (!move_uploaded_file($tmp_name, $where))
				{
					$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_UPLOADING'));
					return false;
				}
			}

			// File object
			$fileObject        = new Models\File(trim($localPath), $this->get('path'));
			$params['file']    = $fileObject;
			$params['replace'] = $exists;

			// Success - check in change
			$this->call('checkin', $params);
		}

		return array('localPath' => $localPath, 'replace' => $exists );
	}

	/**
	 * Is file zip?
	 *
	 * @return  boolean
	 */
	protected function _isZip($file)
	{
		$ext = \Components\Projects\Helpers\Html::getFileExtension($file);
		return in_array($ext, array('zip')) ? true : false;
	}

	/**
	 * Is tar zip?
	 *
	 * @return  boolean
	 */
	protected function _isTar($file)
	{
		$ext = \Components\Projects\Helpers\Html::getFileExtension($file);
		return in_array($ext, array('tar', 'gz')) ? true : false;
	}

	/**
	 * Expand archive
	 *
	 * @return  boolean
	 */
	protected function _expand($file, $tmp_name, $target, $size, &$available, $params)
	{
		$engine    = $this->_isTar($file) ? 'tar' : 'zip';
		$dirPath   = isset($params['subdir']) ? $params['subdir'] : NULL;

		$tempPath    = sys_get_temp_dir();
		$archive     = $tempPath . DS . $file;
		$extractPath = $tempPath . DS . \Components\Projects\Helpers\Html::generateCode (7, 7, 0, 1, 0);

		if (isset($_GET['qqfile']))
		{
			// Stream
			copy("php://input", $archive);
			$tmp_name = $archive;
		}
		elseif (!$tmp_name)
		{
			$this->setError(Lang::txt('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
			return false;
		}

		if ($engine == 'tar')
		{
			// Create dir to extract into
			if (!is_dir($extractPath))
			{
				$this->fileSystem->makeDirectory($extractPath);
			}

			try
			{
				chdir($tempPath);
				exec('tar zxvf ' . $tmp_name . ' -C ' . $extractPath . ' 2>&1', $out );
			}
			catch (Exception $e)
			{
				$this->setError(Lang::txt('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
				return false;
			}
		}
		else
		{
			$zip = new \ZipArchive;
			if ($zip->open($tmp_name) === true)
			{
				$zip->extractTo($extractPath);
				$zip->close();
			}
			else
			{
				$this->setError(Lang::txt('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
				return false;
			}
		}

		// Move extracted contents from temp to repo
		return $this->_addFromExtracted($extractPath, $file, $target, $params, $available);
	}

	/**
	 * Add files to repo from extracted archive
	 *
	 * @return  boolean
	 */
	protected function _addFromExtracted($extractPath, $zipName, $target, $params, &$available)
	{
		$reserved  = isset($params['reserved']) ? $params['reserved'] : array();
		$dirPath   = isset($params['subdir']) ? $params['subdir'] : NULL;

		// Now copy extracted contents into project
		$extracted = $this->fileSystem->directories($extractPath);
		$extracted = \JFolder::files($extractPath, '.', true, true,
			$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX' ));

		$z = 0;
		foreach ($extracted as $e)
		{
			$fileinfo = pathinfo($e);
			$a_dir  = $fileinfo['dirname'];
			$a_dir	= str_replace($extractPath . DS, '', $a_dir);

			// Skip certain system files
			if (preg_match("/__MACOSX/", $e) OR preg_match("/.DS_Store/", $e))
			{
				continue;
			}
			$file = $fileinfo['basename'];
			$size = filesize($e);

			// Run some checks, stop in case of a problem
			if (!$this->_check($file, $e, $size, $available))
			{
				return false;
			}

			// Clean up filename
			$safe_dir  = $a_dir && $a_dir != '.' ? \Components\Projects\Helpers\Html::makeSafeDir($a_dir) : '';
			$safe_dir  = trim($safe_dir, DS);
			$safe_file = \Components\Projects\Helpers\Html::makeSafeFile($file);

			$skipDir = false;
			if (is_array($reserved) && $safe_dir && in_array(strtolower($safe_dir), $reserved))
			{
				$skipDir = true;
			}
			$safeName  = $safe_dir && !$skipDir ? $safe_dir . DS . $safe_file : $safe_file;
			$localPath = $dirPath ? $dirPath . DS . $safeName : $safeName;

			$where = $target . DS . $safeName;
			$exists = is_file($where) ? true : false;

			// Provision directory
			if ($safe_dir && !$skipDir && !is_dir($target . DS . $safe_dir ))
			{
				if ($this->fileSystem->makeDirectory( $target . DS . $safe_dir, 0755, true, true ))
				{
					// File object
					$localDirPath = $dirPath ? $dirPath . DS . $safe_dir : $safe_dir;
					$fileObject = new Models\File(trim($localDirPath), $this->get('path'));
					$fileObject->set('type', 'folder');
					$params['file']    = $fileObject;
					$params['replace'] = false;

					// Success - check in change
					$this->call('checkin', $params);
					$z++;
				}
			}

			// Copy file into project
			if ($this->fileSystem->copy($e, $target . DS . $safeName))
			{
				// File object
				$fileObject        = new Models\File(trim($localPath), $this->get('path'));
				$params['file']    = $fileObject;
				$params['replace'] = $exists;

				// Success - check in change
				$this->call('checkin', $params);
				$z++;
			}
		}

		return $z;
	}

	/**
	 * Pre-insert checks
	 *
	 * @return  boolean
	 */
	protected function _check($file, $tmp_name, $size, &$available, $sizeLimit = 0)
	{
		// Check against upload size limit
		if (intval($sizeLimit) && $size > intval($sizeLimit))
		{
			$this->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_EXCEEDS_LIMIT') . ' '
				. \Hubzero\Utility\Number::formatBytes($sizeLimit) . '. '
				. Lang::txt('PLG_PROJECTS_FILES_ERROR_TOO_LARGE_USE_OTHER_METHOD') );
			return false;
		}

		// Check against quota
		if ($size >= $available)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_OVER_QUOTA'));
			return false;
		}

		// One last check
		if ($tmp_name && \Components\Projects\Helpers\Html::virusCheck($tmp_name))
		{
			$this->setError(Lang::txt('Virus detected, refusing to upload'));
			return false;
		}

		// Reduce available space
		$available = $available - $size;

		return true;
	}

	/**
	 * Create local repo
	 *
	 * @return  boolean
	 */
	public function iniLocal()
	{
		// TBD
	}

	/**
	 * Connect to remote repo
	 *
	 * @return  boolean
	 */
	public function iniRemote()
	{
		// TBD
	}

	/**
	 * Get files stats for all projects
	 *
	 * @param      array 	$aliases	Project aliases for which to compute stats
	 * @param      string 	$get
	 *
	 * @return     mixed
	 */
	public function getStats($aliases = array(), $get = 'total')
	{
		if (empty($aliases))
		{
			return false;
		}

		$files     = 0;
		$diskSpace = 0;
		$commits   = 0;
		$usage     = 0;

		// Publication space
		if ($get == 'pubspace')
		{
			// Load publications component configs
			$pubconfig = Component::params( 'com_publications' );
			$base_path = DS . trim($pubconfig->get('webpath'), DS);

			chdir(PATH_CORE . $base_path);
			exec('du -sk ', $out);
			$used = 0;

			if ($out && isset($out[0]))
			{
				$kb = str_replace('.', '', trim($out[0]));
				$used = $kb * 1024;
			}

			return $used;
		}

		// Compute size of local project repos
		foreach ($aliases as $alias)
		{
			$path = \Components\Projects\Helpers\Html::getProjectRepoPath($alias, 'files', false);

			// Make sure there is .git directory
			if (!is_dir($path) || !is_dir($path . DS . '.git'))
			{
				continue;
			}

			if ($get == 'diskspace')
			{
				$diskSpace = $diskSpace + $this->call('getDiskUsage',
					$params = array(
						'path'    => $path,
						'working' => true,
						'git'     => true
					)
				);
			}
			else
			{
				$git = new \Components\Projects\Helpers\Git($path);
				if ($get == 'commitCount')
				{
					$nf = $git->callGit('ls-files --full-name ');

					if ($nf && substr($nf[0], 0, 5) != 'fatal')
					{
						$out = $git->callGit('log | grep "^commit" | wc -l' );

						if (is_array($out))
						{
							$c =  end($out);
							$commits = $commits + $c;
						}
					}
				}
				else
				{
					$count = count($git->getFiles());
					$files = $files + $count;

					if ($count > 1)
					{
						$usage++;
					}
				}
			}
		}

		// Output
		switch ($get)
		{
			case 'total':
				return $files;
				break;
			case 'usage':
				return $usage;
				break;
			case 'diskspace':
				return $diskSpace;
				break;
			case 'commitCount':
				return $commits;
				break;
		}
	}
}