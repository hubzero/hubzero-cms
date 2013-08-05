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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'contribute.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'assoc.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');

/**
 * Controller class for contributing a tool
 */
class ToolsControllerAttachments extends Hubzero_Controller
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
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		// Ensure we have a parent ID to work with
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
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
		$id = JRequest::getInt('id', 0);
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
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// get tool object
		$obj = new Tool($this->database);
		$this->_toolid = $obj->getToolIdFromResource($pid);

		// make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid)) 
		{
			JError::raiseError(403, JText::_('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_FILE'));
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
		$row->title = ($row->title) ? $row->title : $file['name'];
		$row->introtext = $row->title;
		$row->created = date('Y-m-d H:i:s');
		$row->created_by = $this->juser->get('id');
		$row->published = 1;
		$row->publish_up = date('Y-m-d H:i:s');
		$row->publish_down = '0000-00-00 00:00:00';
		$row->standalone = 0;
		$row->path = ''; // make sure no path is specified just yet

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
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
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		$listdir = ResourcesHtml::build_path($row->created, $row->id, '');
		$path = $this->_buildUploadPath($listdir, '');

		// Make sure the upload path exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask($pid);
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
			// File was uploaded
			// Check the file type
			$row->type = $this->_getChildType($file['name']);
		}

		if (!$row->path) 
		{
			$row->path = $listdir . DS . $file['name'];
		}
		if (substr($row->path, 0, 1) == DS) 
		{
			$row->path = substr($row->path, 1, strlen($row->path));
		}

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
		$order = $assoc->getLastOrder($pid);
		$order = ($order) ? $order : 0;

		// Increase the ordering - new items are always last
		$order = $order + 1;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id = $row->id;
		$assoc->ordering = $order;
		$assoc->grouping = 0;
		if (!$assoc->check()) 
		{
			$this->setError($assoc->getError());
		}
		if (!$assoc->store(true)) 
		{
			$this->setError($assoc->getError());
		}
		$this->_rid = $pid;

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
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// get tool object
		$obj = new Tool($this->database);
		$this->_toolid = $obj->getToolIdFromResource($pid);

		// make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid)) 
		{
			JError::raiseError(403, JText::_('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		// Incoming child ID
		$id = JRequest::getInt('id', 0);
		if (!$id) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		jimport('joomla.filesystem.folder');

		// Load resource info
		$row = new ResourcesResource($this->database);
		$row->load($id);

		// Check for stored file
		if ($row->path == '') 
		{
			$this->setError(JText::_('COM_TOOLS_Error: file path not found.'));
			$this->displayTask($pid);
			return;
		}

		// Get resource path
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		$listdir = ResourcesHtml::build_path($row->created, $id, '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, '');

		// Check if the folder even exists
		if (!is_dir($path) or !$path) 
		{
			$this->setError(JText::_('COM_TOOLS_DIRECTORY_NOT_FOUND'));
		} 
		else 
		{
			// Attempt to delete the file
			if (!JFolder::delete($path)) 
			{
				$this->setError(JText::_('COM_TOOLS_UNABLE_TO_DELETE_DIRECTORY'));
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
			$id = JRequest::getInt('rid', 0);
		}

		// Ensure we have an ID to work with
		if (!$id) 
		{
			JError::raiseError(500, JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return;
		}

		$this->view->id = $id;
		$this->view->allowupload = JRequest::getInt('allowupload', 1);

		$this->view->resource = new ResourcesResource($this->database);
		$this->view->resource->load($id);

		// Initiate a resource helper class
		$helper = new ResourcesHelper($id, $this->database);
		$helper->getChildren();

		// get config
		$this->view->cparams =& JComponentHelper::getParams('com_resources');
		$this->view->path = '';
		$this->view->children = $helper->children;

		$this->_getStyles($this->_option, 'assets/css/component.css');

		// Set errors to view
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->setError($error);
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
	 * Check if user has access
	 * 
	 * @param      integer $toolid       Tool ID
	 * @param      boolean $allowAuthors Allow tool authors?
	 * @return     boolean True if user has access, False if not
	 */
	private function _checkAccess($toolid, $allowAuthors=false)
	{
		// Create a Tool object
		$obj = new Tool($this->database);

		// allow to view if admin
		if ($this->config->get('access-manage-component')) 
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
