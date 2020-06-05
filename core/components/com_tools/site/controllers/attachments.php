<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Resources\Models\Entry;
use Components\Resources\Models\Association;
use Filesystem;
use Component;
use Request;
use Route;
use Lang;
use User;
use Date;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'html.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'tool.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php';
include_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

/**
 * Controller class for contributing a tool
 */
class Attachments extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->_authorize();

		// Load the com_resources component config
		$rconfig = Component::params('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Reorder an attachment
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$pid  = Request::getInt('pid', 0);
		$move = Request::getWord('move', 'down');

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid);
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid);
		}

		switch ($move)
		{
			case 'up':
				$move = -1;
			break;

			case 'down':
				$move = 1;
			break;
		}

		// Move the record
		$association = Association::oneByRelationship($pid, $id);

		if (!$association->move($move))
		{
			$this->setError($association->getError());
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Rename an attachment
	 *
	 * @return  string
	 */
	public function renameTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$name = Request::getString('name', '');

		// Ensure we have everything we need
		if ($id && $name)
		{
			$resource = Entry::oneOrFail($id);
			$resource->set('title', (string)$name);
			$resource->save();
		}

		// Echo the name
		echo $name;
	}

	/**
	 * Save an attachment
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Incoming
		$pid = Request::getInt('pid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid);
		}

		// get tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$this->_toolid = $obj->getToolIdFromResource($pid);

		// make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid))
		{
			App::abort(403, Lang::txt('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		// Incoming file
		$file = Request::getArray('upload', '', 'files');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			return $this->displayTask($pid);
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);

		// Ensure file names fit.
		$ext = Filesystem::extension($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		if (strlen($file['name']) > 230)
		{
			$file['name'] = substr($file['name'], 0, 230);
			$file['name'] .= '.' . $ext;
		}

		// Instantiate a new resource object
		$row = Entry::blank()->set(array(
			'title'        => $file['name'],
			'introtext'    => $file['name'],
			'created'      => Date::toSql(),
			'created_by'   => User::get('id'),
			'published'    => Entry::STATE_PUBLISHED,
			'publish_up'   => Date::toSql(),
			'standalone'   => 0,
			'access'       => 0,
			'path'         => '', // make sure no path is specified just yet
			'type'         => $this->_getChildType($file['name'])
		));

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->displayTask($pid);
		}

		// Build the path
		$path = $row->filespace();

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask($pid);
			}
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_UPLOADING'));
		}
		else
		{
			// File was uploaded
			// Check the file type
			$row->set('type', $this->_getChildType($file['name']));
		}

		if (!$row->get('path'))
		{
			$row->set('path', $row->relativepath() . DS . $file['name']);
		}
		$row->set('path', ltrim($row->get('path'), DS));

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->displayTask($pid);
		}

		// Create new parent/child association
		if (!$row->makeChildOf($pid))
		{
			$this->setError($row->getError());
			return $this->displayTask($pid);
		}

		$this->_rid = $pid;

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid);
		}

		// get tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$this->_toolid = $obj->getToolIdFromResource($pid);

		// make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid))
		{
			App::abort(403, Lang::txt('COM_TOOLS_ALERTNOTAUTH'));
		}

		// Incoming child ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid);
		}

		// Load resource info
		$resource = Entry::oneOrFail($id);

		// Check for stored file
		if ($resource->get('path') != '')
		{
			$listdir = $resource->get('path');
		}
		else
		{
			// No stored path, derive from created date
			$listdir = $resource->relativepath();
		}

		// Build the path
		$path = $resource->basepath() . DS . $listdir;

		// Check if the path is a URL or exists
		if (!file_exists($path) or !$path or substr($resource->get('path'), 0, strlen('http')) == 'http')
		{
			//$this->setError(Lang::txt('COM_CONTRIBUTE_FILE_NOT_FOUND'));
		}
		else
		{
			if ($path == $resource->basepath()
			 || $path == $resource->relativepath())
			{
				$this->setError(Lang::txt('Invalid file path.'));
			}
			else
			{
				// Attempt to delete the file
				if (!Filesystem::delete($path))
				{
					$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_DIRECTORY'));
				}
			}
		}

		if (!$this->getError())
		{
			// Delete resource
			$resource->destroy();
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Display a list of attachments
	 *
	 * @param   integer  $id  Resource ID
	 * @return  void
	 */
	public function displayTask($id=null)
	{
		// Incoming
		if (!$id)
		{
			$id = Request::getInt('rid', 0);
		}

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
		}

		$allowupload = Request::getInt('allowupload', 1);

		$resource = Entry::oneOrNew($id);

		// get config
		$cparams = Component::params('com_resources');
		$path = '';
		$children = $resource->children()
			->ordered()
			->rows();

		// Output HTML
		$this->view
			->set('id', $id)
			->set('resource', $resource)
			->set('children', $children)
			->set('cparams', $cparams)
			->set('path', $path)
			->set('allowupload', $allowupload)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
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
		return PATH_APP . $listdir . $subdir;
	}

	/**
	 * Get the child's type ID based on file extension
	 *
	 * @param   string   $filename  File name
	 * @return  integer
	 */
	private function _getChildType($filename)
	{
		$ftype = strtolower(Filesystem::extension($filename));

		switch ($ftype)
		{
			case 'mov':
				$type = 15;
				break;
			case 'swf':
				$type = 32;
				break;
			case 'ppt':
				$type = 35;
				break;
			case 'asf':
				$type = 37;
				break;
			case 'asx':
				$type = 37;
				break;
			case 'wmv':
				$type = 37;
				break;
			case 'zip':
				$type = 38;
				break;
			case 'tar':
				$type = 38;
				break;
			case 'pdf':
				$type = 33;
				break;
			default:
				$type = 13;
				break;
		}

		return $type;
	}

	/**
	 * Check if user has access
	 *
	 * @param   integer  $toolid        Tool ID
	 * @param   boolean  $allowAuthors  Allow tool authors?
	 * @return  boolean  True if user has access, False if not
	 */
	private function _checkAccess($toolid, $allowAuthors=false)
	{
		// allow to view if admin
		if ($this->config->get('access-manage-component'))
		{
			return true;
		}

		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);

		// check if user in tool dev team
		if ($developers = $obj->getToolDevelopers($toolid))
		{
			foreach ($developers as $dv)
			{
				if ($dv->uidNumber == User::get('id'))
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
	 * @param   string   $assetType  Asset type
	 * @param   integer  $assetId    Asset id to check against
	 * @return  void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (User::get('guest'))
		{
			return;
		}

		// if no admin group is defined, allow superadmin to act as admin
		// otherwise superadmins can only act if they are also a member of the component admin group
		if (($admingroup = trim($this->config->get('admingroup', ''))))
		{
			// Check if they're a member of admin group
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
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
