<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Number;
use Components\Media\Models\Files;
use Components\Media\Admin\Helpers\MediaHelper;
use Filesystem;
use Request;
use Route;
use Event;
use User;
use Lang;
use Html;
use App;

/**
 * Media controller
 */
class Media extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of files
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array();

		$folder = Request::getString('folder', '');
		$session = App::get('session');
		$state = User::getState('folder');
		$folders = Filesystem::directoryTree(COM_MEDIA_BASE);

		$fold = array(
			'id' => 0,
			'parent' => -1,
			'name' => '', //basename(COM_MEDIA_BASE),
			'fullname' => COM_MEDIA_BASE,
			'relname' => substr(COM_MEDIA_BASE, strlen(PATH_ROOT))
		);

		array_unshift($folders, $fold);

		$folderTree = MediaHelper::_buildFolderTree($folders, -1);

		$folderTree[0]['name'] = basename(COM_MEDIA_BASE);

		MediaHelper::createPath($folders, COM_MEDIA_BASE);

		$layout = Request::getState('media.list.layout', 'layout', 'thumbs', 'word');

		$this->view
			->set('session', App::get('session'))
			->set('config', Component::params('com_media'))
			->set('state', $state)
			->set('require_ftp', User::getState('ftp'))
			->set('folders_id', ' id="media-tree"')
			->set('folder', $folder)
			->set('folders', $folders)
			->set('folderTree', $folderTree)
			->set('parent', MediaHelper::getParent($folder))
			->set('layout', $layout)
			->setLayout('default')
			->display();
	}

	/**
	 * New entry
	 *
	 * @return  void
	 */
	public function newTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxNewTask();
		}

		$folder      = Request::getCmd('foldername', '');
		$folderCheck = Request::getString('foldername', null);
		$parent      = Request::getString('parent', '');

		$rtrn = Route::url('index.php?option=com_media&controller=medialist&folder=' . $parent);

		$no_html = Request::getInt('no_html');

		if (strlen($folder) > 0)
		{
			if (!User::authorise('core.create', $this->_option))
			{
				// User is not authorised to delete
				Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
				App::redirect($rtrn);
			}

			Request::setVar('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck))
			{
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'));
				App::redirect($rtrn);
			}

			$path = \Hubzero\Filesystem\Util::normalizePath(COM_MEDIA_BASE . $parent . DS . $folder);

			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new \Hubzero\Base\Obj(array('filepath' => $path));

				$result = Event::trigger('content.onContentBeforeSave', array('com_media.folder', &$object_file, true));

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
					App::redirect($rtrn);
				}

				Filesystem::makeDirectory($path);
				$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
				Filesystem::write($path . '/index.html', $data);

				// Trigger the onContentAfterSave event.
				Event::trigger('content.onContentAfterSave', array('com_media.folder', &$object_file, true));

				Notify::success(Lang::txt('COM_MEDIA_CREATE_COMPLETE', substr($path, strlen(COM_MEDIA_BASE))));
			}

			Request::setVar('folder', ($parent) ? $parent . DS . $folder : $folder);
		}

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=medialist&tmpl=component&folder=' . $parent, false));
	}

	/**
	 * New entry
	 *
	 * @return  void
	 */
	public function ajaxNewTask()
	{
		$folder      = Request::getCmd('foldername', '');
		$folderCheck = Request::getString('foldername', null);
		$parent      = Request::getString('parent', '');

		$no_html = Request::getInt('no_html');

		if (strlen($folder) > 0)
		{
			if (!User::authorise('core.create', $this->_option))
			{
				// User is not authorised to delete
				echo json_encode(array(
					'success' => false,
					'error' => Lang::txt('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED')
				));
				return;
			}

			Request::setVar('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck))
			{
				echo json_encode(array(
					'success' => false,
					'error' => Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME')
				));
				return;
			}

			$path = \Hubzero\Filesystem\Util::normalizePath(COM_MEDIA_BASE . ($parent ? $parent . DS : '') . $folder);

			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new \Hubzero\Base\Obj(array('filepath' => $path));

				$result = Event::trigger('content.onContentBeforeSave', array('com_media.folder', &$object_file, true));

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					echo json_encode(array(
						'success' => false,
						'error' => Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
					));
					return;
				}

				Filesystem::makeDirectory($path);
				$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
				Filesystem::write($path . '/index.html', $data);

				// Trigger the onContentAfterSave event.
				Event::trigger('content.onContentAfterSave', array('com_media.folder', &$object_file, true));
			}

			Request::setVar('folder', ($parent) ? $parent . DS . $folder : $folder);
		}

		echo json_encode(array(
			'success' => true,
			'directory' => COM_MEDIA_BASE . ($parent ? $parent . DS : '') . $folder
		));
	}

	/**
	 * Upload
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Session::checkToken(['get', 'post']);

		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		$params = Component::params('com_media');

		// Get some data from the request
		$files  = Request::getArray('Filedata', array(), 'files');
		$this->folder = Request::getString('folder', '');
		$parent = Request::getString('parent', '');
		$return = Request::getString('return-url', '', 'post');

		// Set the redirect
		if ($return)
		{
			$return = base64_decode($return) . '&folder=' . $this->folder;
		}
		else
		{
			$return = 'index.php?option=' . $this->_option . '&controller=medialist&tmpl=component&folder=' . $parent;
		}

		// Authorize the user
		if (!User::authorise('core.create', $this->_option))
		{
			App::redirect(Route::url($return, false));
		}

		// Input is in the form of an associative array containing numerically indexed arrays
		// We want a numerically indexed array containing associative arrays
		// Cast each item as array in case the Filedata parameter was not sent as such
		$files = array_map(
			array($this, 'reformatFilesArray'),
			(array) $files['name'],
			(array) $files['type'],
			(array) $files['tmp_name'],
			(array) $files['error'],
			(array) $files['size']
		);

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			if ($file['error'] == 1)
			{
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
				continue;
			}

			if ($file['size'] > ($params->get('upload_maxsize', 0) * 1024 * 1024)
			 || $file['size'] > (int)(ini_get('upload_max_filesize'))* 1024 * 1024
			 || $file['size'] > (int)(ini_get('post_max_size'))* 1024 * 1024
			 || (($file['size'] > (int) (ini_get('memory_limit')) * 1024 * 1024) && ((int) (ini_get('memory_limit')) != -1)))
			{
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
				continue;
			}

			if (Filesystem::exists($file['filepath']))
			{
				// A file with this name already exists
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_FILE_EXISTS'));
				continue;
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by Filesystem::clean()
				Notify::error(Lang::txt('COM_MEDIA_INVALID_REQUEST'));
				continue;
			}
		}

		foreach ($files as &$file)
		{
			// The request is valid
			$err = null;
			if (!MediaHelper::canUpload($file, $err))
			{
				// The file can't be upload
				Notify::warning(Lang::txt($err));
				continue;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new \Hubzero\Base\Obj($file);
			$result = Event::trigger('content.onContentBeforeSave', array('com_media.file', &$object_file, true));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				continue;
			}

			if (!Filesystem::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
				continue;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				Event::trigger('content.onContentAfterSave', array('com_media.file', &$object_file, true));

				Notify::success(Lang::txt('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE))));
			}
		}

		App::redirect(Route::url($return, false));
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function ajaxUploadTask()
	{
		$params = Component::params('com_media');

		// Size limit is in MB, so we need to turn it into just B
		$sizeLimit = $params->get('upload_maxsize', 10);
		$sizeLimit = $sizeLimit * 1024 * 1024;

		// get the file
		if (isset($_GET['qqfile']) && isset($_SERVER["CONTENT_LENGTH"])) // make sure we actually have a file
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']) && isset($_FILES['qqfile']['size']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			echo json_encode(array('error' => Lang::txt('File not found')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('File is empty')));
			return;
		}

		if ($size > $sizeLimit
		 || $size > (int)(ini_get('upload_max_filesize'))* 1024 * 1024
		 || $size > (int)(ini_get('post_max_size'))* 1024 * 1024
		 || (($size > (int) (ini_get('memory_limit')) * 1024 * 1024) && ((int) (ini_get('memory_limit')) != -1)))
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('File is too large. Max file upload size is %s', $max)));
			return;
		}

		// don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);
		$ext = strtolower(Filesystem::extension($file));

		$filename = Filesystem::name($filename) . '.' . $ext;

		$folder = Request::getString('folder', '');
		$path = Filesystem::cleanPath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $filename)));
		$path = dirname($path);

		// Define upload directory and make sure its writable
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				echo json_encode(array('error' => Lang::txt('Error uploading. Unable to create path.')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array(
				'success' => false,
				'error' => Lang::txt('Server error. Upload directory isn\'t writable.')
			));
			return;
		}

		// Make sure that file is acceptable type
		$exts = $params->get('upload_extensions');
		$allowed = array_values(array_filter(explode(',', $exts)));

		if (!in_array(strtolower($ext), $allowed))
		{
			echo json_encode(array(
				'success' => false,
				'error' => Lang::txt('COM_MEDIA_ERROR_INCORRECT_FILE_TYPE') . $ext
			));
			return;
		}

		$file = $path . DIRECTORY_SEPARATOR . $filename;

		$object_file = new \Hubzero\Base\Obj($file);
		$result = Event::trigger('content.onContentBeforeSave', array('com_media.file', &$object_file, true));
		if (in_array(false, $result, true))
		{
			echo json_encode(array(
				'success' => false,
				'error'   => Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
			));
			return;
		}

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		if (!Filesystem::isSafe($file))
		{
			if (Filesystem::delete($file))
			{
				echo json_encode(array(
					'success' => false,
					'error'   => Lang::txt('ATTACHMENT: File rejected because the anti-virus scan failed.')
				));
				return;
			}
		}

		// Trigger the onContentAfterSave event.
		Event::trigger('content.onContentAfterSave', array('com_media.file', &$object_file, true));

		echo json_encode(array(
			'success'    => true,
			'file'       => $filename,
			'directory'  => $folder,
		));
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		Request::checkToken(['get', 'post']);

		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxDeleteTask();
		}

		// Get some data from the request
		$tmpl   = Request::getCmd('tmpl');
		$paths  = Request::getArray('rm', array(), '', 'array');
		$folder = Request::getString('folder', '');
		$rm     = Request::getArray('rm', array());

		$redirect = 'index.php?option=com_media&folder=' . $folder;
		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=medialist&tmpl=component';
		}
		$this->setRedirect($redirect);

		// Nothing to delete
		if (empty($rm))
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&folder=' . $folder, false));
		}

		// Authorize the user
		if (!User::authorise('core.delete', $this->_option))
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&folder=' . $folder, false));
		}

		// Initialise variables.
		$ret = true;
		foreach ($rm as $path)
		{
			$path = urldecode($path);

			/*if ($path !== Filesystem::clean($path))
			{
				// filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_MEDIA_BASE))));
				continue;
			}*/

			$fullPath = Filesystem::cleanPath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			$object_file = new \Hubzero\Base\Obj(array('filepath' => $fullPath));
			if (is_file($fullPath))
			{
				// Trigger the onContentBeforeDelete event.
				$result = Event::trigger('content.onContentBeforeDelete', array('com_media.file', &$object_file));
				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
					continue;
				}

				$ret &= Filesystem::delete($fullPath);

				// Trigger the onContentAfterDelete event.
				Event::trigger('content.onContentAfterDelete', array('com_media.file', &$object_file));
				$this->setMessage(Lang::txt('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE))));
			}
			elseif (is_dir($fullPath))
			{
				$contents = Filesystem::files($fullPath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
				if (empty($contents))
				{
					// Trigger the onContentBeforeDelete event.
					$result = Event::trigger('content.onContentBeforeDelete', array('com_media.folder', &$object_file));
					if (in_array(false, $result, true))
					{
						// There are some errors in the plugins
						Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
						continue;
					}

					$ret &= Filesystem::deleteDirectory($fullPath);

					// Trigger the onContentAfterDelete event.
					Event::trigger('content.onContentAfterDelete', array('com_media.folder', &$object_file));
					$this->setMessage(Lang::txt('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE))));
				}
				else
				{
					// This makes no sense...
					Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', substr($fullPath, strlen(COM_MEDIA_BASE))));
				}
			}
		}

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&folder=' . $folder, false));
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function ajaxDeleteTask()
	{
		Request::checkToken(['get', 'post']);

		// Get some data from the request
		$tmpl   = Request::getCmd('tmpl');
		$paths  = Request::getArray('rm', array());
		$folder = Request::getString('folder', '');
		$rm     = Request::getArray('rm', array());

		// Nothing to delete
		if (empty($rm))
		{
			echo json_encode(array(
				'success' => false,
				'error' => Lang::txt('No data provided')
			));
			return;
		}

		// Authorize the user
		if (!User::authorise('core.delete', $this->_option))
		{
			echo json_encode(array(
				'success' => false,
				'error' => Lang::txt('Not authorized')
			));
			return;
		}

		$this->setErrors(array());

		// Initialise variables.
		$ret = true;
		$msg = array();
		foreach ($rm as $path)
		{
			$path = urldecode($path);

			/*if ($path !== Filesystem::clean($path))
			{
				// filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				$this->setError(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_MEDIA_BASE))));
				continue;
			}*/

			$fullPath = Filesystem::cleanPath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));

			$object_file = new \Hubzero\Base\Obj(array('filepath' => $fullPath));
			if (is_file($fullPath))
			{
				// Trigger the onContentBeforeDelete event.
				$result = Event::trigger('content.onContentBeforeDelete', array('com_media.file', &$object_file));
				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					$this->setError(Lang::txts('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
					continue;
				}

				$ret &= Filesystem::delete($fullPath);

				// Trigger the onContentAfterDelete event.
				Event::trigger('content.onContentAfterDelete', array('com_media.file', &$object_file));
				$msg[] = Lang::txt('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE)));
			}
			elseif (is_dir($fullPath))
			{
				$contents = Filesystem::files($fullPath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
				if (empty($contents))
				{
					// Trigger the onContentBeforeDelete event.
					$result = Event::trigger('content.onContentBeforeDelete', array('com_media.folder', &$object_file));
					if (in_array(false, $result, true))
					{
						// There are some errors in the plugins
						$this->setError(Lang::txts('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
						continue;
					}

					$ret &= Filesystem::deleteDirectory($fullPath);

					// Trigger the onContentAfterDelete event.
					Event::trigger('content.onContentAfterDelete', array('com_media.folder', &$object_file));
					$msg[] = Lang::txt('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE)));
				}
				else
				{
					// This makes no sense...
					$this->setError(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', substr($fullPath, strlen(COM_MEDIA_BASE))));
				}
			}
		}

		$data = array(
			'success' => true,
			'message' => $msg
		);

		if ($this->getError())
		{
			$data['error'] = $this->getErrors();
		}

		echo json_encode($data);
	}

	/**
	 * Download a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		Request::checkToken(['get', 'post']);

		// Get some data from the request
		$tmpl = Request::getCmd('tmpl');
		$file = urldecode(Request::getString('file', ''));
		$file = \Hubzero\Filesystem\Util::checkPath(COM_MEDIA_BASE . $file);

		if (!is_file($file))
		{
			App::abort(404);
		}

		// Initiate a new content server and serve up the file
		$server = new \Hubzero\Content\Server();
		$server->filename($file);
		$server->disposition('attachment');
		$server->acceptranges(false); // @TODO fix byte range support

		if (!$server->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('COM_MEDIA_ERROR_SERVING_FILE'));
		}

		exit;
	}

	/**
	 * Format file data
	 *
	 * @param   string   $name
	 * @param   string   $type
	 * @param   string   $tmp_name
	 * @param   string   $error
	 * @param   integer  $size
	 * @return  array
	 */
	protected function reformatFilesArray($name, $type, $tmp_name, $error, $size)
	{
		$name = Filesystem::clean($name);

		return array(
			'name'     => $name,
			'type'     => $type,
			'tmp_name' => $tmp_name,
			'error'    => $error,
			'size'     => $size,
			'filepath' => Filesystem::cleanPath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $name)))
		);
	}
}
