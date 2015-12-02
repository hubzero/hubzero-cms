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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Filesystem;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * Tools controller class for managing user disk storage
 */
class Storage extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		if (User::isGuest())
		{
			// Redirect to home page
			App::redirect(
				$this->config->get('mw_redirect', '/home')
			);
			return;
		}

		// Get the task
		$this->_task = Request::getVar('task', '');
		$this->exceeded = false;

		// Check if middleware is enabled
		if ($this->_task != 'image'
		 && $this->_task != 'css'
		 && $this->_task != 'diskusage'
		 && (!$this->config->get('mw_on') || ($this->config->get('mw_on') > 1 && $this->_authorize() != 'admin')))
		{
			// Redirect to home page
			App::redirect(
				$this->config->get('mw_redirect', '/home')
			);
			return;
		}

		$this->_authorize('storage');

		parent::execute();
	}

	/**
	 * Build the document path (breadcrumbs)
	 *
	 * @return     void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt('COM_MEMBERS'),
				'index.php?option=com_members'
			);
		}
		Pathway::append(
			stripslashes(User::get('name')),
			'index.php?option=com_members&id=' . User::get('id')
		);
		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			'index.php?option=' . $this->_option . '&task=storage'
		);
	}

	/**
	 * Build the document title
	 *
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title  = Lang::txt('COM_MEMBERS');
		$this->_title .= ': ' . stripslashes(User::get('name'));
		$this->_title .= ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task));

		Document::setTitle($this->_title);
	}

	/**
	 * Show a login form
	 *
	 * @return     void
	 */
	protected function _login($rtrn)
	{
		if (!$rtrn)
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task), 'server');
		}
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
		return;
	}

	/**
	 * Display a warning message that the user has exceeded their allowed space
	 * then display a file list and options for managing disk usage
	 *
	 * @return     void
	 */
	public function storageexceededTask()
	{
		$this->displayTask(true);
	}

	/**
	 * Display a file list and options for managing disk usage
	 *
	 * @param      boolean $exceeded Exceeded allowed space?
	 * @return     void
	 */
	public function displayTask($exceeded=false)
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->_login();
			return;
		}

		$this->view->setLayout('display');

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Get their disk space usage
		$this->percent = 0;
		$this->view->monitor = '';
		if ($this->config->get('show_storage'))
		{
			$this->exceeded = $exceeded;
			$this->getDiskUsage();

			$view = new \Hubzero\Component\View(array(
				'name'   => $this->_controller,
				'layout' => 'diskusage'
			));
			$view->option    = $this->_option;
			$view->amt       = $this->percent;
			$view->du        = '';
			$view->percent   = 0;
			$view->msgs      = 0;
			$view->ajax      = 0;
			$view->writelink = 0;
			$view->total     = $this->total;

			$this->view->monitor = $view->loadTemplate();
		}

		// Instantiate the view
		$this->view->exceeded = $exceeded;
		$this->view->output = (isset($this->view->output)) ? $this->view->output : null;
		$this->view->percentage = $this->percent;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Purge old session data
	 *
	 * @return     void
	 */
	public function purgeTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->_login();
			return;
		}

		if (!($shost = $this->config->get('storagehost')))
		{
			App::redirect(
				Route::url($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount'))
			);
			return;
		}

		Request::checkToken();

		$degree = Request::getVar('degree', 'default');

		$info = array();
		$msg = '';

		$fp = stream_socket_client($shost, $errno, $errstr, 30);
		if (!$fp)
		{
			$info[] = "$errstr ($errno)\n";
			$this->setError("$errstr ($errno)\n");
		}
		else
		{
			fwrite($fp, 'purge user=' . User::get('username') . ",degree=$degree \n");
			while (!feof($fp))
			{
				$info[] = fgets($fp, 1024) . "\n";
			}
			fclose($fp);
		}

		foreach ($info as $line)
		{
			if (trim($line) != '')
			{
				$msg .= $line . '<br />';
			}
		}

		// Output HTML
		$this->view->output = $msg;

		$this->displayTask();
	}

	/**
	 * Determine the amount of disk usage
	 *
	 * @param      string $type Type [hard, soft]
	 * @return     void
	 */
	private function getDiskUsage($type='soft')
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->_login();
			return;
		}

		bcscale(6);

		$du = \Components\Tools\Helpers\Utils::getDiskUsage(User::get('username'));
		if (isset($du['space']))
		{
			if ($type == 'hard')
			{
				$val = ($du['hardspace'] != 0) ? bcdiv($du['space'], $du['hardspace']) : 0;
			}
			else
			{
				$val = ($du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			}
		} else {
			$val = 0;
		}
		$percent = round($val * 100);
		$percent = ($percent > 100) ? 100 : $percent;

		if (isset($du['softspace']))
		{
			$total = $du['softspace'] / 1024000000;
		}
		else
		{
			$total = 0;
		}

		$this->remaining = (isset($du['remaining'])) ? $du['remaining'] : 0;
		$this->percent   = $percent;
		$this->total     = $total;

		//if ($this->percent >= 100 && $this->remaining == 0) {
		if ($this->percent >= 100 && !$this->exceeded)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=storageexceeded')
			);
		}
	}

	/**
	 * Display how much disk usage is being used
	 *
	 * @return     void
	 */
	public function diskusageTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->_login();
			return;
		}

		$msgs = Request::getInt('msgs', 0);

		$du = \Components\Tools\Helpers\Utils::getDiskUsage(User::get('username'));
		if (count($du) <=1)
		{
			// error
			$percent = 0;
		}
		else
		{
			bcscale(6);
			$val = (isset($du['softspace']) && $du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			$percent = round($val * 100);
		}

		$amt = ($percent > 100) ? '100' : $percent;
		$total = (isset($du['softspace'])) ? $du['softspace'] / 1024000000 : 0;

		$this->view->amt       = $amt;
		$this->view->total     = $total;
		$this->view->du        = $du;
		$this->view->percent   = $percent;
		$this->view->msgs      = $msgs;
		$this->view->ajax      = 1;
		$this->view->writelink = 1;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Construct the path to be used for file management
	 *
	 * @param      string $listdir Base directory
	 * @param      string $subdir  Sub-directory
	 * @return     string
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		// Get the configured upload path
		$base = $this->config->get('storagepath', 'webdav' . DS . 'home') . DS . User::get('username');
		if ($base)
		{
			$base = DS . trim($base, DS);
		}

		$listdir = DS . trim($listdir, DS);

		if ($subdir)
		{
			$subdir = DS . trim($subdir, DS);
		}

		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base)) == $base)
		{
			// Yes - ... this really shouldn't happen
			App::abort(500, Lang::txt('COM_TOOLS_ERROR_BAD_FILE_PATH'));
			return;
		}

		// Build the path
		return $base . $listdir . $subdir;
	}

	/**
	 * Delete a folder
	 *
	 * @return     void
	 */
	public function deletefolderTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$this->filelistTask();
			return;
		}

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = urldecode(Request::getVar('listdir', ''));
		/*if (!$listdir)
		{
			$this->setError(Lang::txt('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			$this->filelistTask();
			return;
		}*/

		// Build the path
		$path = $this->_buildUploadPath($listdir);

		// Incoming directory to delete
		if (!($folder = urldecode(Request::getVar('delFolder', ''))))
		{
			$this->setError(Lang::txt('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			$this->filelistTask();
			return;
		}

		$folder = DS . trim($folder, DS);

		// Check if the folder even exists
		if (!is_dir($path . $folder) or !$folder)
		{
			$this->setError(Lang::txt('COM_TOOLS_DIRECTORY_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory($path . $folder))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_DIRECTORY'));
			}
		}

		// Push through to the media view
		$this->filelistTask();
	}

	/**
	 * Delete a file
	 *
	 * @return     void
	 */
	public function deletefileTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$this->filelistTask();
			return;
		}

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = urldecode(Request::getVar('listdir', ''));

		// Build the path
		$path = $this->_buildUploadPath($listdir);

		// Incoming file to delete
		if (!($file = urldecode(Request::getVar('file', ''))))
		{
			$this->setError(Lang::txt('COM_TOOLS_FILE_NOT_FOUND'));
			$this->filelistTask();
			return;
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
			}
		}

		// Push through to the media view
		$this->filelistTask();
	}

	/**
	 * Show a file list
	 *
	 * @return     void
	 */
	public function filelistTask()
	{
		$this->view->setLayout('filelist');

		$listdir = Request::getVar('listdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir);

		$dirtree = array();
		$subdir = $listdir;

		if ($subdir)
		{
			$subdir = trim($subdir, DS);

			$dirtree = explode(DS, $subdir);
		}

		$folders = array();
		$docs    = array();

		if (is_dir($path))
		{
			// Loop through all files and separate them into arrays of docs and folders
			$dirIterator = new \DirectoryIterator($path);
			foreach ($dirIterator as $file)
			{
				if ($file->isDot())
				{
					continue;
				}

				if ($file->isDir())
				{
					$name = $file->getFilename();
					$folders[$path . DS . $name] = $name;
					continue;
				}

				if ($file->isFile())
				{
					$name = $file->getFilename();
					if (('cvs' == strtolower($name))
					 || ('.svn' == strtolower($name)))
					{
						continue;
					}

					$docs[$path . DS . $name] = $name;
				}
			}

			ksort($folders);
			ksort($docs);
		}

		// Instantiate a view
		$this->view->dirtree = $dirtree;
		$this->view->docs = $docs;
		$this->view->folders = $folders;
		$this->view->config = $this->config;
		$this->view->listdir = $listdir;
		$this->view->path = $path;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Authorization checks
	 *
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	public function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!User::isGuest())
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}
}

