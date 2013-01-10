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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('Hubzero.Api.Controller');

/**
 * API controller for the time component
 */
class CoursesApiController extends Hubzero_Api_Controller
{
	/**
	 * Execute!
	 * 
	 * @return void
	 */
	function execute()
	{
		// Import some Joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		// Get the request type
		$this->format = JRequest::getVar('format', 'application/json');

		// Get a database object
		$this->db = JFactory::getDBO();

		// Switch based on task (i.e. "/api/courses/xxxxx")
		switch($this->segments[0])
		{
			// Units
			case 'unitsave':               $this->unitSave();                 break;

			// Asset groups
			case 'assetgroupsave':         $this->assetGroupSave();           break;
			case 'assetgroupreorder':      $this->assetGroupReorder();        break;

			// Assets
			case 'assetnew':               $this->assetNew();                 break;
			case 'assetsave':              $this->assetSave();                break;
			case 'assetsreorder':          $this->assetsReorder();            break;
			case 'assettogglepublished':   $this->assetTogglePublished();     break;

			default:                       $this->method_not_found();         break;
		}
	}

	//--------------------------
	// Units functions
	//--------------------------

	/**
	 * Save a course unit
	 * 
	 * @return 201 created on success
	 */
	private function unitSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Import needed courses JTable libraries
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'unit.php');

		$unitObj = new CoursesTableUnit($this->db);

		if($id = JRequest::getInt('id', false))
		{
			if (!$unitObj->load($id))
			{
				$this->setMessage("Loading unit $id failed", 500, 'Internal server error');
				return;
			}
		}

		// We'll always save the title again, even if it's just to the same thing
		$title      = (!empty($unitObj->title)) ? $unitObj->title : 'New Unit';
		$row->title = JRequest::getString('title', $title);
		$row->title = preg_replace("/[^a-zA-Z0-9 \-\:\.]/", "", $row->title);
		$row->alias = strtolower(str_replace(' ', '', $row->title));

		// When creating a new unit
		if(!$id)
		{
			$row->offering_id = JRequest::getInt('offering_id', 0);
			$row->created     = date('Y-m-d H:i:s');
			$row->created_by  = JFactory::getApplication()->getAuthn('user_id');
		}

		// Save the unit
		if (!$unitObj->save($row))
		{
			$this->setMessage("Saving unit $id failed", 500, 'Internal server error');
			return;
		}

		// Create a placeholder for our return object
		$assetGroups = array();

		// If this is a new unit, give it some default asset groups
		// Create a top level asset group for each of lectures, homework, and exam
		if(!$id)
		{
			// Get our asset group object
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');

			foreach (array('Lectures', 'Homework', 'Exam') as $key)
			{
				$assetGroupObj = new CoursesTableAssetGroup($this->db);

				$row = new stdclass();

				$row->id          = null;
				$row->title       = $key;
				$row->alias       = strtolower(str_replace(' ', '', $row->title));
				$row->unit_id     = $unitObj->id;
				$row->parent      = 0;
				$row->created     = date('Y-m-d H:i:s');
				$row->created_by  = JFactory::getApplication()->getAuthn('user_id');

				// Save the asset group
				if (!$assetGroupObj->save($row))
				{
					$this->setMessage("Asset group save failed", 500, 'Internal server error');
					return;
				}

				$return = new stdclass();
				$return->assetgroup_id      = $assetGroupObj->id;
				$return->assetgroup_title   = $assetGroupObj->title;
				$return->course_id          = $this->course_id;
				$return->assetgroup_style   = '';

				$assetGroups[] = $return;
			}
		}

		// Return message
		$this->setMessage(
			array(
				'unit_id'     => $unitObj->id,
				'unit_title'  => $unitObj->title,
				'course_id'   => $this->course_id,
				'assetgroups' => $assetGroups
			),
			201, 'Created');
	}

	//--------------------------
	// Asset group functions
	//--------------------------

	/**
	 * Save an asset group
	 * 
	 * @return 201 created on success
	 */
	private function assetGroupSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Get our asset group object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');
		$assetGroupObj = new CoursesTableAssetGroup($this->db);

		if($id = JRequest::getInt('id', false))
		{
			if (!$assetGroupObj->load($id))
			{
				$this->setMessage("Loading asset group $id failed", 500, 'Internal server error');
				return;
			}
		}

		// We'll always save the title again, even if it's just to the same thing
		$title            = (!empty($assetGroupObj->title)) ? $assetGroupObj->title : 'New asset group';
		$row->title       = JRequest::getString('title', $title);
		$row->title       = preg_replace("/[^a-zA-Z0-9 \-\:\.]/", "", $row->title);
		$row->alias       = strtolower(str_replace(' ', '', $row->title));
		$row->description = '';

		// When creating a new asset group
		if(!$id)
		{
			$row->unit_id     = JRequest::getInt('unit_id', 0);
			$row->parent      = JRequest::getInt('parent', 0);
			$row->created     = date('Y-m-d H:i:s');
			$row->created_by  = JFactory::getApplication()->getAuthn('user_id');
		}

		// Save the asset group
		if (!$assetGroupObj->save($row))
		{
			$this->setMessage("Asset group save failed", 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage(
			array(
				'assetgroup_id'   =>$assetGroupObj->id,
				'assetgroup_title'=>$assetGroupObj->title,
				'assetgroup_style'=>'display:none',
				'course_id'       =>$this->course_id),
			201, 'Created');
	}

	/**
	 * Reorder assets
	 * 
	 * @return 201 created on success
	 */
	private function assetGroupReorder()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		/*$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}*/

		// Get our asset group object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');
		$assetGroupObj = new CoursesTableAssetGroup($this->db);

		$groups = JRequest::getVar('assetgroupitem', array());

		$order = 1;

		foreach ($groups as $id)
		{
			if (!$assetGroupObj->load($id))
			{
				$this->setMessage("Loading asset group $id failed", 500, 'Internal server error');
				return;
			}

			// Save the asset group
			if (!$assetGroupObj->save(array('ordering'=>$order)))
			{
				$this->setMessage("Asset group save failed", 500, 'Internal server error');
				return;
			}

			$order++;
		}


		// Return message
		$this->setMessage('New order saved', 201, 'Created');
	}

	//--------------------------
	// Asset functions
	//--------------------------

	/**
	 * Upload a file, creating an asset and asset association
	 * 
	 * @return 201 created on success
	 */
	private function assetNew()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// @TODO: clean up after errors if we've already created assets or asset groups but something in the upload fails
		// @TODO: add virus scan
		// @TODO: add multi-file support

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Include needed files
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');

		// @FIXME: should these come from the global settings, or should they be courses specific
		// Get config
		$config =& JComponentHelper::getParams('com_media');

		// Allowed extensions for uplaod
		$allowedExtensions = array_values(array_filter(explode(',', $config->get('upload_extensions'))));

		// Max upload size
		$sizeLimit = $config->get('upload_maxsize');

		// Get the file
		if (isset($_GET['files']))
		{
			$stream = true;
			$file = $_GET['files'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['files']))
		{
			$stream = false;
			$file = $_FILES['files']['name'][0];
			$size = (int) $_FILES['files']['size'];
		}
		else
		{
			$this->setMessage("No files given", 500, 'Internal server error');
			return;
		}

		// Get the file extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext = $pathinfo['extension'];

		// Check to make sure we have an allowable file extension
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			$this->setMessage("File has an invalid extension, it should be one of $these", 500, 'Internal server error');
			return;
		}
		// Check to make sure we have a file and its not too big
		if ($size == 0) 
		{
			$this->setMessage("File is empty", 500, 'Internal server error');
			return;
		}
		if ($size > $sizeLimit) 
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Hubzero_View_Helper_Html::formatSize($sizeLimit));
			$this->setMessage("File is too large. Max file upload size is $max", 500, 'Internal server error');
			return;
		}

		// Assign type based on extension
		switch ($ext)
		{
			case 'mp4':
			case 'zip':
				$type = 'video';
				break;

			default:
				$type = 'file';
				break;
		}

		// Create our asset table object
		$assetObj = new CoursesTableAsset($this->db);

		$row->title      = $filename;
		$row->type       = $type;
		$row->url        = $file;
		$row->created    = date('Y-m-d H:i:s');
		$row->created_by = JFactory::getApplication()->getAuthn('user_id');
		$row->state      = 0; // unpublished
		$row->course_id  = JRequest::getInt('course_id', 0);

		// Save the asset
		if (!$assetObj->save($row))
		{
			$this->setMessage("Asset save failed", 500, 'Internal server error');
			return;
		}

		// Create asset assoc object
		$assocObj = new CoursesTableAssetAssociation($this->db);

		$row2->asset_id  = $assetObj->get('id');
		$row2->scope     = JRequest::getCmd('scope', 'asset_group');
		$row2->scope_id  = JRequest::getInt('scope_id', 0);

		// Save the asset association
		if (!$assocObj->save($row2))
		{
			$this->setMessage("Asset association save failed", 500, 'Internal server error');
			return;
		}

		// Get courses config
		$cconfig =& JComponentHelper::getParams('com_courses');

		// Build the upload path if it doesn't exist
		$uploadDirectory = JPATH_ROOT . DS . trim($cconfig->get('uploadpath', '/site/courses'), DS) . DS . $row->course_id . DS . $row2->asset_id . DS;

		// @FIXME: cleanup asset and asset association if directory creation fails

		// Make sure upload directory is writable
		if (!is_dir($uploadDirectory))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($uploadDirectory))
			{
				$this->setMessage("Server error. Unable to create upload directory", 500, 'Internal server error');
				return;
			}
		}
		if (!is_writable($uploadDirectory))
		{
			$this->setMessage("Server error. Upload directory isn't writable", 500, 'Internal server error');
			return;
		}

		// Get the final file path
		$file = $uploadDirectory . $filename . '.' . $ext;

		// Save the file
		if ($stream)
		{
			// Read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			// Move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['files']['tmp_name'][0], $file);

			// Exapand zip file if applicable - we're assuming zips are hubpresenter videos
			if($ext == 'zip')
			{
				$escaped_file = escapeshellarg($file);
				// @FIXME: check for symlinks and other potential security concerns
				// @FIXME: also need to handle zip files where the hubpresenter contents are in a directory,
				//         as opposed to dirctly in the zip file
				if($result = shell_exec("unzip $escaped_file -d $uploadDirectory"))
				{
					// Remove original archive
					jimport('joomla.filesystem.file');
					JFile::delete($file);

					// Remove MACOSX dirs if there
					jimport('joomla.filesystem.folder');
					JFolder::delete($uploadDirectory . '__MACOSX');
				}
			}
		}

		$files = array(
			'asset_id'    => $row2->asset_id,
			'asset_title' => $row->title,
			'asset_type'  => $row->type,
			'asset_url'   => $file,
			'course_id'   => $row->course_id
		);

		// Return message
		$this->setMessage(array('files'=>array($files)), 201, 'Created');
	}

	/**
	 * Save an asset
	 * 
	 * @return 201 created on success
	 */
	private function assetSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Get our asset object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
		$assetObj = new CoursesTableAsset($this->db);

		if($id = JRequest::getInt('id', false))
		{
			if (!$assetObj->load($id))
			{
				$this->setMessage("Loading asset $id failed", 500, 'Internal server error');
				return;
			}
		}

		// We'll always save the title again, even if it's just to the same thing
		$title            = (!empty($assetObj->title)) ? $assetObj->title : 'New asset';
		$row->title       = JRequest::getString('title', $title);
		$row->title       = preg_replace("/[^a-zA-Z0-9 \-\:\.]/", "", $row->title);

		// If we have an incoming url, update the url, otherwise, leave it alone
		if($url = JRequest::getVar('url', false))
		{
			$row->url = urldecode($url);
		}

		// When creating a new asset (which probably won't happen via this method, but rather the assetNew method above)
		if(!$id)
		{
			$row->type        = JRequest::getWord('type', 'file');
			$row->state       = 0;
			$row->course_id   = JRequest::getInt('course_id', 0);
			$row->created     = date('Y-m-d H:i:s');
			$row->created_by  = JFactory::getApplication()->getAuthn('user_id');
		}

		// Save the asset
		if (!$assetObj->save($row))
		{
			$this->setMessage("Asset group save failed", 500, 'Internal server error');
			return;
		}

		$files = array();

		// If we're creating a new asset, we should also create a new asset association
		if(!$id)
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');

			// Create asset assoc object
			$assocObj = new CoursesTableAssetAssociation($this->db);

			$row2->asset_id  = $assetObj->get('id');
			$row2->scope     = JRequest::getCmd('scope', 'asset_group');
			$row2->scope_id  = JRequest::getInt('scope_id', 0);

			// Save the asset association
			if (!$assocObj->save($row2))
			{
				$this->setMessage("Asset association save failed", 500, 'Internal server error');
				return;
			}

			$files = array(
				'asset_id'    => $row2->asset_id,
				'asset_title' => $row->title,
				'asset_type'  => $row->type,
				'asset_url'   => $assetObj->url,
				'course_id'   => $row->course_id
			);
		}

		// Return message
		$this->setMessage(
			array(
				'asset_id'    => $assetObj->id,
				'asset_title' => $assetObj->title,
				'course_id'   => $this->course_id,
				'files'       => array($files)),
			201, 'Created');
	}

	/**
	 * Reorder assets
	 * 
	 * @return 201 created on success
	 */
	private function assetsReorder()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		/*$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}*/

		// Get our asset group object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
		$assetAssocationObj = new CoursesTableAssetAssociation($this->db);

		$assets   = JRequest::getVar('asset', array());
		$scope_id = JRequest::getInt('scope_id', 0);
		$scope    = JRequest::getWord('scope', 'asset_group');

		$order = 1;

		foreach ($assets as $asset_id)
		{
			if (!$assetAssocationObj->loadByAssetScope($asset_id, $scope_id, $scope))
			{
				$this->setMessage("Loading asset association $asset_id failed", 500, 'Internal server error');
				return;
			}

			// Save the asset group
			if (!$assetAssocationObj->save(array('ordering'=>$order)))
			{
				$this->setMessage("Asset asssociation save failed", 500, 'Internal server error');
				return;
			}

			$order++;
		}


		// Return message
		$this->setMessage('New asset order saved', 201, 'Created');
	}

	/**
	 * Toggle the published state of an asset
	 * 
	 * @return 201 created on success
	 */
	private function assetTogglePublished()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// @TODO: log who makes the change

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Get the asset id
		$id = JRequest::getInt('id', 0);

		// Get our asset object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
		$assetObj = new CoursesTableAsset($this->db);

		// Load the asset
		if (!$assetObj->load($id))
		{
			$this->setMessage("Loading asset $id failed", 500, 'Internal server error');
			return;
		}

		$state = ($assetObj->state == 1) ? 0 : 1;

		// Save the asset state
		if (!$assetObj->save(array('state'=>$state)))
		{
			$this->setMessage("Saving asset $id state failed", 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage('Asset state successfully saved', 201, 'Created');
	}

	//--------------------------
	// Miscelaneous methods
	//--------------------------

	/**
	 * Default method - not found
	 * 
	 * @return 404, method not found error
	 */
	private function method_not_found()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Set the error message
		$this->_response->setErrorMessage(404, 'Not found');
		return;
	}

	/**
	 * Helper function to check whether or not someone is using oauth and authorized
	 * 
	 * @return bool - true if in group, false otherwise
	 */
	private function authorize()
	{
		// Get the user id
		$user_id = JFactory::getApplication()->getAuthn('user_id');

		$authorized           = array();
		$authorized['view']   = false;
		$authorized['create'] = false;
		$authorized['manage'] = false;
		$authorized['admin']  = false;

		// Not logged in and/or not using OAuth
		if(!is_numeric($user_id))
		{
			return $authorized;
		}
		else
		{
			$authorized['create'] = true;
		}

		// Get the course id
		$this->course_id = JRequest::getInt('course_id', 0);

		// Load the course page
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
		$course = CoursesModelCourse::getInstance($this->course_id);

		if (in_array($user_id, $course->get('managers')))
		{
			$authorized['view']   = true;
			$authorized['manage'] = true;
			$authorized['admin']  = true;
		}
		elseif (in_array($user_id, $course->get('members')))
		{
			$authorized['view'] = true;
		}

		return $authorized;
	}
}