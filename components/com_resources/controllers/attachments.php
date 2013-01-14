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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'assoc.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');

/**
 * Controller class for contributing a tool
 */
class ResourcesControllerAttachments extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			JError::raiseError(403, JText::_('You must be logged in to access.'));
			return;
		}

		// Load the com_resources component config
		$rconfig =& JComponentHelper::getParams('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Reorder an attachment
	 * 
	 * @return     void
	 */
	public function reorderTask()
	{
		// Incoming
		$id   = JRequest::getInt('id', 0);
		$pid  = JRequest::getInt('pid', 0);
		$move = 'order' . JRequest::getVar('move', 'down');

		// Ensure we have an ID to work with
		if (!$id) 
		{
			$this->setError(JText::_('CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		// Ensure we have a parent ID to work with
		if (!$pid) 
		{
			$this->setError(JText::_('CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Get the element moving down - item 1
		$resource1 = new ResourcesAssoc($this->database);
		$resource1->loadAssoc($pid, $id);

		// Get the element directly after it in ordering - item 2
		$resource2 = clone($resource1);
		$resource2->getNeighbor($move);

		switch ($move)
		{
			case 'orderup':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource2->ordering;
				$orderdn = $resource1->ordering;

				$resource1->ordering = $orderup;
				$resource2->ordering = $orderdn;
			break;

			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource1->ordering;
				$orderdn = $resource2->ordering;

				$resource1->ordering = $orderdn;
				$resource2->ordering = $orderup;
			break;
		}

		// Save changes
		$resource1->store();
		$resource2->store();

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Rename an attachment
	 * 
	 * @return     string
	 */
	public function renameTask()
	{
		// Incoming
		$id   = JRequest::getInt('id', 0);
		$name = JRequest::getVar('name', '');

		// Ensure we have everything we need
		if ($id && $name != '') 
		{
			$r = new ResourcesResource($this->database);
			$r->load($id);
			$r->title = $name;
			$r->store();
		}

		// Echo the name
		echo $name;
	}

	/**
	 * Save an attachment
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Incoming
		$pid = JRequest::getInt('pid', 0);
		if (!$pid) 
		{
			$this->setError(JText::_('CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			$this->setError(JText::_('CONTRIBUTE_NO_FILE'));
			$this->displayTask($pid);
			return;
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		// Ensure file names fit.
		$ext = JFile::getExt($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		if (strlen($file['name']) > 230)
		{
			$file['name'] = substr($file['name'], 0, 230);
			$file['name'] .= '.' . $ext;
		}

		// Instantiate a new resource object
		$row = new ResourcesResource($this->database);
		if (!$row->bind($_POST)) 
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}
		$row->title        = ($row->title) ? $row->title : $file['name'];
		$row->introtext    = $row->title;
		$row->created      = date('Y-m-d H:i:s');
		$row->created_by   = $this->juser->get('id');
		$row->published    = 1;
		$row->publish_up   = date('Y-m-d H:i:s');
		$row->publish_down = '0000-00-00 00:00:00';
		$row->standalone   = 0;
		$row->path         = ''; // make sure no path is specified just yet

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}
		
		// File already exists
		if ($row->loadByFile($file['name'], $pid))
		{
			$this->setError(JText::_('A file with this name and type appears to already exist.'));
			$this->displayTask($pid);
			return;
		}
		
		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}

		if (!$row->id) 
		{
			$row->id = $row->insertid();
		}

		// Build the path
		$listdir = $this->_buildPathFromDate($row->created, $row->id, '');
		$path = $this->_buildUploadPath($listdir, '');

		// Make sure the upload path exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('COM_CONTRIBUTE_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask($pid);
				return;
			}
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setError(JText::_('COM_CONTRIBUTE_ERROR_UPLOADING'));
		} 
		else 
		{
			// File was uploaded
			// Check the file type
			$row->type = $this->_getChildType($file['name']);

			// If it's a package (ZIP, etc) ...
			if ($row->type == 38) 
			{
				/*jimport('joomla.filesystem.archive');
				
				// Extract the files
				if (!JArchive::extract($file_to_unzip, $path)) {
					$this->setError(JText::_('Could not extract package.'));
				}*/
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');

				if (!extension_loaded('zlib')) 
				{
					$this->setError(JText::_('COM_CONTRIBUTE_ZLIB_PACKAGE_REQUIRED'));
				} 
				else 
				{
					// Check the table of contents and look for a Breeze viewer.swf file
					$isbreeze = 0;

					$zip = new PclZip($path . DS . $file['name']);

					$file_to_unzip = preg_replace('/(.+)\..*$/', '$1', $path . DS . $file['name']);

					if (($list = $zip->listContent()) == 0) 
					{
						die('Error: '.$zip->errorInfo(true));
					}

					for ($i=0; $i<sizeof($list); $i++)
					{
						if (substr($list[$i]['filename'], strlen($list[$i]['filename']) - 10, strlen($list[$i]['filename'])) == 'viewer.swf') 
						{
							$isbreeze = $list[$i]['filename'];
							break;
						}
						//$this->setError(substr($list[$i]['filename'], strlen($list[$i]['filename']), -4).' '.substr($file['name'], strlen($file['name']), -4));
					}
					if (!$isbreeze) 
					{
						for ($i=0; $i<sizeof($list); $i++)
						{
							if (strtolower(substr($list[$i]['filename'], -3)) == 'swf'
							 && substr($list[$i]['filename'], strlen($list[$i]['filename']), -4) == substr($file['name'], strlen($file['name']), -4)) 
							{
								$isbreeze = $list[$i]['filename'];
								break;
							}
							//$this->setError(substr($list[$i]['filename'], strlen($list[$i]['filename']), -4).' '.substr($file['name'], strlen($file['name']), -4));
						}
					}

					// It IS a breeze presentation
					if ($isbreeze) 
					{
						// unzip the file
						$do = $zip->extract($path);
						if (!$do) 
						{
							$this->setError(JText::_('COM_CONTRIBUTE_UNABLE_TO_EXTRACT_PACKAGE'));
						} 
						else 
						{
							$row->path = $listdir . DS . $isbreeze;

							@unlink($path . DS . $file['name']);
						}
						$row->type = $this->_getChildType($row->path);
						$row->title = $isbreeze;
					}
				}
			}
		}

		// Scan for viruses
		$fpath = $path . DS . $file['name'];
		exec("clamscan -i --no-summary --block-encrypted $fpath", $output, $status);
		if ($status == 1)
		{
			if (JFile::delete($fpath)) 
			{
				// Delete associations to the resource
				$row->deleteExistence();

				// Delete resource
				$row->delete();
			}

			$this->setError(JText::_('File rejected because the anti-virus scan failed.'));
			$this->displayTask($pid);
			return;
		}

		if (!$row->path) 
		{
			$row->path = $listdir . DS . $file['name'];
		}
		$row->path = ltrim($row->path, DS);

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}

		// Instantiate a ResourcesAssoc object
		$assoc = new ResourcesAssoc($this->database);

		// Get the last child in the ordering
		$assoc->ordering = $assoc->getLastOrder($pid);
		$assoc->ordering = ($assoc->ordering) ? $assoc->ordering : 0;

		// Increase the ordering - new items are always last
		$assoc->ordering++;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id  = $row->id;
		$assoc->grouping  = 0;
		if (!$assoc->check()) 
		{
			$this->setError($assoc->getError());
		}
		if (!$assoc->store(true)) 
		{
			$this->setError($assoc->getError());
		}
		else
		{
			$dbh =& JFactory::getDBO();

			if (is_readable($path . DS . $file['name']))
			{
				$hash = @sha1_file($path . DS . $file['name']);

				if (!empty($hash))
				{
					$dbh->setQuery('SELECT id FROM #__document_text_data WHERE hash = \'' . $hash . '\'');
					if (!($doc_id = $dbh->loadResult()))
						{
						$dbh->execute('INSERT INTO #__document_text_data(hash) VALUES (\'' . $hash . '\')');
						$doc_id = $dbh->insertId();
					}

					$dbh->execute('INSERT IGNORE INTO #__document_resource_rel(document_id, resource_id) VALUES (' . (int)$doc_id . ', ' . (int)$row->id . ')');
					system('/usr/bin/textifier ' . escapeshellarg($path . DS . $file['name']) . ' >/dev/null');
				}
			}
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Delete a file
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{

		// Incoming parent ID
		$pid = JRequest::getInt('pid', 0);
		if (!$pid) 
		{
			$this->setError(JText::_('CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Incoming child ID
		$id = JRequest::getInt('id', 0);
		if (!$id) 
		{
			$this->setError(JText::_('CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Load resource info
		$row = new ResourcesResource($this->database);
		$row->load($id);

		// Check for stored file
		if ($row->path != '') 
		{
			$listdir = $row->path;
		} 
		else 
		{
			// No stored path, derive from created date		
			$listdir = $this->_buildPathFromDate($row->created, $id, '');
		}

		// Build the path
		$path = $this->_buildUploadPath($listdir, '');

		// Check if the folder even exists
		if (!file_exists($path) or !$path) 
		{
			$this->setError(JText::_('COM_CONTRIBUTE_FILE_NOT_FOUND'));
		} 
		else 
		{
			// Attempt to delete the folder
			if (!JFile::delete($path)) 
			{
				$this->setError(JText::_('COM_CONTRIBUTE_UNABLE_TO_DELETE_FILE'));
			}
		}

		if (!$this->getError()) 
		{
			$uploadPath = DS . trim($this->config->get('uploadpath', '/site/resources'), DS);

			$year  = substr(trim($row->created), 0, 4);
			$month = substr(trim($row->created), 5, 2);

			$file  = basename($path);
			$path  = substr($path, 0, (strlen($path) - strlen($file)));
			$path  = str_replace(JPATH_ROOT, '', $path);
			$path  = str_replace($uploadPath, '', $path);

			$bits  = explode('/', $path);
			$p = array();
			$b = '';
			$g = array_pop($bits);
			foreach ($bits as $bit)
			{
				if ($bit == '/' || $bit == $year || $bit == $month || $bit == Hubzero_View_Helper_Html::niceidformat($id)) 
				{
					$b .= ($bit != DS) ? DS . $bit : '';
				} 
				else if ($bit != DS) 
				{
					$p[] = $bit;
				}
			}
			if (count($p) > 1) 
			{
				$p = array_reverse($p);
				foreach ($p as $v)
				{
					$npath = JPATH_ROOT . $uploadPath . $b . DS . $v;

					// Check if the folder even exists
					if (!is_dir($npath) or !$npath) 
					{
						$this->setError(JText::_('COM_CONTRIBUTE_DIRECTORY_NOT_FOUND'));
					} 
					else 
					{
						// Attempt to delete the folder
						if (!JFolder::delete($npath)) 
						{
							$this->setError(JText::_('COM_CONTRIBUTE_UNABLE_TO_DELETE_DIRECTORY'));
						}
					}
				}
			}

			// Delete associations to the resource
			$row->deleteExistence();

			// Delete resource
			$row->delete();
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Display a list of attachments
	 * 
	 * @param      integer $id Resource ID
	 * @return     void
	 */
	public function displayTask($id=null)
	{
		$this->view->setLayout('display');

		// Incoming
		if (!$id) 
		{
			$id = JRequest::getInt('id', 0);
		}

		// Ensure we have an ID to work with
		if (!$id) 
		{
			JError::raiseError(500, JText::_('CONTRIBUTE_NO_ID'));
			return;
		}

		// Initiate a resource helper class
		$helper = new ResourcesHelper($id, $this->database);
		$helper->getChildren();

		// get config
		$this->view->config   = $this->config;
		$this->view->children = $helper->children;
		$this->view->path     = '';
		$this->view->id       = $id;

		// Push some styles to the template
		$this->_getStyles($this->_option, 'create.css');

		// Push some scripts to the template
		$this->_getScripts('assets/js/create');

		// Set errors to view
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Build the absolute path to a resource's file upload
	 * 
	 * @param      string $listdir Primary upload directory
	 * @param      string $subdir  Sub directory of $listdir
	 * @return     string 
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		if ($subdir) 
		{
			$subdir = DS . trim($subdir, DS);
		}

		// Get the configured upload path
		$base = DS . trim($this->rconfig->get('uploadpath', '/site/resources'), DS);

		// Make sure the path doesn't end with a slash
		$listdir = DS . trim($listdir, DS);

		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base)) == $base) 
		{
			// Yes - ... this really shouldn't happen
		} 
		else 
		{
			// No - append it
			$listdir = $base . $listdir;
		}

		// Build the path
		return JPATH_ROOT . $listdir . $subdir;
	}

	/**
	 * Get the child's type ID based on file extension
	 * 
	 * @param      string $filename File name
	 * @return     integer
	 */
	private function _getChildType($filename)
	{
		jimport('joomla.filesystem.file');

		$ftype = strtolower(JFile::getExt($filename));

		switch ($ftype)
		{
			case 'mov': $type = 15; break;
			case 'swf': $type = 32; break;
			case 'ppt': $type = 35; break;
			case 'asf': $type = 37; break;
			case 'asx': $type = 37; break;
			case 'wmv': $type = 37; break;
			case 'zip': $type = 38; break;
			case 'tar': $type = 38; break;
			case 'pdf': $type = 33; break;
			default:    $type = 13; break;
		}

		return $type;
	}

	/**
	 * Build a path from a creation date (0000-00-00 00:00:00)
	 * 
	 * @param      string  $date Resource created date
	 * @param      integer $id   Resource ID
	 * @param      string  $base Base path to prepend
	 * @return     string
	 */
	private function _buildPathFromDate($date, $id, $base='')
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs)) 
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		if ($date) 
		{
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} 
		else 
		{
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = Hubzero_View_Helper_Html::niceidformat($id);

		$path = $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;

		return $path;
	}
}
