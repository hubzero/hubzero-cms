<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_COMPONENT. DS . 'helpers' . DS . 'script.php');

/**
 * System controller class for scripts
 */
class SystemControllerScripts extends \Hubzero\Component\AdminController
{
	/**
	 * Path to a log file
	 *
	 * @var	string
	 */
	private $_log = '';

	/**
	 * Executes the task passed to it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->_authorize();

		if (!$this->config->get('access-admin-component', 0))
		{
			JError::raiseError(403, JText::_('Access Restricted'));
			return;
		}

		$jconfig = JFactory::getConfig();

		$this->_log = $jconfig->getValue('config.log_path') . DS . 'ximport.php';

		$this->registerTask('__default', 'browse');

		parent::execute();
	}

	/**
	 * Outputs a list of available scripts
	 *
	 * @return	void
	 */
	public function browseTask()
	{
		$this->view->path  = JPATH_COMPONENT . DS . 'scripts';
		$this->view->paths = $this->_scanDirectory($this->view->path);
		$this->view->log   = $this->_readLog();

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
	 * Runs a script and echos the script's output
	 *
	 * @return	void
	 */
	public function runTask()
	{
		// Get posted script name
		$this->view->script = JRequest::getVar('script', '', 'post');

		// If no script name passed, default to the _browse method
		if (!$this->view->script)
		{
			$this->_task = 'browse';
			return $this->browseTask();
		}

		// Build the path to the script file
		$path = JPATH_COMPONENT . DS . 'scripts' . DS . $this->view->script . '.php';

		// Check for the script file
		if (is_file($path))
		{
			// Include the script
			include_once($path);

			if (class_exists($this->view->script))
			{
				// Instantiate the script
				$job = new $this->view->script();
				// Ensure the script is of the right type
				if ($job instanceof SystemHelperScript)
				{
					ob_start();
					$job->run();
					$this->view->content = ob_get_contents();
					ob_end_clean();
				}
			}
		}

		// Display the view
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
	 * Scans a directory and builds an array of PHP files
	 *
	 * @param	string	$dir	Directory to scan
	 * @return	array
	 */
	private function _scanDirectory($dir)
	{
		$path = array();
		$stack[] = $dir;
		while ($stack)
		{
			$thisdir = array_pop($stack);
			if ($dircont = scandir($thisdir))
			{
				$i=0;
				while (isset($dircont[$i]))
				{
					if ($dircont[$i] !== '.' && $dircont[$i] !== '..' && $dircont[$i] !== '.DS_Store')
					{
						$current_file = "{$thisdir}/{$dircont[$i]}";
						if (is_file($current_file) && substr($current_file, -4) == '.php')
						{
							$path[] = "{$thisdir}/{$dircont[$i]}";
						}
						elseif (is_dir($current_file))
						{
							$path[] = "{$thisdir}/{$dircont[$i]}";
							$stack[] = $current_file;
						}
					}
					$i++;
				}
			}
		}
		return $path;
	}

	/**
	 * Returns an array of script names with their last run and total runs
	 *
	 * @return	array()
	 */
	private function _readLog()
	{
		$log = array();

		if (!file_exists($this->_log))
		{
			return $log;
		}

		$fp = fopen($this->_log, "r");
		if ($fp)
		{
			while (!feof($fp))
			{
				$line = fgets($fp);
				$timestamp = substr($line, 0, 19);
				$script = trim(str_replace($timestamp, '', $line));
				if (!isset($log[$script]))
				{
					$log[$script] = array(
						'lastRun'   => '',
						'totalRuns' => 0
					);
				}
				$log[$script]['lastRun']   = $timestamp;
				$log[$script]['totalRuns'] = $log[$script]['totalRuns'] + 1;
			}
		}

		return $log;
	}

	/**
	 * Authorization check
	 *
	 * @param      string  $assetType Asset type to authorize
	 * @param      integer $assetId   ID of asset to authorize
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, false);
		if (!$this->juser->get('guest'))
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
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
			else
			{
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
		}
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
