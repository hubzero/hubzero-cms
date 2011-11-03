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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'XImportController'
 * 
 * Long description (if any) ...
 */
class XImportController extends Hubzero_Controller
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
		if (!$this->_authorize()) {
			JError::raiseError(403, JText::_('Access Restricted'));
			return;
		}

		$this->_log = JPATH_ROOT . DS . 'components' . DS . 'com_ximport' . DS . 'logs' . DS . 'runs.log';

		$this->_task = strtolower(JRequest::getVar('task', 'browse'));

		switch ($this->_task)
		{
			case 'run':
				$this->_run();
			break;

			case 'browse':
			default:
				$this->_browse();
			break;
		}
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_('XImport'),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			$pathway->addItem(
				JText::_(ucfirst($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
	}

	/**
	 * Outputs a list of available scripts
	 *
	 * @return	void
	 */
	protected function _browse()
	{
		$this->_buildPathway();

		$view = new JView(array('name'=>'browse'));
		$view->option = $this->_option;

		$view->path = JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'scripts';
		$view->paths = $this->_scanDirectory($view->path);
		$view->log = $this->_readLog();

		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Runs a script and echos the script's output
	 *
	 * @return	void
	 */
	protected function _run()
	{
		// Instantiate a new view
		$view = new JView(array('name'=>'run'));

		// Get posted script name
		$view->script = JRequest::getVar('script', '', 'post');

		// If no script name passed, default to the _browse method
		if (!$view->script) {
			$this->_task = 'browse';
			return $this->_browse();
		}

		// Build the path to the script file
		$path = JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'scripts' . DS . $view->script . '.php';

		// Check for the script file
		if (is_file($path)) {
			// Include the script
			include_once($path);
			if (class_exists($view->script)) {
				// Instantiate the script
				$job = new $view->script();
				// Ensure the script is of the right type
				if ($job instanceof XImportHelperScript) {
					ob_start();
					$job->run();
					$view->content = ob_get_contents();
					ob_end_clean();

					// Log the script run
					ximport('Hubzero_Log_FileHandler');

					$logger = new Hubzero_Log_FileHandler($this->_log);
					$logger->log(64, $view->script);
				}
			}
		}

		$this->_buildPathway();

		// Display the view
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
	   while ($stack) {
	       $thisdir = array_pop($stack);
	       if ($dircont = scandir($thisdir)) {
	           $i=0;
	           while (isset($dircont[$i])) {
	               if ($dircont[$i] !== '.' && $dircont[$i] !== '..' && $dircont[$i] !== '.DS_Store') {
	                   $current_file = "{$thisdir}/{$dircont[$i]}";
	                   if (is_file($current_file) && substr($current_file, -4) == '.php') {
	                       $path[] = "{$thisdir}/{$dircont[$i]}";
	                   } elseif (is_dir($current_file)) {
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

		if (!file_exists($this->_log)) {
			return $log;
		}

		$fp = fopen($this->_log, "r");
		if ($fp) {
			while (!feof($fp))
			{
				$line = fgets($fp);
				$timestamp = substr($line, 0, 19);
				$script = trim(str_replace($timestamp, '', $line));
				if (!isset($log[$script])) {
					$log[$script] = array(
						'lastRun' => '',
						'totalRuns' => 0
					);
				}
				$log[$script]['lastRun'] = $timestamp;
				$log[$script]['totalRuns'] = $log[$script]['totalRuns'] + 1;
			}
		}

		return $log;
	}

	/**
	 * Checks if the user is authorized or not
	 *
	 * @return	boolean 	True if authorized
	 */
	protected function _authorize()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}

		return false;
	}
}
