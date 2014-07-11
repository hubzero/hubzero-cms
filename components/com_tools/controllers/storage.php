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

/**
 * Tools controller class for managing user disk storage
 */
class ToolsControllerStorage extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		if ($this->juser->get('guest'))
		{
			// Redirect to home page
			$this->setRedirect(
				$this->config->get('mw_redirect', '/home')
			);
			return;
		}

		// Get the task
		$this->_task = JRequest::getVar('task', '');

		// Check if middleware is enabled
		if ($this->_task != 'image'
		 && $this->_task != 'css'
		 && $this->_task != 'diskusage'
		 && (!$this->config->get('mw_on') || ($this->config->get('mw_on') > 1 && $this->_authorize() != 'admin')))
		{
			// Redirect to home page
			$this->_redirect = $this->config->get('mw_redirect', '/home');
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
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_('COM_MEMBERS'),
				'index.php?option=com_members'
			);
		}
		$pathway->addItem(
			stripslashes($this->juser->get('name')),
			'index.php?option=com_members&id=' . $this->juser->get('id')
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_option . '_' . $this->_task)),
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
		$this->_title  = JText::_('COM_MEMBERS');
		$this->_title .= ': ' . stripslashes($this->juser->get('name'));
		$this->_title .= ': ' . JText::_(strtoupper($this->_option . '_' . $this->_task));

		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
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
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task), 'server');
		}
		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
		return;
	}

	/**
	 * Display a warning message that the user has exceeded their allowed space
	 * then display a file list and options for managing disk usage
	 *
	 * @return     void
	 */
	public function exceededTask()
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
		if ($this->juser->get('guest'))
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
			$this->getDiskUsage();
			$this->_redirect = '';

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
	 * Purge old session data
	 *
	 * @return     void
	 */
	public function purgeTask()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest'))
		{
			$this->_login();
			return;
		}

		if (!($shost = $this->config->get('storagehost')))
		{
			$this->setRedirect(
				JRoute::_($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount'))
			);
			return;
		}

		$degree = JRequest::getVar('degree', 'default');

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
			fwrite($fp, 'purge user=' . $this->juser->get('username') . ",degree=$degree \n");
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
		if ($this->juser->get('guest'))
		{
			$this->_login();
			return;
		}

		bcscale(6);

		$du = ToolsHelperUtils::getDiskUsage($this->juser->get('username'));
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
		if ($this->percent >= 100)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=storageexceeded')
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
		if ($this->juser->get('guest'))
		{
			$this->_login();
			return;
		}

		$msgs = JRequest::getInt('msgs', 0);

		$du = ToolsHelperUtils::getDiskUsage($this->juser->get('username'));
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
	 * Construct the path to be used for file management
	 *
	 * @param      string $listdir Base directory
	 * @param      string $subdir  Sub-directory
	 * @return     string
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		// Get the configured upload path
		$base = $this->config->get('storagepath', 'webdav' . DS . 'home') . DS . $this->juser->get('username');
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
			JError::raiseError(500, JText::_('COM_TOOLS_ERROR_BAD_FILE_PATH'));
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
		if ($this->juser->get('guest'))
		{
			$this->filelistTask();
			return;
		}

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = urldecode(JRequest::getVar('listdir', ''));
		/*if (!$listdir)
		{
			$this->setError(JText::_('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			$this->filelistTask();
			return;
		}*/

		// Build the path
		$path = $this->_buildUploadPath($listdir);

		// Incoming directory to delete
		if (!($folder = urldecode(JRequest::getVar('delFolder', ''))))
		{
			$this->setError(JText::_('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			$this->filelistTask();
			return;
		}

		$folder = DS . trim($folder, DS);

		// Check if the folder even exists
		if (!is_dir($path . $folder) or !$folder)
		{
			$this->setError(JText::_('COM_TOOLS_DIRECTORY_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.folder');
			if (!JFolder::delete($path . $folder))
			{
				$this->setError(JText::_('COM_TOOLS_UNABLE_TO_DELETE_DIRECTORY'));
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
		if ($this->juser->get('guest'))
		{
			$this->filelistTask();
			return;
		}

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = urldecode(JRequest::getVar('listdir', ''));

		// Build the path
		$path = $this->_buildUploadPath($listdir);

		// Incoming file to delete
		if (!($file = urldecode(JRequest::getVar('file', ''))))
		{
			$this->setError(JText::_('COM_TOOLS_FILE_NOT_FOUND'));
			$this->filelistTask();
			return;
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(JText::_('COM_TOOLS_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError(JText::_('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
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

		$listdir = JRequest::getVar('listdir', '');

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
			$dirIterator = new DirectoryIterator($path);
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
	 * Authorization checks
	 *
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	public function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!$this->juser->get('guest'))
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
			$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
		}
	}
}

