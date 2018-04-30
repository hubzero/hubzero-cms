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
 * @author    Drew Thoennes <dthoenne@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin\Controllers;

use Hubzero\Component\AdminController;
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

		$folder = Request::getVar('folder', '', '', 'path');
		$session = App::get('session');
		$state = User::getState('folder');
		$folders = Filesystem::directoryTree(COM_MEDIA_BASE);
		$folderTree = MediaHelper::_buildFolderTree($folders);

		MediaHelper::createPath($folders, COM_MEDIA_BASE);

		Html::behavior('framework', true);
		\Hubzero\Document\Assets::addComponentScript('com_media', 'mediamanager.js');
		\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'mediamanager.css');
		Html::asset('script', 'system/jquery.treeview.js', true, true, false, false);
		Html::asset('stylesheet', 'system/jquery.treeview.css', array(), true);

		$this->view
			->set('require_ftp', true)
			->set('session', App::get('session'))
			->set('config', Component::params('com_media'))
			->set('state', $state)
			->set('require_ftp', User::getState('ftp'))
			->set('folders_id', ' id="media-tree"')
			->set('folder', $folder)
			->set('folders', $folders)
			->set('folderTree', $folderTree)
			->set('parent', MediaHelper::getParent($folder))
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
		Request::checkToken(['get', 'post']);

		$folder      = Request::getCmd('foldername', '');
		$folderCheck = Request::getVar('foldername', null, '', 'string');
		$parent      = Request::getVar('parent', '', '', 'path');

		$rtrn = Route::url('index.php?option=com_media&controller=medialist&folder=' . $parent);

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
				$object_file = new \Hubzero\Base\Object(array('filepath' => $path));

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
	 * Upload
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Session::checkToken(['get', 'post']);
		$params = Component::params('com_media');

		// Get some data from the request
		$files  = Request::getVar('Filedata', '', 'files', 'array');
		$return = Request::getVar('return-url', null, 'post', 'base64');
		$this->folder = Request::getVar('folder', '', '', '');
		$parent = Request::getVar('parent', '', '', '');
		// Set the redirect
		if ($return)
		{
			$this->setRedirect(base64_decode($return) . '&folder=' . $this->folder);
		}

		// Authorize the user
		//if (!$this->authoriseUser('create'))
		//{
		//        return false;
		//}
		if (
			$_SERVER['CONTENT_LENGTH']>($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('upload_max_filesize'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('post_max_size'))* 1024 * 1024 ||
			(($_SERVER['CONTENT_LENGTH'] > (int) (ini_get('memory_limit')) * 1024 * 1024) && ((int) (ini_get('memory_limit')) != -1))
		)
		{
			Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
			return false;
		}
		// Input is in the form of an associative array containing numerically indexed arrays
		// We want a numerically indexed array containing associative arrays
		// Cast each item as array in case the Filedata parameter was not sent as such
		$files = array_map(array($this, 'reformatFilesArray'),
			(array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['error'], (array) $files['size']
		);

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			if ($file['error']==1)
			{
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
				return false;
			}
			if ($file['size']>($params->get('upload_maxsize', 0) * 1024 * 1024))
			{
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
				return false;
			}
			if (Filesystem::exists($file['filepath']))
			{
				// A file with this name already exists
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_FILE_EXISTS'));
				return false;
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by Filesystem::clean()
				$this->setRedirect('index.php', Lang::txt('COM_MEDIA_INVALID_REQUEST'), 'error');
				return false;
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
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new \Hubzero\Base\Object($file);
			$result = Event::trigger('content.onContentBeforeSave', array('com_media.file', &$object_file, true));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				return false;
			}
			if (!Filesystem::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				Event::trigger('content.onContentAfterSave', array('com_media.file', &$object_file, true));
				$this->setMessage(Lang::txt('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE))));
			}
		}
		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=medialist&tmpl=component&folder=' . $parent, false));
	}

	public function deleteTask()
	{
		Request::checkToken(['get', 'post']);

		// Get some data from the request
		$tmpl   = Request::getCmd('tmpl');
		$paths  = Request::getVar('rm', array(), '', 'array');
		$folder = Request::getVar('folder', '', '', 'path');
		$rm     = Request::getVar('rm', array());

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
			if ($path !== Filesystem::clean($path))
			{
				// filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_MEDIA_BASE))));
				continue;
			}

			$fullPath = Filesystem::cleanPath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			$object_file = new \Hubzero\Base\Object(array('filepath' => $fullPath));
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
