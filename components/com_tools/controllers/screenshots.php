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

ximport('Hubzero_Tool_Version');
ximport('Hubzero_Tool');
ximport('Hubzero_Group');
ximport('Hubzero_Trac_Project');
ximport('Hubzero_Controller');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'helper.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'screenshot.php');

/**
 * Controller class for contributing a tool
 */
class ToolsControllerScreenshots extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		// Load the com_resources component config
		$rconfig =& JComponentHelper::getParams('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Reorder screenshots
	 * 
	 * @return     void
	 */
	public function reorderTask()
	{
		// Incoming parent ID
		$pid = JRequest::getInt('pid', 0);
		$version = JRequest::getVar('version', 'dev');

		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// get tool object
		$obj = new Tool($this->database);
		$this->_toolid = $obj->getToolIdFromResource($pid);

		// make sure user is authorized to go further
		if (!$this->check_access($this->_toolid)) 
		{
			JError::raiseError(403, JText::_('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		// Get version id
		$objV = new ToolVersion($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming
		$file_toleft = JRequest::getVar('fl', '');
		$order_toleft = JRequest::getInt('ol', 1);
		$file_toright = JRequest::getVar('fr', '');
		$order_toright = JRequest::getInt('or', 0);

		$neworder_toleft = ($order_toleft != 0) ? $order_toleft - 1 : 0;
		$neworder_toright = $order_toright + 1;

		// Instantiate a new screenshot object
		$ss = new ResourcesScreenshot($this->database);
		$shot1 = $ss->getScreenshot($file_toright, $pid, $vid);
		$shot2 = $ss->getScreenshot($file_toleft, $pid, $vid);

		// Do we have information stored?
		if ($shot1) 
		{
			$ss->saveScreenshot($file_toright, $pid, $vid, $neworder_toright);
		}
		else 
		{
			$ss->saveScreenshot($file_toright, $pid, $vid, $neworder_toright, true);
		}
		if ($shot1) 
		{
			$ss->saveScreenshot($file_toleft, $pid, $vid, $neworder_toleft);
		}
		else 
		{
			$ss->saveScreenshot($file_toleft, $pid, $vid, $neworder_toleft, true);
		}

		$this->_rid = $pid;

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Edit a screenshot
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		// Incoming parent ID
		$pid = JRequest::getInt('pid', 0);
		$version = JRequest::getVar('version', 'dev');
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming child ID
		$this->view->file = JRequest::getVar('filename', '');
		if (!$this->view->file) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Load resource info
		$row = new ResourcesResource($this->database);
		$row->load($pid);

		// Get version id	
		$objV = new ToolVersion($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		// Build the path
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		$listdir  = ResourcesHtml::build_path($row->created, $pid, '') . DS . $vid;
		$this->view->wpath = DS . trim($this->rconfig->get('uploadpath'), DS) . DS . $listdir;
		$this->view->upath = $this->_buildUploadPath($listdir, '');

		// Instantiate a new screenshot object
		$ss = new ResourcesScreenshot($this->database);
		$this->view->shot = $ss->getScreenshot($this->view->file, $pid, $vid);

		// Get the app
		$app =& JFactory::getApplication();

		// Set the page title
		$this->view->title = JText::_(strtoupper($this->_name)) . ': ' . JText::_('COM_TOOLS_TASK_EDIT_SS');
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		$this->view->pid = $pid;
		$this->view->version = $version;
		$this->view->vid = $vid;

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
	 * Save changes to a screenshot
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Incoming parent ID
		$pid = JRequest::getInt('pid', 0);
		$version = JRequest::getVar('version', 'dev');
		$vid = JRequest::getInt('vid', 0);
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming
		$file = JRequest::getVar('filename', '');
		$title = preg_replace('/\s+/', ' ', JRequest::getVar('title', ''));

		// Instantiate a new screenshot object
		$ss = new ResourcesScreenshot($this->database);
		$shot = $ss->getScreenshot($file, $pid, $vid);
		$files = $ss->getFiles($pid, $vid);

		if ($shot) 
		{
			// update entry
			$ss->loadFromFilename($file, $pid, $vid);
		} 
		else 
		{
			// make new entry
			$ss->versionid = $vid;
			$ordering = $ss->getLastOrdering($pid, $vid);
			$ss->ordering = ($ordering) ? $ordering + 1 : count($files) + 1; // put in the end
			$ss->filename = $file;
			$ss->resourceid = $pid;
		}
		$ss->title = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $title);

		if (!$ss->store()) 
		{
			$this->setError($ss->getError());
		}

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Delete a screenshot
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		// Incoming parent ID
		$pid = JRequest::getInt('pid', 0);
		$version = JRequest::getVar('version', 'dev');
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming child ID
		$file = JRequest::getVar('filename', '');
		if (!$file) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Load resource info
		$row = new ResourcesResource($this->database);
		$row->load($pid);

		// Get version id
		$objV = new ToolVersion($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		// Build the path
		$listdir  = ResourcesHtml::build_path($row->created, $pid, '');
		$listdir .= DS.$vid;
		$path = $this->_buildUploadPath($listdir, '');

		// Check if the folder even exists
		if (!is_dir($path) or !$path) 
		{
			$this->setError(JText::_('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		} 
		else 
		{
			if (!JFile::exists($path . DS . $file)) 
			{
				$this->displayTask($pid, $version);
				return;
			}

			if (!JFile::delete($path . DS . $file)) 
			{
				$this->setError(JText::_('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
				$this->displayTask($pid, $version);
				return;
			}
			else 
			{
				// Delete thumbnail
				$tn = ResourcesHtml::thumbnail($file);
				JFile::delete($path . DS . $tn);

				// Instantiate a new screenshot object
				$ss = new ResourcesScreenshot($this->database);
				$ss->deleteScreenshot($file, $pid, $vid);
			}
		}

		$this->_rid = $pid;

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Upload a screenshot
	 * 
	 * @return     void
	 */
	public function uploadTask()
	{
		// Incoming
		$pid = JRequest::getInt('pid', 0);
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		$version = JRequest::getVar('version', 'dev');
		$title = preg_replace('/\s+/', ' ',JRequest::getVar('title', ''));
		$allowed = array('.gif','.jpg','.png','.bmp');
		$changing_version = JRequest::getInt('changing_version', 0);
		if ($changing_version) 
		{
			// reload screen
			$this->displayTask($pid, $version);
			return;
		}

		// Get resource information
		$resource = new ResourcesResource($this->database);
		$resource->load($pid);

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			$this->displayTask($pid, $version);
			return;
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$file['name'] = str_replace('-tn', '', $file['name']);
		$file_basename = substr($file['name'], 0, strripos($file['name'], '.')); // strip extention
		$file_ext      = substr($file['name'], strripos($file['name'], '.'));

		// Make sure we have an allowed format
		if (!in_array(strtolower($file_ext), $allowed)) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_WRONG_FILE_FORMAT'));
			$this->displayTask($pid, $version);
			return;
		}

		// Get version id	
		$objV = new ToolVersion($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		// Instantiate a new screenshot object
		$row = new ResourcesScreenshot($this->database);

		// Check if file with the same name already exists
		$files = $row->getFiles($pid, $vid);
		if (count($files) > 0) 
		{
			$files = ToolsHelperUtils::transform($files, 'filename');
			foreach ($files as $f) 
			{
				if ($f == $file['name']) 
				{
					// append extra characters in the end
					$file['name'] = $file_basename . '_' . time() . $file_ext;
					$file_basename = $file_basename . '_' . time();
				}
			}
		}

		$row->title = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $title);
		$row->versionid = $vid;
		$ordering = $row->getLastOrdering($pid, $vid);
		$row->ordering = ($ordering) ? $ordering + 1 : count($files) + 1; // put in the end
		$row->filename = $file['name'];
		$row->resourceid = $pid;

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			$this->displayTask($pid, $version);
			return;
		}

		// Build the path
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		$listdir  = ResourcesHtml::build_path($resource->created, $pid, '');
		$listdir .= DS . $vid;
		$path = $this->_buildUploadPath($listdir, '');

		// Make sure the upload path exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH') . $path);
				$this->displayTask($pid, $version);
				return;
			}
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setError(JText::_('COM_TOOLS_ERROR_UPLOADING'));
		}
		else 
		{
			// Store new content
			if (!$row->store()) 
			{
				$this->setError($row->getError());
				$this->displayTask($pid, $version);
				return;
			}

			if (!$row->id) 
			{
				$row->id = $row->insertid();
			}

			// Create thumbnail
			$ss_height = (intval($this->config->get('screenshot_maxheight', 58)) > 30) ? intval($this->config->get('screenshot_maxheight', 58)) : 58;
			$ss_width  = (intval($this->config->get('screenshot_maxwidth', 91)) > 80)  ? intval($this->config->get('screenshot_maxwidth', 91))  : 91;

			$tn = ResourcesHtml::thumbnail($file['name']);
			if ($file_ext != '.swf') 
			{
				$this->_createThumb($path . DS . $file['name'], $ss_width, $ss_height, $path, $tn);
			}
			else 
			{
				//$this->_createAnimThumb($path . DS . $file['name'], $ss_width, $ss_height, $path, $tn);
			}
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			$this->displayTask($pid, $version);
			return;
		}

		$this->_rid = $pid;

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Create a thumbnail for an animation
	 * 
	 * @param      string  $tmpname   Uploaded file name
	 * @param      integer $maxwidth  Max image width
	 * @param      integer $maxheight Max image height
	 * @param      string  $save_dir  Directory to save to
	 * @param      string  $save_name Name to save file as
	 * @return     boolean False if errors, True if successful
	 */
	private function _createAnimThumb($tmpname, $maxwidth, $maxheight, $save_dir, $save_name)
	{
		$imorig = imagecreatefromjpeg(JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'assets' . DS . 'img' . DS . 'anim.jpg');
		$x = imageSX($imorig);
		$y = imageSY($imorig);

		$yc = $y*1.555555;
		$d  = $x>$yc ? $x : $yc;
		$c  = $d>$maxwidth ? $maxwidth/$d : $maxwidth;
		$av = $x*$c;
		$ah = $y*$c;

		$im = imagecreate($av, $ah);
		$im = imagecreatetruecolor($av, $ah);
		if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, $av, $ah, $x, $y)) 
		{
			if (imagegif($im, $save_dir . $save_name)) 
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
	}

	/**
	 * Create a thumbnail from a picture
	 * 
	 * @param      string  $tmpname   Uploaded file name
	 * @param      integer $maxwidth  Max image width
	 * @param      integer $maxheight Max image height
	 * @param      string  $save_dir  Directory to save to
	 * @param      string  $save_name Name to save file as
	 * @return     boolean False if errors, True if successful
	 */
	private function _createThumb($tmpname, $maxwidth, $maxheight, $save_dir, $save_name)
	{
		$save_dir = rtrim($save_dir, DS) . DS;
		$gis = getimagesize($tmpname);

		switch ($gis[2])
		{
			case '1': $imorig = imagecreatefromgif($tmpname);  break;
			case '2': $imorig = imagecreatefromjpeg($tmpname); break;
			case '3': $imorig = imagecreatefrompng($tmpname);  break;
			case '4': $imorig = imagecreatefromwbmp($tmpname); break;
			default:  $imorig = imagecreatefromjpeg($tmpname); break;
		}

		$x = imageSX($imorig);
		$y = imageSY($imorig);
		if ($gis[0] <= $maxwidth)
		{
			$av = $x;
			$ah = $y;
		}
		else
		{
			$yc = $y*1.555555;
			$d  = $x>$yc ? $x : $yc;
			$c  = $d>$maxwidth ? $maxwidth/$d : $maxwidth;
			$av = $x*$c;
			$ah = $y*$c;
		}

		$im = imagecreate($av, $ah);
		$im = imagecreatetruecolor($av, $ah);
		if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, $av, $ah, $x, $y)) 
		{
			if (imagegif($im, $save_dir . $save_name)) 
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
	}

	/**
	 * Copy files
	 * 
	 * @return     void
	 */
	public function copyTask()
	{
		$version = JRequest::getVar('version', 'dev');
		$rid     = JRequest::getInt('rid', 0);
		$from    = $version == 'dev' ? 'current' : 'dev';

		// get admin priviliges
		$this->_authorize();

		// Get version id
		$objV = new ToolVersion($this->database);
		$to   = $objV->getVersionIdFromResource($rid, $version);
		$from = $objV->getVersionIdFromResource($rid, $from);

		// get tool id
		$obj = new Tool($this->database);
		$toolid = $obj->getToolIdFromResource($rid);

		if ($from == 0 or $to == 0 or $rid == 0) 
		{
			JError::raiseError(500, JText::_('COM_TOOLS_Missing ids'));
			return;
		}

		if ($toolid && $this->_checkAccess($toolid)) 
		{
			if ($this->transfer($from, $to, $rid)) 
			{
				// Push through to the screenshot view
				$this->displayTask($rid, $version);
			}
		}
	}

	/**
	 * Move files
	 * 
	 * @return     void
	 */
	public function moveTask()
	{
		$from    = JRequest::getInt('from', 0);
		$to      = JRequest::getInt('to', 0);
		$rid     = JRequest::getInt('rid', 0);
		$version = JRequest::getVar('version', 'dev');

		// get admin priviliges
		$this->_authorize();

		if ($this->config->get('access-admin-component') or $this->_task == 'copy') 
		{
			if ($from == 0 or $to == 0 or $rid == 0) 
			{
				JError::raiseError(500, JText::_('COM_TOOLS_Missing ids'));
				return;
			}

			if ($this->transfer($from, $to, $rid)) 
			{
				if ($this->_task == 'copy')
				{
					$this->_rid = $rid;
					// Push through to the screenshot view
					$this->displayTask($rid, $version);
				}
				else 
				{
					echo JText::_('COM_TOOLS_Success!');
				}
			}
			else if ($this->_task != 'copy') 
			{
				$this->setError(JText::_('COM_TOOLS_Didn\'t work. There were some problems...'));
			}
		}
		else 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
		}
	}

	/**
	 * Transfer files from one version to another
	 * 
	 * @param      string  $sourceid Source version ID
	 * @param      string  $destid   Destination version ID
	 * @param      integer $rid      Resource ID
	 * @return     boolean False if errors, True on success
	 */
	public function transfer($sourceid, $destid, $rid)
	{
		$xlog =& Hubzero_Factory::getLogger();
		$xlog->logDebug(__FUNCTION__ . '()');

		// Get resource information
		$resource = new ResourcesResource($this->database);
		$resource->load($rid);

		// Get screenshot information
		$ss = new ResourcesScreenshot($this->database);
		$shots = $ss->getFiles($rid, $sourceid);

		// Build the path
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		$listdir = ResourcesHtml::build_path($resource->created, $rid, '');
		$srcdir  = $listdir . DS . $sourceid;
		$destdir = $listdir . DS . $destid;
		$src     = $this->_buildUploadPath($srcdir, '');
		$dest    = $this->_buildUploadPath($destdir, '');

		jimport('joomla.filesystem.folder');

		// Make sure the path exist
		if (!is_dir($src)) 
		{
			if (!JFolder::create($src, 0777)) 
			{
				$this->setError(JText::_('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return false;
			}
		}
		$xlog->logDebug(__FUNCTION__ . "() $src");

		// do we have files to transfer?
		$files = JFolder::files($src, '.', false, true, array());
		$xlog->logDebug(__FUNCTION__ . "() $files");
		if (!empty($files)) 
		{
			// Copy directory
			$xlog->logDebug(__FUNCTION__ . "() copying $src to $dest");
			if (!JFolder::copy($src, $dest, '', true)) 
			{
				return false;
			}
			else 
			{
				// Update screenshot information for this resource
				$ss->updateFiles($rid, $sourceid, $destid, $copy=1);

				$xlog->logDebug(__FUNCTION__ . '() updated files');
				return true;
			}
		}

		$xlog->logDebug(__FUNCTION__ . '() done');

		return true;
	}

	/**
	 * Display a list of screenshots for this entry
	 * 
	 * @param      integer $rid     Resource ID
	 * @param      string  $version Tool version
	 * @return     void
	 */
	public function displayTask($rid=NULL, $version=NULL)
	{
		$this->view->setLayout('display');
		
		// Incoming
		if (!$rid) 
		{
			$rid = JRequest::getInt('rid', 0);
		}
		if (!$version) 
		{
			$version = JRequest::getVar('version', 'dev');
		}

		// Ensure we have an ID to work with
		if (!$rid) 
		{
			JError::raiseError(500, JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return;
		}
		// Get resource information
		$resource = new ResourcesResource($this->database);
		$resource->load($rid);

		// Get version id	
		$objV = new ToolVersion($this->database);
		$vid = $objV->getVersionIdFromResource($rid, $version);

		// Do we have a published tool?
		$this->view->published = $objV->getCurrentVersionProperty($resource->alias, 'id');

		// Get screenshot information for this resource
		$ss = new ResourcesScreenshot($this->database);
		$this->view->shots = $ss->getScreenshots($rid, $vid);

		// Build paths
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		$path = ResourcesHtml::build_path($resource->created, $rid, '');
		$this->view->upath = JPATH_ROOT . DS . trim($this->rconfig->get('uploadpath'), DS) . $path;
		$this->view->wpath = DS . trim($this->rconfig->get('uploadpath'), DS) . $path;
		if ($vid) 
		{
			$this->view->upath .= DS . $vid;
			$this->view->wpath .= DS . $vid;
		}

		// get config
		$this->view->cparams =& JComponentHelper::getParams('com_resources');
		$this->view->version = $version;
		$this->view->rid = $rid;

		$this->_getStyles($this->_option, 'assets/css/component.css');

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
	 * Construct a path to upload files to
	 * 
	 * @param      string $listdir Base directory
	 * @param      string $subdir  Sub-directory
	 * @return     string 
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		if ($subdir) 
		{
			$subdir = DS . trim($subdir, DS);
		}

		// Get the configured upload path
		$rconfig =& JComponentHelper::getParams('com_resources');

		$base_path = $rconfig->get('uploadpath');
		if ($base_path) 
		{
			// Make sure the path doesn't end with a slash
			$base_path = DS . trim($base_path, DS);
		}

		// Make sure the path doesn't end with a slash
		$listdir = DS . trim($listdir, DS);
		
		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base_path)) == $base_path) 
		{
			// Yes - ... this really shouldn't happen
		} 
		else 
		{
			// No - append it
			$listdir = $base_path . $listdir;
		}

		// Build the path
		return JPATH_ROOT . $listdir . $subdir;
	}

	/**
	 * Get the resource child type from the file extension
	 * 
	 * @param      string $filename File to check
	 * @return     integer Numerical file type
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
	 * Check if the current user has access to this tool
	 * 
	 * @param      unknown $toolid       Tool ID
	 * @param      integer $allowAdmins  Allow admins access?
	 * @param      boolean $allowAuthors Allow authors access?
	 * @return     boolean True if they have access
	 */
	private function _checkAccess($toolid, $allowAdmins=1, $allowAuthors=false)
	{
		// Create a Tool object
		$obj = new Tool($this->database);

		// allow to view if admin
		if ($this->config->get('access-manage-component') && $allowAdmins) 
		{
			return true;
		}

		// check if user in tool dev team
		if ($developers = $obj->getToolDevelopers($toolid)) 
		{
			foreach ($developers as $dv) 
			{
				if ($dv->uidNumber == $this->juser->get('id')) 
				{
					return true;
				}
			}
		}

		// allow access to tool authors
		if ($allowAuthors) 
		{
			// Nothing here?
		}

		return false;
	}

	/**
	 * Authorization checks
	 * 
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if ($this->juser->get('guest')) 
		{
			return;
		}

		// if no admin group is defined, allow superadmin to act as admin
		// otherwise superadmins can only act if they are also a member of the component admin group
		if (($admingroup = trim($this->config->get('admingroup', '')))) 
		{
			ximport('Hubzero_User_Helper');
			// Check if they're a member of admin group
			$ugs = Hubzero_User_Helper::getGroups($this->juser->get('id'));
			if ($ugs && count($ugs) > 0) 
			{
				$admingroup = strtolower($admingroup);
				foreach ($ugs as $ug)
				{
					if (strtolower($ug->cn) == $admingroup) 
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
		else 
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
}
